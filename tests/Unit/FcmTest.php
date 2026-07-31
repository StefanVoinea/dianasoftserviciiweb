<?php

namespace Tests\Unit;

use App\Services\Notificari\Fcm;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Trimiterea alertelor prin Firebase: autorizarea cu cont de serviciu,
 * recunoașterea tokenelor moarte și comportarea fără configurare.
 */
class FcmTest extends TestCase
{
    protected $caleCont;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::forget('fcm.token_acces');
    }

    protected function tearDown(): void
    {
        if ($this->caleCont && file_exists($this->caleCont)) {
            unlink($this->caleCont);
        }

        Cache::forget('fcm.token_acces');

        parent::tearDown();
    }

    protected function serviciu(): Fcm
    {
        return $this->app->make(Fcm::class);
    }

    /**
     * Cheie RSA generată pe loc, ca JWT-ul să fie semnat cu adevărat.
     *
     * Pe Windows, PHP nu găsește singur openssl.cnf; de aceea se încearcă și
     * căile obișnuite înainte de a renunța.
     */
    protected function cheiePrivata(): ?string
    {
        $optiuni = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];

        $configuratii = array_filter([
            null,
            getenv('OPENSSL_CONF') ?: null,
            dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'ssl'
                . DIRECTORY_SEPARATOR . 'openssl.cnf',
        ]);

        foreach ($configuratii as $config) {
            $cheie = @openssl_pkey_new($config === null ? $optiuni : $optiuni + ['config' => $config]);

            if ($cheie === false) {
                continue;
            }

            $privata = null;
            $exportata = $config === null
                ? @openssl_pkey_export($cheie, $privata)
                : @openssl_pkey_export($cheie, $privata, null, ['config' => $config]);

            if ($exportata && is_string($privata)) {
                return $privata;
            }
        }

        return null;
    }

    protected function pregatesteContul(): void
    {
        $privata = $this->cheiePrivata();

        if ($privata === null) {
            $this->markTestSkipped('OpenSSL nu poate genera chei în acest mediu (lipsește openssl.cnf).');
        }

        $this->caleCont = storage_path('framework/testing/cont-serviciu-' . uniqid() . '.json');

        if (!is_dir(dirname($this->caleCont))) {
            mkdir(dirname($this->caleCont), 0777, true);
        }

        file_put_contents($this->caleCont, json_encode([
            'type' => 'service_account',
            'project_id' => 'proiect-test',
            'client_email' => 'alerte@proiect-test.iam.gserviceaccount.com',
            'private_key' => $privata,
            'token_uri' => 'https://oauth2.googleapis.com/token',
        ]));

        config([
            'firebase.proiect' => 'proiect-test',
            'firebase.cont_serviciu' => $this->caleCont,
        ]);
    }

    public function test_fara_configurare_serviciul_nu_este_activ(): void
    {
        config(['firebase.proiect' => null, 'firebase.cont_serviciu' => null]);

        $this->assertFalse($this->serviciu()->activ());
    }

    /** Fără Firebase nu se încearcă nicio cerere — alertele rămân pe verificarea periodică. */
    public function test_fara_configurare_nu_se_trimite_nimic(): void
    {
        config(['firebase.proiect' => null, 'firebase.cont_serviciu' => null]);

        Http::fake();

        $this->assertSame(Fcm::ESEC, $this->serviciu()->trimite('token', 'Titlu', 'Corp'));

        Http::assertNothingSent();
    }

    public function test_alerta_este_trimisa_cu_token_de_acces_si_datele_asteptate(): void
    {
        $this->pregatesteContul();

        Http::fake([
            'oauth2.googleapis.com/*' => Http::response(['access_token' => 'acces-123', 'expires_in' => 3600]),
            'fcm.googleapis.com/*' => Http::response(['name' => 'projects/proiect-test/messages/1']),
        ]);

        $rezultat = $this->serviciu()->trimite(
            'token-telefon',
            'Dosar 1234/3/2024',
            'Termen nou: 10.02.2026',
            ['modificare_id' => 42]
        );

        $this->assertSame(Fcm::TRIMIS, $rezultat);

        // JWT-ul trimis lui Google trebuie să aibă trei părți și scopul corect.
        Http::assertSent(function (Request $cerere) {
            if (!str_contains($cerere->url(), 'oauth2.googleapis.com')) {
                return false;
            }

            $date = $cerere->data();
            $parti = explode('.', $date['assertion'] ?? '');

            if (count($parti) !== 3) {
                return false;
            }

            $pretentii = json_decode(base64_decode(strtr($parti[1], '-_', '+/')), true);

            return $date['grant_type'] === 'urn:ietf:params:oauth:grant-type:jwt-bearer'
                && $pretentii['scope'] === 'https://www.googleapis.com/auth/firebase.messaging'
                && $pretentii['iss'] === 'alerte@proiect-test.iam.gserviceaccount.com';
        });

        Http::assertSent(function (Request $cerere) {
            if (!str_contains($cerere->url(), 'fcm.googleapis.com')) {
                return false;
            }

            $mesaj = $cerere->data()['message'];

            return str_contains($cerere->url(), 'projects/proiect-test/messages:send')
                && $cerere->hasHeader('Authorization', 'Bearer acces-123')
                && $mesaj['token'] === 'token-telefon'
                && $mesaj['notification']['title'] === 'Dosar 1234/3/2024'
                && $mesaj['android']['notification']['channel_id'] === 'modificari_dosare'
                // Valorile din „data” trebuie să fie text, altfel FCM refuză mesajul.
                && $mesaj['data']['modificare_id'] === '42';
        });
    }

    /** Tokenul de acces se cere o singură dată, apoi se ia din cache. */
    public function test_tokenul_de_acces_nu_se_cere_la_fiecare_alerta(): void
    {
        $this->pregatesteContul();

        Http::fake([
            'oauth2.googleapis.com/*' => Http::response(['access_token' => 'acces-123', 'expires_in' => 3600]),
            'fcm.googleapis.com/*' => Http::response(['name' => 'ok']),
        ]);

        $this->serviciu()->trimite('token-1', 'A', 'B');
        $this->serviciu()->trimite('token-2', 'A', 'B');

        $cereriToken = 0;

        foreach (Http::recorded() as $pereche) {
            if (str_contains($pereche[0]->url(), 'oauth2.googleapis.com')) {
                $cereriToken++;
            }
        }

        $this->assertSame(1, $cereriToken);
    }

    /**
     * Aplicația dezinstalată sau tokenul înlocuit: dispozitivul trebuie scos,
     * altfel s-ar reîncerca la nesfârșit, din oră în oră.
     */
    public function test_tokenul_mort_este_raportat_ca_atare(): void
    {
        $this->pregatesteContul();

        Http::fake([
            'oauth2.googleapis.com/*' => Http::response(['access_token' => 'acces-123', 'expires_in' => 3600]),
            'fcm.googleapis.com/*' => Http::response([
                'error' => ['status' => 'NOT_FOUND', 'message' => 'Requested entity was not found.'],
            ], 404),
        ]);

        $this->assertSame(Fcm::TOKEN_INVALID, $this->serviciu()->trimite('token-mort', 'A', 'B'));
    }

    public function test_tokenul_neinregistrat_este_raportat_ca_atare(): void
    {
        $this->pregatesteContul();

        Http::fake([
            'oauth2.googleapis.com/*' => Http::response(['access_token' => 'acces-123', 'expires_in' => 3600]),
            'fcm.googleapis.com/*' => Http::response([
                'error' => ['status' => 'INVALID_ARGUMENT', 'message' => 'The registration token is not a valid FCM registration token'],
            ], 400),
        ]);

        $this->assertSame(Fcm::TOKEN_INVALID, $this->serviciu()->trimite('token-stricat', 'A', 'B'));
    }

    /** O pană de moment nu e motiv să ștergem dispozitivul. */
    public function test_eroarea_temporara_nu_inseamna_token_invalid(): void
    {
        $this->pregatesteContul();

        Http::fake([
            'oauth2.googleapis.com/*' => Http::response(['access_token' => 'acces-123', 'expires_in' => 3600]),
            'fcm.googleapis.com/*' => Http::response(['error' => ['status' => 'UNAVAILABLE']], 503),
        ]);

        $this->assertSame(Fcm::ESEC, $this->serviciu()->trimite('token-bun', 'A', 'B'));
    }

    public function test_cheia_refuzata_de_google_opreste_trimiterea(): void
    {
        $this->pregatesteContul();

        Http::fake([
            'oauth2.googleapis.com/*' => Http::response(['error' => 'invalid_grant'], 400),
        ]);

        $this->assertSame(Fcm::ESEC, $this->serviciu()->trimite('token', 'A', 'B'));
    }
}
