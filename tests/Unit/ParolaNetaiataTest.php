<?php

namespace Tests\Unit;

use App\Http\Middleware\TrimStrings;
use Illuminate\Http\Request;
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
