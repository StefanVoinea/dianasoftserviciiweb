<?php

namespace App\Services\Anaf;

use App\Models\AnafDeclaratie;
use App\Models\AnafSocietate;
use Carbon\Carbon;

/**
 * Aduce istoricul depunerilor din programul vechi al clientului (declmf.mde).
 *
 * Tabelul „depuneri" tine cate un rand pe depunere: tipul declaratiei, CUI-ul,
 * denumirea firmei, luna si anul, indexul si starea de la ANAF, plus caile de
 * pe calculatorul clientului catre PDF-ul depus si recipisa lui.
 *
 * De aici se fac trei lucruri: firmele inrolate fara denumire si-o primesc din
 * tabel, depunerile intra in fila Declaratii fiscale ca istoric incheiat, iar
 * caile fisierelor se dau mai departe arhivarii — copierea o face programul
 * local, pe calculatorul unde stau si fisierele, si arhiva.
 */
class ImportDepuneri
{
    /**
     * Importa CSV-ul tabelului „depuneri" pentru un client anume.
     *
     * Compania se scrie explicit pe fiecare rand: importul se face din
     * administrare, unde nu exista un client curent.
     *
     * @return array{
     *     randuri: int, scrise: int, existente: int, denumiri: int,
     *     de_arhivat: array<int, array{id: int, fisier: ?string, recipisa: ?string}>,
     *     sarite: array<int, string>
     * }
     */
    public function importaCsv(string $cale, int $companie): array
    {
        $fisier = fopen($cale, 'r');

        if ($fisier === false) {
            throw new \RuntimeException('Fișierul nu a putut fi citit: ' . $cale);
        }

        $antet = fgetcsv($fisier);

        if ($antet === false) {
            fclose($fisier);

            throw new \RuntimeException('CSV-ul este gol.');
        }

        $antet[0] = preg_replace('/^\xEF\xBB\xBF/', '', $antet[0]);
        $antet = array_map(function ($coloana) {
            return strtolower(trim((string) $coloana));
        }, $antet);

        if (!in_array('cui', $antet, true) || !in_array('tip_declaratie', $antet, true)) {
            fclose($fisier);

            throw new \RuntimeException(
                'Fișierul nu arată a tabelul „depuneri": lipsesc coloanele CUI și Tip_declaratie.'
            );
        }

        $rezultat = [
            'randuri' => 0,
            'scrise' => 0,
            'existente' => 0,
            'respinse' => 0,
            'sterse' => 0,
            'denumiri' => 0,
            'de_arhivat' => [],
            'sarite' => [],
        ];

        $denumiri = [];

        while (($rand = fgetcsv($fisier)) !== false) {
            $date = array_combine($antet, array_pad($rand, count($antet), ''));

            $cui = preg_replace('/\D/', '', (string) ($date['cui'] ?? ''));
            $tip = strtoupper(trim((string) ($date['tip_declaratie'] ?? '')));

            if ($cui === '' || $tip === '') {
                continue;
            }

            $rezultat['randuri']++;

            $denumire = trim((string) ($date['den_firma'] ?? ''));

            if ($denumire !== '' && !isset($denumiri[$cui])) {
                $denumiri[$cui] = $denumire;
            }

            /*
             * Intra doar depunerile care au trecut: cele respinse de ANAF sunt
             * incercari, nu istoric de pastrat. Un rand respins importat data
             * trecuta se si sterge, ca tabelul sa ramana curat.
             */
            if (!$this->depusaValid($date)) {
                $rezultat['respinse']++;
                $rezultat['sterse'] += $this->stergeImportata($companie, $cui, $tip, $date);

                continue;
            }

            try {
                $declaratie = $this->scrie($companie, $cui, $tip, $denumire, $date);
            } catch (\Exception $e) {
                $rezultat['sarite'][] = $tip . ' ' . $cui . ': ' . $e->getMessage();

                continue;
            }

            $declaratie->wasRecentlyCreated ? $rezultat['scrise']++ : $rezultat['existente']++;

            $fisierLocal = $this->caleLocala($date['fisier'] ?? '');
            $recipisaLocala = $this->caleLocala($date['recipisa'] ?? '');

            if (($fisierLocal && !$declaratie->arhiva_semnat) || ($recipisaLocala && !$declaratie->arhiva_recipisa)) {
                $rezultat['de_arhivat'][] = [
                    'id' => $declaratie->id,
                    'fisier' => $declaratie->arhiva_semnat ? null : $fisierLocal,
                    'recipisa' => $declaratie->arhiva_recipisa ? null : $recipisaLocala,
                ];
            }
        }

        fclose($fisier);

        $rezultat['denumiri'] = $this->completeazaDenumirile($companie, $denumiri);

        return $rezultat;
    }

