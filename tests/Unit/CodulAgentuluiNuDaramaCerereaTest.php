<?php

namespace Tests\Unit;

use App\Support\ContextCompanie;
use App\Support\ContextUtilizator;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Codul agentului nu e un token al aplicatiei, si nu are voie sa darame cererea.
 *
 * Agentul de la client vine cu codul lui de instalare — un sir oarecare, nu un
 * JWT. Passport, incercand sa-l citeasca, se opreste cu „Malformed UTF-8
 * characters" sau „The JWT string must have two dots".
 *
 * Pana acum, intrebarea „cine e omul din spatele cererii?" lasa exceptia sa iasa
 * afara. Ea a cazut tocmai unde doare cel mai tare: in scrierea unei instiintari
 * de eroare. Poticnirea a luat locul erorii adevarate, care nu a mai ajuns la
 * nimeni — si in jurnal a ramas o urma de o suta de randuri despre JWT, in loc
 * de motivul pentru care programul de la client nu raspunsese.
 */
class CodulAgentuluiNuDaramaCerereaTest extends TestCase
{
    /**
     * Chiar poticnirea din productie: paznicul arunca, iar noi nu cadem cu el.
     *
     * Pe calculatorul de dezvoltare, un cod stricat nu ajunge sa fie citit ca
     * JWT — asa ca o proba care doar trimite un cod oarecare trece si fara
     * indreptare, adica nu pazeste nimic. Se pune deci paznicul sa arunce
     * intocmai ce a aruncat in productie.
     */
    public function test_paznicul_care_arunca_nu_darama_cererea(): void
    {
        \Illuminate\Support\Facades\Auth::shouldReceive('guard')
            ->with('api')
            ->andThrow(new \JsonException('Malformed UTF-8 characters, possibly incorrectly encoded', 5));

        \Illuminate\Support\Facades\Auth::shouldReceive('user')->andReturn(null);

        $this->assertNull(ContextUtilizator::curent());
    }

    /** Un cod care nu e JWT: nimeni conectat, si nimic daramat. */
    public function test_un_cod_care_nu_e_jeton_inseamna_doar_nimeni_conectat(): void
    {
        foreach ([
            'cod-de-instalare-fara-puncte',
            'a.b',
            'aaaa.bbbb.cccc',
            base64_encode(random_bytes(32)),
        ] as $cod) {
            $cerere = Request::create('/api/punte/1/spv/listaMesaje', 'GET');
            $cerere->headers->set('Authorization', 'Bearer ' . $cod);

            $this->app->instance('request', $cerere);

            $this->assertNull(
                ContextUtilizator::curent(),
                'codul „' . mb_substr($cod, 0, 20) . '..." n-are voie să dărâme cererea'
            );
        }
    }

    /** Nici intrebarea „e administratorul serviciului?" nu se poticneste. */
    public function test_nici_intrebarea_de_administrator_nu_se_poticneste(): void
    {
        $cerere = Request::create('/api/punte/1/spv/listaMesaje', 'GET');
        $cerere->headers->set('Authorization', 'Bearer cod-de-instalare-fara-puncte');

        $this->app->instance('request', $cerere);

        $this->assertFalse(ContextCompanie::esteAdministrator());
    }

    /**
     * Caile pe care se cauta omul sunt toate imblanzite: cine intreaba primeste
     * un raspuns, nu o exceptie.
     */
    public function test_cautarea_omului_prinde_orice(): void
    {
        $sursa = file_get_contents(app_path('Support/ContextUtilizator.php'));

        $inceput = strpos($sursa, 'public static function curent');
        $bucata = substr($sursa, $inceput, 700);

        $this->assertSame(
            2,
            substr_count($bucata, 'catch (\\Throwable $e)'),
            'și paznicul aplicației, și cel obișnuit trebuie ținuți în frâu'
        );

        // Iar celalalt loc care intreba de-a dreptul trece acum pe aici.
        $companie = file_get_contents(app_path('Support/ContextCompanie.php'));

        $this->assertStringContainsString('ContextUtilizator::curent()', $companie);
        $this->assertStringNotContainsString(
            "Auth::guard('api')->user() ?: Auth::user()",
            $companie,
            'a rămas o întrebare pusă de-a dreptul'
        );
    }
}
