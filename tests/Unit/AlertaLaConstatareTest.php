<?php

namespace Tests\Unit;

use App\Mail\AlertaConstatareSpvEmail;
use App\Models\AlertaMesajSpv;
use App\Models\SpvMesaj;
use App\Services\Anaf\Spv\AlerteMesaje;
use App\Services\Anaf\Spv\SolicitareService;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Instiintarea pleaca pentru ce s-a citit in document, nu pentru sosirea lui.
 *
 * Aplicatia stia de mult care firme au vectorul fiscal modificat — compara
 * randurile cu cele de la citirea dinainte — si care au restante. Dar scria
 * raspunsul intr-o coloana din tabel, iar la doua sute cincizeci de entitati
 * coloana aceea nu se citeste niciodata.
 *
 * Alertele de pana acum nu ajutau: ele se uita la felul hartiei. O alerta pe
 * „vector fiscal” ar fi trimis doua sute cincizeci de emailuri, desi numai la
 * cateva s-a schimbat ceva — zgomot din care nu se mai citeste nimic.
 */
class AlertaLaConstatareTest extends TestCase
{
    protected const COMPANIE = 993;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);
        Mail::fake();
    }

    protected function tearDown(): void
    {
        AlertaMesajSpv::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function alerta(array $peste = []): AlertaMesajSpv
    {
        return AlertaMesajSpv::create(array_merge([
            'company_id' => self::COMPANIE,
            'email' => 'contabil@firma.ro',
            'activ' => true,
        ], $peste));
    }

    /** Constatarea ajunge pe email, la adresa trecuta pe firma. */
    public function test_vectorul_modificat_ajunge_pe_email(): void
    {
        $this->alerta(['doar_cand' => AlertaMesajSpv::CAND_VECTOR_MODIFICAT, 'cif' => '15208744']);

        $plecate = app(AlerteMesaje::class)->pentruConstatare(
            AlertaMesajSpv::CAND_VECTOR_MODIFICAT,
            '15208744',
            null,
            SolicitareService::VECTOR_MODIFICAT
        );

        $this->assertSame(1, $plecate);

        // Prin coada, nu de-a dreptul: preluarea nu asteapta serverul de email.
        Mail::assertQueued(AlertaConstatareSpvEmail::class, function ($email) {
            return $email->hasTo('contabil@firma.ro')
                && $email->cif === '15208744';
        });
    }

    /** Alerta altei firme nu se atinge. */
    public function test_alerta_altei_firme_nu_pleaca(): void
    {
        $this->alerta(['doar_cand' => AlertaMesajSpv::CAND_VECTOR_MODIFICAT, 'cif' => '99999999']);

        $plecate = app(AlerteMesaje::class)->pentruConstatare(
            AlertaMesajSpv::CAND_VECTOR_MODIFICAT,
            '15208744',
            null,
            SolicitareService::VECTOR_MODIFICAT
        );

        $this->assertSame(0, $plecate);
        Mail::assertNothingQueued();
    }

    /** Restantele si vectorul sunt doua constatari deosebite. */
    public function test_constatarile_nu_se_incurca_intre_ele(): void
    {
        $this->alerta(['doar_cand' => AlertaMesajSpv::CAND_RESTANTE]);

        $this->assertSame(0, app(AlerteMesaje::class)->pentruConstatare(
            AlertaMesajSpv::CAND_VECTOR_MODIFICAT,
            '15208744',
            null,
            SolicitareService::VECTOR_MODIFICAT
        ));

        $this->assertSame(1, app(AlerteMesaje::class)->pentruConstatare(
            AlertaMesajSpv::CAND_RESTANTE,
            '15208744',
            null,
            SolicitareService::RESTANTE
        ));
    }

    /**
     * Miezul: alerta legata de o constatare NU pleaca la fiecare document sosit.
     *
     * Fara asta, ea s-ar aduna peste cele obisnuite si omul ar primi tot doua
     * sute cincizeci de emailuri — adica exact ce trebuia inlaturat.
     */
    public function test_alerta_de_constatare_tace_la_sosirea_documentelor(): void
    {
        $this->alerta(['doar_cand' => AlertaMesajSpv::CAND_VECTOR_MODIFICAT]);

        $mesaj = new SpvMesaj([
            'company_id' => self::COMPANIE,
            'cif' => '15208744',
            'tip' => 'VECTOR FISCAL',
        ]);

        $this->assertSame(0, app(AlerteMesaje::class)->pentruMesajNou($mesaj));
        Mail::assertNothingQueued();
    }

    /** Iar alertele obisnuite merg mai departe neatinse. */
    public function test_alerta_obisnuita_pleaca_mai_departe(): void
    {
        $this->alerta(['tip_document' => 'VECTOR FISCAL']);

        $mesaj = new SpvMesaj([
            'company_id' => self::COMPANIE,
            'cif' => '15208744',
            'tip' => 'VECTOR FISCAL',
        ]);

        $this->assertSame(1, app(AlerteMesaje::class)->pentruMesajNou($mesaj));
    }

    /**
     * Textul din fila si cel din email sunt acelasi text.
     *
     * Scrise de mana in amandoua locurile, s-ar fi departat unul de altul la
     * prima indreptare — iar coloana ar fi spus una si emailul alta.
     */
    public function test_vorba_e_aceeasi_in_fila_si_in_email(): void
    {
        $sursa = file_get_contents(app_path('Services/Anaf/Spv/SolicitareService.php'));

        $this->assertSame(
            1,
            substr_count($sursa, "'ATENȚIE! VECTOR FISCAL MODIFICAT!'"),
            'textul trebuie scris o singură dată, în constantă'
        );

        $this->assertStringContainsString('self::VECTOR_MODIFICAT', $sursa);
        $this->assertStringContainsString('self::RESTANTE', $sursa);
    }

    /**
     * Un server de email obosit nu are voie sa opreasca preluarea: documentul e
     * adus si talcuit, iar observatia se vede oricum in fila.
     */
    public function test_esecul_emailului_nu_darama_preluarea(): void
    {
        $sursa = file_get_contents(app_path('Services/Anaf/Spv/SolicitareService.php'));

        $inceput = strpos($sursa, 'protected function instiinteaza');
        $bucata = substr($sursa, $inceput, 900);

        $this->assertStringContainsString('catch (\\Throwable $e)', $bucata);
        $this->assertStringContainsString('Log::warning', $bucata);
    }
}
