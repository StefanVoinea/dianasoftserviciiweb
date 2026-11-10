<?php

namespace Tests\Unit;

use App\Models\AnafDeclaratie;
use App\Services\Anaf\Declaratii\RecipisaService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * Starea publica a declaratiei, cand SPV n-are inca recipisa.
 *
 * In august 2026, ANAF a mutat StareD112 de pe „www.anaf.ro” pe gazda ei
 * proprie, „stare.anaf.ro”, fara sa lase o mutare anuntata la adresa veche.
 * Interogarea cadea, iar declaratiile ramaneau „In prelucrare” la nesfarsit.
 *
 * Ce nu s-a vazut atunci: „In prelucrare” e si raspunsul dat cand ANAF n-a fost
 * intrebat deloc. De aceea se cantareste aici nu doar adresa, ci si urma lasata
 * in jurnal — ea e singura care deosebeste o declaratie care asteapta raspunsul
 * de una despre care nu s-a putut afla nimic.
 */
class StareaPublicaD112Test extends TestCase
{
    /** Adresa la care se intreaba e cea in lucru, nu cea parasita. */
    public function test_se_intreaba_la_gazda_cea_noua(): void
    {
        foreach (['url_stare', 'url_recipisa'] as $care) {
            $adresa = config('anaf.declaratii.' . $care);

            $this->assertStringStartsWith(
                'https://stare.anaf.ro/StareD112/',
                $adresa,
                $care . ': „www.anaf.ro/StareD112” nu mai răspunde din august 2026'
            );
        }
    }

    /** Cand ANAF nu raspunde, jurnalul spune de ce — nu doar „In prelucrare”. */
    public function test_o_adresa_mutata_lasa_urma_in_jurnal(): void
    {
        /*
         * 404 se alege dinadins: e tocmai raspunsul unei adrese mutate, si
         * tocmai cel care NU arunca exceptie. Fara verificarea starii, pagina de
         * eroare ar fi trecut mai departe ca si cum ar fi fost un raspuns bun.
         */
        Http::fake([
            '*' => Http::response('<html>Not Found</html>', 404),
        ]);

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($vorba, $amanunte) {
                return strpos($vorba, '404') !== false
                    && isset($amanunte['adresa']);
            });

        $declaratie = $this->declaratia();

        $this->cheama($declaratie);

