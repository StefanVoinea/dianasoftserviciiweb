<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\CertificateController;
use App\Models\AnafCertificat;
use App\Models\AnafJurnal;
use App\Services\Anaf\Declaratii\MonitorizareFolder;
use App\Services\Anaf\Spv\CertificatService;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Certificatul cu care clientul nu lucreaza in relatia cu SPV.
 *
 * Pe tokenele lui stau si certificate straine de treaba asta — ale altei firme,
 * ori ramase de la cineva plecat. Scos din uz, certificatul ramane in lista cu
 * tot ce s-a strans pe el, dar aplicatia nu-l mai ia in seama nicaieri.
 */
class CertificatScosDinUzTest extends TestCase
{
    protected const COMPANIE = 991;

    /** Certificatul care se scoate din uz. */
    protected $certificat;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'IONESCU MARIA',
            'activ' => true,
            'implicit' => true,
            'valabil_pana_la' => now()->addYear(),
            'bridge_url' => 'http://192.168.1.42:8099',
            'bridge_token' => 'cod-de-proba',
            'monitorizare_cale' => 'D:\\Declaratii de semnat',
            'monitorizare_activa' => true,
        ]);

        // Nicio operatie nu are voie sa plece cu adevarat spre calculatorul clientului.
        Http::fake();
    }

    protected function tearDown(): void
    {
        AnafJurnal::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /** Butonul il scoate din uz si il repune, de cate ori e nevoie. */
    public function test_comutarea_merge_in_ambele_sensuri()
    {
        $controller = new CertificateController();

        $controller->comutaActiv($this->certificat);
        $this->assertFalse((bool) $this->certificat->fresh()->activ);

        $controller->comutaActiv($this->certificat->fresh());
        $this->assertTrue((bool) $this->certificat->fresh()->activ);
    }

    /**
     * Implicit inseamna „cu asta se lucreaza cand omul n-are altul atribuit".
     * Scos din uz, certificatul nu mai poate fi acela — altfel ar ramane o bifa
     * nevazuta, care impiedica alt certificat sa-i ia locul.
     */
    public function test_scoaterea_din_uz_ii_ia_si_calitatea_de_implicit()
    {
        (new CertificateController())->comutaActiv($this->certificat);

        $dupa = $this->certificat->fresh();

        $this->assertFalse((bool) $dupa->activ);
        $this->assertFalse((bool) $dupa->implicit);
    }

    /** Repunerea in uz nu-l face la loc implicit: alegerea aceea e a omului. */
    public function test_repunerea_in_uz_nu_il_face_la_loc_implicit()
    {
        $controller = new CertificateController();

        $controller->comutaActiv($this->certificat);
        $controller->comutaActiv($this->certificat->fresh());

        $this->assertTrue((bool) $this->certificat->fresh()->activ);
        $this->assertFalse((bool) $this->certificat->fresh()->implicit);
    }

    /** Se scrie in jurnal, ca sa se stie cine si cand a scos certificatul din uz. */
    public function test_scoaterea_din_uz_se_insemneaza_in_jurnal()
    {
        (new CertificateController())->comutaActiv($this->certificat);

        $insemnare = AnafJurnal::query()->toateCompaniile()
            ->where('company_id', self::COMPANIE)
            ->where('actiune', 'certificat_activare')
            ->latest('id')
            ->first();

        $this->assertNotNull($insemnare);
        $this->assertStringContainsString('A scos din uz', $insemnare->descriere);
        $this->assertStringContainsString('IONESCU MARIA', $insemnare->descriere);
    }

    /** Nu mai e ales pentru operatii nici macar cand nu exista altul. */
    public function test_nu_mai_este_ales_pentru_operatii()
    {
        (new CertificateController())->comutaActiv($this->certificat);

        $ales = app(CertificatService::class)->activ();

        $this->assertNotEquals($this->certificat->id, optional($ales)->id);
    }

    /**
     * Dosarul urmarit ramane bifat — bifa se pastreaza pentru ziua cand va fi
     * repus — dar pana atunci nimeni nu umbla in calculatorul lui.
     */
    public function test_dosarul_urmarit_nu_mai_este_citit()
    {
        (new CertificateController())->comutaActiv($this->certificat);

        $certificat = $this->certificat->fresh();

        // Bifa dosarului urmarit nu s-a pierdut...
        $this->assertTrue((bool) $certificat->monitorizare_activa);

        $rezultat = app(MonitorizareFolder::class)->pentruCertificat($certificat);

        // ...dar nu s-a cautat nimic in el, si nici nu s-a chemat calculatorul.
        $this->assertSame(0, $rezultat['gasite']);
        $this->assertSame([], $rezultat['erori']);

        Http::assertNotSent(function ($cerere) use ($certificat) {
            return strpos($cerere->url(), parse_url($certificat->bridge_url, PHP_URL_HOST)) !== false;
        });
    }
}
