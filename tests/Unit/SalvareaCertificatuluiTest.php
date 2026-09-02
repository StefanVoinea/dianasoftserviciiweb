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
 * Salvarea unui certificat scrie ce i s-a cerut și nu așteaptă după nimeni.
 *
 * Două lucruri se probează aici, amândouă văzute pe viu.
 *
 * Întâi, bifa prin care omul deschide trimiterea PIN-ului de la distanță: ea
 * era primită și cântărită, dar nu ajungea printre câmpurile scrise, așa că
 * salvarea trecea cu bine și bifa se întorcea stinsă. Fără ea, tokenul care
 * își așteaptă codul nu se arată nicăieri — deci tocmai facilitatea pentru care
 * era pusă bifa rămânea stinsă pentru totdeauna.
 *
 * Apoi, așteptarea: la fiecare salvare se cerea licența calculatorului, iar
 * cererea aceea vorbește cu programul local. El servește o cerere pe rând și,
 * prins într-o fereastră de PIN, tace minute în șir. Ieșea că tocmai bifa prin
 * care fereastra putea fi dezlegată nu se putea salva cât timp fereastra era
 * deschisă. Licența se cere de acum numai când s-a schimbat ceva din legătura
 * cu calculatorul.
 */
class SalvareaCertificatuluiTest extends TestCase
{
    protected const COMPANIE = 991;

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
            'valabil_pana_la' => now()->addYear(),
            'bridge_url' => 'http://10.0.0.7:8099',
            'bridge_token' => 'cod-de-proba',
            'pin_de_la_distanta' => false,
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

    protected function salveaza(array $date)
    {
        $cerere = Request::create('/api/anaf-certificate/' . $this->certificat->id, 'PUT', $date);

        return (new CertificateController())->update($cerere, $this->certificat);
    }

    /** Cererile prin care se innoieste licenta calculatorului. */
    protected function esteDespreLicenta(string $url): bool
    {
        return strpos($url, '/identitate') !== false || strpos($url, '/licenta') !== false;
    }

    public function test_bifa_pinului_de_la_distanta_se_scrie(): void
    {
        Http::fake();

        $this->salveaza(['pin_de_la_distanta' => true]);

        $this->assertTrue(
            (bool) $this->certificat->fresh()->pin_de_la_distanta,
            'Bifa trimisă de om trebuie să se regăsească în baza de date.'
        );
    }

    public function test_bifa_se_poate_si_stinge(): void
    {
        Http::fake();

        $this->certificat->update(['pin_de_la_distanta' => true]);

        $this->salveaza(['pin_de_la_distanta' => false]);

        $this->assertFalse(
            (bool) $this->certificat->fresh()->pin_de_la_distanta,
            'Stinsă, bifa trebuie să rămână stinsă: altfel omul nu se mai poate răzgândi.'
        );
    }

    public function test_o_bifa_oarecare_nu_asteapta_dupa_programul_local(): void
    {
        Http::fake();

        $raspuns = $this->salveaza(['pin_de_la_distanta' => true]);

        Http::assertNotSent(function ($cerere) {
            return $this->esteDespreLicenta($cerere->url());
        });

        $this->assertFalse(
            (bool) $raspuns->getData(true)['licenta']['emisa'],
            'Fără o schimbare a legăturii, nu se emite nicio licență.'
        );
    }

    public function test_schimbarea_calculatorului_cere_licenta_pe_loc(): void
    {
        /*
         * Aici, dimpotrivă, așteptarea e binevenită: calculatorul tocmai
         * configurat trebuie să primească licența acum, nu la noapte, ca omul
         * să nu salveze și apoi să dea de „fără licență" la prima operație.
         */
        Http::fake(['*' => Http::response(['masina' => 'PROBA-01'], 200)]);

        $this->salveaza(['bridge_url' => 'http://10.0.0.8:8099']);

        Http::assertSent(function ($cerere) {
            return $this->esteDespreLicenta($cerere->url());
        });
    }
}
