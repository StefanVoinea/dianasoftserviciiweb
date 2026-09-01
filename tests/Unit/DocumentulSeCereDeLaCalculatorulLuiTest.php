<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\SpvFileController;
use App\Http\Controllers\Api\SpvSolicitariController;
use App\Models\AnafCertificat;
use App\Models\SpvMesaj;
use App\Models\SpvSolicitare;
use App\Services\Anaf\Arhiva\ArhivaService;
use App\Support\ContextCompanie;
use Illuminate\Http\Client\Request as CerereTrimisa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Documentele din SPV nu mai trec prin server: raman in arhiva de pe
 * calculatorul clientului. Scrise pe statia certificatului cu care au venit de
 * la ANAF, tot de acolo trebuie si citite.
 *
 * Un client poate avea doua tokene pe doua calculatoare, fiecare cu arhiva lui.
 * Cerut de la bridge-ul implicit al firmei, un document scris pe cealalta
 * statie nu se gaseste — si nu se deschidea nici in aplicatia web, nici pe
 * telefon.
 */
class DocumentulSeCereDeLaCalculatorulLuiTest extends TestCase
{
    protected const COMPANIE = 992;

    /** Certificatul implicit al firmei: pe el cade alegerea cand nu se spune altfel. */
    protected $implicit;

