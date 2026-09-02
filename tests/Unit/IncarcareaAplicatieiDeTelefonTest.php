<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\ProgrameMobilController;
use App\Models\AnafJurnal;
use App\Models\User;
use App\Services\Mobil\ProgrameleDeTelefon;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Versiunea nouă a aplicației de telefon se pune pe server din filă.
 *
 * Prin git nu poate merge — arhiva e mare și se schimbă la fiecare compilare —,
 * iar la desfășurare nimeni nu se atinge de dosarul acela. Fără încărcarea de
 * aici, o îndreptare făcută azi ar ajunge la clienți numai dacă se urcă cineva
 * pe server și o copiază de mână.
 */
class IncarcareaAplicatieiDeTelefonTest extends TestCase
{
    protected const COMPANIE = 989;

    protected $controller;
    protected $omul;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(config('filesystems.default'));
        ContextCompanie::fixeaza(self::COMPANIE);

        // Un dosar gol pentru „ce vine cu codul": aici se probează doar urcarea.
        config(['mobil.dosar_din_cod' => 'tests/fixturi/mobil-gol']);

        $this->omul = User::create([
            'name' => 'cel care publică',
            'email' => 'publica@example.com',
            'password' => Hash::make(bin2hex(random_bytes(8))),
        ]);

        Auth::login($this->omul);
        config(['mobil.publica' => 'publica@example.com']);

        $this->controller = new ProgrameMobilController(new ProgrameleDeTelefon());
    }

    protected function tearDown(): void
    {
        AnafJurnal::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        Auth::logout();
        $this->omul->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function incarca(string $nume, string $aplicatia = 'spv_curier')
    {
        $cerere = Request::create('/api/mobil/' . $aplicatia . '/kit', 'POST');
        $cerere->files->set('arhiva', UploadedFile::fake()->createWithContent($nume, 'nu contează ce e înăuntru'));

        return $this->controller->incarca($cerere, $aplicatia);
    }

    protected function pune(string $fisier): void
    {
        Storage::put(ProgrameleDeTelefon::DOSAR . '/' . $fisier, 'de dinainte');
    }

    public function test_o_versiune_noua_se_aseaza_la_locul_ei(): void
    {
        $raspuns = $this->incarca('spv_curier-1.1.0+2.apk');

        $this->assertTrue($raspuns->getData(true)['success']);
        Storage::assertExists(ProgrameleDeTelefon::DOSAR . '/spv_curier-1.1.0+2.apk');
    }

    /**
     * O arhivă cu cod mai mic nu se primește.
     *
     * Telefonul se înnoiește numai când codul de pe server e mai mare decât al
     * lui. Pusă peste una nouă, o arhivă veche n-ar ajunge niciodată nicăieri —
     * și nimeni n-ar ști de ce, fiindcă totul ar părea în regulă.
     */
    public function test_o_versiune_mai_veche_este_oprita(): void
    {
        $this->pune('spv_curier-1.4.0+14.apk');

        $raspuns = $this->incarca('spv_curier-1.2.0+9.apk');

        $this->assertFalse($raspuns->getData(true)['success']);
        $this->assertSame(422, $raspuns->status());
        Storage::assertMissing(ProgrameleDeTelefon::DOSAR . '/spv_curier-1.2.0+9.apk');
    }

    /** Nici aceeași: codul trebuie să crească, altfel nimic nu se mișcă. */
    public function test_acelasi_cod_este_oprit(): void
    {
        $this->pune('spv_curier-1.4.0+14.apk');

        $this->assertFalse($this->incarca('spv_curier-1.4.1+14.apk')->getData(true)['success']);
    }

    /** Numele poartă versiunea, deci un nume care n-o spune nu se primește. */
    public function test_un_nume_fara_versiune_nu_se_primeste(): void
    {
        $raspuns = $this->incarca('app-release.apk');

        $this->assertFalse($raspuns->getData(true)['success']);
        $this->assertSame(422, $raspuns->status());
    }

    /** Și nici arhiva altei aplicații, oricât de nouă ar fi. */
    public function test_arhiva_altei_aplicatii_nu_se_primeste(): void
    {
        $this->assertFalse($this->incarca('etransport-9.0.0+90.apk')->getData(true)['success']);
    }

    /** O aplicație pe care n-o știm nu are unde să-și pună arhiva. */
    public function test_o_aplicatie_nestiuta_este_refuzata(): void
    {
        $raspuns = $this->incarca('altceva-1.0.0+1.apk', 'altceva');

        $this->assertSame(404, $raspuns->status());
    }

    /**
     * Cine nu e trecut anume în configurare nu poate publica nimic.
     *
     * Nu e un drept de administrator de firmă: arhiva pusă aici ajunge singură
     * pe telefoanele tuturor clienților, deci el privește pe toți deodată, nu
     * firma celui care apasă. Lista e goală din start — adică nimeni.
     */
    public function test_fara_dreptul_din_configurare_nu_se_publica(): void
    {
        config(['mobil.publica' => '']);

        $raspuns = $this->incarca('spv_curier-1.1.0+2.apk');

        $this->assertSame(403, $raspuns->status());
        Storage::assertMissing(ProgrameleDeTelefon::DOSAR . '/spv_curier-1.1.0+2.apk');
    }

    /** Nici altcineva decât cel trecut acolo. */
    public function test_alt_om_decat_cel_trecut_nu_publica(): void
    {
        config(['mobil.publica' => 'altcineva@example.com']);

        $this->assertSame(403, $this->incarca('spv_curier-1.1.0+2.apk')->status());
    }

    /** Lista poate ține mai mulți, despărțiți prin virgulă. */
    public function test_lista_poate_tine_mai_multi(): void
    {
        config(['mobil.publica' => 'unul@example.com, publica@example.com ,altul@example.com']);

        $this->assertTrue($this->incarca('spv_curier-1.1.0+2.apk')->getData(true)['success']);
    }
}
