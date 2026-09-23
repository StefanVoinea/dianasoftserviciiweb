<?php

namespace Tests\Unit;

use App\Exceptions\Handler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Tests\TestCase;

/**
 * Care erori merită un email și care nu.
 *
 * O cutie poștală plină de erori care nu înseamnă nimic e mai rea decât una
 * goală: erorile adevărate se pierd printre ele. Nu tot ce se oprește e o
 * defecțiune — o pagină care nu există, un formular greșit, o sesiune expirată
 * sunt viața de zi cu zi.
 *
 * Partea care ne-a costat: jetoanele. Passport prinde singur poticnirea, dar o
 * dă raportorului înainte s-o înghită, așa că o filă lăsată deschisă peste
 * noapte — care întreabă din minut în minut dacă s-a deschis fereastra de PIN —
 * trimitea câte o înștiințare la fiecare întrebare de după expirarea jetonului.
 */
class CareEroriMeritaInstiintareTest extends TestCase
{
    /** Judecata stă într-o metodă ferită; aici se cere părerea ei. */
    protected function merita(\Throwable $e): bool
    {
        $handler = new Handler($this->app);

        $metoda = new \ReflectionMethod($handler, 'meritaInstiintare');
        $metoda->setAccessible(true);

        return $metoda->invoke($handler, $e);
    }

    /** @test */
    public function jetonul_expirat_nu_merita_email()
    {
        $this->assertFalse($this->merita(OAuthServerException::accessDenied('Access token could not be verified')));
    }

    /** @test */
    public function nici_jetonul_lipsa_sau_stricat()
    {
        foreach ([
            OAuthServerException::accessDenied('Missing "Authorization" header'),
            OAuthServerException::accessDenied('The JWT string must have two dots'),
            OAuthServerException::invalidCredentials(),
            OAuthServerException::invalidRefreshToken('a expirat'),
        ] as $eroare) {
            $this->assertFalse($this->merita($eroare), $eroare->getMessage());
        }
    }

    /** O eroare a serverului din aceeași familie rămâne de spus. */
    /** @test */
    public function ce_cere_un_raspuns_de_server_merita_email()
    {
        $eroare = OAuthServerException::serverError('cheile OAuth lipsesc de pe disc');

        $this->assertGreaterThanOrEqual(500, $eroare->getHttpStatusCode());
        $this->assertTrue($this->merita($eroare));
    }

    /** @test */
    public function formularul_gresit_si_sesiunea_expirata_nu_merita_email()
    {
        $this->assertFalse($this->merita(new AuthenticationException()));
        $this->assertFalse($this->merita(ValidationException::withMessages(['cui' => 'lipsește'])));
    }

    /** @test */
    public function pagina_care_nu_exista_nu_merita_email_dar_serverul_picat_da()
    {
        $this->assertFalse($this->merita(new NotFoundHttpException()));
        $this->assertTrue($this->merita(new ServiceUnavailableHttpException()));
    }

    /** Ce nu se încadrează nicăieri rămâne de spus: mai bine un email în plus. */
    /** @test */
    public function o_eroare_oarecare_merita_email()
    {
        $this->assertTrue($this->merita(new \RuntimeException('s-a stricat ceva')));
    }
}
