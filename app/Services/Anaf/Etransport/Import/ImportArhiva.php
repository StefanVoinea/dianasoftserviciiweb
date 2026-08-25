<?php

namespace App\Services\Anaf\Etransport\Import;

use App\Models\EtransportDeclaratie;
use App\Models\EtransportGestiune;
use App\Services\Anaf\Etransport\EtransportException;

/**
 * Arhiva zilnică a furnizorului: câte o ciornă de declarație pe fiecare factură.
 *
 * Emporio primește de la Teddy câte un ZIP pe zi de livrare, cu câte trei
 * fișiere pe factură: T02_* — recapitulația pe coduri vamale (liniile
 * declarației), D01_* — distinta cu destinația finală (magazinul, codul lui și
 * adresa), FT1_* — detaliul de articole, care nu ne trebuie.
 *
 * Dintr-o arhivă ies gata completate: partenerul, liniile cu denumirile din
 * nomenclator, valoarea în lei la cursul zilei facturii, factura la documente
 * și locul de descărcare — cu județul dedus din oraș. Rămân de completat doar
 * vehiculul, transportatorul și data transportului, apoi se depun pe rând.
 */
class ImportArhiva
{
    /** PTF-ul obișnuit al camioanelor Teddy: Borș 2 - A3. Rămâne editabil. */
    protected const PTF_IMPLICIT = 38;

    /** Orașele reședință de județ, pentru județul locului de descărcare. */
    protected const ORASE = [
        'BUCURESTI' => 40, 'BUCUREŞTI' => 40, 'BUCHARESTI' => 40, 'BUCHAREST' => 40,
        'ALBA IULIA' => 1, 'ARAD' => 2, 'PITESTI' => 3,
        'BACAU' => 4, 'ORADEA' => 5, 'BISTRITA' => 6, 'BOTOSANI' => 7, 'BRASOV' => 8,
        'BRAILA' => 9, 'BUZAU' => 10, 'RESITA' => 11, 'CLUJ-NAPOCA' => 12, 'CLUJ NAPOCA' => 12,
        'CONSTANTA' => 13, 'SFANTU GHEORGHE' => 14, 'TARGOVISTE' => 15, 'CRAIOVA' => 16,
        'GALATI' => 17, 'TARGU JIU' => 18, 'MIERCUREA CIUC' => 19, 'DEVA' => 20,
        'SLOBOZIA' => 21, 'IASI' => 22, 'VOLUNTARI' => 23, 'BAIA MARE' => 24,
        'DROBETA-TURNU SEVERIN' => 25, 'TARGU MURES' => 26, 'PIATRA NEAMT' => 27,
        'SLATINA' => 28, 'PLOIESTI' => 29, 'SATU MARE' => 30, 'ZALAU' => 31, 'SIBIU' => 32,
        'SUCEAVA' => 33, 'ALEXANDRIA' => 34, 'TIMISOARA' => 35, 'TULCEA' => 36,
        'VASLUI' => 37, 'RAMNICU VALCEA' => 38, 'FOCSANI' => 39, 'CALARASI' => 51,
        'GIURGIU' => 52,
    ];

    protected $fisiere;

    public function __construct(?ImportFisiere $fisiere = null)
    {
        $this->fisiere = $fisiere ?: new ImportFisiere();
    }

    /** Gestiunile companiei, pe codul furnizorului; se încarcă la primul import. */
    protected $gestiuni;

