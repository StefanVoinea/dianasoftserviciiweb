<?php

namespace App\Services\Anaf\Declaratii\D300;

use App\Services\Anaf\Declaratii\DeclaratieException;
use XMLReader;

/**
 * Decontul de TVA (D300), socotit din jurnalele unui SAF-T (D406).
 *
 * Aplicatia ANAF care face acest lucru — D300_2026.jar — nu poate fi chemata de
 * pe server: toata prelucrarea ei sta intr-un „main" cu fereastra, iar exportul
 * se face din butoane. Regulile ei sunt insa mutate intreg in PHP, mecanic, in
 * CoduriD300 si ReguliD300 (vezi tools/d300). Aici e doar citirea fisierului si
 * starea purtata de la o linie la alta.
 *
 * Citirea se face in flux, cu XMLReader: un SAF-T lunar are sute de mii de
 * linii, iar incarcarea lui intreaga in memorie n-ar incapea.
 *
 * Ce iese de aici nu e inca declaratia: e decontul in cifre. Din el se scrie
 * apoi XML-ul D300, care intra in fluxul obisnuit — validare cu DUKIntegrator,
 * PDF, semnare, depunere.
 */
class DecontDinSaft
{
    /**
     * Conturile de la care se ia soldul de TVA de plata de la sfarsitul
     * perioadei trecute (randul 39) si platile facute in contul lui.
     */
    protected const CONT_SOLD = '4423';
    protected const CONT_PLATA = '35323';

    /** Tipurile de taxa care socotesc o plata drept plata de TVA. */
    protected const TAXE_PLATA = ['301', '302', '303', '000'];

    /** Codul pus de programele de contabilitate acolo unde n-au ce cod sa puna. */
    protected const COD_GENERIC = '000000';

    /**
     * Cate coduri deosebite se tin minte pentru diagnostic.
     *
     * Nomenclatorul are vreo sapte sute; intr-un fisier sanatos se intalnesc
     * cateva zeci. Numarul e o incuietoare impotriva unui fisier stricat, nu o
     * masura a nomenclatorului.
     */
    protected const CODURI_TINUTE_MINTE = 100;

    /**
     * @return array{
     *     cif: string,
     *     denumire: string,
     *     luna: string,
     *     an: string,
     *     linii: int,
     *     randuri: array<string, float>,
     *     diagnostic: array,
     *     lamurire: array
     * }
     *
     * @throws DeclaratieException
     */
    public function genereaza(string $caleXml): array
    {
        if (!is_file($caleXml)) {
            throw new DeclaratieException('Fișierul declarației nu a fost găsit: ' . $caleXml);
        }

        $citire = new XMLReader();

        if (!@$citire->open($caleXml)) {
            throw new DeclaratieException('Fișierul nu a putut fi deschis pentru citire: ' . $caleXml);
        }

        $s = $this->stareaDeInceput();

        try {
            $this->citeste($citire, $s);
        } finally {
            $citire->close();
        }

        /*
         * Un SAF-T poate veni impartit pe sectiuni („modal"), iar decontul se
         * face numai din cea cu jurnalele. Fara ele n-are din ce se socoti
         * nimic, iar un decont de zerouri ar fi mai rau decat niciun raspuns.
         */
        if ($s['linii'] === 0) {
            throw new DeclaratieException(
                'Fișierul nu conține jurnale (General Ledger Entries). La declarațiile SAF-T'
                . ' împărțite pe secțiuni, decontul se face din fișierul cu jurnalele.'
            );
        }

        ReguliD300::laFinal($s, $s['an']);

        $randuri = $this->faraSemn(array_intersect_key($s, array_flip(ReguliD300::RANDURI)));

        $numere = [
            'linii' => $s['linii'],
            'linii_cu_taxa' => $s['liniiCuTaxa'],
            'linii_cu_cod' => $s['liniiCuCod'],
            'linii_cod_generic' => $s['liniiCodGeneric'],
            'linii_cu_suma' => $s['liniiCuSuma'],
            'coduri' => $s['coduri'],
            'coduri_in_afara' => $s['coduriInAfara'],
        ];

        return [
            'cif' => $s['cif'],
            'denumire' => $s['denumire'],
            'luna' => $s['luna'],
            'an' => $s['an'],
            'linii' => $s['linii'],
            'randuri' => $randuri,
            'diagnostic' => $numere,

            /*
             * Decontul nu pleaca niciodata singur: un teanc de zerouri fara o
             * vorba langa el il lasa pe om sa creada ca a gresit undeva.
             */
            'lamurire' => DiagnosticDecont::explica($numere, $randuri),
        ];
    }

