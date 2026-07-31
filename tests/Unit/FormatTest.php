<?php

namespace Tests\Unit;

use App\Services\Anaf\Format;
use Carbon\Carbon;
use Tests\TestCase;

class FormatTest extends TestCase
{
    /** @dataProvider valoriData */
    public function test_datele_calendaristice_sunt_afisate_zi_luna_an($valoare, ?string $asteptat): void
    {
        $this->assertSame($asteptat, Format::data($valoare));
    }

    public function valoriData(): array
    {
        return [
            'obiect Carbon' => [Carbon::create(2026, 12, 20, 9, 42, 7), '20.12.2026'],
            'șir ISO cu oră' => ['2026-12-20 09:42:07', '20.12.2026'],
            'șir ISO doar dată' => ['2026-07-02', '02.07.2026'],
            'null' => [null, null],
            'șir gol' => ['', null],
        ];
    }

    /** @dataProvider valoriDataOra */
    public function test_momentele_sunt_afisate_cu_ora($valoare, ?string $asteptat): void
    {
        $this->assertSame($asteptat, Format::dataOra($valoare));
    }

    public function valoriDataOra(): array
    {
        return [
            'obiect Carbon' => [Carbon::create(2026, 7, 28, 12, 30, 34), '28.07.2026 12:30:34'],
            'șir din baza de date' => ['2026-07-02 12:30:34', '02.07.2026 12:30:34'],
            'șir ISO 8601' => ['2026-07-28T09:10:48.000000Z', '28.07.2026 09:10:48'],
            'dată fără oră' => ['2026-07-02', '02.07.2026 00:00:00'],
            'null' => [null, null],
        ];
    }

    /** O valoare neinterpretabilă nu trebuie să arunce, ci să fie păstrată. */
    public function test_valoarea_neinterpretabila_este_pastrata(): void
    {
        $this->assertSame('cândva', Format::data('cândva'));
    }
}