    /**
     * Citește arhiva și face câte o ciornă pe fiecare factură din ea.
     *
     * @return array{ciorne: array<int, array{id: int, factura: string, magazin: ?string}>, avertismente: array<int, string>, gestiuni_noi: array<int, array{cod_furnizor: string, denumire_furnizor: ?string}>}
     */
    public function importa(string $caleArhiva, ?string $cifDeclarant, ?int $userId = null): array
    {
        $arhiva = new \ZipArchive();

        if ($arhiva->open($caleArhiva) !== true) {
            throw new EtransportException('Arhiva nu a putut fi deschisă (se așteaptă un ZIP).');
        }

        $facturi = [];

        for ($i = 0; $i < $arhiva->numFiles; $i++) {
            $nume = basename($arhiva->getNameIndex($i));

            if (!preg_match('/^(T02|D01)_.*_(\d+)\.txt$/i', $nume, $gasit)) {
                continue;
            }

            $facturi[$gasit[2]][strtoupper($gasit[1])] = $arhiva->getFromIndex($i);
        }

        if ($facturi === []) {
            $arhiva->close();

            throw new EtransportException(
                'Arhiva nu are fișiere T02_* (recapitulația pe coduri vamale). Este arhiva zilnică a furnizorului?'
            );
        }

        ksort($facturi);

        $rezultat = ['ciorne' => [], 'avertismente' => [], 'gestiuni_noi' => []];

        foreach ($facturi as $factura => $bucati) {
            // Arhiva importata a doua oara nu dubleaza ciornele.
            if (EtransportDeclaratie::where('referinta_interna', 'Factura ' . $factura)->exists()) {
                $rezultat['avertismente'][] = 'Factura ' . $factura . ' era deja adusă; sărită.';

                continue;
            }

            if (!isset($bucati['T02'])) {
                $rezultat['avertismente'][] = 'Factura ' . $factura
                    . ': arhiva nu are recapitulația T02 — ciorna s-a făcut fără linii;'
                    . ' completați-le manual sau importați-le din fișier.';
            }

            try {
                $declaratie = $this->ciorna((string) $factura, $bucati, $cifDeclarant, $userId);
            } catch (\Exception $e) {
                $rezultat['avertismente'][] = 'Factura ' . $factura . ': ' . $e->getMessage();

                continue;
            }

            $rezultat['ciorne'][] = [
                'id' => $declaratie->id,
                'factura' => (string) $factura,
                'magazin' => $declaratie->loc_final['magazin_denumire'] ?? null,
            ];

            // Un cod de magazin nestiut inca: utilizatorul e intrebat cum se numeste gestiunea.
            $codMagazin = mb_strtoupper((string) ($declaratie->loc_final['magazin_cod'] ?? ''));

            if ($codMagazin !== ''
                && !isset($this->gestiunile()[$codMagazin])
                && !isset($rezultat['gestiuni_noi'][$codMagazin])) {
                $rezultat['gestiuni_noi'][$codMagazin] = [
                    'cod_furnizor' => $codMagazin,
                    'denumire_furnizor' => $declaratie->loc_final['magazin_denumire'] ?? null,
                ];
            }
        }

        $rezultat['gestiuni_noi'] = array_values($rezultat['gestiuni_noi']);

        $arhiva->close();

        return $rezultat;
    }

    /**
     * O ciornă dintr-o factură: liniile din T02, destinația din D01.
     *
     * Unele arhive vin fără T02 la anumite facturi: ciorna se face atunci
     * doar cu destinația și factura, iar liniile le pune omul.
     */
    protected function ciorna(string $factura, array $bucati, ?string $cifDeclarant, ?int $userId): EtransportDeclaratie
    {
        $citit = ['linii' => [], 'antet' => []];

        if (isset($bucati['T02'])) {
            // T02 se citeste cu parserul lui obisnuit, dintr-un fisier trecator.
            $cale = tempnam(sys_get_temp_dir(), 'etr');
            file_put_contents($cale, $bucati['T02']);

            try {
                $citit = $this->fisiere->importa([['nume' => 'T02_' . $factura . '.txt', 'cale' => $cale]]);
            } finally {
                @unlink($cale);
            }

            if ($citit['linii'] === []) {
                throw new EtransportException('recapitulația T02 nu are nicio linie de citit.');
            }
        }

        $antet = $citit['antet'];
        $destinatie = isset($bucati['D01']) ? $this->destinatia($bucati['D01']) : [];

        // Cand gestiunea e stiuta, denumirea magazinului se ia din ea, nu de la furnizor.
        $gestiune = isset($destinatie['magazin_cod'])
            ? ($this->gestiunile()[mb_strtoupper($destinatie['magazin_cod'])] ?? null)
            : null;

        if ($gestiune !== null) {
            $destinatie['magazin_denumire'] = $gestiune->denumire;
        }

        $dataFacturii = $antet['document_data']
            ?? (isset($bucati['D01']) ? $this->dataDinD01($bucati['D01']) : null);
        $curs = $dataFacturii ? (float) cursBNR($dataFacturii, $antet['valuta'] ?? 'EUR') : 0.0;

        $linii = [];

        foreach ($citit['linii'] as $linie) {
            $linie['scop_operatiune'] = 101;
            $linie['valoare_lei'] = $curs > 0 && $linie['valoare'] !== null
                ? round($linie['valoare'] * $curs, 2)
                : null;

            $linii[] = $linie;
        }

        return EtransportDeclaratie::create([
            'stare' => 'ciorna',
            'cif_declarant' => $cifDeclarant,
            'referinta_interna' => 'Factura ' . $factura,
            'tip_operatiune' => 10,
            'partener_tara' => $antet['partener_tara'] ?? 'IT',
            'partener_cod' => $antet['partener_cod'] ?? null,
            'partener_denumire' => $antet['partener_denumire'] ?? null,
            'transportator_tara' => 'RO',
            'loc_start' => ['tip' => 'ptf', 'cod_ptf' => self::PTF_IMPLICIT],
            'loc_final' => ['tip' => 'adresa'] + $destinatie,
            'documente' => [[
                'tip' => 20,
                'numar' => $factura,
                'data' => $dataFacturii ?: '',
                'observatii' => '',
            ]],
            'linii' => $linii,
            'valuta' => $antet['valuta'] ?? 'EUR',
            'curs' => $curs ?: null,
            'fisiere_importate' => ['arhiva: factura ' . $factura],
            'user_id' => $userId,
        ]);
    }