    /** Trecerea prin fisier, nod cu nod. */
    protected function citeste(XMLReader $citire, array &$s): void
    {
        while (@$citire->read()) {
            switch ($citire->nodeType) {
                case XMLReader::ELEMENT:
                    $nume = $citire->localName;
                    $this->laDeschidere($nume, $s);

                    /*
                     * Un element scris scurt („<Amount/>") n-are text si nici
                     * inchidere de sine statatoare: se poarta aici ca si cum
                     * ar fi avut si una, si alta, goale.
                     */
                    if ($citire->isEmptyElement) {
                        $this->laText($nume, '', $s);
                        $this->laInchidere($nume, $s);
                        break;
                    }

                    $s['element'] = $nume;
                    $s['text'] = '';
                    break;

                case XMLReader::TEXT:
                case XMLReader::CDATA:
                case XMLReader::SIGNIFICANT_WHITESPACE:
                    if ($s['element'] !== null) {
                        $s['text'] .= $citire->value;
                    }
                    break;

                case XMLReader::END_ELEMENT:
                    $nume = $citire->localName;

                    if ($s['element'] === $nume) {
                        $this->laText($nume, trim($s['text']), $s);
                        $s['element'] = null;
                        $s['text'] = '';
                    }

                    $this->laInchidere($nume, $s);
                    break;
            }
        }
    }

    /** Deschiderea unui element: de aici incolo se stie unde suntem. */
    protected function laDeschidere(string $nume, array &$s): void
    {
        switch ($nume) {
            case 'Header':
                $s['inAntet'] = true;
                break;

            case 'Account':
                $s['inCont'] = true;
                break;

            case 'PaymentLine':
                $s['inPlata'] = true;
                break;

            case 'PaymentLineAmount':
                $s['inSumaPlatii'] = true;
                break;

            case 'Transaction':
                /*
                 * Randurile 24 si 25 se aduna intai deoparte, pe tranzactie:
                 * ele intra in decont numai daca tranzactia se dovedeste a fi
                 * una de taxare inversa (vezi inchiderea tranzactiei).
                 */
                $s['isTa'] = false;
                $s['conditie1'] = false;
                $s['conditie2'] = false;
                $s['RD24_TVA_P'] = 0.0;
                $s['RD24_BAZA_P'] = 0.0;
                $s['RD25_TVA_P'] = 0.0;
                $s['RD25_BAZA_P'] = 0.0;
                break;

            case 'TransactionLine':
                $s['inLinie'] = true;
                $s['linii']++;

                // Ce se stie despre linia aceasta, pentru cand decontul iese gol
                $s['codulLiniei'] = '';
                $s['areTaxaLinie'] = false;
                break;

            case 'CreditAmount':
                if ($s['inLinie']) {
                    $s['asteptCredit'] = true;
                }
                break;

            case 'DebitAmount':
                if ($s['inLinie']) {
                    $s['asteptDebit'] = true;
                }
                break;

            case 'TaxInformation':
                if ($s['inLinie']) {
                    $s['inTaxa'] = true;
                    $s['areTaxaLinie'] = true;
                }
                break;

            case 'TaxAmount':
                if ($s['inTaxa']) {
                    $s['asteptTaxa'] = true;
                }
                break;
        }
    }

