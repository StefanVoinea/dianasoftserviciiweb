<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\AnafSocietatiController;
use App\Models\AnafSocietate;
use App\Services\Anaf\Declaratii\D300\AntetD300;
use App\Support\ContextCompanie;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Datele firmei care intra in antetul declaratiilor.
 *
 * Cifrele decontului ies din jurnalele SAF-T, dar antetul cere lucruri care nu
 * se afla nicaieri in fisier: adresa, banca si contul, codul CAEN, cine
 * semneaza. Ele nu se schimba de la o luna la alta, asa ca stau pe fisa firmei
 * si se iau de acolo — nu se cer la fiecare declaratie.
 */
class DateDeDeclaratieTest extends TestCase
{
    protected const COMPANIE = 993;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);
    }

    protected function tearDown(): void
    {
        AnafSocietate::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function societate(array $atribute = []): AnafSocietate
    {
        return AnafSocietate::create(array_merge([
            'company_id' => self::COMPANIE,
            'cif' => '15208744',
            'denumire' => 'DIANA SOFT SRL',
        ], $atribute));
    }

    /** Datele intregi, cu care se poate scrie antetul. */
    protected function dateIntregi(): array
    {
        return [
            'adresa' => 'Str. Bradului nr. 13, Năvodari',
            'banca' => 'Banca Transilvania',
            'cont' => 'RO49AAAA1B31007593840000',
            'caen' => '6201',
            'nume_declarant' => 'Voinea',
            'prenume_declarant' => 'Ștefan',
            'functie_declarant' => 'Administrator',
            'd300_tip_decont' => 'L',
        ];
    }

    protected function scrie(AnafSocietate $societate, array $date): array
    {
        $controller = $this->app->make(AnafSocietatiController::class);

        return $controller->dateDeclaratii(new Request($date), $societate)->getData(true);
    }

    public function test_datele_scrise_se_regasesc_pe_fisa_firmei(): void
    {
        $societate = $this->societate();

        $raspuns = $this->scrie($societate, $this->dateIntregi());

        $this->assertTrue($raspuns['success']);
        $this->assertTrue($raspuns['data']['gata'], 'cu toate câmpurile cerute, antetul e gata');
        $this->assertSame([], $raspuns['data']['lipsesc']);

        $this->assertSame('6201', $societate->fresh()->caen);
    }

    /**
     * Ce lipseste se spune pe nume, dinainte.
     *
     * Altfel omul afla de la validatorul ANAF, dupa ce declaratia a fost
     * respinsa — si atunci mesajul e „atribut lipsa: banca", nu „completeaza
     * banca in fisa firmei".
     */
    public function test_ce_lipseste_se_spune_pe_nume(): void
    {
        $societate = $this->societate();

        $raspuns = $this->scrie($societate, ['adresa' => 'Constanța']);

        $this->assertFalse($raspuns['data']['gata']);
        $this->assertContains('Banca', $raspuns['data']['lipsesc']);
        $this->assertContains('Codul CAEN', $raspuns['data']['lipsesc']);
        $this->assertNotContains('Adresa', $raspuns['data']['lipsesc']);
    }

    /**
     * Bifa de imputernicit schimba si temeiul declaratiei.
     *
     * In schema ANAF sunt doua atribute deosebite — „depusReprezentant" si
     * „temei" —, dar ele spun acelasi lucru si nu pot merge razlete: o
     * declaratie depusa prin imputernicit are temeiul 2, nu 0.
     */
    public function test_imputernicitul_schimba_si_temeiul(): void
    {
        $societate = $this->societate();

        $this->scrie($societate, $this->dateIntregi());
        $antet = AntetD300::pentru($societate->fresh())['atribute'];

        $this->assertSame('0', $antet['depusReprezentant']);
        $this->assertSame('0', $antet['temei']);

        $this->scrie($societate, $this->dateIntregi() + ['prin_reprezentant' => true]);
        $antet = AntetD300::pentru($societate->fresh())['atribute'];

        $this->assertSame('1', $antet['depusReprezentant']);
        $this->assertSame('2', $antet['temei']);
    }

    /** Bifele formularului se scriu „D" sau „N", cum le cere schema ANAF. */
    public function test_bifele_se_scriu_cum_le_cere_schema(): void
    {
        $societate = $this->societate();

        $this->scrie($societate, $this->dateIntregi() + [
            'd300_bifa_cereale' => true,
            'd300_solicit_ramb' => true,
        ]);

        $antet = AntetD300::pentru($societate->fresh())['atribute'];

        $this->assertSame('D', $antet['bifa_cereale']);
        $this->assertSame('D', $antet['solicit_ramb']);
        $this->assertSame('N', $antet['bifa_mob']);

        // Pro-rata se scrie chiar si cand n-a fost completata: schema o cere.
        $this->assertSame('100.00', $antet['pro_rata']);
    }

    /** Felul decontului se alege dintre cele din schema; altceva nu se primeste. */
    public function test_felul_decontului_e_dintre_cele_din_schema(): void
    {
        $societate = $this->societate();

        $this->assertSame(['L', 'T', 'S', 'A'], array_keys(AntetD300::FELURI_DECONT));

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $this->scrie($societate, ['d300_tip_decont' => 'X']);
    }

    /** Fara fisa de firma nu se poate scrie antetul, si se spune tot ce lipseste. */
    public function test_fara_fisa_de_firma_lipseste_tot(): void
    {
        $antet = AntetD300::pentru(null);

        $this->assertFalse($antet['gata']);
        $this->assertSame([], $antet['atribute']);
        $this->assertContains('Adresa', $antet['lipsesc']);
    }
}