    /** Gestiunile companiei curente, pe codul furnizorului (NEG*). */
    protected function gestiunile()
    {
        if ($this->gestiuni === null) {
            $this->gestiuni = EtransportGestiune::peCodFurnizor();
        }

        return $this->gestiuni;
    }

    /**
     * Data facturii din antetul distinctei D01, când T02 lipsește:
     * „Number ......:   10053419     del  3/07/2026".
     */
    protected function dataDinD01(string $continut): ?string
    {
        if (preg_match('/(?:Number|Numero)\s*\.*\s*:\s*\d+\s+del\s+(\d{1,2})\/(\d{1,2})\/(\d{4})/i', $continut, $gasit)) {
            return sprintf('%04d-%02d-%02d', $gasit[3], $gasit[2], $gasit[1]);
        }

        return null;
    }

    /**
     * Destinația finală, din antetul distinctei D01.
     *
     * Blocul arată așa, pe trei rânduri:
     *
     *   Destinazione.:  0029818 007 S.C. EMPORIO COM SRL MAGAZIN TERRAN
     *   NEG0000548      BD GEN GH MAGHERU, NR 33, SECTOR 1,
     *                   000000     BUCURESTI     RO
     *
     * Primul rând poartă denumirea magazinului, al doilea codul lui și strada,
     * al treilea orașul — din care se deduce județul.
     *
     * @return array<string, mixed>
     */
    protected function destinatia(string $continut): array
    {
        $randuri = preg_split('/\r\n|\r|\n/', $continut);

        foreach ($randuri as $index => $rand) {
            /*
             * Distinta are doua coloane pe acelasi rand: destinatia in stanga,
             * scadentele si sconturile in dreapta, despartite de spatii multe.
             * Se ia doar coloana din stanga — taiata la 3+ spatii.
             */
            if (!preg_match('/Destinazione\.*\s*:\s*\d+\s+\d+\s+(.+?)(?:\s{3,}|\s*$)/i', $rand, $gasit)) {
                continue;
            }

            $destinatie = ['magazin_denumire' => trim($gasit[1])];
            $oras = '/^\s*(\d{5,6})?\s+(.+?)\s{2,}RO(?:\s|$)/i';

            /*
             * Randul urmator: codul magazinului si strada. Depozitele nu au
             * cod NEG — ramane doar strada, tot in coloana din stanga.
             */
            if (preg_match('/^\s*(NEG\w+)\s{2,}(.+?)(?:\s{3,}|\s*$)/i', $randuri[$index + 1] ?? '', $gasit)) {
                $destinatie['magazin_cod'] = $gasit[1];
                $destinatie['strada'] = trim($gasit[2], ' ,');
            } elseif (!preg_match($oras, $randuri[$index + 1] ?? '')
                && preg_match('/^\s+(\S.*?)(?:\s{3,}|\s*$)/', $randuri[$index + 1] ?? '', $gasit)) {
                $destinatie['strada'] = trim($gasit[1], ' ,');
            }

            // Apoi orasul: "000000     BUCURESTI     RO".
            foreach ([$index + 2, $index + 1] as $randOras) {
                if (!preg_match($oras, $randuri[$randOras] ?? '', $gasit)) {
                    continue;
                }

                $destinatie['localitate'] = trim($gasit[2]);

                $judet = self::ORASE[strtoupper(trim($gasit[2]))] ?? null;

                if ($judet !== null) {
                    $destinatie['cod_judet'] = $judet;
                }

                if (!empty($gasit[1]) && (int) $gasit[1] !== 0) {
                    $destinatie['cod_postal'] = $gasit[1];
                }

                break;
            }

            return $destinatie;
        }

        return [];
    }
}