    /**
     * A trecut depunerea la ANAF?
     *
     * Starea buna incepe cu „Documentul este valid"; cea respinsa cu „Fisierul
     * depus nu este un document valid" — inceputul deosebeste, nu cuprinsul,
     * care contine „este valid" in amandoua. Fara stare scrisa, recipisa
     * descarcata e semn ca depunerea a trecut.
     */
    protected function depusaValid(array $date): bool
    {
        $stare = trim((string) ($date['stare_declaratie'] ?? ''));

        if ($stare !== '') {
            return stripos($stare, 'documentul este valid') === 0;
        }

        return $this->caleLocala((string) ($date['recipisa'] ?? '')) !== null;
    }

    /**
     * Sterge randul respins adus de un import de dinaintea filtrarii.
     *
     * Doar randurile cu semnatura importului (fara certificat si fara fisiere
     * de lucru pe server): o declaratie lucrata in aplicatie nu se atinge.
     */
    protected function stergeImportata(int $companie, string $cui, string $tip, array $date): int
    {
        $index = trim((string) ($date['index_recipisa'] ?? '')) ?: null;

        if ($index === null) {
            return 0;
        }

        return AnafDeclaratie::query()->toateCompaniile()
            ->where('company_id', $companie)
            ->where('cui', $cui)
            ->where('tip', $tip)
            ->where('index_recipisa', $index)
            ->where('pas', 'finalizat')
            ->whereNull('certificat_id')
            ->whereNull('cale_xml')
            ->delete();
    }

    /** Scrie sau regaseste depunerea; reimportul nu dubleaza nimic. */
    protected function scrie(int $companie, string $cui, string $tip, string $denumire, array $date): AnafDeclaratie
    {
        $index = trim((string) ($date['index_recipisa'] ?? '')) ?: null;
        $luna = (int) ($date['luna'] ?? 0) ?: null;
        $anul = (int) ($date['anul'] ?? 0) ?: null;

        /*
         * Indexul de incarcare deosebeste depunerile intre ele; fara el, cheia
         * cade pe fisierul depus, ca doua depuneri ale aceleiasi luni sa nu se
         * calce una pe alta.
         */
        $cheie = [
            'company_id' => $companie,
            'cui' => $cui,
            'tip' => $tip,
            'index_recipisa' => $index,
        ];

        if ($index === null) {
            $cheie['nume_fisier'] = basename(str_replace('\\', '/', (string) ($date['fisier'] ?? ''))) ?: null;
        }

        return AnafDeclaratie::query()->toateCompaniile()->firstOrCreate($cheie, [
            'den_firma' => $denumire ?: null,
            'luna' => $luna,
            'anul' => $anul,
            'rectificativa' => stripos((string) ($date['rectificativa'] ?? ''), 'da') === 0,
            'nume_fisier' => basename(str_replace('\\', '/', (string) ($date['fisier'] ?? ''))) ?: null,
            // Istoric incheiat: depusa demult, cu soarta ei stiuta. „finalizat"
            // o tine departe de coada care mai cere recipise de la ANAF.
            'pas' => 'finalizat',
            'semnat' => true,
            'stare_declaratie' => mb_substr(trim((string) ($date['stare_declaratie'] ?? '')), 0, 1000) ?: null,
            'data_depunere' => $this->data($date['data_depunere'] ?? ''),
            'data_recipisa' => $this->data($date['date_inregistrare'] ?? ''),
        ]);
    }

    /** Firmele inrolate fara denumire si-o primesc din tabel. */
    protected function completeazaDenumirile(int $companie, array $denumiri): int
    {
        $completate = 0;

        foreach ($denumiri as $cui => $denumire) {
            $completate += AnafSocietate::query()->toateCompaniile()
                ->where('company_id', $companie)
                ->where('cif', $cui)
                ->where(function ($intrebare) {
                    $intrebare->whereNull('denumire')->orWhere('denumire', '');
                })
                ->update(['denumire' => $denumire, 'denumire_sursa' => 'declmf.mde']);
        }

        return $completate;
    }

    /**
     * O cale de pe calculatorul clientului, sau nimic.
     *
     * Coloana „Recipisa" tine si cuvantul „Eroare" cand depunerea a fost
     * respinsa: nu e o cale, deci nu e nimic de copiat.
     */
    protected function caleLocala(string $valoare): ?string
    {
        $valoare = trim($valoare);

        if ($valoare === '' || !preg_match('/^([A-Za-z]:[\\\\\\/]|\\\\\\\\)/', $valoare)) {
            return null;
        }

        return $valoare;
    }

    protected function data(string $valoare): ?Carbon
    {
        $valoare = trim($valoare);

        if ($valoare === '') {
            return null;
        }

        /*
         * Randurile vechi tin data ca text romanesc (23.07.2012 12:15:12);
         * cele noi vin din driverul Access cu ora americana (2/4/2026 10:54:30 AM).
         */
        foreach ([
            'd.m.Y H:i:s', 'd.m.Y H:i', 'd.m.Y',
            'n/j/Y g:i:s A', 'n/j/Y g:i A', 'n/j/Y H:i:s', 'n/j/Y',
            'Y-m-d H:i:s', 'Y-m-d',
        ] as $format) {
            try {
                return Carbon::createFromFormat($format, $valoare);
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }
}
