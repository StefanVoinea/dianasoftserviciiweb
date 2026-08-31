<?php

namespace App\Services\Anaf\Declaratii;

use App\Models\AnafDeclaratie;

/**
 * Motorul SPV Wizard: traduce erorile DUKIntegrator in explicatii pe intelesul
 * oricui. In interfata, asistentul poarta numele „SPV Wizard".
 *
 * Sabloanele de mesaje sunt luate din chiar validatorul ANAF: DecValidation.jar
 * (motorul comun al tuturor declaratiilor) le pastreaza cu marcaje @0@, @1@ etc.
 * Fiecare tipar de mai jos corespunde unuia dintre ele, iar formularile au fost
 * verificate ruland DUKIntegrator pe declaratii stricate intentionat.
 *
 * Un mesaj are forma:
 *     <eroare|atentionare> <atribut|regula|structura>: [<camp sau cod>:] <mesaj>
 * iar sectiunea in care apare este data de randul anterior („E: informatii (1)").
 */
class InterpretareErori
{
    /** Peste atatea caractere, randul se arata doar pe bucati in jurul potrivirii. */
    protected const FEREASTRA_RAND = 150;

    /**
     * Cate probleme se explica cel mult.
     *
     * Un SAF-T respins are adesea zeci de mii de erori, dar ele se repeta:
     * aceeasi greseala pe fiecare tranzactie. Explicate toate, ar tine browserul
     * in loc fara sa spuna nimic in plus.
     */
    protected const MAXIM_EXPLICATE = 300;

    /** De la cate erori incolo se cauta pricina comuna, nu randul gresit. */
    protected const MULTE_ERORI = 20;

    /**
     * @param string|null $xml continutul fisierului, pentru a arata unde anume
     *                         trebuie corectat (linia si coloana)
     *
     * @return array{rezumat: string, probleme: array<int, array>, netradus: bool}
     */
    public function interpreteaza(?string $erori, ?AnafDeclaratie $declaratie = null, ?string $xml = null): array
    {
        $probleme = [];
        $rezumat = '';
        $netradus = false;

        foreach ($this->pasCuPas($erori, $declaratie, $xml) as $pas) {
            if ($pas['tip'] === 'problema') {
                $probleme[] = $pas['data'];
            } elseif ($pas['tip'] === 'gata') {
                $rezumat = $pas['rezumat'];
                $netradus = $pas['netradus'];
            }
        }

        return ['rezumat' => $rezumat, 'probleme' => $probleme, 'netradus' => $netradus];
    }

    /**
     * Aceeasi interpretare, dar data pe bucati: fiecare problema pleaca de
     * indata ce e gata, fara sa se astepte terminarea tuturor.
     *
     * Cautarea locului din fisier este partea inceata — pe un SAF-T de cateva
     * milioane de caractere se simte — asa ca utilizatorul vede primele erori
     * cat timp se lucreaza la urmatoarele.
     *
     * Trimite, in ordine: un pas „inceput" cu cate probleme sunt, cate un pas
     * „problema" pentru fiecare si un pas „gata" cu rezumatul.
     */
    public function pasCuPas(?string $erori, ?AnafDeclaratie $declaratie = null, ?string $xml = null): \Generator
    {
        $erori = trim((string) $erori);

        if ($erori === '') {
            yield ['tip' => 'inceput', 'total' => 0];
            yield ['tip' => 'gata', 'rezumat' => 'Declarația nu are erori de validare.', 'netradus' => false];

            return;
        }

        $deLucru = $this->liniiDeLucru($erori);
        $gasite = count($deLucru);

        /*
         * Un SAF-T respins poate avea zeci de mii de erori: validatorul scrie
         * cate un rand pentru fiecare tranzactie gresita. Explicate toate, ar
         * umple browserul fara folos — ele se repeta, iar omul corecteaza dupa
         * primele cateva. Se lucreaza deci pe un inceput de lista, iar restul
         * se spune la sfarsit.
         */
        $sarite = max(0, $gasite - self::MAXIM_EXPLICATE);

        if ($sarite > 0) {
            $deLucru = array_slice($deLucru, 0, self::MAXIM_EXPLICATE);
        }

        yield ['tip' => 'inceput', 'total' => count($deLucru), 'gasite' => $gasite, 'sarite' => $sarite];

        $probleme = [];
        $netradus = false;

        foreach ($deLucru as $index => $item) {
            $problema = $this->construieste($item['linie'], $item['sectiune'], $declaratie, $xml);

            if ($problema['explicatie'] === null) {
                $netradus = true;
            }

            $probleme[] = $problema;

            yield ['tip' => 'problema', 'index' => $index, 'data' => $problema];
        }

        yield [
            'tip' => 'gata',
            'rezumat' => $this->rezumat($probleme, $declaratie),
            'netradus' => $netradus,
            'sarite' => $sarite,
        ];
    }

    /**
     * Randurile care chiar descriu o problema, fiecare cu sectiunea din care
     * face parte. Se desparte de restul prelucrarii ca sa se stie din start
     * cate probleme sunt — altfel nu s-ar putea arata un „3 din 5".
     *
     * @return array<int, array{linie:string, sectiune:?array}>
     */
    protected function liniiDeLucru(string $erori): array
    {
        $deLucru = [];
        $sectiune = null;

        foreach (preg_split('/\r?\n/', $erori) as $linie) {
            $linie = trim($linie);

            if ($linie === '') {
                continue;
            }

            // Antetul de sectiune: „E: informatii (1)" sau „F: validari globale"
            if (preg_match('/^([EFW]):\s*(.+)$/u', $linie, $m)) {
                $sectiune = ['severitate' => $m[1], 'nume' => trim($m[2])];

                continue;
            }

            if ($this->deSarit($linie)) {
                continue;
            }

            $deLucru[] = ['linie' => $linie, 'sectiune' => $sectiune];
        }

        return $deLucru;
    }

    /** O problema gata de aratat: explicata si localizata in fisier. */
    protected function construieste(string $linie, ?array $sectiune, ?AnafDeclaratie $declaratie, ?string $xml): array
    {
        $problema = $this->analizeaza($linie, $sectiune, $declaratie);

        $locatie = $this->localizeaza($xml, $problema['candidati'], $sectiune);

        // Se arata utilizatorului exact sirul care a fost gasit, nu o varianta
        // incercata si nepotrivita.
        if ($locatie !== null) {
            $problema['cauta'] = $locatie['cauta'];
            unset($locatie['cauta']);
        }

        unset($problema['candidati']);
        $problema['locatie'] = $locatie;

        return $problema;
    }

    /**
     * Randuri care nu descriu o problema: indicatia generica pe care validatorul
     * o repeta inaintea erorilor de structura si numerotarea („1.") de dinaintea
     * celor fatale.
     */
    protected function deSarit(string $linie): bool
    {
        return strpos($linie, 'va rugam sa verificati daca folositi versiunea corecta') === 0
            || strpos($linie, 'please check if you use the right PDF version') === 0
            || (bool) preg_match('/^\d+\.$/', $linie);
    }

    protected function analizeaza(string $linie, ?array $sectiune, ?AnafDeclaratie $declaratie): array
    {
        $antet = $this->desprindeAntetul($linie);

        // La atribute eticheta este numele campului, la reguli codul regulii.
        $context = [
            'tip' => $antet['tip'],
            'camp' => $antet['tip'] === 'atribut' ? $antet['eticheta'] : null,
            'cod' => $antet['tip'] === 'regula' ? $antet['eticheta'] : null,
        ];

        $rezolvat = $this->potriveste($antet['mesaj'], $declaratie, $context);

        $camp = $rezolvat['camp'] ?? $context['camp'];
        $explicatie = $rezolvat['explicatie'] ?? null;

        // Regulile fara sablon propriu primesc totusi o explicatie: conditia
        // ceruta e chiar in mesaj, cu valorile de acum trecute in paranteze.
        if ($explicatie === null && $context['tip'] === 'regula') {
            $talmacita = $this->talmacesteConditia($antet['mesaj']);

            $explicatie = 'Regulile de corelare verifică potrivirea dintre câmpuri: dacă un câmp are o anumită '
                . 'valoare, atunci altele trebuie completate într-un anumit fel. Condiția cerută, pe înțeles: '
                . $talmacita
                . ($talmacita !== $antet['mesaj'] ? ' (scrisă de validator așa: ' . $antet['mesaj'] . ')' : '');

            $rezolvat['de_corectat'] = 'Citește condiția de mai sus și verifică în fișier câmpurile pe care le '
                . 'numește: valorile lor de acum sunt trecute în paranteze, chiar în textul regulii. '
                . 'De obicei e de ajuns să corectezi unul singur dintre ele.';
        }

        if ($explicatie !== null && $context['cod']) {
            $explicatie = 'Regula de corelare ' . $context['cod'] . ' nu este respectată. ' . $explicatie;
        }

        // Sablonul stie ce sa caute doar cand mesajul poarta valoarea gresita;
        // altfel se deduce din mesaj si din calea sectiunii. Se incearca mai
        // multe variante, pentru ca aceeasi greseala se scrie in feluri diferite
        // (atribut gol, element gol, element auto-inchis).
        $dinSablon = $rezolvat['cauta'] ?? null;

        $candidati = $dinSablon !== null
            ? [$this->completeazaCautarea($dinSablon, $camp)]
            : $this->candidatiiCautarii($antet, $sectiune, $camp);

        $candidati = array_values(array_unique(array_filter($candidati)));

        return [
            'candidati' => $candidati,
            'severitate' => $this->severitate($sectiune, $antet['fel']),
            'fel' => $antet['tip'],
            'sectiune' => $sectiune ? $sectiune['nume'] : null,
            'camp' => $camp,
            'regula' => $context['cod'],
            'mesaj' => $linie,
            'explicatie' => $explicatie,
            'de_corectat' => $rezolvat['de_corectat'] ?? null,
            // Textul de cautat in XML, ca utilizatorul sa ajunga direct la el;
            // daca fisierul e disponibil, ramane varianta chiar gasita in el.
            'cauta' => $candidati[0] ?? null,
        ];
    }

