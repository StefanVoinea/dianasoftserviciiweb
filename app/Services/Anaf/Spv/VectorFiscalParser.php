<?php

namespace App\Services\Anaf\Spv;

use App\Models\VectorSpv;
use Carbon\Carbon;
use Smalot\PdfParser\Parser;

/**
 * Citeste documentele SPV cu continut structurat: vectorul fiscal (obligatiile
 * declarative), situatia sintetica (obligatii de plata restante) si datele de
 * identificare ale contribuabilului.
 */
class VectorFiscalParser
{
    /**
     * Parseaza PDF-ul de vector fiscal si sincronizeaza tabela vector_spv.
     *
     * @return array{modificat: bool, randuri: array}
     */
    public function citesteVectorFiscal(string $calePdf, string $cui): array
    {
        $linii = $this->linii($calePdf);
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
     * Randurile din PDF-ul ANAF au forma (coloanele separate prin tab-uri):
     *   <cod_imp> <data_sfarsit sau "/ /"> <perfisc><semnificatie> <data_inceput>
     * ex: "100\t31/12/2010 TrimestrialaProfit\t01/01/2010"
     *     "480  /  / LunaraContribuţie asiguratorie\t01/01/2018"
     * Periodicitatea e lipita de semnificatie, iar datele sunt in format zz/ll/aaaa.
     */
    protected function parseazaRand(string $linie): ?array
    {
        $linie = trim(preg_replace('/[\t ]+/u', ' ', $linie));

        $tipar = '#^(\d{2,4})\s+(?:(\d{2}/\d{2}/\d{4})|/\s*/)\s*'
            . '(Lunar[ăa]|Trimestrial[ăa]|Semestrial[ăa]|Anual[ăa])\s*(.*?)\s+(\d{2}/\d{2}/\d{4})$#ui';

        if (!preg_match($tipar, $linie, $m)) {
            return null;
        }

        return [
            'cod_imp' => $m[1],
            'semnificatie' => trim($m[4]) ?: null,
            'perfisc' => $this->periodicitate($m[3]),
            'data_inceput' => $this->data($m[5]),
            'data_sfarsit' => $m[2] !== '' ? $this->data($m[2]) : null,
        ];
    }

    /** "Trimestriala" -> "Trimestrial", ca in vectorul declarat manual. */
    protected function periodicitate(string $valoare): string
    {
        return ucfirst(strtolower(rtrim(trim($valoare), 'ăa')));
    }

    /** Situatia sintetica: exista obligatii de plata restante? */
    public function areObligatiiRestante(string $calePdf): bool
    {
        $text = $this->text($calePdf);

        return stripos($text, 'NU SUNT OBLIGATII DE PLATA RESTANTE') === false
            && stripos($text, 'OBLIGATII DE PLATA RESTANTE') !== false;
    }

    /**
     * Denumirea contribuabilului. Vectorul fiscal o are in antet
     * ("DATE PRIVIND SOCIETATEA <nume> CE ARE CUI-ul <cif>"), iar documentul de
     * date identificare o listeaza pe un rand de forma "Denumire: <nume>".
     */
    public function citesteDenumire(string $calePdf, ?string $cui = null): ?string
    {
        $text = $this->text($calePdf);

        // Vectorul fiscal: "DATE PRIVIND SOCIETATEA <nume> CE ARE CUI-ul <cif>"
        if (preg_match('/DATE PRIVIND SOCIETATEA\s+(.+?)\s+CE ARE CUI[-\s]*ul/iu', $text, $m)) {
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

    /** Textul brut al documentului, pentru pastrare ca referinta. */
    public function textDocument(string $calePdf): string
    {
        return trim(preg_replace('/[ \t]+/u', ' ', $this->text($calePdf)));
    }

    protected function curata(string $valoare): ?string
    {
        $valoare = trim(preg_replace('/\s+/u', ' ', $valoare));

        return $valoare !== '' ? $valoare : null;
    }

    protected function text(string $calePdf): string
    {
        try {
            return (new Parser())->parseFile($calePdf)->getText();
        } catch (\Exception $e) {
            throw new SpvException('Nu s-a putut citi PDF-ul: ' . $e->getMessage());
        }
    }

    protected function linii(string $calePdf): array
    {
        return preg_split('/\r\n|\r|\n/', $this->text($calePdf));
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