    /**
     * Textul unui element, cu tot ce trage el dupa sine.
     *
     * Ordinea de aici e cea din aplicatia ANAF: la „Amount" se uita intai daca
     * se astepta creditul, apoi debitul, apoi taxa. Steagurile se sting pe
     * masura ce sunt folosite, asa ca aceeasi denumire de element ajunge, pe
     * rand, in locul care trebuie.
     */
    protected function laText(string $nume, string $text, array &$s): void
    {
        if ($s['inAntet']) {
            $this->antet($nume, $text, $s);
        }

        if ($s['inCont']) {
            $this->cont($nume, $text, $s);
        }

        if ($s['inPlata'] || $s['inSumaPlatii']) {
            $this->plata($nume, $text, $s);
        }

        if ($s['inLinie'] && $nume === 'AccountID') {
            $this->conturileLiniei($text, $s);
        }

        if ($nume === 'Amount') {
            if ($s['asteptCredit']) {
                $s['ca'] = $this->numar($text);
                $s['asteptCredit'] = false;
            } elseif ($s['asteptDebit']) {
                $s['da'] = $this->numar($text);
                $s['asteptDebit'] = false;
            } elseif ($s['asteptTaxa']) {
                $s['asteptTaxa'] = false;
                $s['ta'] = $this->numar($text);

                ReguliD300::laSumaTaxa(
                    $s,
                    $s['cod'],
                    $s['ca'],
                    $s['da'],
                    $s['ta'],
                    $s['baza'],
                    $s['areBaza'],
                    $s['cont']
                );
            }
        }

        if ($s['inLinie'] && $s['inTaxa'] && $nume === 'TaxBase') {
            $s['baza'] = $this->numar($text);
            $s['areBaza'] = true;
        }

        if ($nume === 'TaxCode') {
            if ($s['inLinie'] && $s['inTaxa']) {
                $s['cod'] = $text;
                $s['codulLiniei'] = $text;

                ReguliD300::laCodTaxa($s, $text, $s['ca'], $s['da']);
            } elseif ($text !== '' && $text !== self::COD_GENERIC) {
                /*
                 * Cod de taxa in afara jurnalelor — pe facturile din
                 * SourceDocuments. Decontul nu se face din ele, dar numarul lor
                 * spune de ce a iesit gol: programul de contabilitate le-a pus
                 * pe facturi si nu pe notele contabile.
                 */
                $s['coduriInAfara']++;
            }
        }
    }

    /** Cine depune si pe ce perioada. */
    protected function antet(string $nume, string $text, array &$s): void
    {
        switch ($nume) {
            case 'Name':
                $s['denumire'] = $text;
                break;

            // Codul fiscal e scris altfel de la o versiune de schema la alta.
            case 'TaxRegistrationNumber':
            case 'RegistrationNumber':
                $s['cif'] = $text;
                break;

            case 'PeriodEnd':
                $s['luna'] = $text;
                break;

            case 'PeriodEndYear':
                $s['an'] = $text;
                break;

            /*
             * Data de sfarsit e plasa de siguranta pentru perioada.
             *
             * In schema D406 ea vine inaintea lui PeriodEnd si PeriodEndYear,
             * asa ca acolo unde fisierul le are pe amandoua raman ele din urma.
             * Unde nu, luna si anul se scot de aici. Aplicatia ANAF face la fel,
             * iar ordinea aceasta e chiar ce alege intre ele.
             */
            case 'SelectionEndDate':
                if (strlen($text) >= 10) {
                    $s['luna'] = substr($text, 5, 2);
                    $s['an'] = substr($text, 0, 4);
                }
                break;
        }
    }

    /** Soldul de TVA de plata ramas din perioada trecuta (randul 39). */
    protected function cont(string $nume, string $text, array &$s): void
    {
        if ($nume === 'AccountID') {
            $s['esteSold'] = $text === self::CONT_SOLD;

            return;
        }

        if (!$s['esteSold']) {
            return;
        }

        if ($nume === 'OpeningCreditBalance') {
            $s['OpeningCreditBalance'] += $this->numar($text);
        }

        if ($nume === 'OpeningDebitBalance') {
            $s['OpeningDebitBalance'] += $this->numar($text);
        }
    }

    /**
     * Platile de TVA facute in cursul perioadei.
     *
     * Ele se scad din soldul de la randul 39: ce s-a platit deja nu se mai
     * cere o data. Suma se retine cand se citeste, dar se aduna abia cand
     * tipul taxei arata ca plata a fost intr-adevar de TVA.
     */
    protected function plata(string $nume, string $text, array &$s): void
    {
        if ($s['inPlata'] && $nume === 'AccountID') {
            $s['contPlatii'] = $text;
        }

        if (
            $s['inSumaPlatii']
            && $nume === 'Amount'
            && ($this->incepeCu($s['contPlatii'], self::CONT_SOLD) || $this->incepeCu($s['contPlatii'], self::CONT_PLATA))
        ) {
            $s['sumaPlatii'] = $this->numar($text);
        }

        if ($s['inPlata'] && $nume === 'TaxType') {
            foreach (self::TAXE_PLATA as $taxa) {
                if ($this->incepeCu($text, $taxa)) {
                    $s['FinalPayment4423'] += $s['sumaPlatii'];

                    break;
                }
            }
        }
    }

