<?php

namespace Tests\Unit;

use App\Models\Etransporttokens;
use App\Services\Anaf\Oauth\OauthAnaf;
use App\Services\Anaf\Oauth\OauthException;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * Autorizarea OAuth2 la ANAF: construirea cererii, legitimarea răspunsului și
 * valabilitatea tokenelor.
 */
class OauthAnafTest extends TestCase
{
    protected const CIF = 'TEST-OAUTH';

    protected function tearDown(): void
    {
        Etransporttokens::where('cui', self::CIF)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function serviciu(): OauthAnaf
    {
        return $this->app->make(OauthAnaf::class);
    }

    public function test_adresa_de_autorizare_contine_parametrii_ceruti_de_anaf(): void
    {
        $url = $this->serviciu()->urlAutorizare(self::CIF);

        $this->assertStringContainsString('anaf', $url);
        $this->assertStringContainsString('response_type=code', $url);
        $this->assertStringContainsString('client_id=', $url);
        $this->assertStringContainsString('redirect_uri=', $url);
        $this->assertStringContainsString('state=', $url);
    }

    /**
     * Fără datele de client primite de la ANAF, autorizarea nu poate porni:
     * utilizatorul trebuie să afle asta, nu să primească o adresă inutilă.
     */
    public function test_fara_date_de_client_autorizarea_este_refuzata_cu_explicatie(): void
    {
        config(['anaf.oauth.client_id' => '', 'anaf.oauth.client_secret' => '']);

        $this->assertFalse($this->serviciu()->configurat());

        $this->expectException(OauthException::class);
        $this->expectExceptionMessageMatches('/CLIENT_ANAF_ID/');
        $this->serviciu()->urlAutorizare(self::CIF);
    }

    public function test_adresa_de_retur_are_valoare_implicita_din_adresa_aplicatiei(): void
    {
        config(['anaf.oauth.redirect' => null, 'app.url' => 'https://exemplu.test/']);

        $this->assertSame(
            'https://exemplu.test/api/anaf-oauth/callback',
            $this->serviciu()->redirectUri()
        );
    }

    /** Starea semnată leagă răspunsul ANAF de cererea inițială. */
    public function test_starea_modificata_este_respinsa(): void
    {
        $url = $this->serviciu()->urlAutorizare(self::CIF);
        parse_str(parse_url($url, PHP_URL_QUERY), $parametri);

        $stareFalsificata = str_replace('.', 'x.', $parametri['state']);

        $this->expectException(OauthException::class);
        $this->serviciu()->preiaToken('cod-oarecare', $stareFalsificata);
    }

    public function test_starea_fara_semnatura_este_respinsa(): void
    {
        $this->expectException(OauthException::class);
        $this->serviciu()->preiaToken('cod-oarecare', base64_encode('{"cif":"123"}'));
    }

    public function test_fara_autorizare_nu_exista_token(): void
    {
        $this->assertNull($this->serviciu()->token(self::CIF));

        $stare = $this->serviciu()->stare(self::CIF);
        $this->assertFalse($stare['autorizat']);
    }

    public function test_tokenul_valabil_este_folosit_ca_atare(): void
    {
        Etransporttokens::create([
            'cui' => self::CIF,
            'company_id' => 1,
            'access_token' => 'token-valabil',
            'refresh_token' => 'reimprospatare',
            'data_obtinerii' => now(),
            'data_expirare' => now()->addDays(30),
        ]);

        ContextCompanie::pentru(1, function () {
            $this->assertSame('token-valabil', $this->serviciu()->token(self::CIF));

            $stare = $this->serviciu()->stare(self::CIF);
            $this->assertTrue($stare['autorizat']);
            $this->assertGreaterThan(0, $stare['zile_ramase']);
        });
    }

    /** Un token expirat fără posibilitate de reînnoire cere reautorizare. */
    public function test_tokenul_expirat_fara_reimprospatare_cere_reautorizare(): void
    {
        Etransporttokens::create([
            'cui' => self::CIF,
            'company_id' => 1,
            'access_token' => 'token-expirat',
            'refresh_token' => null,
            'data_obtinerii' => now()->subDays(100),
            'data_expirare' => now()->subDay(),
        ]);

        ContextCompanie::pentru(1, function () {
            $this->assertNull($this->serviciu()->token(self::CIF));
        });
    }
}
