<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\UtilizatoriClientController;
use App\Http\Middleware\TrimStrings;
use App\Models\Company;
use App\Models\User;
use App\Support\ContextCompanie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Parola se salveaza intocmai cum a fost scrisa.
 *
 * Aplicatia taie spatiile de la capetele campurilor trimise, ceea ce e bun
 * pentru un nume sau un email, dar nu si pentru o parola: la autentificare,
 * campul „password" nu e taiat, deci o parola salvata fara spatiul de la capat
 * nu s-ar mai potrivi niciodata cu cea scrisa de om. Contul ar fi creat si n-ar
 * putea intra — cu mesajul „datele de autentificare sunt gresite".
 */
class ParolaNetaiataTest extends TestCase
{
    protected function trecutPrinCuratare(array $campuri): Request
    {
        $cerere = Request::create('/proba', 'POST', $campuri);

        (new TrimStrings())->handle($cerere, function (Request $trecuta) {
            return $trecuta;
        });

        return $cerere;
    }

    public function test_parola_nu_e_taiata_la_capete(): void
    {
        $cerere = $this->trecutPrinCuratare([
            'parola' => ' parola cu spatii ',
            'parola_noua' => ' alta parola ',
            'password' => ' la fel ',
        ]);

        $this->assertSame(' parola cu spatii ', $cerere->input('parola'));
        $this->assertSame(' alta parola ', $cerere->input('parola_noua'));
        $this->assertSame(' la fel ', $cerere->input('password'));
    }

    /**
     * La modificarea unui utilizator, campul de parola lasat gol inseamna
     * „parola ramane cum era". Nici spatiile n-au voie sa treaca drept parola
     * noua: omul n-ar mai putea intra, fara ca nimeni sa fi vrut asta.
     */
    public function test_modificarea_fara_parola_nu_schimba_parola(): void
    {
        $client = Company::create(['denumire' => 'FIRMA PAROLA SRL', 'cui' => '99000444']);

        $utilizator = User::create([
            'name' => 'Ion Parola',
            'email' => 'ion.parola@example.com',
            'password' => Hash::make('ParolaVeche1'),
            'user_type' => 'user',
            'blocat' => 'Nu',
            'status' => 'activ',
        ]);

        $client->users()->attach($utilizator->id, ['administrator' => false]);
        ContextCompanie::fixeaza($client->id);

        $controller = new UtilizatoriClientController();

        // Fara campul de parola
        $controller->update(new Request(['telefon' => '0722000111']), $utilizator);
        $this->assertTrue(Hash::check('ParolaVeche1', User::find($utilizator->id)->password));

        // Cu campul gol
        $controller->update(new Request(['parola' => '']), $utilizator);
        $this->assertTrue(Hash::check('ParolaVeche1', User::find($utilizator->id)->password));

        // Cu campul plin de spatii, destule cat sa treaca de lungimea ceruta
        $controller->update(new Request(['parola' => '          ']), $utilizator);
        $this->assertTrue(Hash::check('ParolaVeche1', User::find($utilizator->id)->password));

        // Iar cand chiar se scrie una noua, ea se schimba
        $controller->update(new Request(['parola' => 'ParolaNoua1']), $utilizator);
        $this->assertTrue(Hash::check('ParolaNoua1', User::find($utilizator->id)->password));

        DB::table('company_user')->where('company_id', $client->id)->delete();
        $utilizator->forceDelete();
        $client->delete();
        ContextCompanie::elibereaza();
    }

    /** Restul campurilor se curata mai departe: acolo spatiile sunt greseli. */
    public function test_celelalte_campuri_raman_taiate(): void
    {
        $cerere = $this->trecutPrinCuratare([
            'nume' => '  Ion Popescu  ',
            'email' => '  ion@example.com ',
        ]);

        $this->assertSame('Ion Popescu', $cerere->input('nume'));
        $this->assertSame('ion@example.com', $cerere->input('email'));
    }
}
