<?php

namespace Tests\Unit;

use App\Http\Middleware\IpMiddleware;
use App\Models\User;
use App\Services\AccesIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * De unde se poate intra in aplicatie.
 *
 * Autentificarea trecea printr-o lista globala de adrese („ipautorizat"), buna
 * pe vremea cand aplicatia lucra intr-un singur birou. Intr-un serviciu cu
 * clienti in toata tara, ea inchidea afara pe oricine intra de la alta adresa —
 * inclusiv pe administratorul aplicatiei, si inainte de a-si scrie parola.
 *
 * Adresele permise se tin acum pe fiecare cont: lista goala inseamna de
 * oriunde, iar cand e scrisa se verifica si la autentificare, si la fiecare
 * cerere de mai departe.
 */
class AutentificareDeOriundeTest extends TestCase
{
    /** Ruta de autentificare nu mai are poarta globala de adrese. */
    public function test_autentificarea_nu_mai_trece_prin_lista_globala(): void
    {
        $ruta = collect(Route::getRoutes())->first(function ($ruta) {
            return $ruta->uri() === 'api/login';
        });

        $this->assertNotNull($ruta, 'Ruta de autentificare nu mai există.');
        $this->assertNotContains('ipcheck', $ruta->gatherMiddleware());
    }

    /** Contul fara lista scrisa intra de la orice adresa. */
    public function test_contul_fara_lista_intra_de_oriunde(): void
    {
        $user = $this->cont(null);

        $this->assertTrue(AccesIp::arePermisiune($user, '203.0.113.7'));
        $this->assertTrue(AccesIp::arePermisiune($user, '86.121.50.233'));

        $user->forceDelete();
    }

    /** Cand lista e scrisa, ea se respecta mai departe. */
    public function test_contul_cu_lista_e_oprit_de_la_alta_adresa(): void
    {
        $user = $this->cont('86.121.50.0/24, 5.2.224.189');

        $this->assertTrue(AccesIp::arePermisiune($user, '86.121.50.233'));
        $this->assertTrue(AccesIp::arePermisiune($user, '5.2.224.189'));
        $this->assertFalse(AccesIp::arePermisiune($user, '203.0.113.7'));

        $user->forceDelete();
    }

    /**
     * Poarta globala, acolo unde ar mai fi folosita, raspunde cu un refuz —
     * nu cade in eroare de server daca instiintarea administratorului nu pleaca.
     */
    public function test_poarta_globala_raspunde_cu_refuz_nu_cu_eroare(): void
    {
        $cerere = Request::create('/api/proba', 'POST', ['email' => 'cineva@example.com', 'password' => 'secret']);
        $cerere->headers->set('Accept', 'application/json');
        $cerere->server->set('REMOTE_ADDR', '203.0.113.7');

        $raspuns = (new IpMiddleware())->handle($cerere, function () {
            return response()->json(['success' => true]);
        });

        $this->assertSame(403, $raspuns->getStatusCode());
        $this->assertStringNotContainsString('secret', $raspuns->getContent());
    }

    protected function cont(?string $ipPermise): User
    {
        return User::create([
            'name' => 'Proba Adrese',
            'email' => 'proba.adrese' . random_int(1000, 9999) . '@example.com',
            'password' => Hash::make('ParolaDeProba1'),
            'user_type' => 'user',
            'blocat' => 'Nu',
            'status' => 'activ',
            'ip_permise' => $ipPermise,
        ]);
    }
}
