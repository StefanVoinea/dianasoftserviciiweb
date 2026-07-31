<?php

namespace App\Services\Anaf\Declaratii;

use Symfony\Component\Process\Process;

/**
 * Validarea declaratiei si generarea PDF-ului oficial se fac cu DUKIntegrator,
 * utilitarul ANAF (java). Comanda replica apelul aplicatiei desktop:
 *   java -jar DUKIntegrator.jar -p <tip> <intrare.xml> <erori.txt> 0 <zip> <iesire.pdf>
 *
 * Exceptia e D406 (SAF-T): vezi valideazaD406().
 */
class DukIntegrator
{
    /** Declaratia a carei validare cere perioada raportata. */
    protected const CU_PERIOADA = 'D406';

    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param ?int    $an          anul raportat (numai D406)
     * @param ?int    $luna        luna raportata (numai D406)
     * @param ?string $tipPerioada L, T sau A (numai D406)
     *
     * @return array{valid: bool, erori: ?string, cale_pdf: ?string}
     */
    public function valideazaSiGenereazaPdf(
        string $caleXml,
        string $tip,
        string $calePdf,
        ?string $caleZip = null,
        ?int $an = null,
        ?int $luna = null,
        ?string $tipPerioada = null
    ): array {
        if (!is_file($this->config['jar'])) {
            throw new DeclaratieException('DUKIntegrator.jar nu a fost găsit la ' . $this->config['jar']);
        }

        $caleErori = $calePdf . '_ERR.txt';
        $this->sterge($caleErori);
        $this->sterge($calePdf);

        $proces = $tip === self::CU_PERIOADA && $an
            ? $this->procesD406($caleXml, $caleErori, $calePdf, $caleZip, $an, $luna, $tipPerioada)
            : $this->procesDuk($caleXml, $tip, $caleErori, $calePdf, $caleZip);

        $proces->setTimeout($this->config['timeout']);
        $proces->run();

        $erori = is_file($caleErori) ? trim(file_get_contents($caleErori)) : '';
        $this->sterge($caleErori);

        // DUKIntegrator scrie exact "ok" in fisierul de erori cand declaratia e valida.
        $valid = strtolower($erori) === 'ok';

        if (!$valid) {
            $this->sterge($calePdf);

            return [
                'valid' => false,
                'erori' => $erori !== '' ? $erori : trim($proces->getErrorOutput() . ' ' . $proces->getOutput()),
                'cale_pdf' => null,
            ];
        }

        if (!is_file($calePdf)) {
            /*
             * Motivul îl spune programul ANAF. Se ia de pe ieșirea de erori:
             * pe cea obișnuită validatorul își varsă raportul de secțiuni, care
             * n-are ce căuta în dreptul unei declarații din tabel.
             */
            $motiv = trim($proces->getErrorOutput()) ?: trim($proces->getOutput());

            return [
                'valid' => false,
                'erori' => 'Declarația a trecut de validare, dar PDF-ul nu a fost generat.'
                    . ($motiv !== '' ? ' ' . $motiv : ''),
                'cale_pdf' => null,
            ];
        }

        return ['valid' => true, 'erori' => null, 'cale_pdf' => $calePdf];
    }

    protected function procesDuk(string $caleXml, string $tip, string $caleErori, string $calePdf, ?string $caleZip): Process
    {
        return new Process([
            $this->config['java'],
            '-jar', $this->config['jar'],
            '-p', $tip,
            $caleXml,
            $caleErori,
            '0',
            $caleZip ?: '',
            $calePdf,
        ]);
    }

    /**
     * D406 (SAF-T) se valideaza cu perioada raportata.
     *
     * Validatorul ANAF are cate o versiune de reguli si de nomenclatoare pentru
     * fiecare perioada, iar DUKIntegrator nu are pe unde primi luna si anul din
     * linia de comanda: fara ele se compara cu nomenclatoarele din 2019 si ies
     * peste o mie de erori inventate („valoarea '310344' nu se afla in lista"
     * pentru un cod intrat in lista in iulie 2025). De aceea validatorul ANAF se
     * cheama direct, printr-un lansator subtire pus langa jar-urile lui.
     */
    protected function procesD406(
        string $caleXml,
        string $caleErori,
        string $calePdf,
        ?string $caleZip,
        int $an,
        ?int $luna,
        ?string $tipPerioada
    ): Process {
        $lansator = $this->lansatorD406();

        if (!is_file($lansator)) {
            throw new DeclaratieException(
                'Lansatorul pentru D406 nu a fost găsit la ' . $lansator . '.'
                . ' Fără el, SAF-T se validează cu nomenclatoare vechi și apar erori care nu există.'
            );
        }

        return new Process(array_filter([
            $this->config['java'],
            '-jar', $lansator,
            $caleXml,
            $caleErori,
            $calePdf,
            (string) $an,
            (string) ($luna ?: 12),
            $tipPerioada ?: 'L',
            $caleZip ?: null,
        ]));
    }

    /** Lansatorul stă lângă DUKIntegrator.jar, dacă nu s-a spus altfel. */
    protected function lansatorD406(): string
    {
        if (!empty($this->config['jar_d406'])) {
            return $this->config['jar_d406'];
        }

        return dirname($this->config['jar']) . DIRECTORY_SEPARATOR . 'DukD406.jar';
    }

    private function sterge(string $cale): void
    {
        if (is_file($cale)) {
            unlink($cale);
        }
    }
}
