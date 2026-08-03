<?php

namespace Tests\Unit;

use App\Services\Anaf\CaleWindows;
use Tests\TestCase;

/**
 * Caile scrise pentru calculatorul clientului.
 *
 * Verificarea se facea cu tipare scrise in PHP, unde bara oblica inversa
 * trebuie indoita de patru ori. O scapare de o bara a stricat amandoua
 * tiparele: unul nu mai putea fi citit deloc — „preg_match(): Unknown modifier
 * ']'", adica eroare de server la salvarea certificatului — iar celalalt nu mai
 * potrivea nicio cale adevarata. De aceea acum se lucreaza pe text.
 */
class CaleWindowsTest extends TestCase
{
    /** @dataProvider caiIntregi */
    public function test_calea_intreaga_e_primita(string $cale): void
    {
        $this->assertTrue(CaleWindows::esteIntreaga($cale), 'Calea „' . $cale . '” trebuia primită.');
        $this->assertNull(CaleWindows::motivRefuz($cale, 'Calea arhivei'));
    }

    public function caiIntregi(): array
    {
        return [
            ['D:\Documente fiscale'],
            ['d:\spv'],
            ['C:/Documente'],
            ['\\\\server\arhiva'],
            ['\\\\server-fisiere\Documente\SPV'],
        ];
    }

    /** @dataProvider caiPeJumatate */
    public function test_calea_pe_jumatate_e_respinsa(string $cale): void
    {
        $this->assertFalse(CaleWindows::esteIntreaga($cale), 'Calea „' . $cale . '” nu trebuia primită.');
        $this->assertStringContainsString('întreagă', (string) CaleWindows::motivRefuz($cale, 'Calea arhivei'));
    }

    public function caiPeJumatate(): array
    {
        return [
            ['Documente'],
            ['documente\spv'],
            ['D:'],
            ['/var/documente'],
            ['\\\\'],
        ];
    }

    /** Salturile „.." ar scoate documentele din dosarul ales. */
    public function test_salturile_sunt_oprite(): void
    {
        $this->assertTrue(CaleWindows::areSalturi('D:\a\..\b'));
        $this->assertStringContainsString('..', (string) CaleWindows::motivRefuz('D:\a\..\b', 'Dosarul urmărit'));

        $this->assertFalse(CaleWindows::areSalturi('D:\Documente fiscale'));
    }

    /** Gol inseamna „ce scrie pe calculatorul acela", deci nu e o greseala. */
    public function test_calea_goala_nu_e_o_greseala(): void
    {
        $this->assertNull(CaleWindows::motivRefuz('', 'Calea arhivei'));
        $this->assertNull(CaleWindows::motivRefuz(null, 'Calea arhivei'));
    }
}
