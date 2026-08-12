<?php

namespace Tests\Unit;

use App\Support\ContextUtilizator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * Un client cu multe firme nu-i mai opreste pe ceilalti, si nu mai umple jurnalul.
 *
 * La un client cu trei sute de coduri fiscale, „Solicită datele lipsă" trecea
 * repede prin vreo suta de firme si se oprea cu „Too Many Attempts", iar in
 * jurnalul serverului se aduna, la fiecare cerere, o urma de o suta de randuri
 * despre JWT. Doua lucruri deosebite, amandoua cu radacina in acelasi loc:
 * puntea.
 */
class PunteaNuSeSufocaSingurTest extends TestCase
{
    /**
     * Numaratoarea nu se mai face pe adresa celui care bate.
     *
     * La punte bate, cel mai des, chiar serverul nostru: fata dinspre aplicatie
     * e o adresa web pe care aplicatia o cheama singura. Toate cererile tuturor
     * clientilor porneau deci de la aceeasi masina — deci de la aceeasi adresa —
     * si se adunau intr-o singura galeata.
     */
    public function test_limita_se_numara_pe_certificat_nu_pe_adresa(): void
    {
        $sursa = file_get_contents(app_path('Providers/RouteServiceProvider.php'));

        $inceput = strpos($sursa, "\$request->is('api/punte/*')");
        $bucata = substr($sursa, $inceput, 400);

        $this->assertStringContainsString("\$request->route('certificat')", $bucata);
        $this->assertStringContainsString("'punte:' . \$cheie", $bucata);

        $this->assertStringNotContainsString(
            'perMinute(1200)->by($request->ip())',
            $sursa,
            'adresa nu mai poate fi cheia: toate cererile aplicației vin de la aceeași'
        );
    }

    /**
     * Cheia chiar iese deosebita pentru doua certificate.
     *
     * Se lucreaza pe ruta adevarata si pe limitatorul adevarat: altfel proba ar
     * cantari o repovestire a lor, iar tocmai potrivirea rutei era lucrul de
     * care atarna totul — parametrul trebuie sa fie citibil in clipa in care
     * limitatorul lucreaza, inaintea legarii modelelor.
     */
    public function test_doua_certificate_au_galeti_deosebite(): void
    {
        $this->assertSame('punte:11', $this->cheiaLimitei('/api/punte/11/spv/listaMesaje'));
        $this->assertSame('punte:12', $this->cheiaLimitei('/api/punte/12/spv/listaMesaje'));
    }

    /** Iar caile agentului, care n-au certificat, se numara pe codul lui. */
    public function test_agentul_se_numara_pe_codul_lui(): void
    {
        $cheie = $this->cheiaLimitei('/api/punte/agent/asteapta', 'cod-de-instalare', 'POST');

        $this->assertSame('punte:' . sha1('cod-de-instalare'), $cheie);

        // Doi agenti, doua galeti.
        $this->assertNotSame(
            $cheie,
            $this->cheiaLimitei('/api/punte/agent/asteapta', 'alt-cod', 'POST')
        );
    }

    /** Cheia pe care o alege limitatorul adevarat pentru o cerere. */
    protected function cheiaLimitei(string $cale, ?string $jeton = null, string $metoda = 'GET'): string
    {
        $cerere = Request::create($cale, $metoda);

        if ($jeton) {
            $cerere->headers->set('Authorization', 'Bearer ' . $jeton);
        }

        $ruta = $this->app['router']->getRoutes()->match($cerere);
        $cerere->setRouteResolver(function () use ($ruta) {
            return $ruta;
        });

        $limita = \Illuminate\Support\Facades\RateLimiter::limiter('api')($cerere);

        return $limita->key;
    }

    /**
     * Pe caile puntii nu se mai intreaba Passport.
     *
     * Prinderea poticnirii nu ajungea: Passport isi prinde singur exceptia si o
     * raporteaza inainte s-o inghita, asa ca ea nu ajunge la „catch", dar ajunge
     * in jurnal — la fiecare cerere a fiecarui agent.
     */
    public function test_pe_caile_puntii_nu_se_intreaba_passport(): void
    {
        // Daca ar fi intrebat, ar afla: paznicul e pus sa strige.
        Auth::shouldReceive('guard')
            ->with('api')
            ->never();

        Auth::shouldReceive('user')->andReturn(null);

        $cerere = Request::create('/api/punte/11/spv/listaMesaje', 'GET');
        $cerere->headers->set('Authorization', 'Bearer v1.ceva.altceva');

        $this->app->instance('request', $cerere);

        $this->assertNull(ContextUtilizator::curent());
    }

    /** Pe celelalte cai se intreaba mai departe: acolo chiar sunt utilizatori. */
    public function test_pe_celelalte_cai_se_intreaba_ca_pana_acum(): void
    {
        Auth::shouldReceive('guard')
            ->with('api')
            ->once()
            ->andReturnSelf();

        Auth::shouldReceive('user')->andReturn(null);

        $cerere = Request::create('/api/spv/mesaje', 'GET');
        $cerere->headers->set('Authorization', 'Bearer un.jeton.adevarat');

        $this->app->instance('request', $cerere);

        ContextUtilizator::curent();
    }
}
