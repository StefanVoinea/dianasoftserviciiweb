<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\CertificateController;
use App\Models\AnafCertificat;
use App\Models\AnafJurnal;
use App\Services\Anaf\Spv\CertificatService;
use App\Support\ContextCompanie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * PIN-ul scris de om ajunge în fereastra care îl așteaptă — și nicăieri altundeva.
 *
 * Facilitatea e pornită anume, pentru fiecare token în parte: e cheia celui
 * care îl ține, și el hotărăște dacă vrea să o poată trimite de la distanță.
 * Cât timp e stinsă, cererea e refuzată de server, oricine ar trimite-o.
 *
 * Codul trece o singură dată și nu se oprește nicăieri: nu în baza de date, nu
 * în jurnal, nu în răspuns. Aici se probează tocmai asta.
 */
class PinulTrimisDeLaDistantaTest extends TestCase
{
    protected const COMPANIE = 998;

    /** Codul de probă, căutat apoi peste tot ca să nu se găsească. */
    protected const CODUL = '481596';

    protected $certificat;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'POPESCU ION',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
            'bridge_url' => 'http://10.0.0.1:8099',
            'pin_de_la_distanta' => true,
        ]);

        $this->app->make(CertificatService::class)->foloseste($this->certificat);
    }

    protected function tearDown(): void
    {
        AnafJurnal::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function trimite(string $pin = self::CODUL)
    {
        $cerere = Request::create('/api/anaf-certificate/1/pin', 'POST', ['pin' => $pin]);

        return (new CertificateController())->trimitePin(
            $cerere,
            $this->certificat,
            $this->app->make(CertificatService::class)
        );
    }

    /** Codul ajunge la programul local, iar acolo în fereastră. */
    public function test_codul_ajunge_la_programul_local(): void
    {
        Http::fake(['*/pin/scrie' => Http::response(['scris' => true, 'motiv' => ''], 200)]);

        $raspuns = $this->trimite();

        $this->assertSame(200, $raspuns->getStatusCode());
        $this->assertTrue($raspuns->getData()->success);

        // Codul pleacă în corpul cererii, nu în adresă: adresele ajung în
        // jurnalele serverelor de web.
        Http::assertSent(function ($cerere) {
            return strpos($cerere->url(), self::CODUL) === false
                && ($cerere->data()['pin'] ?? null) === self::CODUL;
        });
    }

    /** Fără facilitatea pornită, cererea e refuzată — oricine ar trimite-o. */
    public function test_fara_alegerea_omului_cererea_e_refuzata(): void
    {
        $this->certificat->update(['pin_de_la_distanta' => false]);

        Http::fake(['*' => Http::response(['scris' => true], 200)]);

        $raspuns = $this->trimite();

        $this->assertSame(422, $raspuns->getStatusCode());
        Http::assertNothingSent();
    }

    /** Codul nu rămâne nicăieri: nici în token, nici în jurnal. */
    public function test_codul_nu_ramane_nicaieri(): void
    {
        Http::fake(['*/pin/scrie' => Http::response(['scris' => true, 'motiv' => ''], 200)]);

        $this->trimite();

        $inToken = json_encode($this->certificat->fresh()->toArray(), JSON_UNESCAPED_UNICODE);
        $this->assertStringNotContainsString(self::CODUL, $inToken);

        $inJurnal = json_encode(
            AnafJurnal::query()->toateCompaniile()->where('company_id', self::COMPANIE)->get()->toArray(),
            JSON_UNESCAPED_UNICODE
        );

        $this->assertStringNotContainsString(self::CODUL, $inJurnal);

        // Dar că s-a trimis, rămâne scris: cine a făcut-o și când.
        $this->assertStringContainsString('POPESCU ION', $inJurnal);
    }

    /** Un cod greșit se spune, dar tot nu ajunge în răspuns. */
    public function test_codul_gresit_se_spune_fara_sa_fie_repetat(): void
    {
        Http::fake(['*/pin/scrie' => Http::response([
            'scris' => false,
            'motiv' => 'fereastra a rămas deschisă — codul poate fi greșit',
        ], 200)]);

        $raspuns = $this->trimite();

        $this->assertSame(422, $raspuns->getStatusCode());
        $this->assertStringNotContainsString(self::CODUL, json_encode($raspuns->getData()));
        $this->assertStringContainsString('poate fi greșit', $raspuns->getData()->message);

        $this->assertSame('refuzat', $this->certificat->fresh()->pin_stare);
    }

    /** Un program local mai vechi o spune pe înțeles, nu cu un cod de eroare. */
    public function test_programul_vechi_o_spune_pe_inteles(): void
    {
        Http::fake(['*/pin/scrie' => Http::response(['eroare' => 'necunoscut'], 404)]);

        $raspuns = $this->trimite();

        $this->assertSame(422, $raspuns->getStatusCode());
        $this->assertStringContainsString('nu cunoaște', $raspuns->getData()->message);
    }
}
