<?php

namespace App\Services\Anaf\Declaratii\D300;

use ReflectionClass;

/**
 * De ce a iesit decontul asa cum a iesit.
 *
 * Un decont de zerouri nu inseamna neaparat ca socoteala a dat gres. Pe
 * fisierele adevarate din SPV Curier s-au vazut, pana acum, doua pricini care
 * n-au nimic de-a face cu socoteala:
 *
 *   - programul de contabilitate scrie codurile TVA numai pe facturi, iar pe
 *     notele contabile pune codul generic „000000". Decontul se face insa numai
 *     din jurnale — asa face si aplicatia ANAF —, deci n-are din ce;
 *   - firma are numai operatiuni scutite, cu coduri care nu duc in niciun rand
 *     din decont (o societate financiara, de pilda).
 *
 * Fara lamurirea aceasta, omul primeste o pagina de zerouri si nu are de unde
 * sti daca a gresit el, a gresit programul sau chiar asa e. De aceea decontul
 * vine intotdeauna insotit de un raspuns la „de ce".
 */
class DiagnosticDecont
{
    /** Sub cat se socoteste ca un rand e gol (decontul se depune in lei intregi). */
    protected const PRAG = 0.5;

    /** Cate coduri se dau ca pilda in explicatie. */
    protected const CODURI_ARATATE = 4;

    /**
     * Randurile care nu vin din operatiunile lunii.
     *
     * Randul 39 e soldul de TVA ramas de plata din perioada trecuta: el se ia
     * din soldul contului 4423 si e acolo si cand luna n-a avut nicio
     * operatiune. Daca l-am socoti drept cifra a decontului, un fisier fara
     * niciun cod TVA ar trece drept decont bun — chiar asa s-a si intamplat, pe
     * fisierul unei firme careia programul de contabilitate ii scrie codurile
     * numai pe facturi.
     *
     * Randurile „_P" sunt sumele stranse deoparte in cuprinsul unei tranzactii;
     * ele nu sunt randuri ale decontului.
     */
    protected const IN_AFARA_OPERATIUNILOR = ['RD39_TVA'];

    /**
     * @param array $diagnostic numaratorile stranse la citire
     * @param array $randuri    randurile decontului
     *
     * @return array{stare: string, titlu: string, explicatie: string, de_facut: ?string}
     */
    public static function explica(array $diagnostic, array $randuri): array
    {
        if (self::areCifre($randuri)) {
            return [
                'stare' => 'bun',
                'titlu' => 'Decontul are cifre',
                'explicatie' => 'S-au citit ' . $diagnostic['linii'] . ' linii din jurnale, din care '
                    . $diagnostic['linii_cu_suma'] . ' cu sumă de taxă.',
                'de_facut' => null,
            ];
        }

        return self::deCeEGol($diagnostic);
    }

