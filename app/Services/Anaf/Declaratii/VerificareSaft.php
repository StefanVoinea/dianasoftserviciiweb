<?php

namespace App\Services\Anaf\Declaratii;

use Symfony\Component\Process\Process;

/**
 * Verificarea de consistenta a declaratiei D406 (SAF-T), cu unealta ANAF.
 *
 * DUKIntegrator spune daca declaratia e corecta ca forma. Aplicatia aceasta —
 * tot de la ANAF, TestSaftT.jar — se uita la fond: trece prin toate liniile din
 * jurnale si compara contul, tipul taxei, codul TVA, cota, baza si taxa intre
 * ele. Asa ies la iveala facturile taxabile inregistrate fara TVA sau TVA-ul
 * care nu iese din baza si cota — greseli pe care validatorul le lasa sa treaca.
 *
 * Apelul:
 *   java -jar TestSaftT.jar <director cu fisiere xml>
 *
 * Unealta lucreaza pe directoare, nu pe fisiere, si isi scrie rezultatele
 * langa intrare: pentru fiecare xml, un „Header-...csv" cu datele declaratiei
 * si, numai daca a gasit ceva, un „Err-...csv" cu liniile gresite. Delimitatorul
 * e „#". De aceea fiecare verificare primeste aici directorul ei, gol, din care
 * nu iese nimic: altfel s-ar verifica, la fiecare apel, tot ce mai sta acolo.
 */
class VerificareSaft
{
    /** Numele sub care intra declaratia in directorul de lucru.
     *
     * Fix, nu cel original: unealta ia in seama doar fisierele al caror nume se
     * termina in „.xml" si sare peste celelalte fara sa spuna nimic. Cu un nume
     * ales de noi, nu are cum sa treaca o declaratie neverificata din pricina
     * numelui cu care a venit.
     */
    protected const NUME_LUCRU = 'saft.xml';

    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array{
     *     stare: string,
     *     numar: int,
     *     antet: array,
     *     erori: array,
     *     pe_teste: array,
     *     trunchiat: bool
     * }
     *
     * @throws DeclaratieException
     */
    public function verifica(string $caleXml): array
    {
        $jar = $this->jar();

        if (!is_file($jar)) {
            throw new DeclaratieException(
                'Aplicația ANAF de verificare a SAF-T nu a fost găsită la ' . $jar . '.'
            );
        }

        if (!is_file($caleXml)) {
            throw new DeclaratieException('Fișierul declarației nu a fost găsit: ' . $caleXml);
        }

        $dosar = $this->dosarDeLucru();

        try {
            if (!@copy($caleXml, $dosar . DIRECTORY_SEPARATOR . self::NUME_LUCRU)) {
                throw new DeclaratieException('Declarația nu a putut fi copiată în dosarul de lucru ' . $dosar . '.');
            }

            $iesire = $this->ruleaza($jar, $dosar);

            $antet = $this->citesteCsv($this->fisierul($dosar, 'Header-'));

            /*
             * Un antet lipsa inseamna ca unealta n-a ajuns la capat. Cel mai des
             * pentru ca o tranzactie n-are luna sau an: acolo ea cade cu
             * NumberFormatException, fara sa spuna la care. Omului i se spune ce
             * are de cautat, nu i se arata urma din java.
             */
            if (!$antet) {
                throw new DeclaratieException($this->deCeNAMers($iesire));
            }

            $linii = $this->citesteCsv($this->fisierul($dosar, 'Err-'), true);
        } finally {
            $this->stergeDosarul($dosar);
        }

        return $this->rezultat($antet[0] ?? [], $linii);
    }

    /** Rezultatul, asa cum ajunge in tabel si in modal. */
    protected function rezultat(array $antet, array $linii): array
    {
        $pastrate = $this->liniiPastrate();
        $numar = count($linii);

        $peTeste = [];

        foreach ($linii as $linie) {
            $cod = $linie['stare'] ?? 'NOK';
            $peTeste[$cod] = ($peTeste[$cod] ?? 0) + 1;
        }

        arsort($peTeste);

        return [
            'stare' => $numar > 0 ? 'erori' : 'curata',
            'numar' => $numar,
            'antet' => $antet,
            'erori' => array_slice($linii, 0, $pastrate),
            'pe_teste' => $peTeste,
            'trunchiat' => $numar > $pastrate,
        ];
    }

    /**
     * Fisierul CSV cerut, dintre cele scrise de unealta.
     *
     * Numele lor poarta si clipa verificarii („Err-saft-20260827_090013.csv"),
     * asa ca se cauta dupa inceput. In dosar sta o singura declaratie, deci nu
     * are cum sa fie decat unul din fiecare fel.
     */
    protected function fisierul(string $dosar, string $inceput): ?string
    {
        $gasite = glob($dosar . DIRECTORY_SEPARATOR . $inceput . '*.csv');

        return $gasite ? $gasite[0] : null;
    }