    /**
     * Regulile de corelare sunt scrise in limbajul validatorului, nu in romana.
     *
     * ANAF le da asa cum stau in DecValidation.jar: „Data_CMI # null daca (D_3 #
     * null si D_4 # null)". Cine nu stie ca „#" inseamna „diferit de" si ca
     * „null" inseamna „necompletat" citeste randul de trei ori si tot nu afla ce
     * i se cere. Se talmacesc semnele, pastrand numele campurilor asa cum sunt —
     * dupa ele se cauta in fisier.
     */
    protected function talmacesteConditia(string $mesaj): string
    {
        $talmacit = $mesaj;

        // „daca ... atunci ..." se scrie de multe ori fara „atunci"
        $talmacit = preg_replace('/^\s*daca\s+/ui', 'Dacă ', $talmacit);
        $talmacit = preg_replace('/\s+atunci\s+/ui', ', atunci ', $talmacit);

        $inlocuiri = [
            '/\s*#\s*null\b/ui' => ' este completat',
            '/\s*=\s*null\b/ui' => ' este necompletat',
            '/\s*#\s*/u' => ' ≠ ',
            '/\bnull\b/ui' => 'necompletat',
            '/\bsi\b/ui' => 'și',
            '/\bsau\b/ui' => 'sau',
            '/\bnu trebuie sa apara\b/ui' => 'nu are voie să apară',
            '/\btrebuie sa apara\b/ui' => 'trebuie să apară',
            '/\btrebuie sa fie\b/ui' => 'trebuie să fie',
            '/\btrebuie sa existe\b/ui' => 'trebuie să existe',
            '/\bsectiunea\b/ui' => 'secțiunea',
            '/\batributul\b/ui' => 'atributul',
        ];

        foreach ($inlocuiri as $tipar => $inlocuire) {
            $talmacit = preg_replace($tipar, $inlocuire, $talmacit);
        }

        return trim(preg_replace('/\s{2,}/u', ' ', $talmacit));
    }

    /**
     * Ce anume sa caute utilizatorul in fisier, cand sablonul nu a putut spune.
     *
     * Trei surse, in ordinea increderii: atributul gol se cauta ca atare;
     * regulile de corelare pun valoarea de acum in paranteze, dupa numele
     * campului; iar la restul se cauta valoarea dintre apostrofuri — pusa intre
     * etichete, daca sectiunea ne spune ce element este.
     */
    protected function candidatiiCautarii(array $antet, ?array $sectiune, ?string $camp): array
    {
        $mesaj = $antet['mesaj'];
        $element = $this->elementulSectiunii($sectiune);
        $candidati = [];

        // Atribut sau element gol: se scrie in trei feluri, le incercam pe toate.
        if (stripos($mesaj, 'atribut prezent dar vid nepermis') === 0) {
            if ($camp) {
                $candidati[] = $camp . '=""';
            }

            if ($element) {
                $candidati[] = '<' . $element . '></' . $element . '>';
                $candidati[] = '<' . $element . '/>';
                $candidati[] = '<' . $element . ' />';
            }

            return $candidati;
        }

        // Regulile de corelare pun valoarea de acum in paranteze, dupa numele campului.
        if ($antet['tip'] === 'regula' && preg_match('/([A-Za-z_][\w.]*)\s*\(([^)\s]*)\)/u', $mesaj, $m)) {
            $candidati[] = $m[1] . '="' . $m[2] . '"';
            $candidati[] = '<' . $m[1] . '>' . $m[2] . '</' . $m[1] . '>';
        }

        // Valoarea gresita, cand mesajul o poarta intre apostrofuri.
        if (preg_match("/'([^']+)'/u", $mesaj, $m)) {
            $valoare = $m[1];

            if ($camp) {
                $candidati[] = $camp . '="' . $valoare . '"';
            }

            // In SAF-T valoarea sta intre etichete, nu intr-un atribut.
            if ($element) {
                $candidati[] = '<' . $element . '>' . $valoare . '</' . $element . '>';
                $candidati[] = '>' . $valoare . '</' . $element . '>';
            }

            $candidati[] = $valoare;
        }

        // Mesaje scurte, de forma „cuiP invalid": macar campul, in sectiunea lui.
        if ($candidati === [] && preg_match('/^([A-Za-z_][\w.]*)\s+invalid/ui', $mesaj, $m)) {
            $candidati[] = $m[1] . '="';
        }

        return $candidati;
    }

    /**
     * Cand s-a cautat doar deschiderea unui atribut („cuiP=\""), potrivirea se
     * duce pana la ghilimeaua de inchidere: si evidentierea, si textul de cautat
     * cuprind atunci campul intreg, nu o bucata din el.
     */
    protected function intregesteAtributul(string $xml, string $cauta, int $pozitie): string
    {
        if (mb_substr($cauta, -2) !== '="') {
            return $cauta;
        }

        $dupaGhilimea = $pozitie + strlen($cauta);
        $inchidere = strpos($xml, '"', $dupaGhilimea);

        // O valoare peste aceasta lungime inseamna ca ghilimeaua cautata e de
        // fapt a altui atribut, deci se lasa potrivirea asa cum era.
        if ($inchidere === false || $inchidere - $dupaGhilimea > 200) {
            return $cauta;
        }

        return substr($xml, $pozitie, $inchidere - $pozitie + 1);
    }

    /** Ultimul element din calea sectiunii: „... sectiune AccountID (1)" -> AccountID. */
    protected function elementulSectiunii(?array $sectiune): ?string
    {
        if ($sectiune === null) {
            return null;
        }

        if (!preg_match_all('/sectiune\s+(\S+?)\s*\(\d+\)/u', $sectiune['nume'], $m)) {
            return null;
        }

        return end($m[1]) ?: null;
    }

    /**
     * De unde sa inceapa cautarea: chiar de la elementul reclamat.
     *
     * Antetul sectiunii e o cale intreaga, cu cate un numar de ordine la fiecare
     * treapta — „GeneralLedgerEntries (1) sectiune Journal (1) sectiune
     * Transaction (1621) sectiune Description (1)". Se merge pe ea treapta cu
     * treapta: in fiecare, se numara aparitiile de la locul parintelui incolo.
     *
     * Asa se ajunge chiar la elementul gresit, nu doar in preajma lui — ceea ce
     * la SAF-T e singura cale, fiindca acolo erorile nu spun ce valoare e
     * gresita, ci a cata sectiune e.
     */
    protected function inceputulSectiunii(string $xml, ?array $sectiune): int
    {
        if ($sectiune === null) {
            return 0;
        }

        if (!preg_match_all('/(\S+?)\s*\((\d+)\)/u', $sectiune['nume'], $m, PREG_SET_ORDER)) {
            return 0;
        }

        $pozitie = 0;

        foreach ($m as $treapta) {
            $gasit = $this->aCataAparitie($xml, '<' . $treapta[1], (int) $treapta[2], $pozitie);

            /*
             * O treapta negasita nu strica ce s-a aflat pana atunci: mai bine
             * randul parintelui decat niciun rand. Se intampla la elementele
             * scrise scurt, fara eticheta de deschidere de sine statatoare.
             */
            if ($gasit === null) {
                break;
            }

            $pozitie = $gasit;
        }

        return $pozitie;
    }

