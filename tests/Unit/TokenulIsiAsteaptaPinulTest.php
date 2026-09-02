<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\PinAsteaptaException;
use App\Services\Anaf\Spv\SpvException;
use App\Services\Anaf\Spv\Transport\BridgeTransport;
use App\Services\Anaf\Spv\SpvClient;
use App\Support\Aplicatia;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Când apelul cade fiindcă tokenul își așteaptă PIN-ul, se spune asta.
 *
 * Până acum arăta la fel cu un server picat sau cu o rețea proastă: omul căuta
 * vina în legătură, în vreme ce pe calculatorul clientului stătea deschisă o
 * fereastră care aștepta pe cineva să scrie codul.
 *
 * PIN-ul nu trece nici prin server, nici prin aplicație: se scrie în fereastra
 * lui, de omul care ține tokenul. De aici se află doar că fereastra e deschisă,
 * și se așteaptă până se închide.
 */
class TokenulIsiAsteaptaPinulTest extends TestCase
{
    protected const COMPANIE = 997;

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
        ]);

        $this->app->make(CertificatService::class)->foloseste($this->certificat);
    }

    protected function tearDown(): void
    {
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /**
     * Cât se așteaptă în probe.
     *
     * Puțin: aici nu se măsoară răbdarea, ci că apelul se reia. Cu răbdarea
     * adevărată de nouăzeci de secunde, proba ar ține un minut și jumătate
     * degeaba.
     */
    protected function setari(): array
    {
        return ['throttle_ms' => 0, 'pin_asteptare_secunde' => 4, 'pin_pas_secunde' => 1]
            + config('anaf.spv');
    }

    protected function client(): SpvClient
    {
        $setari = $this->setari();

        return new SpvClient(
            new BridgeTransport($setari, $this->app->make(CertificatService::class)),
            $setari,
            $this->app->make(CertificatService::class)
        );
    }

    /**
     * Programul local de probă: răspunde după adresa cerută.
     *
     * Se alege după adresă, nu după tipare puse unul peste altul: fereastra de
     * PIN și apelul la ANAF trec prin același program, iar un tipar prea larg
     * le-ar amesteca — și tocmai numărătoarea apelurilor e ce se probează aici.
     */
    protected function programulLocal(callable $laApel, callable $laFereastra): void
    {
        Http::fake(function ($cerere) use ($laApel, $laFereastra) {
            return strpos($cerere->url(), '/pin/fereastra') !== false
                ? $laFereastra()
                : $laApel();
        });
    }

    /** Ce răspunde programul local când tokenul își așteaptă PIN-ul. */
    protected function panaCuPin(): array
    {
        return [
            'eroare' => 'Apelul către ANAF a eșuat',
            'pin_asteapta' => true,
            'pin_fereastra' => 'Introduceți PIN-ul tokenului',
            'pin_proces' => 'SACSrv',
        ];
    }

    /** Pana de PIN se spune pe numele ei, cu numele tokenului care așteaptă. */
    public function test_pana_de_pin_se_spune_pe_numele_ei(): void
    {
        $this->programulLocal(
            function () {
                return Http::response($this->panaCuPin(), 502);
            },
            function () {
                // Fereastra rămâne deschisă: nimeni nu scrie codul.
                return Http::response(['deschisa' => true, 'titlu' => 'PIN', 'proces' => 'SACSrv'], 200);
            }
        );

        try {
            $this->client()->listaMesaje(1);
            $this->fail('trebuia să se plângă de PIN');
        } catch (PinAsteaptaException $e) {
            $this->assertStringContainsString('POPESCU ION', $e->getMessage());
            $this->assertStringContainsString('calculatorul clientului', $e->getMessage());
            $this->assertSame('SACSrv', $e->proces);
            $this->assertSame('Introduceți PIN-ul tokenului', $e->fereastra);
        }
    }

    /** Nu e o pană oarecare: cine prinde SpvException prinde și asta. */
    public function test_pana_de_pin_ramane_o_pana_spv(): void
    {
        $this->assertInstanceOf(SpvException::class, new PinAsteaptaException('proba'));
    }

    /**
     * După ce omul scrie PIN-ul, apelul se reia singur.
     *
     * Fără asta, omul scria codul și tot trebuia să apese din nou butonul — iar
     * de cele mai multe ori nici nu știa de ce căzuse.
     */
    public function test_apelul_se_reia_dupa_ce_pin_ul_a_fost_scris(): void
    {
        $cerute = 0;

        $this->programulLocal(
            function () use (&$cerute) {
                $cerute++;

                return $cerute === 1
                    ? Http::response($this->panaCuPin(), 502)
                    : Http::response(['mesaje' => []], 200);
            },
            function () {
                // Fereastra s-a închis: omul a scris codul.
                return Http::response(['deschisa' => false, 'titlu' => '', 'proces' => ''], 200);
            }
        );

        $iesit = $this->client()->listaMesaje(1);

        $this->assertSame(['mesaje' => []], $iesit);
        $this->assertSame(2, $cerute, 'apelul trebuia încercat din nou');
    }

    /** Un program local mai vechi, care nu cunoaște proba, nu ține cererea pe loc. */
    public function test_programul_vechi_nu_tine_cererea_pe_loc(): void
    {
        $this->programulLocal(
            function () {
                return Http::response($this->panaCuPin(), 502);
            },
            function () {
                // Program local mai vechi: nu cunoaște proba ferestrei.
                return Http::response(['eroare' => 'necunoscut'], 404);
            }
        );

        $inceput = microtime(true);

        try {
            $this->client()->listaMesaje(1);
            $this->fail('trebuia să se plângă');
        } catch (PinAsteaptaException $e) {
            // Se aștepta.
        }

        $this->assertLessThan(3, microtime(true) - $inceput, 'n-avea de ce să aștepte');
    }

    /**
     * Orice cerere către programul local spune de unde a plecat lucrarea.
     *
     * Prin tunel, cererea mai face un drum — serverul se cheamă pe sine, ca să
     * pună comanda în coadă —, iar la capătul acela nu mai e nici omul, nici
     * antetul lui: e alt proces, cu cererea lui. Ce nu pleacă scris de aici se
     * pierde acolo, iar comanda ajunge în coadă fără stăpân: atunci, când
     * tokenul își cere codul, el se cere în toate părțile deodată.
     */
    public function test_cererea_spune_de_unde_a_plecat(): void
    {
        Http::fake(['*' => Http::response(['mesaje' => []], 200)]);

        $this->client()->listaMesaje(1);

        Http::assertSent(function ($cerere) {
            return $cerere->hasHeader(Aplicatia::ANTETUL, Aplicatia::curenta());
        });
    }

    /**
     * După ce omul scrie codul, apelul picat se reia singur.
     *
     * Așa se întâmplă la cea dintâi lucrare de după pornirea calculatorului:
     * cererea pleacă, driverul cere codul chiar în mijlocul strângerii de mână
     * cu ANAF, iar cât omul îl scrie, sesiunea securizată se stinge — apelul
     * pică cu SEC_E_CONTEXT_EXPIRED, deși tokenul e acum dezlegat.
     *
     * Fără reluare, omul primea eroarea tocmai după ce făcuse tot ce i se
     * ceruse: scrisese codul, văzuse fereastra închizându-se, și totuși lucrarea
     * nu se făcea.
     */
    public function test_apelul_se_reia_dupa_ce_codul_a_fost_scris(): void
    {
        $apeluri = 0;

        Http::fake(function () use (&$apeluri) {
            $apeluri++;

            if ($apeluri > 1) {
                return Http::response(['mesaje' => []], 200);
            }

            // Cât ținea apelul, altcineva a scris codul în fereastră.
            $this->certificat->update([
                'pin_stare' => 'gata',
                'pin_verificat_la' => now(),
            ]);

            return Http::response([
                'eroare' => 'Apelul către ANAF a eșuat: legătura s-a rupt în timp ce se primea răspunsul'
                    . ' (SEC_E_CONTEXT_EXPIRED)',
            ], 502);
        });

        $this->client()->listaMesaje(1);

        $this->assertSame(2, $apeluri, 'Apelul trebuia încercat din nou, o dată.');
    }

    /** Se reia o singură dată: la a doua pană, eroarea se spune. */
    public function test_reluarea_nu_se_face_la_nesfarsit(): void
    {
        $apeluri = 0;

        Http::fake(function () use (&$apeluri) {
            $apeluri++;

            $this->certificat->update(['pin_stare' => 'gata', 'pin_verificat_la' => now()]);

            return Http::response(['eroare' => 'Apelul către ANAF a eșuat'], 502);
        });

        try {
            $this->client()->listaMesaje(1);
            $this->fail('trebuia să se plângă');
        } catch (SpvException $e) {
            // Se aștepta.
        }

        $this->assertSame(2, $apeluri, 'O singură reluare, nu mai multe.');
    }

    /** Fără o scriere de cod în rastimpul apelului, nu se reia nimic. */
    public function test_o_pana_obisnuita_nu_se_reia(): void
    {
        $apeluri = 0;

        Http::fake(function () use (&$apeluri) {
            $apeluri++;

            return Http::response(['eroare' => 'Nu se poate ajunge la ANAF'], 502);
        });

        try {
            $this->client()->listaMesaje(1);
            $this->fail('trebuia să se plângă');
        } catch (SpvException $e) {
            // Se aștepta.
        }

        $this->assertSame(1, $apeluri, 'O pană de rețea nu are de ce să fie încercată de două ori.');
    }

    /** Amprenta tokenului nu se pierde pe drum, acum că antetele se fac laolaltă. */
    public function test_amprenta_tokenului_pleaca_mai_departe(): void
    {
        Http::fake(['*' => Http::response(['mesaje' => []], 200)]);

        $this->client()->listaMesaje(1);

        Http::assertSent(function ($cerere) {
            return $cerere->hasHeader('X-Thumbprint', $this->certificat->thumbprint);
        });
    }
}
