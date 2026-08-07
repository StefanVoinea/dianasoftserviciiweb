<?php

namespace Tests\Unit;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use ReflectionMethod;
use Tests\TestCase;

/**
 * Datele unui client nu se pot cere cu un id ghicit.
 *
 * Rutele modulului primesc modelul de-a gata, cautat dupa id-ul din adresa
 * („route model binding"). Cautarea aceea trece prin filtrul pe companie, cel
 * care tine datele clientilor despartite — dar numai daca la ceasul ei exista
 * context de client.
 *
 * Cat timp legarea se facea inaintea middleware-ului care pune contextul,
 * filtrul nu se aplica deloc: orice utilizator al modulului putea cere, cu un id
 * ghicit, declaratia sau certificatul altui client, iar controlerul le primea
 * fara sa mai aiba de unde sti ca nu sunt ale lui. Scria negru pe alb in
 * ApartineCompaniei ca asa ceva „nu gaseste nimic si se incheie cu 404" — si
 * totusi nu era asa.
 *
 * Ordinea nu se vede citind rutele: o hotaraste $middlewarePriority din Kernel,
 * iar framework-ul o aseza inaintea noastra. De aceea se cantareste aici.
 */
class IzolareaClientilorTest extends TestCase
{
    /** @return array<int, string> middleware-ele rutei, in ordinea rularii */
    protected function randuiala(string $uri): array
    {
        $ruta = collect(app('router')->getRoutes())->first(function ($ruta) use ($uri) {
            return $ruta->uri() === $uri;
        });

        $this->assertNotNull($ruta, 'nu există ruta ' . $uri);

        $kernel = app(Kernel::class);
        $metoda = new ReflectionMethod($kernel, 'gatherRouteMiddleware');
        $metoda->setAccessible(true);

        $cerere = Request::create('/' . $uri, 'GET');
        $cerere->setRouteResolver(function () use ($ruta) {
            return $ruta;
        });

        return array_map(function ($mw) {
            return is_string($mw) ? explode(':', $mw)[0] : get_class($mw);
        }, $metoda->invoke($kernel, $cerere));
    }

    /**
     * Contextul clientului se pune INAINTE de cautarea modelului dupa id.
     *
     * Rutele alese sunt cele cu id in adresa, adica tocmai cele la care un id
     * ghicit ar aduce datele altui client.
     */
    public function test_contextul_clientului_se_pune_inaintea_legarii_din_ruta(): void
    {
        $rute = [
            'api/declaratii/{declaratie}/erori',
            'api/spv/solicitari/{solicitare}/fisier',
            'api/anaf-societati/{societate}',
        ];

        foreach ($rute as $uri) {
            $randuiala = $this->randuiala($uri);

            $companie = array_search(\App\Http\Middleware\CompanieAnaf::class, $randuiala, true);
            $legarea = array_search(\Illuminate\Routing\Middleware\SubstituteBindings::class, $randuiala, true);

            $this->assertNotFalse($companie, $uri . ' nu trece prin middleware-ul de client');
            $this->assertNotFalse($legarea, $uri . ' nu leagă modelul din rută');

            $this->assertLessThan(
                $legarea,
                $companie,
                $uri . ': modelul se caută înainte să se știe al cui client e cererea,'
                    . ' deci filtrul pe companie nu se aplică și un id ghicit aduce datele altui client'
            );
        }
    }

    /** Randuiala e scrisa la noi, nu lasata pe seama framework-ului. */
    public function test_randuiala_e_scrisa_in_kernel(): void
    {
        $kernel = file_get_contents(app_path('Http/Kernel.php'));

        $this->assertStringContainsString('$middlewarePriority', $kernel);

        $prioritate = new \ReflectionProperty(\App\Http\Kernel::class, 'middlewarePriority');
        $prioritate->setAccessible(true);
        $lista = $prioritate->getValue(app(Kernel::class));

        $companie = array_search(\App\Http\Middleware\CompanieAnaf::class, $lista, true);
        $legarea = array_search(\Illuminate\Routing\Middleware\SubstituteBindings::class, $lista, true);

        $this->assertNotFalse($companie, 'middleware-ul de client lipsește din rânduială');
        $this->assertLessThan($legarea, $companie);
    }
}