        $this->assertSame(
            'In prelucrare',
            $declaratie->stare_declaratie,
            'omului i se spune adevărul: încă nu se știe nimic'
        );
    }

    /** Cand cererea nici nu pleaca, la fel: se spune in jurnal. */
    public function test_cererea_care_nu_ajunge_se_scrie_in_jurnal(): void
    {
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('Could not resolve host');
        });

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($vorba) {
                return strpos($vorba, 'Could not resolve host') !== false;
            });

        $declaratie = $this->declaratia();

        $this->cheama($declaratie);

        $this->assertSame('In prelucrare', $declaratie->stare_declaratie);
    }

    /** Un raspuns bun se citeste ca pana acum, si nu se plange nimeni. */
    public function test_raspunsul_bun_trece_neatins(): void
    {
        Http::fake([
            '*' => Http::response('<table><tr><td>44556677</td><td>Declaratie valida</td></tr></table>', 200),
        ]);

        Log::shouldReceive('warning')->never();

        $declaratie = $this->declaratia();

        $this->cheama($declaratie);

        $this->assertStringContainsString('Declaratie valida', $declaratie->stare_declaratie);
    }

    /**
     * Starea se ia din randul indicelui, nu din toata pagina.
     *
     * Pagina StareD112 insira toate depunerile din ultimele luni, cu antetul
     * ministerului scris cu entitati HTML. Inainte se pastra tot textul ei —
     * „Ministerul Finan&#355;elor... IndexTip documentStare..." — iar starea
     * adevarata se ineca in el. Asa s-a si vazut la un client: trei declaratii
     * valide aratau in fila un text fara noima.
     */
    public function test_starea_se_ia_din_randul_indicelui_nu_din_toata_pagina(): void
    {
        $pagina = '<html><body>'
            . '<p>Ministerul Finan&#355;elor Agen&#355;ia Na&#355;ional&#259; de Administrare Fiscal&#259;</p>'
            . '<p>Documente depuse pentru cui: 15208744 in perioada 12.05.2026 / 12.08.2026</p>'
            . '<table>'
            . '<tr><th>Index</th><th>Tip document</th><th>Stare document</th>'
            . '<th>Data inregistrare</th><th>Consulta&#355;i</th><th>Data incarcare</th></tr>'
            . '<tr><td>99999999</td><td>D406</td><td>Documentul are erori de validare</td>'
            . '<td>INTERNT-99999999-2026 din 01.08.2026</td><td><a href="#">recipisa</a></td><td>2026-08-01</td></tr>'
            . '<tr><td>44556677</td><td>D112</td><td>Documentul este valid</td>'
            . '<td>INTERNT-44556677-2026 din 12.08.2026</td><td><a href="#">recipisa</a></td><td>2026-08-12</td></tr>'
            . '</table></body></html>';

        Http::fake(['*' => Http::response($pagina, 200)]);
        Log::shouldReceive('warning')->never();

        $declaratie = $this->declaratia();

        $this->cheama($declaratie);

        $this->assertSame(
            'D112 Documentul este valid INTERNT-44556677-2026 din 12.08.2026 2026-08-12',
            $declaratie->stare_declaratie,
            'doar rândul indicelui căutat, cu entitățile decodate'
        );

        // Iar clasificarea o recunoaste drept valida, nu „necunoscut".
        $this->assertSame('valid', RecipisaService::clasifica($declaratie->stare_declaratie));
    }

    /** Pagina StareD112 cu rândul unei declarații, cu legătura reală spre recipisă. */
    protected function paginaCuRecipisa(string $stare): string
    {
        return '<html><body><table>'
            . '<tr><th>Index</th><th>Tip document</th><th>Stare document</th>'
            . '<th>Data inregistrare</th><th>Consulta&#355;i</th><th>Data incarcare</th></tr>'
            . '<tr><td>44556677</td><td>D112</td><td>' . $stare . '</td>'
            . '<td>INTERNT-44556677-2026 din 12.08.2026</td>'
            . '<td><a href="/StareD112/ObtineRecipisa?numefisier=44556677.pdf">recipisa</a></td><td>2026-08-12</td></tr>'
            . '</table></body></html>';
    }

    /**
     * [2026-11-10] Fără împuternicire în SPV pentru firmă, mesajul RECIPISA nu
     * vine niciodată. Recipisa se ia atunci de pe pagina publică, după indice.
     */
    public function test_fara_spv_recipisa_se_ia_de_pe_pagina_publica(): void
    {
        \Illuminate\Support\Facades\Storage::fake();

        Http::fake([
            'stare.anaf.ro/StareD112/ObtineRecipisa*' => Http::response('%PDF-1.4 recipisa de proba', 200, ['Content-Type' => 'application/pdf']),
            'stare.anaf.ro/*' => Http::response($this->paginaCuRecipisa('Documentul este valid'), 200),
        ]);

        $declaratie = $this->declaratia();

        $this->assertTrue($this->cheama($declaratie), 'recipisa trebuia adusă');

        $this->assertSame('finalizat', $declaratie->pas);
        $this->assertNotNull($declaratie->data_recipisa);
        $this->assertStringContainsString('Documentul este valid', $declaratie->stare_declaratie);

        // Fara arhiva la client, recipisa ramane pe server.
        $this->assertNotEmpty($declaratie->cale_recipisa);
        \Illuminate\Support\Facades\Storage::assertExists($declaratie->cale_recipisa);

        Http::assertSent(function ($cerere) {
            return $cerere->url() === 'https://stare.anaf.ro/StareD112/ObtineRecipisa?numefisier=44556677.pdf';
        });
    }

    /** Cât timp ANAF încă prelucrează, recipisa nu se cere. */
    public function test_in_prelucrare_recipisa_nu_se_cere(): void
    {
        Http::fake(['*' => Http::response($this->paginaCuRecipisa('In prelucrare'), 200)]);

        $declaratie = $this->declaratia();

        $this->assertFalse($this->cheama($declaratie));
        $this->assertNull($declaratie->pas);

        Http::assertNotSent(function ($cerere) {
            return strpos($cerere->url(), 'ObtineRecipisa') !== false;
        });
    }

    /**
     * O declaratie care tine minte ce i se scrie, fara sa treaca prin baza.
     *
     * Aici se cantareste interogarea, nu asezarea in baza de date; iar o
     * declaratie nesalvata pierde in tacere ce i se scrie prin „update”, asa ca
     * proba ar fi aratat mereu gol si n-ar fi cantarit nimic.
     */
    protected function declaratia(): AnafDeclaratie
    {
        $declaratie = new class() extends AnafDeclaratie {
            public function update(array $atribute = [], array $optiuni = [])
            {
                $this->forceFill($atribute);

                return true;
            }
        };

        $declaratie->index_recipisa = '44556677';
        $declaratie->cui = '15208744';

        return $declaratie;
    }

    protected function cheama(AnafDeclaratie $declaratie): bool
    {
        $serviciu = app(RecipisaService::class);

        $interogarea = new \ReflectionMethod($serviciu, 'preiaStareaPublica');
        $interogarea->setAccessible(true);

        return $interogarea->invoke($serviciu, $declaratie);
    }
}
