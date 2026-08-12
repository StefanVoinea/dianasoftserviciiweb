<?php

namespace Tests\Unit;

use App\Services\Anaf\Bridge\Licente;
use App\Services\Anaf\Spv\CertificatService;
use Tests\TestCase;

/**
 * Jetonul cu care serverul isi dovedeste identitatea nu are voie sa expire in
 * mijlocul unei lucrari lungi.
 *
 * El e bun cinci minute — destul pentru orice cerere obisnuita. Dar aducerea
 * documentelor in flux e o singura cerere care tine o jumatate de ora si face
 * sute de apeluri catre programul local. Jetonul se facea o data, la pornire, si
 * se tinea minte pana la sfarsit.
 *
 * La un client cu doua sute cincizeci de entitati inrolate pe un certificat s-au
 * adus 390 de documente din 568, iar celelalte 148 au cazut toate cu aceeasi
 * vorba: „cererea nu poarta jeton semnat de server”. Cheile erau la locul lor —
 * dovada chiar cele 390 aduse inainte. Numai jetonul imbatranise.
 */
class JetonulNuExpiraInLucrareTest extends TestCase
{
    /** Cat timp e proaspat, se foloseste acelasi: semnarea nu e degeaba. */
    public function test_jetonul_proaspat_se_foloseste_mai_departe(): void
    {
        $serviciu = app(CertificatService::class);

        $intai = $this->jetonul($serviciu);
        $apoi = $this->jetonul($serviciu);

        if ($intai === null) {
            $this->markTestSkipped('Serverul acesta nu are chei de semnare.');
        }

        $this->assertSame($intai, $apoi, 'între două apeluri apropiate se folosește același jeton');
    }

    /** Cand a imbatranit, se face altul — fara sa astepte sa fie refuzat. */
    public function test_jetonul_imbatranit_se_reinnoieste(): void
    {
        $serviciu = app(CertificatService::class);

        $intai = $this->jetonul($serviciu);

        if ($intai === null) {
            $this->markTestSkipped('Serverul acesta nu are chei de semnare.');
        }

        // Se muta inapoi clipa pana la care jetonul mai e bun, ca si cum ar fi
        // trecut lucrarea de un sfert de ora.
        $ceas = new \ReflectionProperty($serviciu, 'jetonPanaLa');
        $ceas->setAccessible(true);
        $ceas->setValue($serviciu, time() - 1);

        $this->jetonul($serviciu);

        /*
         * Se cantareste ca s-a semnat din nou, nu ca a iesit alt text: doua
         * semnaturi facute in aceeasi secunda ies la fel, fiindca poarta
         * aceleasi clipe inauntru. Ce conteaza e ca s-a trecut pe drumul
         * refacerii, iar asta se vede din clipa pusa din nou inainte.
         */
        $this->assertGreaterThan(
            time(),
            $ceas->getValue($serviciu),
            'după ce s-a învechit, jetonul trebuie făcut din nou'
        );
    }

    /**
     * Se reinnoieste din vreme, nu chiar la expirare: intre semnare si sosirea
     * cererii la punte trece drumul pana la calculatorul clientului.
     */
    public function test_reinnoirea_se_face_inainte_de_expirare(): void
    {
        $serviciu = app(CertificatService::class);

        if ($this->jetonul($serviciu) === null) {
            $this->markTestSkipped('Serverul acesta nu are chei de semnare.');
        }

        $ceas = new \ReflectionProperty($serviciu, 'jetonPanaLa');
        $ceas->setAccessible(true);

        $rest = $ceas->getValue($serviciu) - time();

        $this->assertLessThan(
            Licente::JETON_SECUNDE,
            $rest,
            'jetonul trebuie schimbat înainte de a expira, nu după'
        );

        $this->assertGreaterThan(0, $rest);
    }

    /**
     * Sfatul din eroare nu mai trimite la rescrierea cheilor cand ele exista.
     *
     * „anaf:chei-bridge --forteaza” lasa fara valabilitate toate licentele emise
     * si cere kit nou pe fiecare calculator cu token. E ultimul lucru de facut
     * cand vina e doar a unui jeton expirat.
     */
    public function test_eroarea_nu_indeamna_la_rescrierea_cheilor_degeaba(): void
    {
        $sursa = file_get_contents(app_path('Http/Controllers/Api/PunteController.php'));

        $this->assertStringContainsString('$this->punte->areChei()', $sursa, 'cele două cazuri trebuie despărțite');

        // Cazul jetonului expirat: fara indemn la chei noi, si spunand limpede.
        $inceput = strpos($sursa, '!$this->punte->cerereDeLaServer($request)');
        $bucata = substr($sursa, $inceput, 400);

        $this->assertStringNotContainsString('anaf:chei-bridge', $bucata);
        $this->assertStringContainsString('nu le rescrieți', $bucata);
    }

    protected function jetonul(CertificatService $serviciu): ?string
    {
        $facerea = new \ReflectionMethod($serviciu, 'jetonul');
        $facerea->setAccessible(true);

        return $facerea->invoke($serviciu);
    }
}
