<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\User;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Nomenclatoarele cerute imediat dupa autentificare.
 *
 * Aplicatia le cere pentru oricine intra, ca sa aiba judetele, tarile si
 * optiunile la indemana. Poarta rutei cerea dreptul „viewArticol", care e al
 * altui modul: un utilizator facut din „Utilizatori" nu-l are, asa ca
 * autentificarea lui se oprea aici, dupa parola, cu „Not authorized".
 */
class NomenclatoarePornireTest extends TestCase
{
    protected $client;
    protected $utilizator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Company::create(['denumire' => 'FIRMA NOMENCLATOR SRL', 'cui' => '99000555']);

        $this->utilizator = User::create([
            'name' => 'Om Fara Drepturi Vechi',
            'email' => 'om.nomenclator@example.com',
            'password' => Hash::make('ParolaDeProba1'),
            'user_type' => 'user',
            'blocat' => 'Nu',
            'status' => 'activ',
        ]);

        $this->client->users()->attach($this->utilizator->id, ['administrator' => false]);
    }

    protected function tearDown(): void
    {
        DB::table('company_user')->where('company_id', $this->client->id)->delete();
        $this->utilizator->forceDelete();
        $this->client->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /** Ruta nu mai cere un drept din alt modul. */
    public function test_ruta_nu_mai_cere_dreptul_de_articole(): void
    {
        $ruta = collect(Route::getRoutes())->first(function ($ruta) {
            return $ruta->uri() === 'api/utilizatori/cookiesLocal';
        });

        $this->assertNotNull($ruta, 'Ruta nomenclatoarelor de pornire nu mai există.');
        $this->assertNotContains('permission:viewArticol', $ruta->gatherMiddleware());
        $this->assertContains('companie.anaf', $ruta->gatherMiddleware());
    }

    /** Iar omul fara drepturi vechi trece de ea si isi primeste nomenclatoarele. */
    public function test_utilizatorul_unui_client_primeste_nomenclatoarele(): void
    {
        $raspuns = $this->actingAs($this->utilizator, 'api')
            ->postJson('/api/utilizatori/cookiesLocal', [], [
                'AuthorizationHeader' => (string) $this->client->id,
            ]);

        $raspuns->assertStatus(200);

        $date = json_decode($raspuns->getContent(), true);

        $this->assertIsArray($date);
        $this->assertArrayHasKey('judet', $date);
        $this->assertArrayHasKey('tari', $date);
    }

    /** Societatea straina ramane inchisa, ca peste tot. */
    public function test_societatea_straina_ramane_inchisa(): void
    {
        $straina = Company::create(['denumire' => 'FIRMA STRAINA SRL', 'cui' => '99000556']);

        $raspuns = $this->actingAs($this->utilizator, 'api')
            ->postJson('/api/utilizatori/cookiesLocal', [], [
                'AuthorizationHeader' => (string) $straina->id,
            ]);

        $straina->delete();

        $raspuns->assertStatus(403);
    }
}
