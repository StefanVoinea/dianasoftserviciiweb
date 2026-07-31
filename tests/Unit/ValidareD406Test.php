<?php

namespace Tests\Unit;

use App\Services\Anaf\Declaratii\DeclaratieException;
use App\Services\Anaf\Declaratii\DukIntegrator;
use Tests\TestCase;

/**
 * D406 (SAF-T) se validează cu perioada raportată.
 *
 * Validatorul ANAF ține câte o versiune de reguli și de nomenclatoare pentru
 * fiecare perioadă și o alege după an și lună. DUKIntegrator nu are pe unde le
 * primi din linia de comandă, așa că fără ele compară cu nomenclatoarele din
 * 2019: pe o declarație pe iunie 2026 ies peste o mie de erori inexistente,
 * de felul „valoarea '310344' nu se află în listă" — cod intrat în listă în
 * iulie 2025. De aceea SAF-T merge pe lansatorul care cheamă validatorul direct.
 */
class ValidareD406Test extends TestCase
{
    /** @var array<int, string> */
    protected $fisiere = [];

    protected function tearDown(): void
    {
        foreach ($this->fisiere as $cale) {
            @unlink($cale);
        }

        parent::tearDown();
    }

    /** Un fișier gol care ține locul unui jar, ca serviciul să-l găsească. */
    protected function jarFals(string $nume): string
    {
        $cale = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $nume;
        file_put_contents($cale, 'jar de probă');
        $this->fisiere[] = $cale;

        return $cale;
    }

    /**
     * Serviciul, cu comanda scoasă la vedere: se verifică ce s-ar rula, fără să
     * fie nevoie de java în mediul de test.
     */
    protected function serviciu(array $config): object
    {
        return new class($config) extends DukIntegrator {
            public function comanda(
                string $caleXml,
                string $tip,
                string $calePdf,
                ?int $an = null,
                ?int $luna = null,
                ?string $tipPerioada = null
            ): array {
                $proces = $tip === 'D406' && $an
                    ? $this->procesD406($caleXml, $calePdf . '_ERR.txt', $calePdf, null, $an, $luna, $tipPerioada)
                    : $this->procesDuk($caleXml, $tip, $calePdf . '_ERR.txt', $calePdf, null);

                return $proces->getCommandLine() ? explode(' ', $proces->getCommandLine()) : [];
            }
        };
    }

    public function test_d406_pleaca_la_lansatorul_cu_perioada(): void
    {
        $duk = $this->serviciu([
            'java' => 'java',
            'jar' => $this->jarFals('DUKIntegrator.jar'),
            'jar_d406' => $this->jarFals('DukD406.jar'),
            'timeout' => 180,
        ]);

        $comanda = implode(' ', $duk->comanda('/tmp/saft.xml', 'D406', '/tmp/saft.pdf', 2026, 6, 'L'));

        $this->assertStringContainsString('DukD406.jar', $comanda);
        $this->assertStringNotContainsString('DUKIntegrator.jar', $comanda);

        // Ordinea cerută de lansator: xml, erori, pdf, an, lună, tip perioadă.
        $this->assertMatchesRegularExpression('/saft\.xml.+saft\.pdf_ERR\.txt.+saft\.pdf.+2026.+6.+L/', $comanda);
    }

    /** Raportarea anuală n-are lună; validatorul cere totuși una validă. */
    public function test_raportarea_anuala_trimite_decembrie(): void
    {
        $duk = $this->serviciu([
            'java' => 'java',
            'jar' => $this->jarFals('DUKIntegrator.jar'),
            'jar_d406' => $this->jarFals('DukD406.jar'),
            'timeout' => 180,
        ]);

        $comanda = implode(' ', $duk->comanda('/tmp/saft.xml', 'D406', '/tmp/saft.pdf', 2025, null, 'A'));

        $this->assertMatchesRegularExpression('/2025.+12.+A/', $comanda);
    }

    /** Celelalte declarații merg mai departe pe DUKIntegrator, neschimbat. */
    public function test_restul_declaratiilor_raman_pe_dukintegrator(): void
    {
        $duk = $this->serviciu([
            'java' => 'java',
            'jar' => $this->jarFals('DUKIntegrator.jar'),
            'jar_d406' => $this->jarFals('DukD406.jar'),
            'timeout' => 180,
        ]);

        $comanda = implode(' ', $duk->comanda('/tmp/d112.xml', 'D112', '/tmp/d112.pdf', 2026, 6, null));

        $this->assertStringContainsString('DUKIntegrator.jar', $comanda);
        $this->assertStringContainsString('-p D112', $comanda);
        $this->assertStringNotContainsString('DukD406.jar', $comanda);
    }

    /**
     * Fără lansator instalat nu se validează SAF-T pe tăcute cu nomenclatoare
     * vechi: se spune limpede ce lipsește.
     */
    public function test_lipsa_lansatorului_este_spusa_pe_inteles(): void
    {
        $duk = new DukIntegrator([
            'java' => 'java',
            'jar' => $this->jarFals('DUKIntegrator.jar'),
            'jar_d406' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'lipsa_DukD406.jar',
            'timeout' => 180,
        ]);

        $this->expectException(DeclaratieException::class);
        $this->expectExceptionMessage('Lansatorul pentru D406');

        $duk->valideazaSiGenereazaPdf('/tmp/saft.xml', 'D406', '/tmp/saft.pdf', null, 2026, 6, 'L');
    }

    /** Nesetat, lansatorul se caută lângă DUKIntegrator.jar. */
    public function test_lansatorul_se_cauta_langa_dukintegrator(): void
    {
        $jar = $this->jarFals('DUKIntegrator.jar');
        $this->jarFals('DukD406.jar');

        $duk = $this->serviciu(['java' => 'java', 'jar' => $jar, 'timeout' => 180]);

        $comanda = implode(' ', $duk->comanda('/tmp/saft.xml', 'D406', '/tmp/saft.pdf', 2026, 6, 'L'));

        $this->assertStringContainsString(
            rtrim(sys_get_temp_dir(), '\\/') . DIRECTORY_SEPARATOR . 'DukD406.jar',
            $comanda
        );
    }
}