    /**
     * A cata aparitie a unei etichete, incepand de la un loc dat.
     *
     * Numele se ia intreg, nu ca inceput de nume: „<Account" se potriveste si cu
     * „<AccountID", si cu „<AccountDescription", iar numarand asa, a 56-a
     * sectiune „Account" cadea in cu totul alta parte a fisierului. De aceea
     * dupa nume trebuie sa urmeze ce incheie o eticheta — „>", un spatiu sau
     * „/".
     *
     * @return ?int locul ei, sau null daca nu sunt atatea
     */
    protected function aCataAparitie(string $xml, string $eticheta, int $aCata, int $deLa): ?int
    {
        $gasite = 0;
        $cauta = $deLa;

        while (true) {
            $gasit = strpos($xml, $eticheta, $cauta);

            if ($gasit === false) {
                return null;
            }

            $cauta = $gasit + 1;
            $dupa = $xml[$gasit + strlen($eticheta)] ?? '';

            if (strpos(">/ \t\r\n", $dupa) === false) {
                continue;
            }

            if (++$gasite >= max($aCata, 1)) {
                return $gasit;
            }
        }
    }

    /**
     * Unde anume in fisier trebuie mers, ca sa nu fie cautat de mana.
     *
     * Se intoarce linia si coloana (numarate de la 1, ca in Notepad++), plus
     * randul respectiv. Randurile foarte lungi — declaratiile generate automat
     * au uneori tot fisierul pe o singura linie — se arata doar pe o bucata din
     * jurul potrivirii.
     *
     * @param array<int, string> $candidati variante de cautat, in ordinea increderii
     *
     * @return array{cauta:string, linie:int, coloana:int, text:string, trunchiat:bool, aparitii:int}|null
     */
    protected function localizeaza(?string $xml, array $candidati, ?array $sectiune = null): ?array
    {
        /*
         * Lipsa unei valori de cautat nu mai inseamna ca nu se poate spune
         * nimic: calea sectiunii duce singura la elementul reclamat. La SAF-T,
         * unde erorile nu spun ce valoare e gresita, ea e tot ce avem.
         */
        if ($xml === null) {
            return null;
        }

        $deLa = $this->inceputulSectiunii($xml, $sectiune);

        $cauta = null;
        $pozitie = false;

        /*
         * Cautarea se face pe octeti, nu pe caractere: pe un fisier de cateva
         * milioane de caractere, mb_strpos ar dura secunde. Pozitia in caractere
         * — cea care conteaza pentru Notepad++ — se calculeaza la final, doar in
         * randul gasit.
         */
        foreach ($candidati as $varianta) {
            if ($varianta === '') {
                continue;
            }

            // Intai in sectiunea indicata, apoi in tot fisierul.
            $gasit = $deLa > 0 ? strpos($xml, $varianta, $deLa) : false;

            if ($gasit === false) {
                $gasit = strpos($xml, $varianta);
            }

            if ($gasit !== false) {
                $cauta = $varianta;
                $pozitie = $gasit;
                break;
            }
        }

        /*
         * Fara nicio valoare de cautat — asa sunt mai toate erorile de SAF-T,
         * care spun „atribut prezent dar vid nepermis", fara sa spuna care —
         * ramane calea sectiunii. Ea duce chiar la elementul reclamat, si e tot
         * ce-i trebuie omului ca sa deschida fisierul la randul bun.
         */
        if (($cauta === null || $pozitie === false) && $deLa > 0) {
            $cauta = '<' . ($this->elementulSectiunii($sectiune) ?: '');
            $pozitie = $deLa;
        }

        if ($cauta === null || $cauta === '<' || $pozitie === false) {
            return null;
        }

        $cauta = $this->intregesteAtributul($xml, $cauta, $pozitie);

        // Numararea randurilor merge pe octeti: "\n" ocupa un singur octet.
        $linie = substr_count($xml, "\n", 0, $pozitie) + 1;

        // Cautarea inapoi se face pe loc, fara sa se taie o copie a fisierului:
        // la un SAF-T de zeci de megaocteti, copia aceea s-ar face la fiecare eroare.
        $inceputRand = $pozitie > 0 ? strrpos($xml, "\n", $pozitie - strlen($xml)) : false;
        $inceputRand = $inceputRand === false ? 0 : $inceputRand + 1;

        $sfarsitRand = strpos($xml, "\n", $inceputRand);
        $textRand = $sfarsitRand === false
            ? substr($xml, $inceputRand)
            : substr($xml, $inceputRand, $sfarsitRand - $inceputRand);

        $textRand = rtrim($textRand, "\r");

        // Coloana se numara in caractere, ca in Notepad++, dar doar in randul
        // acesta: diacriticele dinaintea potrivirii ar decala altfel pozitia.
        $coloana = mb_strlen(substr($textRand, 0, $pozitie - $inceputRand)) + 1;

        /*
         * Randul se da in trei bucati, ca interfata sa poata scoate in evidenta
         * exact partea gresita. Impartirea se face aici, pe pozitia reala, nu
         * prin cautare in text: altfel, pe randurile in care aceeasi valoare
         * apare de mai multe ori, s-ar colora prima aparitie, nu cea gasita.
         */
        $inainte = mb_substr($textRand, 0, $coloana - 1);
        $potrivire = mb_substr($textRand, $coloana - 1, mb_strlen($cauta));
        $dupa = mb_substr($textRand, $coloana - 1 + mb_strlen($cauta));

        $trunchiat = false;
        $margineStanga = (int) (self::FEREASTRA_RAND / 3);
        $margineDreapta = self::FEREASTRA_RAND - $margineStanga;

        if (mb_strlen($inainte) > $margineStanga) {
            $inainte = '…' . mb_substr($inainte, -$margineStanga);
            $trunchiat = true;
        } else {
            // Inceputul de rand: indentarea nu spune nimic, ocupa doar loc.
            $inainte = ltrim($inainte);
        }

        if (mb_strlen($dupa) > $margineDreapta) {
            $dupa = mb_substr($dupa, 0, $margineDreapta) . '…';
            $trunchiat = true;
        } else {
            $dupa = rtrim($dupa);
        }

        return [
            'cauta' => $cauta,
            'linie' => $linie,
            'coloana' => $coloana,
            'inainte' => $inainte,
            'potrivire' => $potrivire,
            'dupa' => $dupa,
            'text' => $inainte . $potrivire . $dupa,
            'trunchiat' => $trunchiat,
            'aparitii' => substr_count($xml, $cauta),
        ];
    }

    /**
     * Sabloanele stiu doar valoarea gresita; numele campului vine din antetul
     * mesajului, deci cautarea se intregeste aici: cui="99999999".
     */
    protected function completeazaCautarea(?string $cauta, ?string $camp): ?string
    {
        if ($cauta === null || $camp === null) {
            return $cauta;
        }

        return strpos($cauta, '"') === 0 ? $camp . '=' . $cauta : $cauta;
    }

    /**
     * Desparte „eroare atribut: cui: CUI invalid (...)" in fel, tip, camp si mesaj.
     *
     * @return array{fel:string, tip:?string, eticheta:?string, mesaj:string}
     */
    protected function desprindeAntetul(string $linie): array
    {
        $tipar = '/^(eroare|atentionare)\s+(atribut|regula|structura)\s*:\s*(.*)$/ui';

        if (!preg_match($tipar, $linie, $m)) {
            return ['fel' => 'eroare', 'tip' => null, 'eticheta' => null, 'mesaj' => $linie];
        }

        $fel = mb_strtolower($m[1]);
        $tip = mb_strtolower($m[2]);
        $rest = trim($m[3]);
        $eticheta = null;

        /*
         * La atribute urmeaza numele campului, la reguli codul regulii. Numele
         * poate lipsi cu totul („eroare atribut: : numar intreg eronat"): la
         * SAF-T valoarea gresita sta intre etichete, nu intr-un atribut, iar
         * validatorul lasa atunci locul gol.
         */
        if (($tip === 'atribut' || $tip === 'regula') && preg_match('/^(\S*?)\s*:\s*(.*)$/u', $rest, $c)) {
            $eticheta = $c[1] !== '' ? $c[1] : null;
            $rest = trim($c[2]);
        }

        return ['fel' => $fel, 'tip' => $tip, 'eticheta' => $eticheta, 'mesaj' => $rest];
    }

    /** @return array{camp?:?string, explicatie:?string, de_corectat:?string, cauta?:?string} */
    protected function potriveste(string $mesaj, ?AnafDeclaratie $declaratie, array $context = []): array
    {
        foreach ($this->sabloane() as $sablon) {
            if (preg_match($sablon['tipar'], $mesaj, $m)) {
                return $sablon['explica']($m, $declaratie, $context);
            }
        }

        return ['explicatie' => null, 'de_corectat' => null];
    }

    /**
     * Sabloanele de mesaj ale validatorului si traducerea lor.
     *
     * Ordinea conteaza: tiparele specifice trebuie sa fie inaintea celor generale.
     *
     * @return array<int, array{tipar:string, explica:callable}>
     */
    protected function sabloane(): array
    {
        return array_merge(
            $this->sabloaneIdentificatori(),
            $this->sabloaneValori(),
            $this->sabloaneAtribute(),
            $this->sabloaneStructura(),
            $this->sabloaneReguli(),
            $this->sabloaneFatale()
        );
    }

