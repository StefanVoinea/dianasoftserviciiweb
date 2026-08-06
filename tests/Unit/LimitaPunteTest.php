<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * Cate cereri are voie sa faca programul de la client.
 *
 * Puntea nu poarta trafic de om: un lot de mesaje din SPV trece prin ea cu
 * doua-trei cereri de fiecare document — panda, corpul comenzii si rezultatul.
 * Cu limita obisnuita de 60 pe minut, chiar aplicatia noastra ii raspundea „bate
 * mai rar la usa" (429) tocmai in mijlocul descarcarii.
 */
class LimitaPunteTest extends TestCase
{
    protected function limitaPentru(string $cale, string $metoda = 'POST')
    {
        $limitator = RateLimiter::limiter('api');

        return $limitator(Request::create($cale, $metoda));
    }

    /** Agentul are loc pentru un lot intreg de documente. */
    public function test_puntea_are_limita_larga()
    {
        $limita = $this->limitaPentru('/api/punte/agent/asteapta');

        $this->assertSame(1200, $limita->maxAttempts);
    }

    /** Si rezultatele, si corpurile comenzilor trec pe acolo. */
    public function test_toate_caile_puntii_au_aceeasi_limita()
    {
        foreach (['/api/punte/agent/rezultat/7', '/api/punte/agent/corp/7', '/api/punte/12/spv/listaMesaje'] as $cale) {
            $this->assertSame(1200, $this->limitaPentru($cale)->maxAttempts, 'Alta limita pe ' . $cale);
        }
    }

    /** Restul aplicatiei ramane cum era: limita larga e doar pentru punte. */
    public function test_restul_aplicatiei_ramane_la_limita_obisnuita()
    {
        $this->assertSame(60, $this->limitaPentru('/api/anaf-certificate', 'GET')->maxAttempts);
    }
}