    /** Al doilea token, pe alt calculator si cu alta arhiva. */
    protected $celalalt;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->implicit = $this->certificat('CONTABIL SEF', 'http://10.0.0.1:8099', 'D:\\ArhivaBirou', true);
        $this->celalalt = $this->certificat('COLEGUL', 'http://10.0.0.2:8099', 'E:\\ArhivaColeg', false);
    }

    protected function tearDown(): void
    {
        SpvSolicitare::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        SpvMesaj::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function certificat(string $nume, string $url, string $arhiva, bool $implicit): AnafCertificat
    {
        return AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => $nume,
            'activ' => true,
            'implicit' => $implicit,
            'valabil_pana_la' => now()->addYear(),
            'bridge_url' => $url,
            'bridge_token' => 'token-' . strtolower($nume),
            'arhiva_cale' => $arhiva,
        ]);
    }

    protected function mesaj(array $atribute = []): SpvMesaj
    {
        return SpvMesaj::create(array_merge([
            'company_id' => self::COMPANIE,
            'mesaj_id' => '900000001',
            'cif' => '15208744',
            'tip' => 'CERTIFICAT FISCAL',
            'data_creare' => '2026-08-20 09:00:00',
            'descarcat_la' => '2026-08-20 09:05:00',
            'arhiva_cale' => 'DIANA SOFT SRL (15208744)/SPV/CERTIFICAT FISCAL/x.pdf',
        ], $atribute));
    }

    /** Amandoua bridge-urile raspund, ca sa se vada la care s-a ajuns. */
    protected function amandouaRaspund(): void
    {
        Http::fake([
            '10.0.0.1:8099/arhiva*' => Http::response('%PDF-birou', 200),
            '10.0.0.2:8099/arhiva*' => Http::response('%PDF-coleg', 200),
        ]);
    }

    /** Adresa si dosarul de arhiva ale cererii plecate. */
    protected function asteaptaSaCearaDeLa(string $gazda, string $arhiva): void
    {
        Http::assertSent(function (CerereTrimisa $cerere) use ($gazda, $arhiva) {
            return strpos($cerere->url(), $gazda) !== false
                && $cerere->hasHeader('X-Arhiva-Cale', $arhiva);
        });
    }

    /*
     * ------------------------------------------------------------------
     * Serviciul
     * ------------------------------------------------------------------
     */

    /** Fara numarul certificatului, documentul se cere de la bridge-ul implicit. */
    public function test_fara_certificat_documentul_se_cere_de_la_bridge_ul_implicit(): void
    {
        $this->amandouaRaspund();

        $this->app->make(ArhivaService::class)->iaDeLa(null, 'F/SPV/x.pdf');

        $this->asteaptaSaCearaDeLa('10.0.0.1', 'D:\\ArhivaBirou');
    }

    /** Cu numarul lui, se cere de la calculatorul pe care a fost scris. */
    public function test_cu_certificatul_lui_documentul_se_cere_de_la_calculatorul_acela(): void
    {
        $this->amandouaRaspund();

        $continut = $this->app->make(ArhivaService::class)
            ->iaDeLa($this->celalalt->id, 'F/SPV/x.pdf');

        $this->asteaptaSaCearaDeLa('10.0.0.2', 'E:\\ArhivaColeg');
        $this->assertSame('%PDF-coleg', $continut);
    }

    /** Un certificat scos din uz nu lasa lucrarea fara niciun bridge. */
    public function test_certificatul_scos_din_uz_lasa_bridge_ul_implicit(): void
    {
        $this->celalalt->update(['activ' => false]);
        $this->amandouaRaspund();

        $this->app->make(ArhivaService::class)->iaDeLa($this->celalalt->id, 'F/SPV/x.pdf');

        $this->asteaptaSaCearaDeLa('10.0.0.1', 'D:\\ArhivaBirou');
    }

    /*
     * ------------------------------------------------------------------
     * Mesajele din SPV
     * ------------------------------------------------------------------
     */

    public function test_documentul_mesajului_se_cere_de_la_calculatorul_lui(): void
    {
        $this->mesaj(['certificat_id' => $this->celalalt->id]);
        $this->amandouaRaspund();

        $raspuns = (new SpvFileController())->open(
            Request::create('/api/spv/fisier', 'GET', ['id' => '900000001']),
            $this->app->make(ArhivaService::class)
        );

        $this->assertSame(200, $raspuns->getStatusCode());
        $this->assertSame('application/pdf', $raspuns->headers->get('Content-Type'));
        $this->assertSame('%PDF-coleg', $raspuns->getContent());
        $this->asteaptaSaCearaDeLa('10.0.0.2', 'E:\\ArhivaColeg');
    }

    /** Mesajele vechi, ramase fara certificat, merg mai departe ca inainte. */
    public function test_mesajul_fara_certificat_merge_pe_bridge_ul_implicit(): void
    {
        $this->mesaj(['certificat_id' => null]);
        $this->amandouaRaspund();

        (new SpvFileController())->open(
            Request::create('/api/spv/fisier', 'GET', ['id' => '900000001']),
            $this->app->make(ArhivaService::class)
        );

        $this->asteaptaSaCearaDeLa('10.0.0.1', 'D:\\ArhivaBirou');
    }

    /*
     * ------------------------------------------------------------------
     * Raspunsurile la solicitari
     * ------------------------------------------------------------------
     */

    /** Documentul l-a scris mesajul care a adus raspunsul, deci el spune unde e. */
    public function test_raspunsul_solicitarii_se_cere_de_la_calculatorul_mesajului(): void
    {
        $this->mesaj(['certificat_id' => $this->celalalt->id]);

        $solicitare = SpvSolicitare::create([
            'company_id' => self::COMPANIE,
            'cif' => '15208744',
            'tip_document' => 'Fisa Rol',
            'stare' => 'preluata',
            'mesaj_id' => '900000001',
            // Solicitarea a plecat cu celalalt certificat decat cel al raspunsului
            'certificat_id' => $this->implicit->id,
            'arhiva_cale' => 'DIANA SOFT SRL (15208744)/SPV/Fisa Rol/x.pdf',
        ]);

        $this->amandouaRaspund();

        $raspuns = (new SpvSolicitariController())->fisier(
            $solicitare,
            $this->app->make(ArhivaService::class)
        );

        $this->assertSame(200, $raspuns->getStatusCode());
        $this->assertSame('%PDF-coleg', $raspuns->getContent());
        $this->asteaptaSaCearaDeLa('10.0.0.2', 'E:\\ArhivaColeg');
    }

    /** Fara mesajul lui, se incearca certificatul cu care a plecat solicitarea. */
    public function test_fara_mesaj_se_ia_certificatul_solicitarii(): void
    {
        $solicitare = SpvSolicitare::create([
            'company_id' => self::COMPANIE,
            'cif' => '15208744',
            'tip_document' => 'Fisa Rol',
            'stare' => 'preluata',
            'certificat_id' => $this->celalalt->id,
            'arhiva_cale' => 'DIANA SOFT SRL (15208744)/SPV/Fisa Rol/x.pdf',
        ]);

        $this->amandouaRaspund();

        (new SpvSolicitariController())->fisier(
            $solicitare,
            $this->app->make(ArhivaService::class)
        );

        $this->asteaptaSaCearaDeLa('10.0.0.2', 'E:\\ArhivaColeg');
    }

    /** Documentul negasit la client se spune pe intelesul omului, nu ca 500. */
    public function test_documentul_negasit_la_client_da_un_raspuns_lamurit(): void
    {
        $this->mesaj(['certificat_id' => $this->celalalt->id]);

        Http::fake([
            '10.0.0.2:8099/arhiva*' => Http::response(['message' => 'Fișierul nu există.'], 404),
        ]);

        $raspuns = (new SpvFileController())->open(
            Request::create('/api/spv/fisier', 'GET', ['id' => '900000001']),
            $this->app->make(ArhivaService::class)
        );

        $this->assertSame(502, $raspuns->getStatusCode());
        $this->assertFalse($raspuns->getData()->success);
    }
}
