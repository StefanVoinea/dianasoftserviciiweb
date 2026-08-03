<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\CertificateController;
use Tests\TestCase;

/**
 * Dosarele de ales se arata alfabetic.
 *
 * Programul local le da asa cum le tine Windows-ul, adica pe coduri de
 * caractere: acolo „Zeta" vine inaintea lui „arhiva", iar „Ședințe" ajunge
 * tocmai la urma. Omul care alege unde sa stea arhiva nu are de unde sa
 * ghiceasca ordinea asta.
 */
class OrdineFoldereTest extends TestCase
{
    public function test_dosarele_se_aseaza_alfabetic(): void
    {
        $asezate = $this->asaza(['Zeta', 'arhiva', 'Balanțe', 'ședințe', 'Acte']);

        $this->assertSame(['Acte', 'arhiva', 'Balanțe', 'ședințe', 'Zeta'], $asezate);
    }

    /** Diacriticele stau la locul literei, nu dupa tot alfabetul. */
    public function test_diacriticele_stau_la_locul_lor(): void
    {
        $asezate = $this->asaza(['Situații', 'Salarii', 'Ștate de plată', 'Sedii']);

        $this->assertSame(['Salarii', 'Sedii', 'Situații', 'Ștate de plată'], $asezate);
    }

    /** Numerele din denumire se citesc ca numere: „2" inaintea lui „10". */
    public function test_numerele_se_citesc_ca_numere(): void
    {
        $asezate = $this->asaza(['Luna 10', 'Luna 2', 'Luna 1']);

        $this->assertSame(['Luna 1', 'Luna 2', 'Luna 10'], $asezate);
    }

    /** Un raspuns fara dosare ramane cum e, fara sa se strice. */
    public function test_raspunsul_fara_dosare_ramane_intreg(): void
    {
        $metoda = new \ReflectionMethod(CertificateController::class, 'inOrdine');
        $metoda->setAccessible(true);

        $payload = ['cale' => 'D:\\', 'parinte' => '', 'foldere' => []];

        $this->assertSame($payload, $metoda->invoke(new CertificateController(), $payload));
        $this->assertSame(['cale' => 'D:\\'], $metoda->invoke(new CertificateController(), ['cale' => 'D:\\']));
    }

    /** @return array<int, string> denumirile, in ordinea in care ies */
    protected function asaza(array $nume): array
    {
        $foldere = array_map(function (string $unul) {
            return ['nume' => $unul, 'cale' => 'D:\\' . $unul];
        }, $nume);

        $metoda = new \ReflectionMethod(CertificateController::class, 'inOrdine');
        $metoda->setAccessible(true);

        $rezultat = $metoda->invoke(new CertificateController(), ['foldere' => $foldere]);

        return array_column($rezultat['foldere'], 'nume');
    }
}
