<?php

namespace Tests\Unit;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Fiecare middleware folosit pe rute trebuie sa aiba alias inregistrat.
 *
 * Un alias uitat nu se vede la pornire: ruta merge pana la capat, iar aplicatia
 * cade abia la sfarsitul cererii, cand Laravel incearca sa-l rezolve — deci un
 * 500 dupa ce treaba s-a facut deja.
 */
class MiddlewareInregistratTest extends TestCase
{
    /** Grupuri definite de Laravel, care nu sunt aliasuri. */
    protected const GRUPURI = ['web', 'api'];

    public function test_toate_aliasurile_de_pe_rute_sunt_inregistrate(): void
    {
        $inregistrate = $this->aliasuri();
        $lipsa = [];

        foreach (Route::getRoutes() as $ruta) {
            foreach ($ruta->gatherMiddleware() as $middleware) {
                if (!is_string($middleware)) {
                    continue;
                }

                // „modul:spv" — aliasul e partea dinaintea parametrilor.
                $nume = explode(':', $middleware)[0];

                if (in_array($nume, self::GRUPURI, true) || class_exists($nume)) {
                    continue;
                }

                if (!array_key_exists($nume, $inregistrate)) {
                    $lipsa[$nume] = $ruta->uri();
                }
            }
        }

        $this->assertSame([], $lipsa, 'Aliasuri fără înregistrare în Kernel: ' . json_encode($lipsa));
    }

    public function test_clasele_din_spatele_aliasurilor_exista(): void
    {
        foreach ($this->aliasuri() as $nume => $clasa) {
            $this->assertTrue(class_exists($clasa), 'Aliasul „' . $nume . '” arată spre o clasă inexistentă: ' . $clasa);
        }
    }

    /** @return array<string, string> */
    protected function aliasuri(): array
    {
        $kernel = $this->app->make(Kernel::class);

        $proprietate = new \ReflectionProperty(get_class($kernel), 'routeMiddleware');
        $proprietate->setAccessible(true);

        return $proprietate->getValue($kernel);
    }
}