    /**
     * Ce fel de cont poarta linia.
     *
     * Steagurile acestea hotarasc mai departe daca suma intra in baza sau in
     * taxa: pe conturile de TVA (4426, 4427, 4428 si perechile lor din regimul
     * special) sta taxa, pe celelalte sta baza.
     */
    protected function conturileLiniei(string $cont, array &$s): void
    {
        if (!$this->incepeCu($cont, '442')) {
            $s['cont']['not442'] = true;
        }

        if (!$this->incepeCu($cont, '3532')) {
            $s['cont']['not3532'] = true;
        }

        foreach (['4426', '4427', '4428', '35326', '35327', '35328'] as $tvaCont) {
            if ($this->incepeCu($cont, $tvaCont)) {
                $s['cont']['is' . $tvaCont] = true;
            }
        }
    }

    /** Inchiderea unui element: se sting steagurile si se face socoteala. */
    protected function laInchidere(string $nume, array &$s): void
    {
        switch ($nume) {
            case 'Header':
                $s['inAntet'] = false;
                break;

            case 'Account':
                $s['inCont'] = false;
                $s['esteSold'] = false;
                break;

            case 'PaymentLineAmount':
                $s['inSumaPlatii'] = false;
                break;

            case 'PaymentLine':
                $s['inPlata'] = false;
                $s['sumaPlatii'] = 0.0;
                $s['contPlatii'] = '';
                break;

            case 'Transaction':
                /*
                 * Taxarea inversa se vede abia la sfarsitul tranzactiei: TVA-ul
                 * ei sta si in deductibila, si in neexigibila, fara sa vina
                 * vreo suma de taxa pe linii. Daca asa a fost, ce s-a strans
                 * deoparte intra in decont.
                 */
                if ($s['conditie1'] && $s['conditie2'] && !$s['isTa']) {
                    $s['RD24_TVA'] += $s['RD24_TVA_P'];
                    $s['RD24_BAZA'] += $s['RD24_BAZA_P'];
                }

                if ($s['conditie3'] && $s['conditie2'] && !$s['isTa']) {
                    $s['RD25_TVA'] += $s['RD25_TVA_P'];
                    $s['RD25_BAZA'] += $s['RD25_BAZA_P'];
                }

                $s['conditie1'] = false;
                $s['conditie2'] = false;
                $s['conditie3'] = false;
                $s['isTa'] = true;
                $s['RD24_TVA_P'] = 0.0;
                $s['RD24_BAZA_P'] = 0.0;
                $s['RD25_TVA_P'] = 0.0;
                $s['RD25_BAZA_P'] = 0.0;
                break;

            case 'TransactionLine':
                $this->numaraLinia($s);

                $s['inLinie'] = false;
                $s['inTaxa'] = false;
                $s['asteptTaxa'] = false;
                $s['asteptCredit'] = false;
                $s['asteptDebit'] = false;
                $s['areBaza'] = false;
                $s['ca'] = 0.0;
                $s['da'] = 0.0;
                $s['ta'] = 0.0;
                $s['baza'] = 0.0;

                /*
                 * Aici se sting numai steagurile pe care le stinge si aplicatia
                 * ANAF. Doua nu le stinge: „is4428" si „is35328" raman aprinse
                 * pana la sfarsitul fisierului, de la prima linie de TVA
                 * neexigibila incolo, si tot asa ramane si codul de taxa, care
                 * trece de la o linie la alta pana vine altul.
                 *
                 * Are toate semnele unei scapari, dar nu se indreapta aici:
                 * decontul acesta se compara cu cel scos de ANAF din acelasi
                 * fisier, iar o socoteala mai buna decat a lor ar fi, pentru
                 * omul care depune, o socoteala diferita. Cand ANAF indreapta,
                 * se regenereaza si se sterge nota aceasta.
                 */
                $s['cont']['not442'] = false;
                $s['cont']['not3532'] = false;
                $s['cont']['is4426'] = false;
                $s['cont']['is4427'] = false;
                $s['cont']['is35326'] = false;
                $s['cont']['is35327'] = false;
                break;
        }
    }

