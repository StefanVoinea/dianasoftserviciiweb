<?php

namespace App\Services\Anaf\Spv;

use App\Models\VectorSpv;
use Carbon\Carbon;

/**
 * Citeste documentele SPV cu continut structurat: vectorul fiscal (obligatiile
 * declarative), situatia sintetica (obligatii de plata restante) si datele de
 * identificare ale contribuabilului.
 *
 * Lucreaza pe textul documentului, nu pe fisierul lui: textul vine de la
 * programul local, care citeste documentul acolo unde sta — pe calculatorul
 * clientului — fara sa-l mai trimita incoace.
 */
class VectorFiscalParser
{
    /**
     * Citeste textul vectorului fiscal si sincronizeaza tabela vector_spv.
     *
     * @return array{modificat: bool, randuri: array}
     */
    public function citesteVectorFiscal(string $text, string $cui): array
    {
        $linii = $this->linii($text);

        $dataVector = null;
        $randuri = [];
        $inTabel = false;

        foreach ($linii as $linie) {
            if ($dataVector === null && !$inTabel && preg_match('#(\d{2}[./]\d{2}[./]\d{4})#', $linie, $m)) {
                $dataVector = $this->data($m[1]);
            }

            if (stripos($linie, 'DATA_SFARSIT') !== false) {
                $inTabel = true;
                continue;
            }

            if (!$inTabel) {
                continue;
            }

            $rand = $this->parseazaRand($linie);

            if ($rand !== null) {
                $randuri[] = $rand;
            }
        }

        // La prima preluare totul e "nou" — nu inseamna ca vectorul s-a modificat.
        $primaPreluare = VectorSpv::where('cui', $cui)->count() === 0;
        $modificat = false;

        foreach ($randuri as $rand) {
            $existent = VectorSpv::where('cui', $cui)
                ->where('cod_imp', $rand['cod_imp'])
                ->where('data_inceput', $rand['data_inceput'])
                ->where('data_sfarsit', $rand['data_sfarsit'])
                ->where('perfisc', $rand['perfisc'])
                ->first();

            if (!$existent) {
                $modificat = true;
                VectorSpv::create(array_merge($rand, [
                    'cui' => $cui,
                    'data_vector' => $dataVector,
                ]));
            }
        }

        // Obligatiile care nu mai apar in vectorul curent sunt eliminate.
        if ($randuri !== []) {
            $coduriCurente = array_column($randuri, 'cod_imp');
            $sterse = VectorSpv::where('cui', $cui)->whereNotIn('cod_imp', $coduriCurente)->delete();

            if ($sterse > 0) {
                $modificat = true;
            }
        }

        return [
            'modificat' => $modificat && !$primaPreluare,
            'prima_preluare' => $primaPreluare,
            'randuri' => $randuri,
        ];
    }

    /**
     * Acelasi rand arata altfel dupa cine a citit PDF-ul, pentru ca extractoarele
     * insira bucatile de text in ordini diferite:
     *
     *   citit aici, pe server:  "100\t31/12/2010 TrimestrialaProfit\t01/01/2010"
     *   citit la client:        "100 Profit 01/01/2010 31/12/2010 Trimestriala"
     *
     * Se incearca amandoua. Nu se pot incurca intre ele: primul se termina cu o
     * data, al doilea cu periodicitatea. Datele sunt in format zz/ll/aaaa, iar
     * "/ /" tine locul datei de sfarsit la obligatiile inca in vigoare.
     */
    protected function parseazaRand(string $linie): ?array
    {
        $linie = trim(preg_replace('/[\t ]+/u', ' ', $linie));
        $perioade = 'Lunar[ăa]|Trimestrial[ăa]|Semestrial[ăa]|Anual[ăa]';

        // <cod> <sfarsit> <perfisc><semnificatie> <inceput>
        $amestecat = '#^(\d{2,4})\s+(?:(\d{2}/\d{2}/\d{4})|/\s*/)\s*'
            . '(' . $perioade . ')\s*(.*?)\s+(\d{2}/\d{2}/\d{4})$#ui';

        if (preg_match($amestecat, $linie, $m)) {
            return [
                'cod_imp' => $m[1],
                'semnificatie' => trim($m[4]) ?: null,
                'perfisc' => $this->periodicitate($m[3]),
                'data_inceput' => $this->data($m[5]),
                'data_sfarsit' => $m[2] !== '' ? $this->data($m[2]) : null,
            ];
        }

        // <cod> <semnificatie> <inceput> <sfarsit> <perfisc>
        $inOrdine = '#^(\d{2,4})\s+(.*?)\s+(\d{2}/\d{2}/\d{4})\s+'
            . '(?:(\d{2}/\d{2}/\d{4})|/\s*/)\s*(' . $perioade . ')$#ui';

        if (preg_match($inOrdine, $linie, $m)) {
            return [
                'cod_imp' => $m[1],
                'semnificatie' => trim($m[2]) ?: null,
                'perfisc' => $this->periodicitate($m[5]),
                'data_inceput' => $this->data($m[3]),
                'data_sfarsit' => $m[4] !== '' ? $this->data($m[4]) : null,
            ];
        }

        return null;
    }

