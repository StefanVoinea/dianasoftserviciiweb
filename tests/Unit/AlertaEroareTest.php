<?php

namespace Tests\Unit;

use App\Mail\AlertaEroareSpvEmail;
use App\Models\AnafCertificat;
use App\Models\Company;
use App\Services\Anaf\AlertaEroare;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Instiintarea celui care tine aplicatia, cand ceva se strica.
 *
 * Un mesaj de eroare fara adresa nu foloseste nimanui: trebuie sa spuna al cui
 * client e, cu ce certificat s-a lucrat si ce e de facut. Si nu trebuie sa
 * inece cutia postala: o pana care se repeta de o suta de ori ar trimite o suta
 * de emailuri tocmai cand cutia trebuie citita.
 */
class AlertaEroareTest extends TestCase
{
    protected $client;
    protected $certificat;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        Cache::flush();

        $this->client = Company::create(['denumire' => 'ALERTE PROBA SRL', 'cui' => '99000777']);

        ContextCompanie::fixeaza($this->client->id);

        $this->certificat = AnafCertificat::create([
            'company_id' => $this->client->id,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'POPESCU ION',
            'activ' => true,
            'mod_legatura' => 'tunel',
            'valabil_pana_la' => now()->addYear(),
        ]);
    }

    protected function tearDown(): void
    {
        AnafCertificat::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
        ContextCompanie::elibereaza();
        $this->client->delete();

        parent::tearDown();
    }

    /** Instiintarea poarta clientul, certificatul si ce se lucra. */
    public function test_instiintarea_spune_al_cui_e_si_cu_ce_s_a_lucrat()
    {
        $trimisa = AlertaEroare::trimite('programul de la client', 'Programul local nu a răspuns.', [
            'company_id' => $this->client->id,
            'certificat_id' => $this->certificat->id,
            'comanda' => 'GET /spv/listaMesaje',
        ]);

        $this->assertTrue($trimisa);

        Mail::assertSent(AlertaEroareSpvEmail::class, function (AlertaEroareSpvEmail $email) {
            return strpos($email->date['client'], 'ALERTE PROBA SRL') !== false
                && strpos($email->date['certificat'], 'POPESCU ION') !== false
                && strpos($email->date['certificat'], 'tunel') !== false
                && $email->date['context']['comanda'] === 'GET /spv/listaMesaje';
        });
    }

    /** Aceeasi eroare nu se trimite de doua ori inainte de racire. */
    public function test_aceeasi_eroare_nu_se_repeta_pana_la_racire()
    {
        $this->assertTrue(AlertaEroare::trimite('punte', 'curl 56: legătura s-a rupt'));
        $this->assertFalse(AlertaEroare::trimite('punte', 'curl 56: legătura s-a rupt'));

        Mail::assertSent(AlertaEroareSpvEmail::class, 1);
    }

    /**
     * Racirea prinde acelasi fel de eroare, nu doar acelasi text: numerele
     * dinauntru se schimba de la o data la alta.
     */
    public function test_racirea_prinde_acelasi_fel_de_eroare()
    {
        $this->assertTrue(AlertaEroare::trimite('punte', 'Comanda 145 nu a putut fi dusă la capăt'));
        $this->assertFalse(AlertaEroare::trimite('punte', 'Comanda 981 nu a putut fi dusă la capăt'));
    }

    /** O eroare de alt fel trece: racirea nu inchide gura tuturor. */
    public function test_alta_eroare_trece()
    {
        $this->assertTrue(AlertaEroare::trimite('punte', 'curl 56: legătura s-a rupt'));
        $this->assertTrue(AlertaEroare::trimite('punte', 'Coloana lipsește din baza de date'));

        Mail::assertSent(AlertaEroareSpvEmail::class, 2);
    }

    /** Fiecare fel de eroare cunoscut isi are reteaua lui. */
    public function test_rezolvarile_cunoscute_sunt_spuse_pe_nume()
    {
        $perechi = [
            'schannel: SEC_E_CONTEXT_EXPIRED' => 'PIN',
            'Programul local nu a răspuns (curl 7)' => 'firewall',
            'SQLSTATE[42S22]: Unknown column' => 'migrate',
            'HTTP 429 — prea multe cereri' => 'limita',
            'The argument pdf-info.ps1 does not exist' => 'kit nou',
        ];

        foreach ($perechi as $eroare => $cuvant) {
            $this->assertStringContainsString(
                $cuvant,
                AlertaEroare::rezolvarea($eroare),
                'Pentru „' . $eroare . '" nu se spune ce e de făcut.'
            );
        }
    }

    /**
     * Emailul chiar se scrie.
     *
     * Trimiterea e imbracata in try/catch — o instiintare picata n-are voie sa
     * darame lucrarea — asa ca un sablon stricat ar trece nevazut: nu s-ar mai
     * trimite nimic, iar noi am crede ca pur si simplu nu s-a stricat nimic.
     */
    public function test_emailul_se_scrie_intreg()
    {
        // Se scrie chiar sablonul, nu prin postas: acela e inlocuit cu unul de
        // proba in setUp si nu stie sa randeze.
        $date = [
            'unde' => 'programul de la client',
            'mesaj' => 'Programul local nu a răspuns.',
            'cand' => now()->format('d.m.Y H:i:s'),
            'client' => 'ALERTE PROBA SRL (#1)',
            'certificat' => 'POPESCU ION (tunel)',
            'utilizator' => 'Cineva <cineva@example.com>',
            'context' => ['comanda' => 'GET /spv/listaMesaje'],
            'rezolvare' => AlertaEroare::rezolvarea('Programul local nu a răspuns.'),
            'urma' => "undeva.php:12\n#0 altundeva.php(3)",
        ];

        $scris = (string) app(\Illuminate\Mail\Markdown::class)->render('emails.alertaeroarespv', ['date' => $date]);

        $this->assertStringContainsString('ALERTE PROBA SRL', $scris);
        $this->assertStringContainsString('POPESCU ION', $scris);
        $this->assertStringContainsString('GET /spv/listaMesaje', $scris);
        $this->assertStringContainsString('Ce e de făcut', $scris);
    }

    /** Ce nu se cunoaste nu ramane fara raspuns: se spune unde sa se caute. */
    public function test_eroarea_necunoscuta_trimite_la_jurnal()
    {
        $this->assertStringContainsString('diagnoza', AlertaEroare::rezolvarea('ceva cu totul nou'));
    }
}