    /** Coduri de identificare: CUI, CIF, CNP, IBAN, email. */
    protected function sabloaneIdentificatori(): array
    {
        return [
            [
                'tipar' => "/^(?:Cod identificare fiscala \\(CIF\\)|CUI|CIF) invalid\\s*\\('([^']*)'\\)/ui",
                'explica' => function ($m, $declaratie) {
                    $asteptat = $declaratie && $declaratie->cui
                        ? ' Pentru această declarație, codul așteptat este ' . $declaratie->cui . '.'
                        : '';

                    return [
                        'cauta' => '"' . $m[1] . '"',
                        'explicatie' => 'Codul fiscal „' . $m[1] . '" nu este valid: cifra lui de control nu se '
                            . 'potrivește. Nu înseamnă că firma nu ar exista, ci că numărul în sine e greșit '
                            . '(o cifră lipsă, inversată sau în plus).',
                        'de_corectat' => 'Caută în fișier valoarea ' . $m[1] . ' și înlocuiește-o cu codul fiscal '
                            . 'corect, scris doar cu cifre, fără prefixul RO și fără spații.' . $asteptat
                            . ' Dacă e vorba de un partener străin, greșeala e de obicei alta: codul lui poartă '
                            . 'prefixul României în loc de al țării sale (HU, BG, DE și așa mai departe), iar '
                            . 'atunci verificarea cifrei de control se face după regulile greșite.',
                    ];
                },
            ],
            [
                // Mesaj scurt, folosit de unele declaratii: „cuiP invalid".
                'tipar' => '/^(\S+)\s+invalid\s*$/ui',
                'explica' => function ($m) {
                    return [
                        'camp' => $m[1],
                        'explicatie' => 'Codul din câmpul „' . $m[1] . '" nu este valid: nu trece verificarea '
                            . 'cifrei de control. La codurile de partener, cauza obișnuită este un cod scris '
                            . 'din memorie sau preluat greșit din factură.',
                        'de_corectat' => 'Verifică codul fiscal al partenerului în registrul ANAF și corectează-l '
                            . 'în fișier. Scrie-l doar cu cifre, fără prefixul RO.',
                    ];
                },
            ],
            [
                'tipar' => "/^CNP invalid\\s*\\('([^']*)'\\)/ui",
                'explica' => function ($m) {
                    return [
                        'cauta' => '"' . $m[1] . '"',
                        'explicatie' => 'CNP-ul „' . $m[1] . '" nu trece verificarea: ultima cifră a unui CNP se '
                            . 'calculează din celelalte, iar aici calculul nu iese. Cel mai des e o greșeală de tastare.',
                        'de_corectat' => 'Scrie CNP-ul corect: exact 13 cifre, fără spații sau liniuțe.',
                    ];
                },
            ],
            [
                'tipar' => "/^cod IBAN invalid\\s*\\('([^']*)'\\)/ui",
                'explica' => function ($m) {
                    return [
                        'cauta' => '"' . $m[1] . '"',
                        'explicatie' => 'Contul „' . $m[1] . '" nu este un IBAN valid. Un IBAN românesc are 24 de '
                            . 'caractere, începe cu RO, urmat de două cifre de control și de codul băncii.',
                        'de_corectat' => 'Copiază IBAN-ul din extrasul de cont, fără spații. Verifică mai ales '
                            . 'literele care se confundă cu cifre: O cu 0, I cu 1.',
                    ];
                },
            ],
            [
                'tipar' => "/^Email invalid\\s*\\('([^']*)'\\)/ui",
                'explica' => function ($m) {
                    return [
                        'cauta' => '"' . $m[1] . '"',
                        'explicatie' => 'Adresa de email „' . $m[1] . '" nu are o formă corectă.',
                        'de_corectat' => 'Scrie adresa sub forma nume@domeniu.ro, fără spații și fără diacritice.',
                    ];
                },
            ],
        ];
    }

