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
 * La retururi (nota de credit, arhiva „RESO") recapitulația lipsește; în locul
 * ei vine T01_* — lista pe articole, din care liniile ies grupate pe cod vamal.
 * Numele fișierelor nu poartă atunci numărul documentului; el se ia dinăuntru.
 * Returul se declară ca livrare intracomunitară: traseul e întors, de la
 * magazin (codul lui e pe rândul „From" al distinctei) la punctul de frontieră.
 * Adresa magazinului se ia din ultima declarație a aceluiași magazin.
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

    /** Ce e de spus despre ciorna în lucru (adresă lipsă etc.); iese ca avertisment. */
    protected $note = [];

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

        /*
         * Fisierele aceleiasi facturi au acelasi nume dupa prefix:
         * T02_2_TEDDY_..._10053419.TXT si D01_2_TEDDY_..._10053419.TXT. Dupa el
         * se aduna; numarul facturii se afla abia dupa, din nume sau dinauntru.
         */
        $grupuri = [];

        for ($i = 0; $i < $arhiva->numFiles; $i++) {
            $nume = basename($arhiva->getNameIndex($i));

            if (!preg_match('/^(T01|T02|D01)_(.+)\.txt$/i', $nume, $gasit)) {
                continue;
            }

            $grupuri[$gasit[2]][strtoupper($gasit[1])] = $arhiva->getFromIndex($i);
        }

        if ($grupuri === []) {
            $arhiva->close();

            throw new EtransportException(
                'Arhiva nu are fișiere T02_* (recapitulația pe coduri vamale) sau T01_* (lista pe articole).'
                . ' Este arhiva zilnică a furnizorului?'
            );
        }

        $facturi = [];

        foreach ($grupuri as $stem => $bucati) {
            $facturi[$this->numarulFacturii($stem, $bucati)] = $bucati;
        }

        ksort($facturi);

        $rezultat = ['ciorne' => [], 'avertismente' => [], 'gestiuni_noi' => []];

        foreach ($facturi as $factura => $bucati) {
            // Arhiva importata a doua oara nu dubleaza ciornele.
            if (EtransportDeclaratie::whereIn('referinta_interna', ['Factura ' . $factura, 'Retur ' . $factura])->exists()) {
                $rezultat['avertismente'][] = 'Factura ' . $factura . ' era deja adusă; sărită.';

                continue;
            }

            if (!isset($bucati['T02']) && !isset($bucati['T01'])) {
                $rezultat['avertismente'][] = 'Factura ' . $factura
                    . ': arhiva nu are recapitulația T02 (nici lista T01) — ciorna s-a făcut fără linii;'
                    . ' completați-le manual sau importați-le din fișier.';
            }

            $this->note = [];

            try {
                $declaratie = $this->ciorna((string) $factura, $bucati, $cifDeclarant, $userId);
            } catch (\Exception $e) {
                $rezultat['avertismente'][] = 'Factura ' . $factura . ': ' . $e->getMessage();

                continue;
            }

            foreach ($this->note as $nota) {
                $rezultat['avertismente'][] = 'Factura ' . $factura . ': ' . $nota;
            }

            $locMagazin = $declaratie->loc_magazin;

            $rezultat['ciorne'][] = [
                'id' => $declaratie->id,
                'factura' => (string) $factura,
                'magazin' => $locMagazin['magazin_denumire'] ?? null,
            ];

            // Un cod de magazin nestiut inca: utilizatorul e intrebat cum se numeste gestiunea.
            $codMagazin = mb_strtoupper((string) ($locMagazin['magazin_cod'] ?? ''));

            if ($codMagazin !== ''
                && !isset($this->gestiunile()[$codMagazin])
                && !isset($rezultat['gestiuni_noi'][$codMagazin])) {
                $rezultat['gestiuni_noi'][$codMagazin] = [
                    'cod_furnizor' => $codMagazin,
                    'denumire_furnizor' => $locMagazin['magazin_denumire'] ?? null,
                ];
            }
        }

        $rezultat['gestiuni_noi'] = array_values($rezultat['gestiuni_noi']);

        $arhiva->close();

        return $rezultat;
    }

    /**
     * Numărul facturii: din coada numelui („..._10053419.TXT"), cum vine la
     * livrări; la retururi numele nu-l poartă și se citește din antetul
     * fișierelor — „Documents: 10074615 of ...", „Doc number: ..." sau, în
     * distinta D01, „Number ......: 10074615 del ...".
     */
    protected function numarulFacturii(string $stem, array $bucati): string
    {
        if (preg_match('/_(\d+)$/', $stem, $gasit)) {
            return $gasit[1];
        }

        foreach (['T02', 'T01', 'D01'] as $tip) {
            if (!isset($bucati[$tip])) {
                continue;
            }

            if (preg_match('/^\s*(?:Doc number|Documents|Numero documento|Number)\s*\.*\s*:\s*(\d+)\s+(?:of|del)\b/im', $bucati[$tip], $gasit)) {
                return $gasit[1];
            }
        }

        return $stem;
    }

    /**
     * O ciornă dintr-o factură: liniile din T02 (sau din T01, la retururi),
     * destinația din D01.
     *
     * Unele arhive vin fără T02 la anumite facturi: ciorna se face atunci
     * doar cu destinația și factura, iar liniile le pune omul.
     */
    protected function ciorna(string $factura, array $bucati, ?string $cifDeclarant, ?int $userId): EtransportDeclaratie
    {
        $citit = ['linii' => [], 'antet' => []];
        $tipLinii = isset($bucati['T02']) ? 'T02' : (isset($bucati['T01']) ? 'T01' : null);

        if ($tipLinii !== null) {
            // Raportul se citeste cu parserul lui obisnuit, dintr-un fisier trecator.
            $cale = tempnam(sys_get_temp_dir(), 'etr');
            file_put_contents($cale, $bucati[$tipLinii]);

            try {
                $citit = $this->fisiere->importa([['nume' => $tipLinii . '_' . $factura . '.txt', 'cale' => $cale]]);
            } finally {
                @unlink($cale);
            }

            if ($citit['linii'] === []) {
                throw new EtransportException(
                    ($tipLinii === 'T02' ? 'recapitulația T02' : 'lista pe articole T01') . ' nu are nicio linie de citit.'
                );
            }
        }

        $antet = $citit['antet'];
        $retur = $this->esteRetur($bucati);

        /*
         * Magazinul: la livrari e destinatia din blocul „Destinazione" al
         * distinctei; la retururi e expeditorul, cu codul pe randul „From", iar
         * adresa lui se ia din ultima declaratie a aceluiasi magazin.
         */
        $magazin = $retur
            ? $this->adresaMagazinului($this->magazinulDinD01($bucati['D01'] ?? ''))
            : (isset($bucati['D01']) ? $this->destinatia($bucati['D01']) : []);

        // Cand gestiunea e stiuta, denumirea magazinului se ia din ea, nu de la furnizor.
        $gestiune = isset($magazin['magazin_cod'])
            ? ($this->gestiunile()[mb_strtoupper($magazin['magazin_cod'])] ?? null)
            : null;

        if ($gestiune !== null) {
            $magazin['magazin_denumire'] = $gestiune->denumire;
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

        // Livrare: de la frontiera la magazin (AIC). Retur: de la magazin la frontiera (LIC).
        $ptf = ['tip' => 'ptf', 'cod_ptf' => self::PTF_IMPLICIT];
        $adresaMagazin = ['tip' => 'adresa'] + $magazin;

        return EtransportDeclaratie::create([
            'stare' => 'ciorna',
            'cif_declarant' => $cifDeclarant,
            'referinta_interna' => ($retur ? 'Retur ' : 'Factura ') . $factura,
            'tip_operatiune' => $retur ? 20 : 10,
            'partener_tara' => $antet['partener_tara'] ?? 'IT',
            'partener_cod' => $antet['partener_cod'] ?? null,
            'partener_denumire' => $antet['partener_denumire'] ?? null,
            'transportator_tara' => 'RO',
            'loc_start' => $retur ? $adresaMagazin : $ptf,
            'loc_final' => $retur ? $ptf : $adresaMagazin,
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

    /**
     * Retur, nu livrare: distinta e o notă de credit, sau liniile vin din
     * lista T01 în lipsa recapitulației T02 (așa vin arhivele „RESO").
     */
    protected function esteRetur(array $bucati): bool
    {
        if (isset($bucati['D01']) && preg_match('/CREDIT\s+NOTE/i', $bucati['D01'])) {
            return true;
        }

        return isset($bucati['T01']) && !isset($bucati['T02']);
    }

    /**
     * Codul magazinului care trimite returul, de pe rândul distinctei:
     * „From  S.C. EMPORIO COM SRL                     NEG0001474".
     */
    protected function magazinulDinD01(string $continut): ?string
    {
        if (preg_match('/^\s*From\s+.+?\s{2,}(NEG\w+)/im', $continut, $gasit)) {
            return mb_strtoupper($gasit[1]);
        }

        return null;
    }

    /**
     * Adresa magazinului, din ultima declarație care l-a avut ca destinație
     * (sau ca plecare, la un retur anterior). Fără una, ciorna pornește doar
     * cu codul și denumirea, iar adresa o pune omul.
     *
     * @return array<string, mixed>
     */
    protected function adresaMagazinului(?string $cod): array
    {
        if ($cod === null) {
            $this->note[] = 'distinta nu spune de la ce magazin pleacă returul; completați adresa de plecare.';

            return [];
        }

        $anterioara = EtransportDeclaratie::where(function ($q) use ($cod) {
            $q->where('loc_final->magazin_cod', $cod)->orWhere('loc_start->magazin_cod', $cod);
        })->orderByDesc('id')->first();

        if ($anterioara === null) {
            $this->note[] = 'magazinul ' . $cod . ' nu are nicio declarație anterioară din care să-i iau adresa;'
                . ' completați adresa de plecare.';

            return ['magazin_cod' => $cod];
        }

        $adresa = $anterioara->loc_magazin;
        unset($adresa['tip']);

        return ['magazin_cod' => $cod] + $adresa;
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
