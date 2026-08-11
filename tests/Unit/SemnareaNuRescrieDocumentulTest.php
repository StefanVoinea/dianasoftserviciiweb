<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Semnarea se adauga la document, nu-l rescrie.
 *
 * Declaratia venita de la ANAF nu e un PDF oarecare: e un formular XFA, iar
 * ANAF ii da si drepturi de completare pentru Adobe Reader — o semnatura de
 * folosinta (/UR3), care acopera octetii fisierului asa cum sunt.
 *
 * Rescris de la capat, fisierul nu-i mai poarta acei octeti. Semnatura de
 * folosinta ramane in el, dar nu mai are ce acoperi: Adobe nu mai da drepturile,
 * iar formularul XFA nu se mai deseneaza — omul deschide declaratia semnata si
 * vede o pagina care nu seamana cu ce a semnat.
 *
 * In adaugare, fisierul dinainte ramane neatins octet cu octet, iar semnatura
 * noastra se scrie in coada lui. Asa lucreaza si Adobe, si asa cere formatul.
 */
class SemnareaNuRescrieDocumentulTest extends TestCase
{
    protected function scriptul(): string
    {
        return file_get_contents(base_path('spv-bridge/sign-pdf.ps1'));
    }

    /** Caseta se stampileaza in adaugare: si ea atinge fisierul. */
    public function test_caseta_se_stampileaza_in_adaugare(): void
    {
        $this->assertStringContainsString(
            'New-Object iTextSharp.text.pdf.PdfStamper($reader, $fs, [char]0, $true)',
            $this->scriptul(),
            'ștampilarea casetei rescrie documentul în loc să adauge la el'
        );
    }

    /** Si semnatura propriu-zisa. */
    public function test_semnatura_se_pune_in_adaugare(): void
    {
        $script = $this->scriptul();

        $this->assertStringContainsString(
            'CreateSignature($reader, $fs, [char]0, $temporarSemnare, $true)',
            $script,
            'semnătura rescrie documentul în loc să adauge la el'
        );

        $this->assertStringNotContainsString(
            'CreateSignature($reader, $fs, [char]0)',
            $script,
            'varianta care rescrie n-are ce căuta aici'
        );
    }

    /**
     * Proba adevarata: se stampileaza un PDF si se cantareste ce a ramas din el.
     *
     * Se sare acolo unde nu e Windows cu PowerShell si biblioteca de PDF-uri —
     * pe serverul aplicatiei nu se semneaza nimic, semnarea se face la client.
     */
    public function test_documentul_dinainte_ramane_neatins(): void
    {
        if (stripos(PHP_OS, 'WIN') !== 0) {
            $this->markTestSkipped('Semnarea se face pe Windows, la client.');
        }

        $biblioteca = base_path('spv-bridge/itextsharp.dll');

        if (!is_file($biblioteca)) {
            $this->markTestSkipped('Biblioteca de PDF-uri nu se află lângă bridge pe acest calculator.');
        }

        $dosar = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'semnare-proba-' . bin2hex(random_bytes(4));
        mkdir($dosar);

        $sursa = $dosar . DIRECTORY_SEPARATOR . 'sursa.pdf';
        $iesire = $dosar . DIRECTORY_SEPARATOR . 'stampilat.pdf';

        $script = <<<PS
\$ErrorActionPreference = 'Stop'
Add-Type -Path '{$biblioteca}'

# Un PDF de doua parale, dar adevarat: destul cat sa se vada ce se intampla cu el.
\$doc = New-Object iTextSharp.text.Document
\$fs = [System.IO.File]::Create('{$sursa}')
\$writer = [iTextSharp.text.pdf.PdfWriter]::GetInstance(\$doc, \$fs)
\$doc.Open()
\$doc.Add((New-Object iTextSharp.text.Paragraph('declaratie de proba'))) | Out-Null
\$doc.Close()
\$fs.Close()

\$reader = New-Object iTextSharp.text.pdf.PdfReader('{$sursa}')
\$iesireFs = [System.IO.File]::Create('{$iesire}')
\$stamper = New-Object iTextSharp.text.pdf.PdfStamper(\$reader, \$iesireFs, [char]0, \$true)
\$panza = \$stamper.GetOverContent(1)
\$panza.Rectangle(40, 40, 150, 50)
\$panza.Stroke()
\$stamper.Close()
\$reader.Close()
\$iesireFs.Close()
PS;

        $caleScript = $dosar . DIRECTORY_SEPARATOR . 'proba.ps1';
        file_put_contents($caleScript, $script);

        exec('powershell -NoProfile -ExecutionPolicy Bypass -File ' . escapeshellarg($caleScript) . ' 2>&1', $iesireCmd, $cod);

        if ($cod !== 0 || !is_file($iesire)) {
            $this->stergeDosarul($dosar);
            $this->markTestSkipped('Proba nu s-a putut face: ' . implode(' | ', $iesireCmd));
        }

        $inainte = file_get_contents($sursa);
        $dupa = file_get_contents($iesire);

        $this->assertSame(
            $inainte,
            substr($dupa, 0, strlen($inainte)),
            'documentul dinainte trebuie să rămână neatins octet cu octet; altfel semnătura ANAF'
                . ' de pe el nu mai are ce acoperi'
        );

        $this->assertGreaterThan(strlen($inainte), strlen($dupa), 'adăugarea trebuie să scrie ceva în coadă');

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