    /** Decontul e gol: se cauta pricina prin numaratori, de la cea mai limpede. */
    protected static function deCeEGol(array $d): array
    {
        if ($d['linii_cu_taxa'] === 0) {
            return [
                'stare' => 'fara_informatie_de_taxa',
                'titlu' => 'Notele contabile n-au informație de taxă',
                'explicatie' => 'Niciuna din cele ' . $d['linii'] . ' linii din jurnale nu poartă'
                    . ' TaxInformation. Fără ea, decontul n-are din ce se face.',
                'de_facut' => 'Cere programului de contabilitate un export SAF-T cu informația de taxă'
                    . ' pe notele contabile.',
            ];
        }

        /*
         * Cazul cel mai des intalnit: codurile stau pe facturi, nu pe notele
         * contabile. Se vede limpede — pe linii e numai „000000", iar in
         * SourceDocuments sunt coduri adevarate cu miile.
         */
        if ($d['linii_cod_generic'] > 0 && $d['linii_cod_generic'] === $d['linii_cu_cod'] && $d['coduri_in_afara'] > 0) {
            return [
                'stare' => 'coduri_doar_pe_facturi',
                'titlu' => 'Codurile TVA stau pe facturi, nu pe notele contabile',
                'explicatie' => 'Toate cele ' . $d['linii_cu_cod'] . ' linii din jurnale poartă codul generic'
                    . ' 000000, iar codurile TVA adevărate — ' . $d['coduri_in_afara'] . ' la număr — sunt scrise'
                    . ' pe facturile din SourceDocuments. Decontul se face numai din jurnale, la fel ca în'
                    . ' aplicația ANAF, așa că din fișierul acesta ar ieși gol și la ei.',
                'de_facut' => 'Cere programului de contabilitate să treacă codul TVA și pe notele contabile'
                    . ' (elementul TaxCode din TransactionLine).',
            ];
        }

        if ($d['linii_cod_generic'] > 0 && $d['linii_cod_generic'] === $d['linii_cu_cod']) {
            return [
                'stare' => 'numai_cod_generic',
                'titlu' => 'Toate liniile poartă codul generic 000000',
                'explicatie' => 'Cele ' . $d['linii_cu_cod'] . ' linii cu informație de taxă au codul 000000,'
                    . ' adică „fără cod". Din el nu se poate ști în ce rând din decont merge operațiunea.',
                'de_facut' => 'Cere programului de contabilitate codurile TVA din nomenclatorul ANAF,'
                    . ' pe fiecare notă contabilă.',
            ];
        }

        $necunoscute = self::necunoscute($d['coduri']);

        if ($necunoscute !== [] && count($necunoscute) === count($d['coduri'])) {
            return [
                'stare' => 'coduri_fara_rand',
                'titlu' => 'Codurile de pe linii nu duc în niciun rând din decont',
                'explicatie' => 'Liniile poartă ' . self::insirate($necunoscute) . '. Codurile acestea nu se'
                    . ' regăsesc în regulile decontului — așa sunt, de pildă, operațiunile scutite fără drept'
                    . ' de deducere.',
                'de_facut' => 'Dacă firma are și operațiuni taxabile, verifică ce coduri le-a pus programul'
                    . ' de contabilitate.',
            ];
        }

        if ($d['linii_cu_suma'] === 0) {
            return [
                'stare' => 'fara_sume_de_taxa',
                'titlu' => 'Nicio linie n-are sumă de taxă',
                'explicatie' => 'Cele ' . $d['linii_cu_taxa'] . ' linii cu informație de taxă au TaxAmount zero.'
                    . ' Aproape toate rândurile decontului se adună numai acolo unde există o sumă de taxă.',
                'de_facut' => 'Verifică dacă perioada chiar a fost fără TVA sau dacă programul de contabilitate'
                    . ' a omis sumele.',
            ];
        }

        return [
            'stare' => 'gol_fara_pricina_stiuta',
            'titlu' => 'Decontul a ieșit gol',
            'explicatie' => 'S-au citit ' . $d['linii'] . ' linii, din care ' . $d['linii_cu_suma']
                . ' cu sumă de taxă, cu ' . self::insirate(array_keys($d['coduri'])) . '.'
                . ' Niciuna n-a adus ceva într-un rând din decont.',
            'de_facut' => 'Merită privit fișierul mai de aproape.',
        ];
    }

    /** A adus vreo operatiune a lunii o cifra in decont? */
    protected static function areCifre(array $randuri): bool
    {
        foreach ($randuri as $rand => $valoare) {
            if (in_array($rand, self::IN_AFARA_OPERATIUNILOR, true) || substr($rand, -2) === '_P') {
                continue;
            }

            if (abs((float) $valoare) >= self::PRAG) {
                return true;
            }
        }

        return false;
    }

    /**
     * Codurile care nu se regasesc in regulile decontului.
     *
     * @param array<string, int> $coduri
     * @return array<int, string>
     */
    protected static function necunoscute(array $coduri): array
    {
        $cunoscute = self::codurileCunoscute();

        return array_values(array_filter(array_keys($coduri), function ($cod) use ($cunoscute) {
            return !isset($cunoscute[$cod]);
        }));
    }

    /**
     * Toate codurile pe care regulile le iau in seama.
     *
     * Ele se strang din chiar fisierele generate: multimile de coduri si
     * ramurile de la „laCodTaxa”. Asa, cand ANAF innoieste regulile, lista se
     * innoieste odata cu ele.
     *
     * @return array<string, bool>
     */
    protected static function codurileCunoscute(): array
    {
        static $cunoscute = null;

        if ($cunoscute !== null) {
            return $cunoscute;
        }

        $cunoscute = ReguliD300::CODURI_LA_COD_TAXA;

        foreach ((new ReflectionClass(CoduriD300::class))->getConstants() as $multime) {
            $cunoscute += $multime;
        }

        return $cunoscute;
    }

    /**
     * Codurile, spuse pe intelesul omului.
     *
     * Un cod singur se spune cu tot cu ce inseamna — atat incape intr-o
     * propozitie si atata are nevoie cine cauta pricina. Mai multe se insira
     * doar cu numarul, ca fraza sa ramana citibila.
     *
     * @param array<int, string> $coduri
     */
    protected static function insirate(array $coduri): string
    {
        if ($coduri === []) {
            return 'niciun cod';
        }

        if (count($coduri) === 1) {
            $lamurire = NomenclatorTva::descrie($coduri[0]);

            return 'codul ' . $coduri[0] . ($lamurire ? ' (' . $lamurire . ')' : '');
        }

        $aratate = array_slice($coduri, 0, self::CODURI_ARATATE);
        $sirul = implode(', ', $aratate);

        if (count($coduri) > count($aratate)) {
            return 'codurile ' . $sirul . ' și încă ' . (count($coduri) - count($aratate));
        }

        return 'codurile ' . $sirul;
    }
}