    /** "Trimestriala" -> "Trimestrial", ca in vectorul declarat manual. */
    protected function periodicitate(string $valoare): string
    {
        return ucfirst(strtolower(rtrim(trim($valoare), 'ăa')));
    }

    /** Situatia sintetica: exista obligatii de plata restante? */
    public function areObligatiiRestante(string $text): bool
    {
        return stripos($text, 'NU SUNT OBLIGATII DE PLATA RESTANTE') === false
            && stripos($text, 'OBLIGATII DE PLATA RESTANTE') !== false;
    }

    /**
     * Denumirea contribuabilului. Vectorul fiscal o are in antet
     * ("DATE PRIVIND SOCIETATEA <nume> CE ARE CUI-ul <cif>"), iar documentul de
     * date identificare o listeaza pe un rand de forma "Denumire: <nume>".
     */
    public function citesteDenumire(string $text, ?string $cui = null): ?string
    {
        /*
         * Vectorul fiscal: "DATE PRIVIND SOCIETATEA <nume> CE ARE CUI-ul <cif>".
         *
         * Antetul se cauta in textul intins pe un singur rand: cand documentul e
         * citit la client, denumirea si "CE ARE CUI-ul" cad pe randuri diferite,
         * iar cautarea pe randul original n-ar mai gasi nimic.
         */
        $intins = preg_replace('/\s+/u', ' ', $text);

        if (preg_match('/DATE PRIVIND SOCIETATEA\s+(.+?)\s+CE ARE CUI[-\s]*ul/iu', $intins, $m)) {
            return $this->curata($m[1]);
        }

        $linii = $this->linii($text);
        /*
         * Antetul vectorului fiscal, cand extractorul il rupe in bucati.
         *
         * Desenat, el arata „<nume> DATE PRIVIND SOCIETATEA CE ARE CUI-ul
         * <cif>". Scos din PDF, cade asa:
         *
         *     CRISTI & DANA INSTAL      <- numele, fara forma juridica
         *     DATE PRIVIND SOCIETATEA
         *      CE ARE CUI-ul
         *     22489650
         *     SRL                       <- coada numelui
         *
         * Cautarea de mai sus nu gaseste nimic (intre „SOCIETATEA" si „CE ARE
         * CUI-ul" nu mai e nimic), iar cea de dupa CUI lua doar „SRL" — si asa
         * s-a inregistrat o firma cu numele „SRL". Se strang deci amandoua
         * bucatile: cea de deasupra si coada de sub cod.
         */
        foreach ($linii as $i => $linie) {
            if (stripos($linie, 'DATE PRIVIND SOCIETATEA') === false || $i === 0) {
                continue;
            }

            $inceputul = $this->curata($linii[$i - 1]);

            if ($inceputul === null || !$this->pareDenumire($inceputul)) {
                continue;
            }

            $coada = $this->coadaDupaCui($linii, $i, $cui);

            return $coada === null ? $inceputul : $inceputul . ' ' . $coada;
        }


        // Date identificare: un rand de forma "Denumire <nume>" (fara separator)
        foreach ($linii as $linie) {
            /*
             * „nume" nu mai e printre etichete: e un cuvant prea scurt si prea
             * obisnuit. O firma numita „NUME NOU SRL" isi pierdea primul cuvant,
             * fiindca randul ei parea o eticheta urmata de valoare.
             */
            if (preg_match('/^\s*denumire(?: contribuabil)?\s*[:\-]?\s+(\S.*)$/iu', $linie, $m)) {
                return $this->curata($m[1]);
            }
        }

        /*
         * Documentul de date identificare e un tabel pe doua coloane, iar
         * extractorul de PDF scoate valoarea INAINTEA etichetei:
         *
         *     CRISTI & DANA INSTAL SRL
         *     Denumire
         *     JUD. ARAD, MUN. ARAD, STR. ...
         *     Domiciliul Fiscal
         *
         * Se cauta deci eticheta singura pe rand si se ia randul dinaintea ei.
         */
        foreach ($linii as $i => $linie) {
            if (!preg_match('/^\s*(?:denumire|denumire contribuabil)\s*$/iu', $linie)) {
                continue;
            }

            for ($j = $i - 1; $j >= 0; $j--) {
                $candidat = $this->curata($linii[$j]);

                if ($candidat !== null && $this->pareDenumire($candidat)) {
                    return $candidat;
                }
            }
        }

        /*
         * Ultima incercare: numele urmeaza dupa CUI in antet. E cea mai slaba —
         * pe unele documente dupa CUI vine doar forma juridica, rupta de restul
         * numelui ("CE ARE CUI-ul 22489650" si, pe randul urmator, "SRL"), si
         * asa s-a si inregistrat o firma cu denumirea "SRL". De aceea ce se
         * gaseste aici trece prin aceeasi cantarire.
         */
        if ($cui !== null && preg_match('/' . preg_quote($cui, '/') . '\s*([^
	]+)/u', $text, $m)) {
            $candidat = $this->curata($m[1]);

            if ($candidat !== null && $this->pareDenumire($candidat)) {
                return $candidat;
            }
        }

