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

        // Date identificare: un rand de forma "Denumire <nume>" (fara separator)
        foreach (preg_split('/\r\n|\r|\n/', $text) as $linie) {
            if (preg_match('/^\s*(?:denumire|denumire contribuabil|nume)\s*[:\-]?\s+(\S.*)$/iu', $linie, $m)) {
                return $this->curata($m[1]);
            }
        }

        // Antetul documentului de identificare vine cu coloanele amestecate de
        // extractorul PDF, dar numele urmeaza imediat dupa CUI.
        if ($cui !== null && preg_match('/' . preg_quote($cui, '/') . '\s*([^\r\n\t]+)/u', $text, $m)) {
            return $this->curata($m[1]);
        }

        return null;
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
