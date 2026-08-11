<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Ce PDF se semneaza: cel primit, sau cel scos de DUKIntegrator.
 *
 * Programele de contabilitate dau un „PDF inteligent" — formular XFA, a carui
 * pagina e doar un paravan cu „Please wait...", iar declaratia se deseneaza abia
 * in Adobe Reader, din datele XFA. Semnat, un asemenea document nu poate purta
 * caseta de semnatura (ea cade pe paravan, si Adobe n-o arata niciodata) si nu
 * se poate tipari decat prin Adobe — altfel iese pe hartie chiar paravanul.
 *
 * PDF-ul scos de DUKIntegrator din acelasi XML e insa o foaie obisnuita, cu
 * XML-ul atasat si de zece ori mai mica: se vede in orice program, se tipareste
 * oriunde, si poarta caseta. E tot documentul oficial — chiar cel pe care il
 * face aplicatia ANAF la „Validare + creare PDF".
 *
 * Il si faceam, la fiecare validare, si il aruncam.
 *
 * Ce ramane neatins e documentul care vine deja semnat: acolo semnatura e pe
 * fisierul acela anume, iar un altul, oricat de bun, n-ar mai purta-o.
 */
class PdfulCareSeSemneazaTest extends TestCase
{
    /** Documentul nesemnat se inlocuieste cu cel scos de validator. */
    public function test_documentul_nesemnat_se_inlocuieste_cu_cel_al_validatorului(): void
    {
        $sursa = file_get_contents(app_path('Http/Controllers/Api/DeclaratiiController.php'));

        $this->assertStringContainsString('$pastreazaPrimitul', $sursa);
        $this->assertStringContainsString(
            "'cale_pdf' => preg_replace('/\\.pdf\$/i', '', \$calePdf) . '_duk.pdf'",
            $sursa,
            'declarația nesemnată trebuie să rămână cu PDF-ul scos de DUKIntegrator'
        );
    }

    /** Documentul venit semnat ramane neatins: semnatura e pe el. */
    public function test_documentul_semnat_ramane_cel_primit(): void
    {
        $sursa = file_get_contents(app_path('Http/Controllers/Api/DeclaratiiController.php'));

        $this->assertStringContainsString(
            "\$pastreazaPrimitul = \$info['semnat']",
            $sursa,
            'un document venit semnat nu se schimbă cu altul'
        );
    }

    /** Dosarul urmarit lucreaza la fel: acolo intra cele mai multe declaratii. */
    public function test_dosarul_urmarit_face_la_fel(): void
    {
        $sursa = file_get_contents(app_path('Services/Anaf/Declaratii/MonitorizareFolder.php'));

        $this->assertStringContainsString("'cale_pdf' => \$trunchi . '_duk.pdf'", $sursa);
        $this->assertStringContainsString("!\$info['semnat'] && is_file(\$caleGenerat)", $sursa);
    }

    /**
     * Proba adevarata, cand validatorul ANAF se afla pe acest calculator:
     * PDF-ul scos de el nu e formular XFA si poarta XML-ul cu el.
     */
    public function test_pdful_validatorului_e_o_foaie_obisnuita(): void
    {
        $jar = config('anaf.declaratii.duk.jar');

        if (!$jar || !is_file($jar)) {
            $this->markTestSkipped('DUKIntegrator.jar nu se află pe acest calculator.');
        }

        $dosar = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'duk-proba-' . bin2hex(random_bytes(4));
        mkdir($dosar);

        $xml = $dosar . DIRECTORY_SEPARATOR . 'd100.xml';
        $pdf = $dosar . DIRECTORY_SEPARATOR . 'iesit.pdf';
        $erori = $dosar . DIRECTORY_SEPARATOR . 'erori.txt';

        copy(base_path('tests/fixturi/d100-de-proba.xml'), $xml);

        $comanda = escapeshellarg(config('anaf.declaratii.duk.java', 'java')) . ' -jar ' . escapeshellarg($jar)
            . ' -p D100 ' . escapeshellarg($xml) . ' ' . escapeshellarg($erori)
            . ' 0 "" ' . escapeshellarg($pdf) . ' 2>&1';

        exec($comanda, $iesire, $cod);

        if (!is_file($pdf)) {
            $this->stergeDosarul($dosar);
            $this->markTestSkipped('Validatorul nu a scos niciun PDF: ' . implode(' | ', $iesire));
        }

        $continut = file_get_contents($pdf);

        $this->assertStringNotContainsString(
            '/NeedsRendering',
            $continut,
            'PDF-ul validatorului nu trebuie să fie formular XFA: pe acela nu se poate pune caseta'
        );

        $this->assertStringContainsString('/EmbeddedFiles', $continut, 'XML-ul trebuie să călătorească în PDF');

        $this->stergeDosarul($dosar);
    }

    protected function stergeDosarul(string $dosar): void
    {
        foreach (glob($dosar . DIRECTORY_SEPARATOR . '*') as $fisier) {
            @unlink($fisier);
        }

        @rmdir($dosar);
    }
}