    /** Valori: liste, intervale, lungimi, numere, date calendaristice. */
    protected function sabloaneValori(): array
    {
        return [
            [
                'tipar' => "/^valoarea\\s*'([^']*)'\\s*nu se afla in lista/ui",
                'explica' => function ($m, $declaratie, $context = []) {
                    /*
                     * La SAF-T, aceasta e eroarea cea mai deasa, si aproape
                     * niciodata nu inseamna „valoare gresita": inseamna ca in
                     * fisier a plecat codul intern al programului de
                     * contabilitate in locul codului din nomenclatorul ANAF.
                     * Se vede dupa forma: coduri numerice de sase cifre, la zeci
                     * de randuri deodata.
                     */
                    $pareCodDeTaxa = (bool) preg_match('/^\d{5,6}$/', $m[1]);

                    if ($pareCodDeTaxa) {
                        return [
                            'cauta' => '"' . $m[1] . '"',
                            'explicatie' => 'Codul „' . $m[1] . '" nu se află în nomenclatorul ANAF. La D406, '
                                . 'aceasta e cea mai deasă eroare și rareori înseamnă o valoare greșită: de '
                                . 'obicei în fișier a plecat codul intern al programului de contabilitate, în '
                                . 'locul codului oficial cerut de ANAF.',
                            'de_corectat' => 'Se face maparea în programul de contabilitate: fiecărui cod intern '
                                . '(de taxă, de cont, de tip de document) i se pune în dreptul lui codul din '
                                . 'nomenclatorul SAF-T al ANAF. După mapare, fișierul se generează din nou. '
                                . 'Dacă erorile sunt zeci, aproape sigur lipsește maparea întreagă, nu un cod.',
                        ];
                    }

                    return [
                        'cauta' => '"' . $m[1] . '"',
                        'explicatie' => 'Acest câmp acceptă doar câteva valori dinainte stabilite, iar „' . $m[1]
                            . '" nu este una dintre ele.',
                        'de_corectat' => 'Pune una dintre valorile permise. Lista lor se află în schema XSD a '
                            . 'declarației, publicată de ANAF; cel mai simplu este însă să regenerezi fișierul, '
                            . 'alegând corect opțiunea din programul care l-a creat.',
                    ];
                },
            ],
            [
                'tipar' => "/^valoarea\\s*'([^']*)'\\s*nu se incadreaza in intervalul/ui",
                'explica' => function ($m) {
                    return [
                        'cauta' => '"' . $m[1] . '"',
                        'explicatie' => 'Valoarea „' . $m[1] . '" este în afara intervalului permis. La majoritatea '
                            . 'câmpurilor din declarații sunt acceptate doar numere pozitive sau zero.',
                        'de_corectat' => 'Pune o valoare din intervalul cerut. O valoare negativă acolo unde se '
                            . 'numără documente înseamnă aproape sigur o eroare de calcul în programul care a '
                            . 'generat fișierul.',
                    ];
                },
            ],
            [
                'tipar' => "/^sir mai lung de\\s*(\\d+)\\s*caractere\\s*\\('([^']*)'\\)/ui",
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Textul are ' . mb_strlen($m[2]) . ' de caractere, dar ANAF acceptă cel mult '
                            . $m[1] . '.',
                        'de_corectat' => 'Scurtează valoarea la maximum ' . $m[1] . ' de caractere. Dacă e o '
                            . 'denumire de firmă, folosește forma prescurtată.',
                    ];
                },
            ],
            [
                'tipar' => "/^valoare nenumerica\\s*\\('([^']*)'\\)/ui",
                'explica' => function ($m) {
                    return [
                        'cauta' => '"' . $m[1] . '"',
                        'explicatie' => 'Câmpul acceptă doar cifre, iar în fișier scrie „' . $m[1] . '".',
                        'de_corectat' => 'Scrie o valoare numerică, fără separator de mii, fără simbol de monedă '
                            . 'și fără spații. Zecimalele se despart cu punct, nu cu virgulă.',
                    ];
                },
            ],
            [
                'tipar' => "/^data calendaristica eronata\\s*\\(format:\\s*([^)]*)\\)\\s*:\\s*'([^']*)'/ui",
                'explica' => function ($m) {
                    return [
                        'cauta' => '"' . $m[2] . '"',
                        'explicatie' => 'Data „' . $m[2] . '" nu este scrisă în formatul cerut (' . $m[1] . ').',
                        'de_corectat' => 'Scrie data ca ' . $m[1] . ' — de exemplu 2026-03-15 pentru 15 martie 2026. '
                            . 'Atenție la ordinea an-lună-zi, nu zi-lună-an.',
                    ];
                },
            ],
            [
                'tipar' => "/^data calendaristica eronata\\s*:\\s*'([^']*)'/ui",
                'explica' => function ($m) {
                    return [
                        'cauta' => '"' . $m[1] . '"',
                        'explicatie' => 'Data „' . $m[1] . '" nu există în calendar — de pildă 31 aprilie sau '
                            . '29 februarie într-un an fără zi bisectă.',
                        'de_corectat' => 'Corectează ziua sau luna, astfel încât data să existe cu adevărat.',
                    ];
                },
            ],
            [
                'tipar' => "/^numar intreg eronat\\s*:\\s*'([^']*)'/ui",
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Aici se așteaptă un număr întreg, iar în fișier scrie „' . $m[1] . '". '
                            . 'Un număr întreg nu are parte zecimală: nici punct, nici virgulă.',
                        'de_corectat' => 'Scrie valoarea fără zecimale. Dacă e o sumă care chiar are bani, '
                            . 'înseamnă că a fost pusă în câmpul greșit — locul ei este într-un câmp de sumă, '
                            . 'nu într-unul de numărare sau de cod.',
                    ];
                },
            ],
            [
                'tipar' => "/^numar real eronat\\s*:\\s*'([^']*)'/ui",
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Aici se așteaptă un număr, iar „' . $m[1] . '" nu poate fi citit ca atare.',
                        'de_corectat' => 'Scrie numărul cu punct pentru zecimale (1234.56), fără separator de mii, '
                            . 'fără spații și fără simbolul monedei.',
                    ];
                },
            ],
            [
                'tipar' => "/^numarul zecimal\\s*\\('([^']*)'\\)\\s*nu respecta modelul\\s*:\\s*(\\d+)\\.(\\d+)/ui",
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Numărul „' . $m[1] . '" nu respectă formatul cerut: cel mult ' . $m[2]
                            . ' cifre în total, dintre care cel mult ' . $m[3] . ' după virgulă.',
                        'de_corectat' => 'Rotunjește valoarea la ' . $m[3] . ' zecimale. Dacă numărul are prea '
                            . 'multe cifre în total, verifică dacă nu s-a strecurat o eroare de calcul.',
                    ];
                },
            ],
            [
                'tipar' => '/^valoarea din content trebuie sa existe/ui',
                'explica' => function () {
                    return [
                        'explicatie' => 'Eticheta există în fișier, dar este goală: nu are nicio valoare între '
                            . 'eticheta de deschidere și cea de închidere.',
                        'de_corectat' => 'Completează valoarea între etichete, de forma <element>valoare</element>.',
                    ];
                },
            ],
        ];
    }

    /** Prezenta si forma atributelor. */
    protected function sabloaneAtribute(): array
    {
        return [
            [
                'tipar' => '/^atributul trebuie sa existe/ui',
                'explica' => function ($m, $declaratie, $context = []) {
                    $nume = $context['camp'] ?? null;

                    return [
                        'cauta' => $nume ? $nume . '=' : null,
                        'explicatie' => ($nume ? 'Atributul „' . $nume . '"' : 'Acest atribut')
                            . ' lipsește cu totul din fișier, iar schema ANAF îl cere obligatoriu. '
                            . 'Programul care a generat XML-ul nu l-a scris.',
                        'de_corectat' => 'Adaugă ' . ($nume ? $nume . '="..."' : 'atributul')
                            . ' în eticheta secțiunii indicate. Dacă nu știi ce valoare trebuie să aibă, '
                            . 'regenerează declarația din programul de contabilitate — de obicei înseamnă că '
                            . 'un câmp a rămas necompletat acolo.',
                    ];
                },
            ],
            [
                'tipar' => '/^atribut prezent dar vid nepermis/ui',
                'explica' => function () {
                    return [
                        'explicatie' => 'Câmpul este scris în fișier, dar nu are nicio valoare — fie un atribut '
                            . 'gol (camp=""), fie o etichetă fără conținut (<camp></camp>). ANAF nu acceptă '
                            . 'câmpuri goale: ori le completezi, ori le scoți.',
                        'de_corectat' => 'Completează valoarea sau șterge câmpul cu totul, dacă schema îl '
                            . 'permite ca opțional. Un câmp lăsat gol înseamnă de obicei o rubrică necompletată '
                            . 'în programul de contabilitate.',
                    ];
                },
            ],
            [
                'tipar' => "/^atributul cu valoarea:\\s*'([^']*)'\\s*nu trebuie sa existe aici/ui",
                'explica' => function ($m) {
                    return [
                        'cauta' => '"' . $m[1] . '"',
                        'explicatie' => 'Atributul acesta nu are ce căuta în secțiunea în care se află, chiar dacă '
                            . 'în alte secțiuni ale declarației este permis.',
                        'de_corectat' => 'Șterge atributul din această secțiune. Dacă informația e reală, locul ei '
                            . 'este în altă secțiune a declarației.',
                    ];
                },
            ],
            [
                'tipar' => "/^atribut necunoscut\\s*\\('([^']*)'\\)(?:\\s*in namespace='([^']*)')?/ui",
                'explica' => function ($m) {
                    return [
                        'camp' => $m[1],
                        'cauta' => $m[1] . '=',
                        'explicatie' => 'Fișierul conține atributul „' . $m[1] . '", pe care schema ANAF nu îl '
                            . 'cunoaște. Fie este scris greșit, fie provine dintr-o versiune mai veche sau mai nouă '
                            . 'a declarației.',
                        'de_corectat' => 'Șterge atributul ' . $m[1] . ', sau corectează-i numele dacă e o greșeală '
                            . 'de scriere. Dacă fișierul vine dintr-un program, verifică dacă acesta este actualizat.',
                    ];
                },
            ],
        ];
    }

    /** Asezarea sectiunilor si a elementelor in fisier. */
    protected function sabloaneStructura(): array
    {
        return [
            [
                'tipar' => "/^namespace\\s*\\('([^']*)'\\)\\s*lipsa sau incorect la sectiunea\\s*(\\S+?)\\.?\\s*(?:Valoarea corecta este\\s*xmlns='([^']*)')?$/ui",
                'explica' => function ($m, $declaratie) {
                    $perioada = $declaratie && $declaratie->anul
                        ? ' Declarația aceasta este pentru '
                            . ($declaratie->luna ? 'luna ' . $declaratie->luna . '/' : 'anul ')
                            . $declaratie->anul . '.'
                        : '';

                    $corect = isset($m[3]) && $m[3] !== ''
                        ? ' Validatorul spune că valoarea corectă ar fi xmlns=\'' . $m[3] . '\'.'
                        : '';

                    return [
                        'camp' => 'xmlns',
                        'cauta' => "xmlns='" . $m[1] . "'",
                        'explicatie' => 'Fișierul declară versiunea de schemă „' . $m[1] . '", iar validatorul nu o '
                            . 'acceptă pentru perioada raportată. ANAF schimbă versiunea schemei de la an la an, '
                            . 'iar versiunea se alege după luna și anul din declarație.' . $perioada . $corect
                            . ' Atenție: acest mesaj apare deseori și când luna sau anul sunt greșite — '
                            . 'validatorul caută atunci o schemă care nu există.',
                        'de_corectat' => 'Verifică întâi luna și anul din declarație; dacă sunt greșite, corectează-le '
                            . 'și revalidează. Dacă sunt corecte, înlocuiește valoarea xmlns cu cea indicată mai sus, '
                            . 'sau regenerează declarația cu versiunea curentă a programului.',
                    ];
                },
            ],
            [
                'tipar' => "/^Perioada de raportare incorecta\\s*\\('([^']*)'\\)\\s*sau anterioara perioadei de raportare minime[^(]*\\(([^)]*)\\)/ui",
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Perioada „' . $m[1] . '" nu poate fi raportată: fie este scrisă greșit, fie '
                            . 'este mai veche decât prima perioadă pentru care ANAF acceptă acest tip de declarație ('
                            . $m[2] . ').',
                        'de_corectat' => 'Corectează luna și anul din declarație. Pentru perioade vechi, ANAF cere '
                            . 'formularul valabil la acea dată, nu pe cel curent.',
                    ];
                },
            ],
            [
                'tipar' => "/^continut nepermis\\s*\\('([^']*)'\\)\\s*in sau dupa sectiunea\\s*(\\S+)/ui",
                'explica' => function ($m) {
                    return [
                        'cauta' => $m[1],
                        'explicatie' => 'În secțiunea „' . $m[2] . '" apare text care nu ar trebui să fie acolo: '
                            . '„' . $m[1] . '". Într-un XML de declarație, între etichete nu se scrie text liber.',
                        'de_corectat' => 'Șterge textul rătăcit dintre etichete. De obicei apare când fișierul a '
                            . 'fost editat manual sau a fost lipit conținut din altă parte.',
                    ];
                },
            ],
            [
                'tipar' => "/^elementul\\s*'([^']*)'\\s*\\(namespace = '([^']*)'\\)\\s*nu poate sa apara in descendeta caii XML\\s*'([^']*)'/ui",
                'explica' => function ($m) {
                    return [
                        'camp' => $m[1],
                        'cauta' => '<' . $m[1],
                        'explicatie' => 'Elementul „' . $m[1] . '" este pus în locul greșit: nu are voie să apară '
                            . 'în interiorul căii „' . $m[3] . '".',
                        'de_corectat' => 'Mută elementul ' . $m[1] . ' în secțiunea în care schema îl așteaptă. '
                            . 'Cel mai sigur este să regenerezi fișierul din programul care l-a creat.',
                    ];
                },
            ],
            [
                // Greseala de scriere „namesapace" este chiar in validatorul ANAF.
                'tipar' => "/^element necunoscut\\s*\\('([^']*)'\\)\\s*in namesapace\\s*(.*)$/ui",
                'explica' => function ($m) {
                    return [
                        'camp' => $m[1],
                        'cauta' => '<' . $m[1],
                        'explicatie' => 'Fișierul conține elementul „' . $m[1] . '", pe care schema ANAF nu îl '
                            . 'cunoaște. Fie e scris greșit, fie provine din altă versiune a declarației.',
                        'de_corectat' => 'Șterge elementul <' . $m[1] . '> sau corectează-i numele. Atenție și la '
                            . 'litere mari/mici: în XML „Cota" și „cota" sunt elemente diferite.',
                    ];
                },
            ],
            [
                'tipar' => "/^(?:elementul|grupul)\\s*'([^']*)'\\s*a depasit numarul maxim de aparitii\\s*\\((\\d+)\\);\\s*a aparut de\\s*(\\d+)\\s*ori/ui",
                'explica' => function ($m) {
                    return [
                        'camp' => $m[1],
                        'cauta' => '<' . $m[1],
                        'explicatie' => 'Elementul „' . $m[1] . '" apare de ' . $m[3] . ' ori, deși sunt permise '
                            . 'cel mult ' . $m[2] . '.',
                        'de_corectat' => 'Șterge aparițiile în plus, păstrându-le pe primele ' . $m[2]
                            . '. Dacă toate sunt necesare, înseamnă că datele au fost puse în secțiunea greșită.',
                    ];
                },
            ],
            [
                'tipar' => "/^(?:elementul|grupul)\\s*'([^']*)'\\s*ar fi trebuit sa apara de minimum\\s*(\\d+)\\s*ori, dar apare efectiv de\\s*(\\d+)\\s*ori/ui",
                'explica' => function ($m) {
                    return [
                        'camp' => $m[1],
                        'cauta' => '<' . $m[1],
                        'explicatie' => 'Elementul „' . $m[1] . '" apare de ' . $m[3] . ' ori, dar schema cere cel '
                            . 'puțin ' . $m[2] . '.',
                        'de_corectat' => ((int) $m[3] === 0
                            ? 'Adaugă secțiunea <' . $m[1] . '>, care lipsește cu totul. '
                            : 'Completează până la ' . $m[2] . ' apariții. ')
                            . 'De regulă înseamnă că o rubrică a rămas necompletată în programul de contabilitate.',
                    ];
                },
            ],
            [
                'tipar' => "/^(?:elementul\\s*'([^']*)'\\s*are|prea multe sau prea putine sectiuni repetitive|.*are prea multe sau prea putine aparitii).*minimum\\/maximum permise:\\s*(\\d+)\\/(\\d+);\\s*(\\d+)\\s*efective/ui",
                'explica' => function ($m) {
                    $nume = isset($m[1]) && $m[1] !== '' ? '„' . $m[1] . '"' : 'Această secțiune';

                    return [
                        'camp' => $m[1] ?: null,
                        'cauta' => isset($m[1]) && $m[1] !== '' ? '<' . $m[1] : null,
                        'explicatie' => $nume . ' apare de ' . $m[4] . ' ori, dar schema permite între ' . $m[2]
                            . ' și ' . $m[3] . ' apariții.',
                        'de_corectat' => ((int) $m[4] > (int) $m[3]
                            ? 'Șterge aparițiile în plus. '
                            : 'Completează aparițiile care lipsesc. ')
                            . 'Numărul de repetări este fixat de schema declarației, nu poate fi ales liber.',
                    ];
                },
            ],
            [
                'tipar' => "/^depasire maxOccurs choice\\s*\\('([^']*)'\\)\\s*sau selectie multipla choice in descendenta elementului\\s*'([^']*)',\\s*prin elementele\\s*'([^']*)'\\s*si\\s*'([^']*)'/ui",
                'explica' => function ($m) {
                    return [
                        'camp' => $m[2],
                        'explicatie' => 'În interiorul elementului „' . $m[2] . '" sunt completate deodată „' . $m[3]
                            . '" și „' . $m[4] . '", deși schema cere să fie ales doar unul dintre ele.',
                        'de_corectat' => 'Păstrează doar varianta care se potrivește situației și șterge-o pe '
                            . 'cealaltă. Sunt alternative, nu completări una la alta.',
                    ];
                },
            ],
            [
                'tipar' => "/^lipsa sectiune obligatorie inainte de sfarsitul sectiunii\\s*'([^']*)'/ui",
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Secțiunea „' . $m[1] . '" se termină fără să conțină o secțiune pe care '
                            . 'schema o cere obligatoriu.',
                        'de_corectat' => 'Completează secțiunea lipsă. De regulă înseamnă că în programul de '
                            . 'contabilitate a rămas necompletată o rubrică întreagă a declarației.',
                    ];
                },
            ],
            [
                'tipar' => "/ordine incorecta a descendentilor elementului\\s*'([^']*)':\\s*elementul\\s*'([^']*)'\\s*trebuie sa preceada elementul\\s*'([^']*)'/ui",
                'explica' => function ($m) {
                    return [
                        'camp' => $m[1],
                        'explicatie' => 'În interiorul elementului „' . $m[1] . '", secțiunile nu sunt în ordinea '
                            . 'cerută: „' . $m[2] . '" trebuie să vină înaintea lui „' . $m[3] . '". '
                            . 'La declarațiile ANAF ordinea etichetelor contează, nu doar conținutul lor.',
                        'de_corectat' => 'Mută elementul ' . $m[2] . ' înaintea lui ' . $m[3] . '. Dacă mesajul '
                            . 'vorbește și de depășirea numărului maxim de repetări, verifică întâi dacă o secțiune '
                            . 'nu apare de mai multe ori decât e permis.',
                    ];
                },
            ],
            [
                'tipar' => "/^sectiunea\\s*'?([^'\\s]+)'?\\s*apare pe un nivel incorect/ui",
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Secțiunea „' . $m[1] . '" este pusă la alt nivel de imbricare decât cel '
                            . 'cerut: fie prea adânc, fie prea aproape de rădăcina fișierului.',
                        'de_corectat' => 'Mută secțiunea la nivelul corect. Regenerarea fișierului rezolvă de '
                            . 'obicei mai repede decât mutarea manuală.',
                    ];
                },
            ],
            [
                'tipar' => "/^sectiunea\\s*'?([^'\\s]+)'?\\s*este gresit pozitionata sau lipsesc sectiuni anterioare obligatorii/ui",
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Secțiunea „' . $m[1] . '" apare prea devreme sau după o secțiune care '
                            . 'lipsește. Validatorul nu poate spune care dintre cele două este cazul.',
                        'de_corectat' => 'Verifică dacă toate secțiunile dinaintea ei sunt prezente și în ordinea '
                            . 'din schemă.',
                    ];
                },
            ],
            [
                'tipar' => "/^sectiunea\\s*'?([^'\\s]+)'?\\s*nu poate sa apara de mai multe ori in acest context/ui",
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Secțiunea „' . $m[1] . '" apare de mai multe ori, deși aici este permisă '
                            . 'o singură dată.',
                        'de_corectat' => 'Șterge secțiunile în plus, păstrând-o pe cea corectă. Dacă ai nevoie de '
                            . 'mai multe înregistrări, ele se adaugă în secțiunea de detaliu, nu prin repetarea '
                            . 'acestei secțiuni.',
                    ];
                },
            ],
        ];
    }

    /** Regulile de corelare dintre campuri, specifice fiecarei declaratii. */
    protected function sabloaneReguli(): array
    {
        return [
            [
                /*
                 * Forma cea mai deasa la D394: „daca atributul X = V atunci
                 * sectiunea Y nu trebuie sa apara". Omul citeste conditia si
                 * nu-si da seama care dintre cele doua e de schimbat, fiindca
                 * regula nu spune care e adevarul si care urmarea.
                 */
                'tipar' => '/^daca\s+atributul\s+(\S+)\s*=\s*(\S+)\s+atunci\s+sectiunea\s+(.+?)\s+nu trebuie sa apara/ui',
                'explica' => function ($m) {
                    return [
                        'camp' => $m[1],
                        'cauta' => $m[1] . '="' . $m[2] . '"',
                        'explicatie' => 'Cele două nu pot sta împreună: câmpul „' . $m[1] . '" are valoarea '
                            . $m[2] . ', iar cu ea secțiunea „' . trim($m[3]) . '" nu are voie să apară în '
                            . 'declarație.',
                        'de_corectat' => 'Una dintre ele e greșită, și numai dumneavoastră știți care. Dacă '
                            . 'secțiunea „' . trim($m[3]) . '" chiar are ce căuta acolo, atunci greșit e câmpul '
                            . '„' . $m[1] . '" — schimbați-l în programul de contabilitate. Dacă valoarea lui e '
                            . 'cea corectă, scoateți secțiunea.',
                    ];
                },
            ],
            [
                /*
                 * D394: „daca cota = 0 atunci tip (C) trebuie sa fie unul din
                 * 'LS', 'AS', 'N', 'V'". Valoarea de acum sta in paranteza, iar
                 * cele ingaduite in lista de la capat — se spun amandoua.
                 */
                'tipar' => '/^daca\s+(\S+)\s*=\s*(\S+)\s+atunci\s+(\S+)\s*(?:\(([^)]*)\))?\s*trebuie sa fie unul din\s*(.+)$/ui',
                'explica' => function ($m) {
                    $acum = isset($m[4]) && $m[4] !== '' ? ' Acum are valoarea ' . $m[4] . '.' : '';

                    return [
                        'camp' => $m[3],
                        'cauta' => isset($m[4]) && $m[4] !== '' ? $m[3] . '="' . $m[4] . '"' : $m[3] . '="',
                        'explicatie' => 'Când „' . $m[1] . '" este ' . $m[2] . ', câmpul „' . $m[3] . '" nu poate '
                            . 'lua decât una dintre valorile: ' . rtrim(trim($m[5]), '.') . '.' . $acum,
                        'de_corectat' => 'Alegeți pentru „' . $m[3] . '" una dintre valorile de mai sus, în '
                            . 'programul de contabilitate. La D394, greșeala vine de obicei din felul '
                            . 'operațiunii: o factură cu cota zero trebuie trecută pe tipul care îi arată '
                            . 'pricina (scutită, neimpozabilă, taxare inversă), nu pe cel obișnuit.',
                    ];
                },
            ],
            [
                /*
                 * D394: „daca tip_partener = 2 atunci cuiP trebuie sa fie un CIF
                 * valid". Pricina obisnuita nu e codul, ci felul partenerului.
                 */
                'tipar' => '/^daca\s+(\S+)\s*=\s*(\S+)\s+atunci\s+(\S+)\s+trebuie sa fie un\s+(CIF|CUI|CNP)\s+valid/ui',
                'explica' => function ($m) {
                    return [
                        'camp' => $m[3],
                        'cauta' => $m[3] . '="',
                        'explicatie' => 'Partenerul e trecut cu „' . $m[1] . '" = ' . $m[2] . ', iar pentru felul '
                            . 'acesta de partener câmpul „' . $m[3] . '" trebuie să fie un ' . strtoupper($m[4])
                            . ' valid — ceea ce nu este.',
                        'de_corectat' => 'De cele mai multe ori nu codul e greșit, ci felul partenerului: o '
                            . 'persoană fizică trecută din greșeală ca persoană juridică. Verificați întâi „'
                            . $m[1] . '" în nomenclatorul de parteneri; dacă acolo e corect, atunci corectați '
                            . 'codul fiscal, scris doar cu cifre, fără prefix și fără spații.',
                    ];
                },
            ],
            [
                /*
                 * D112 si celelalte declaratii cu asigurati: reguli scrise cu
                 * semnele validatorului („# null" = „completat"). Ele se
                 * talmacesc, ca omul sa nu descifreze notatia.
                 */
                'tipar' => '/^(\S+)\s*#\s*null\s+daca\s*\((.+)\)\s*$/ui',
                'explica' => function ($m) {
                    /*
                     * Certificatele medicale de continuare: pricina cea mai
                     * deasa a regulilor din familia Data_CMI. Omul completeaza
                     * certificatul de acum, dar nu si datele celui dintai, iar
                     * validatorul cere legatura dintre ele.
                     */
                    $esteCertificatMedical = stripos($m[1], 'CMI') !== false;

                    $deCorectat = 'Completați „' . $m[1] . '" în programul de contabilitate, la înregistrarea '
                        . 'la care s-a oprit validarea, și generați declarația din nou. Dacă în realitate '
                        . 'câmpul n-are ce valoare să primească, atunci greșite sunt celelalte câmpuri din '
                        . 'condiție — ele fac ca acesta să fie cerut.';

                    if ($esteCertificatMedical) {
                        $deCorectat = 'La certificatele medicale de continuare, aceasta e aproape întotdeauna '
                            . 'aceeași scăpare: s-a completat certificatul de acum, dar nu și datele '
                            . 'certificatului inițial (seria, numărul și data lui). Completați-le la angajatul '
                            . 'în cauză și refaceți declarația — legătura dintre certificate e chiar ce cere '
                            . 'regula.';
                    }

                    return [
                        'camp' => $m[1],
                        'cauta' => $m[1] . '="',
                        'explicatie' => 'Câmpul „' . $m[1] . '" a rămas necompletat, deși în situația aceasta '
                            . 'el este obligatoriu. Condiția care îl cere: ' . $this->talmacesteConditia($m[2]) . '.',
                        'de_corectat' => $deCorectat,
                    ];
                },
            ],
            [
                /*
                 * A4200 (case de marcat): numarul de zile fiscale declarate nu
                 * se potriveste cu al fisierelor gasite. Pricina obisnuita nu e
                 * in declaratie, ci in stickul de memorie.
                 */
                'tipar' => '/Numarul de zile fiscale declarate\s*\((\d+)\)\s*este diferit de numarul de fisiere[^(]*\((\d+)\)/ui',
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Declarația spune ' . $m[1] . ' zile fiscale, dar s-au găsit ' . $m[2]
                            . ' fișiere de tip zi fiscală. Cele două trebuie să fie la fel.',
                        'de_corectat' => ((int) $m[2] > (int) $m[1]
                            ? 'Sunt mai multe fișiere decât zile: pe stickul de memorie au rămas rapoarte de la '
                                . 'descărcări mai vechi. Ștergeți tot ce e pe stick, descărcați din nou din casa '
                                . 'de marcat și refaceți declarația.'
                            : 'Lipsesc fișiere: descărcarea din casa de marcat nu a mers până la capăt, sau '
                                . 'câteva zile n-au fost salvate. Descărcați din nou, de pe stick gol.'),
                    ];
                },
            ],
            [
                'tipar' => '/^atributul\s+(\S+)\s*\(([^)]*)\)\s*trebuie sa fie egal cu\s*(.+?)\s*\(([^)]*)\)\s*$/ui',
                'explica' => function ($m) {
                    return [
                        'camp' => $m[1],
                        'cauta' => $m[1] . '="' . $m[2] . '"',
                        'explicatie' => 'Câmpul „' . $m[1] . '" trebuie să fie egal cu suma calculată din alte '
                            . 'câmpuri ale declarației. În fișier scrie ' . $m[2] . ', dar din celelalte câmpuri '
                            . 'rezultă ' . $m[4] . '. Cele două nu se potrivesc, deci undeva o cifră nu a fost '
                            . 'actualizată.',
                        'de_corectat' => 'Ai două variante: fie corectezi ' . $m[1] . ' la valoarea ' . $m[4]
                            . ', fie corectezi câmpurile din care se calculează suma (' . $m[3] . '). '
                            . 'Alege-o pe a doua dacă totalul din fișier este cel corect din contabilitate — '
                            . 'altfel ascunzi o greșeală de detaliu sub un total potrivit.',
                    ];
                },
            ],
            [
                'tipar' => '/\(([^)]*)\)\s*trebuie sa fie egal cu valoarea calculata\s*\(([^)]*)\)/ui',
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Valoarea din fișier (' . $m[1] . ') nu este egală cu cea pe care validatorul '
                            . 'o calculează din celelalte câmpuri (' . $m[2] . ').',
                        'de_corectat' => 'Pune valoarea ' . $m[2] . ' sau corectează câmpurile din care se calculează. '
                            . 'Dacă totalul tău e cel corect, greșeala e într-una dintre pozițiile de detaliu.',
                    ];
                },
            ],
            [
                'tipar' => '/^atributul\s+luna\s*\(([^)]*)\)\s*trebuie sa fie in intervalul\s*(\S+)\s*-\s*(\S+)/ui',
                'explica' => function ($m) {
                    return [
                        'camp' => 'luna',
                        'cauta' => 'luna="' . $m[1] . '"',
                        'explicatie' => 'Luna raportată este „' . $m[1] . '", dar pentru acest tip de declarație '
                            . 'sunt acceptate doar valorile de la ' . $m[2] . ' la ' . $m[3] . '. La declarațiile '
                            . 'trimestriale, de pildă, luna arată trimestrul, nu luna calendaristică.',
                        'de_corectat' => 'Pune în atributul luna o valoare între ' . $m[2] . ' și ' . $m[3] . '.',
                    ];
                },
            ],
            [
                'tipar' => '/^Incepand cu perioada\s+(\S+)\s+atributul\s+(\S+)\s*\(([^)]*)\)\s*trebuie sa fie egal cu\s*(\S+)/ui',
                'explica' => function ($m) {
                    return [
                        'camp' => $m[2],
                        'cauta' => $m[2] . '="' . $m[3] . '"',
                        'explicatie' => 'Începând cu perioada ' . $m[1] . ', câmpul „' . $m[2] . '" nu se mai '
                            . 'completează: legea l-a scos din declarație, iar ANAF cere valoarea ' . $m[4] . '. '
                            . 'În fișier are valoarea ' . $m[3] . '.',
                        'de_corectat' => 'Pune ' . $m[2] . '="' . $m[4] . '". Dacă programul tău încă scrie acolo '
                            . 'sume, înseamnă că nu a fost actualizat la forma curentă a declarației.',
                    ];
                },
            ],
            [
                'tipar' => '/^Nu exista sectiune\s+(\S+)\s+pentru\s*(.+)$/ui',
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Declarația conține date care ar trebui rezumate într-o secțiune „' . $m[1]
                            . '", dar acea secțiune lipsește pentru ' . rtrim($m[2], '.') . '.',
                        'de_corectat' => 'Adaugă secțiunea ' . $m[1] . ' corespunzătoare. Practic, totalurile pe '
                            . 'categorii nu au fost generate pentru toate combinațiile care apar în detalii.',
                    ];
                },
            ],
            [
                'tipar' => '/^Exista deja o sectiune\s+(\S+)\s+cu\s*(.+)$/ui',
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Secțiunea „' . $m[1] . '" apare de două ori pentru aceeași combinație ('
                            . rtrim($m[2], '.') . '). Fiecare combinație trebuie să apară o singură dată, cu '
                            . 'valorile însumate.',
                        'de_corectat' => 'Unește cele două secțiuni într-una singură, adunând valorile lor.',
                    ];
                },
            ],
            [
                'tipar' => '/^(.+?)\s*-\s*mod de calcul eronat$/ui',
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Totalul „' . trim($m[1]) . '" nu corespunde cu suma pozițiilor din care '
                            . 'ar trebui să rezulte.',
                        'de_corectat' => 'Verifică pozițiile de detaliu care compun acest total. Greșeala e de '
                            . 'obicei o poziție trecută la altă cotă de TVA sau la alt tip de operațiune.',
                    ];
                },
            ],
            [
                'tipar' => '/trebuie sa fie unic/ui',
                'explica' => function () {
                    return [
                        'explicatie' => 'Aceeași combinație de valori apare de mai multe ori, deși trebuie să fie '
                            . 'unică pe declarație — de pildă același partener trecut de două ori la aceeași cotă '
                            . 'și același tip de operațiune.',
                        'de_corectat' => 'Găsește înregistrările care se repetă și unește-le într-una singură, '
                            . 'adunând sumele.',
                    ];
                },
            ],
            [
                'tipar' => '/^(?:\(([^)]*)\)\s*)?nu trebuie (?:sa fie )?(?:completat|sa existe)/ui',
                'explica' => function ($m) {
                    $valoare = isset($m[1]) && $m[1] !== '' ? ' (are acum valoarea ' . $m[1] . ')' : '';

                    return [
                        'explicatie' => 'Acest câmp nu trebuie completat în situația declarată' . $valoare . '. '
                            . 'Alte opțiuni bifate în declarație îl fac inaplicabil.',
                        'de_corectat' => 'Șterge valoarea din acest câmp, sau corectează opțiunea care îl face '
                            . 'inaplicabil, dacă aceea e greșită.',
                    ];
                },
            ],
        ];
    }

    /** Erori care opresc validarea din start. */
    protected function sabloaneFatale(): array
    {
        return [
            [
                'tipar' => "/^Eroare fatala de parsare:\\s*'?([^']*)'?/ui",
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Fișierul nu a putut fi citit ca XML: ' . trim($m[1]) . '. Nu e vorba de o '
                            . 'valoare greșită, ci de structura fișierului — o etichetă neînchisă, un caracter '
                            . 'nepermis sau un fișier trunchiat.',
                        'de_corectat' => 'Deschide fișierul într-un editor care verifică XML-ul (Notepad++ sau '
                            . 'browserul web) — îți va arăta linia unde se rupe. Cel mai adesea, soluția este să '
                            . 'exporți din nou declarația.',
                    ];
                },
            ],
            [
                'tipar' => '/Erori la validare fisier;\s*cod eroare\s*=\s*(-?\d+)/ui',
                'explica' => function ($m) {
                    return [
                        'explicatie' => 'Fișierul nu a putut fi citit ca XML (cod ' . $m[1] . '). Nu e vorba de o '
                            . 'valoare greșită, ci de structura fișierului: o etichetă neînchisă, un caracter '
                            . 'nepermis sau un fișier trunchiat la copiere.',
                        'de_corectat' => 'Deschide fișierul într-un editor care verifică XML-ul (Notepad++, '
                            . 'browserul web) — îți va arăta linia unde se rupe. Cel mai adesea, soluția este '
                            . 'să exporți din nou declarația din programul de contabilitate.',
                    ];
                },
            ],
            [
                'tipar' => '/^tip de data eronat;\s*eroare de programare/ui',
                'explica' => function () {
                    return [
                        'explicatie' => 'Validatorul ANAF a întâmpinat o situație pe care nu o poate trata — este '
                            . 'o defecțiune a validatorului, nu a declarației tale.',
                        'de_corectat' => 'Verifică dacă ai ultima versiune de DUKIntegrator. Dacă da, semnalează '
                            . 'problema la ANAF, cu fișierul cu tot.',
                    ];
                },
            ],
            [
                'tipar' => '/^Eroare fatala disc/ui',
                'explica' => function () {
                    return [
                        'explicatie' => 'Validatorul nu a putut scrie pe disc: fie nu are drepturi în folderul de '
                            . 'lucru, fie spațiul s-a terminat.',
                        'de_corectat' => 'Eliberează spațiu pe disc și verifică drepturile de scriere. Nu are '
                            . 'legătură cu conținutul declarației.',
                    ];
                },
            ],
        ];
    }

    protected function severitate(?array $sectiune, string $fel): string
    {
        // Randul spune cel mai precis despre ce e vorba; antetul sectiunii doar
        // completeaza cand randul nu are prefix.
        if ($fel === 'atentionare') {
            return 'atentionare';
        }

        if ($sectiune === null) {
            return 'eroare';
        }

        if ($sectiune['severitate'] === 'F') {
            return 'blocant';
        }

        return $sectiune['severitate'] === 'W' ? 'atentionare' : 'eroare';
    }

    protected function rezumat(array $probleme, ?AnafDeclaratie $declaratie): string
    {
        $cate = count($probleme);

        if ($cate === 0) {
            return 'Validatorul a raportat o eroare, dar nu am putut desprinde din ea o problemă anume.';
        }

        $context = '';

        if ($declaratie) {
            $perioada = $declaratie->luna
                ? $declaratie->luna . '/' . $declaratie->anul
                : (string) $declaratie->anul;

            $context = ' la declarația ' . $declaratie->tip
                . ($declaratie->cui ? ' pentru ' . $declaratie->cui : '')
                . ($perioada !== '' ? ' (' . $perioada . ')' : '');
        }

        $explicate = count(array_filter($probleme, function ($p) {
            return $p['explicatie'] !== null;
        }));

        $rezumat = $cate === 1
            ? 'Validatorul ANAF a găsit o problemă' . $context . '.'
            : 'Validatorul ANAF a găsit ' . $cate . ' probleme' . $context . '.';

        if ($explicate < $cate) {
            $rezumat .= ' Pentru ' . ($cate - $explicate) . ' dintre ele nu am o explicație pregătită, '
                . 'așa că apar cu mesajul original.';
        }

        /*
         * Cand erorile curg cu sutele, pricina e rareori in date. La D406 sunt
         * doua greseli de pornire care dau exact asta, si amandoua se dreg
         * dintr-o apasare — merita spuse inainte ca omul sa inceapa sa
         * corecteze randuri.
         */
        if ($cate >= self::MULTE_ERORI && $declaratie && $declaratie->tip === 'D406') {
            $rezumat .= ' Fiind atât de multe, verificați întâi două lucruri care le dau pe toate deodată: '
                . 'că validatorul DUKIntegrator este actualizat, și că declarația a fost validată ca D406, nu '
                . 'ca D406T (cea de test). Dacă erorile sunt „valoarea … nu se află în listă", atunci lipsește '
                . 'maparea codurilor interne pe nomenclatorul ANAF, în programul de contabilitate.';
        }

        return $rezumat;
    }
}