        return null;
    }

    /**
     * Coada numelui, cea de sub codul fiscal.
     *
     * E de obicei forma juridica — „SRL", „SA" —, adica tocmai bucata pe care
     * extractorul o desparte de restul. Se ia numai daca chiar seamana a asa
     * ceva: altfel s-ar lipi de nume primul rand din tabelul de mai jos.
     */
    protected function coadaDupaCui(array $linii, int $dela, ?string $cui): ?string
    {
        $forme = array('SRL', 'SA', 'PFA', 'SNC', 'SCS', 'SRL-D', 'II', 'IF', 'ONG', 'PFI');

        // Se cauta in cele cateva randuri de dupa antet, nu in tot documentul.
        for ($i = $dela; $i < min(count($linii), $dela + 6); $i++) {
            $rand = trim($linii[$i]);

            // Randul cu codul fiscal, sau codul singur pe randul lui.
            if ($cui === null || strpos($rand, $cui) === false) {
                continue;
            }

            for ($j = $i + 1; $j < min(count($linii), $i + 3); $j++) {
                $candidat = trim(str_replace('.', '', mb_strtoupper(trim($linii[$j]))));

                if (in_array($candidat, $forme, true)) {
                    return trim($linii[$j]);
                }
            }

            return null;
        }

        return null;
    }

    /**
     * Seamana a denumire de firma, sau e doar o bucata ramasa pe drum?
     *
     * Forma juridica singura - "SRL", "SA", "PFA" - nu e denumire, oricat ar
     * arata a text: e coada numelui, taiata de extractorul de PDF. Inregistrata
     * ca atare, firma apare in liste cu numele "SRL", iar cine o cauta n-o mai
     * gaseste.
     */
    protected function pareDenumire(string $valoare): bool
    {
        $forme = array('SRL', 'SA', 'PFA', 'SNC', 'SCS', 'SRL-D', 'II', 'IF', 'ONG', 'PFI');

        $fara = trim(str_replace('.', '', mb_strtoupper($valoare)));

        if (in_array($fara, $forme, true)) {
            return false;
        }

        // Nici datele, nici numerele singure nu sunt nume de firma.
        return mb_strlen($fara) >= 3 && preg_match('/\p{L}{3}/u', $valoare) === 1;
    }

    /** Textul documentului, strans la un singur spatiu, pentru pastrare ca referinta. */
    public function textDocument(string $text): string
    {
        return trim(preg_replace('/[ \t]+/u', ' ', $text));
    }

    protected function curata(string $valoare): ?string
    {
        $valoare = trim(preg_replace('/\s+/u', ' ', $valoare));

        return $valoare !== '' ? $valoare : null;
    }

    protected function linii(string $text): array
    {
        return preg_split('/\r\n|\r|\n/', $text);
    }

    protected function data(string $valoare): ?string
    {
        foreach (['!d/m/Y', '!d.m.Y', '!Y-m-d'] as $format) {
            try {
                return Carbon::createFromFormat($format, trim($valoare))->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }
}