    /**
     * Ce a adus linia, pentru cand trebuie spus de ce e gol decontul.
     *
     * Un decont de zerouri poate veni din doua locuri deosebite: ori socoteala
     * n-a gasit ce sa adune, ori fisierul n-avea ce sa-i dea. Numerele acestea
     * lamuresc care din ele — vezi DiagnosticDecont.
     */
    protected function numaraLinia(array &$s): void
    {
        if ($s['areTaxaLinie']) {
            $s['liniiCuTaxa']++;
        }

        if ($s['ta'] != 0.0) {
            $s['liniiCuSuma']++;
        }

        if ($s['codulLiniei'] === '') {
            return;
        }

        $s['liniiCuCod']++;

        if ($s['codulLiniei'] === self::COD_GENERIC) {
            $s['liniiCodGeneric']++;

            return;
        }

        if (isset($s['coduri'][$s['codulLiniei']]) || count($s['coduri']) < self::CODURI_TINUTE_MINTE) {
            $s['coduri'][$s['codulLiniei']] = ($s['coduri'][$s['codulLiniei']] ?? 0) + 1;
        }
    }

    /**
     * Decontul, cu sumele scrise pozitiv.
     *
     * In socoteala, semnul arata partea notei contabile din care vine suma:
     * regulile ANAF orienteaza fiecare rand asa incat operatiunea lui obisnuita
     * sa iasa pozitiva — livrarile din credit, achizitiile din debit. Cand
     * programul de contabilitate pune informatia de taxa pe linia partenerului,
     * si nu pe linia de baza, tot decontul iese oglindit.
     *
     * Decontul se depune insa cu sume pozitive, asa ca semnul se lasa deoparte
     * dupa ce s-a terminat socoteala. Se face aici, o singura data, ca fereastra
     * si fisierul sa arate acelasi lucru.
     *
     * @param array<string, float> $randuri
     * @return array<string, float>
     */
    protected function faraSemn(array $randuri): array
    {
        return array_map(function ($valoare) {
            return abs((float) $valoare);
        }, $randuri);
    }

    /**
     * Numarul, asa cum vine din fisier.
     *
     * Aplicatia ANAF cade cand campul e gol; aici se ia drept zero. O suma
     * lipsa nu strica decontul — ea nu are ce sa adune —, iar o declaratie
     * intreaga nu merita oprita pentru un camp nescris.
     */
    protected function numar(string $text): float
    {
        return $text === '' ? 0.0 : (float) str_replace(',', '.', $text);
    }

    protected function incepeCu(string $text, string $inceput): bool
    {
        return strncmp($text, $inceput, strlen($inceput)) === 0;
    }

    protected function conturiNoi(): array
    {
        return [
            'not442' => false,
            'not3532' => false,
            'is4426' => false,
            'is4427' => false,
            'is4428' => false,
            'is35326' => false,
            'is35327' => false,
            'is35328' => false,
        ];
    }

    /** Decontul gol, cu toate randurile pornite de la zero. */
    protected function stareaDeInceput(): array
    {
        $s = array_fill_keys(ReguliD300::RANDURI, 0.0);

        return array_merge($s, [
            // Cine si pe cand
            'cif' => '',
            'denumire' => '',
            'luna' => '',
            'an' => '',

            // Unde suntem in fisier
            'element' => null,
            'text' => '',
            'inAntet' => false,
            'inCont' => false,
            'inPlata' => false,
            'inSumaPlatii' => false,
            'inLinie' => false,
            'inTaxa' => false,
            'asteptCredit' => false,
            'asteptDebit' => false,
            'asteptTaxa' => false,
            'esteSold' => false,

            // Linia in lucru
            'cod' => '',
            'ca' => 0.0,
            'da' => 0.0,
            'ta' => 0.0,
            'baza' => 0.0,
            'areBaza' => false,
            'cont' => $this->conturiNoi(),

            // Numaratorile din care se lamureste un decont gol
            'linii' => 0,
            'liniiCuTaxa' => 0,
            'liniiCuCod' => 0,
            'liniiCodGeneric' => 0,
            'liniiCuSuma' => 0,
            'coduri' => [],
            'coduriInAfara' => 0,
            'codulLiniei' => '',
            'areTaxaLinie' => false,

            // Tranzactia in lucru
            'conditie1' => false,
            'conditie2' => false,
            'conditie3' => false,
            'isTa' => false,
            'isCod1' => false,
            'CA_DA' => 0.0,

            // Soldul de TVA si platile din perioada
            'OpeningCreditBalance' => 0.0,
            'OpeningDebitBalance' => 0.0,
            'FinalPayment4423' => 0.0,
            'contPlatii' => '',
            'sumaPlatii' => 0.0,
        ]);
    }
}