    /**
     * CSV-ul ANAF, cu „#" intre coloane, adus ca randuri cu chei.
     *
     * Cheile vin din primul rand al fisierului, scrise cu litere mici: daca
     * ANAF mai adauga o coloana, ea intra de la sine, fara sa strice ce era.
     */
    protected function citesteCsv(?string $cale, bool $doarNok = false): array
    {
        if (!$cale || !is_file($cale)) {
            return [];
        }

        $randuri = preg_split('/\r\n|\r|\n/', (string) file_get_contents($cale));
        $capul = null;
        $rezultat = [];

        foreach ($randuri as $rand) {
            if (trim($rand) === '') {
                continue;
            }

            $bucati = explode('#', $rand);

            if ($capul === null) {
                $capul = array_map(function ($coloana) {
                    return mb_strtolower(trim($coloana));
                }, $bucati);

                continue;
            }

            $linie = [];

            foreach ($capul as $pozitie => $cheie) {
                $linie[$cheie] = isset($bucati[$pozitie]) ? trim($bucati[$pozitie]) : '';
            }

            // Randurile „OK" nu ajung in fisierul de erori, dar paza buna...
            if ($doarNok && strpos($linie['stare'] ?? '', 'NOK') === false) {
                continue;
            }

            $rezultat[] = $linie;
        }

        return $rezultat;
    }

    /** Unealta ANAF, pusa pe treaba peste dosarul de lucru. */
    protected function ruleaza(string $jar, string $dosar): string
    {
        $proces = new Process([$this->java(), '-jar', $jar, $dosar]);
        $proces->setTimeout($this->timeout());
        $proces->run();

        /*
         * Codul de iesire nu spune nimic: unealta iese cu 0 si cand a gasit
         * greseli, si cand n-a gasit. Ce a facut se vede din fisierele scrise,
         * iar ce a scris pe ecran ramane pentru cazul in care n-a scris niciunul.
         */
        return trim($proces->getErrorOutput() . ' ' . $proces->getOutput());
    }

    /** Ce s-a intamplat, cand unealta n-a scris nici macar antetul. */
    protected function deCeNAMers(string $iesire): string
    {
        if (strpos($iesire, 'NumberFormatException') !== false) {
            return 'Verificarea de consistență s-a oprit: în declarație este cel puțin o tranzacție'
                . ' fără lună sau fără an (elementele Period și PeriodYear). Aplicația ANAF nu poate'
                . ' trece peste ele. Cere programului de contabilitate un export cu perioada completă.';
        }

        if ($iesire === '') {
            $iesire = 'aplicația ANAF nu a scris niciun rezultat';
        }

        return 'Verificarea de consistență nu a putut fi făcută: ' . $iesire;
    }

    /**
     * Un dosar numai al acestei verificari.
     *
     * Unealta ia tot ce gaseste in dosarul primit si isi lasa acolo fisierele
     * CSV. Cu un dosar comun, a doua verificare ar prelucra si declaratia de la
     * prima, iar rezultatele s-ar amesteca.
     */
    protected function dosarDeLucru(): string
    {
        $radacina = !empty($this->config['dosar_lucru'])
            ? $this->config['dosar_lucru']
            : storage_path('app' . DIRECTORY_SEPARATOR . 'declaratii' . DIRECTORY_SEPARATOR . 'verificare');

        $dosar = $radacina . DIRECTORY_SEPARATOR . uniqid('saft_', true);

        if (!is_dir($dosar) && !@mkdir($dosar, 0775, true) && !is_dir($dosar)) {
            throw new DeclaratieException('Dosarul de lucru pentru verificare nu a putut fi creat: ' . $dosar);
        }

        return $dosar;
    }

    protected function stergeDosarul(string $dosar): void
    {
        foreach ((array) glob($dosar . DIRECTORY_SEPARATOR . '*') as $fisier) {
            @unlink($fisier);
        }

        @rmdir($dosar);
    }

    /** Unealta sta langa DUKIntegrator, daca nu s-a spus altfel. */
    protected function jar(): string
    {
        if (!empty($this->config['jar'])) {
            return $this->config['jar'];
        }

        return dirname((string) config('anaf.declaratii.duk.jar')) . DIRECTORY_SEPARATOR . 'TestSaftT.jar';
    }

    protected function java(): string
    {
        return $this->config['java'] ?? config('anaf.declaratii.duk.java', 'java');
    }

    protected function timeout(): int
    {
        return (int) ($this->config['timeout'] ?? 600);
    }

    /**
     * Cate linii gresite se tin minte.
     *
     * Un SAF-T scapat de sub mana poate avea sute de mii de linii marcate.
     * Trecute toate prin baza de date si prin pagina, ar bloca si una, si alta;
     * numarul intreg ramane scris, iar in tabel intra atatea cate ajung ca omul
     * sa priceapa ce are de indreptat.
     */
    protected function liniiPastrate(): int
    {
        return (int) ($this->config['linii_pastrate'] ?? 1000);
    }
}
