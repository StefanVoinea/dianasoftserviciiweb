<?php

namespace Tests\Unit;

use Tests\TestCase;
use ZipArchive;

/**
 * Documentul de prezentare, scris din comanda.
 *
 * Se face din comanda tocmai ca sa poata fi refacut la fiecare schimbare a
 * modulului; testul pazeste ca el chiar se scrie si ca e un fisier Word bun de
 * deschis — un document care nu se deschide s-ar afla abia la client.
 */
class PrezentareSpvTest extends TestCase
{
    protected $iesire;

    protected function setUp(): void
    {
        parent::setUp();

        $this->iesire = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'prezentare-proba-' . bin2hex(random_bytes(4)) . '.docx';
    }

    protected function tearDown(): void
    {
        @unlink($this->iesire);

        parent::tearDown();
    }

    /** @return string textul din document, fara etichetele XML */
    protected function textul(string $cale): string
    {
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($cale) === true, 'Documentul nu se deschide ca fișier Word.');

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        $this->assertNotFalse($xml, 'Documentul nu are cuprins.');

        return preg_replace('/<[^>]+>/', ' ', $xml);
    }

    public function test_documentul_se_scrie_si_se_poate_deschide()
    {
        $this->artisan('spv:prezentare', ['--iesire' => $this->iesire])->assertExitCode(0);

        $this->assertFileExists($this->iesire);
        $this->assertGreaterThan(5000, filesize($this->iesire));

        $text = $this->textul($this->iesire);

        // Cele doua parti cerute: ce stie sa faca si cum se foloseste.
        $this->assertStringContainsString('Puncte forte', $text);
        $this->assertStringContainsString('Cum se folose', $text);
        $this->assertStringContainsString('SPV Wizard', $text);
    }

    /**
     * Fara capturi, documentul lasa locurile insemnate: se poate da mai departe
     * si asa, iar cand pozele apar se ruleaza comanda din nou.
     */
    public function test_locurile_capturilor_raman_insemnate()
    {
        $this->artisan('spv:prezentare', [
            '--iesire' => $this->iesire,
            '--capturi' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'capturi-care-nu-exista',
        ])->assertExitCode(0);

        $this->assertStringContainsString('aici vine captura', $this->textul($this->iesire));
    }
}
