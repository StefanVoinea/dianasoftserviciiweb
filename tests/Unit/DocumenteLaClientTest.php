<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Models\AnafSocietate;
use App\Models\SpvMesaj;
use App\Models\SpvSolicitare;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\SpvStorage;
use App\Support\ContextCompanie;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Documentele clientului nu mai trec prin server.
 *
 * Pana acum documentul din SPV facea un ocol fara rost: venea de la ANAF la
 * programul local, urca pe server si se intorcea ca sa fie scris tot pe
 * calculatorul clientului. Acum programul local il ia si il scrie de-a dreptul
 * acolo, iar incoace vine doar calea sub care l-a pus — si, cand aplicatia are
 * ce citi din el, textul din pagini.
 */
class DocumenteLaClientTest extends TestCase
{
    protected const COMPANIE = 992;

    protected $certificat;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        // Discul serverului e unul de proba: la sfarsit se verifica pe el ca
        // n-a ajuns niciun document.
        Storage::fake('local');

        $this->certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'POPESCU ION',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
            'bridge_url' => 'http://192.168.1.44:8099',
            'bridge_token' => 'token-contabil',
        ]);

        $this->app->make(CertificatService::class)->foloseste($this->certificat);
    }

    protected function tearDown(): void
    {
        AnafDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        SpvMesaj::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafSocietate::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        SpvSolicitare::query()->toateCompaniile()->totiUtilizatorii()
            ->where('company_id', self::COMPANIE)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function mesaj(array $atribute = []): SpvMesaj
    {
        return SpvMesaj::create(array_merge([
            'company_id' => self::COMPANIE,
            'mesaj_id' => '5104283612',
            'cif' => '15208744',
            'tip' => 'Situatie Sintetica',
            'detalii' => 'Situatia sintetica pe iunie',
            'descarcat_la' => '2026-07-29 08:30:00',
        ], $atribute));
    }

    public function test_documentul_din_spv_nu_mai_ajunge_pe_server(): void
    {
        AnafSocietate::create([
            'company_id' => self::COMPANIE,
            'cif' => '15208744',
            'denumire' => 'DIANA SOFT SRL',
        ]);

        $mesaj = $this->mesaj();

        Http::fake([
            '192.168.1.44:8099/spv-arhiva*' => Http::response([
                'cale' => 'DIANA SOFT SRL (15208744)/SPV/Situatie Sintetica/x.pdf',
                'extensie' => 'pdf',
                'marime' => 12345,
                'hash' => 'abcdef',
            ], 200),
        ]);

        $adus = $this->app->make(SpvStorage::class)->aduce($mesaj);

        $this->assertSame('DIANA SOFT SRL (15208744)/SPV/Situatie Sintetica/x.pdf', $adus['cale']);
        $this->assertNull($adus['pe_server']);

        // Pe discul serverului nu s-a scris nimic.
        $this->assertSame([], Storage::allFiles());
        $this->assertNull($mesaj->fresh()->cale_fisier);
        $this->assertSame('DIANA SOFT SRL (15208744)/SPV/Situatie Sintetica/x.pdf', $mesaj->fresh()->arhiva_cale);
    }

    /** Programului local i se spune firma, dosarul si numele, fara extensie. */
    public function test_programul_local_afla_unde_sa_puna_documentul(): void
    {
        AnafSocietate::create([
            'company_id' => self::COMPANIE,
            'cif' => '15208744',
            'denumire' => 'DIANA SOFT SRL',
        ]);

        Http::fake([
            '192.168.1.44:8099/spv-arhiva*' => Http::response(['cale' => 'F/SPV/x.pdf'], 200),
        ]);

        $this->app->make(SpvStorage::class)->aduce($this->mesaj());

        Http::assertSent(function (Request $cerere) {
            parse_str((string) parse_url($cerere->url(), PHP_URL_QUERY), $intrebare);

            return ($intrebare['id'] ?? null) === '5104283612'
                && ($intrebare['firma'] ?? null) === 'DIANA SOFT SRL (15208744)'
                && ($intrebare['dosar'] ?? null) === 'SPV/Situatie Sintetica'
                // Fara extensie: abia raspunsul ANAF spune daca e pdf sau zip.
                && ($intrebare['nume'] ?? null) === 'Situatie Sintetica_15208744_2026-07-29_5104283612';
        });
    }

    /**
     * Raspunsurile la solicitari poarta in SPV acelasi tip, „RASPUNS
     * SOLICITARE", fie ca inauntru e vector fiscal sau bilant. In arhiva ele
     * stau dupa documentul cerut, ca sa se vada din dosar si din nume ce sunt.
     */
    public function test_raspunsul_la_solicitare_se_aseaza_dupa_documentul_cerut(): void
    {
        SpvSolicitare::create([
            'company_id' => self::COMPANIE,
            'cif' => '15208744',
            'tip_document' => 'VECTOR FISCAL',
            'id_solicitare' => '77123',
            'stare' => 'trimisa',
        ]);

        $mesaj = $this->mesaj([
            'mesaj_id' => '5104283615',
            'tip' => 'RASPUNS SOLICITARE',
            'id_solicitare' => '77123',
        ]);

        Http::fake([
            '192.168.1.44:8099/spv-arhiva*' => Http::response(['cale' => 'F/SPV/x.pdf'], 200),
        ]);

        $this->app->make(SpvStorage::class)->aduce($mesaj, false, 'solicitari');

        Http::assertSent(function (Request $cerere) {
            parse_str((string) parse_url($cerere->url(), PHP_URL_QUERY), $intrebare);

            return ($intrebare['dosar'] ?? null) === 'SPV/VECTOR FISCAL'
                && ($intrebare['nume'] ?? null) === 'VECTOR FISCAL_15208744_2026-07-29_5104283615';
        });
    }

    /** Fara solicitare cunoscuta se ramane la tipul spus de SPV. */
    public function test_fara_solicitare_cunoscuta_ramane_tipul_din_spv(): void
    {
        $mesaj = $this->mesaj([
            'mesaj_id' => '5104283616',
            'tip' => 'RASPUNS SOLICITARE',
            'id_solicitare' => '99999',
        ]);

        Http::fake([
            '192.168.1.44:8099/spv-arhiva*' => Http::response(['cale' => 'F/SPV/x.pdf'], 200),
        ]);

        $this->app->make(SpvStorage::class)->aduce($mesaj, false, 'solicitari');

        Http::assertSent(function (Request $cerere) {
            parse_str((string) parse_url($cerere->url(), PHP_URL_QUERY), $intrebare);

            return ($intrebare['dosar'] ?? null) === 'SPV/RASPUNS SOLICITARE';
        });
    }

    /**
     * Recipisa sta si langa declaratia la care raspunde. Copia se face intre
     * doua dosare de pe calculatorul clientului, nu prin server.
     */
    public function test_copia_recipisei_se_face_la_client(): void
    {
        $declaratie = AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'proba.xml',
            'tip' => 'D112',
            'cui' => '15208744',
            'den_firma' => 'DIANA SOFT SRL',
            'luna' => 6,
            'anul' => 2026,
            'index_recipisa' => '912239948',
        ]);

        $mesaj = $this->mesaj([
            'mesaj_id' => '5104283611',
            'tip' => 'RECIPISA',
            'detalii' => 'Recipisa pentru incarcarea cu indexul 912239948',
        ]);

        Http::fake([
            '192.168.1.44:8099/spv-arhiva*' => Http::response([
                'cale' => 'DIANA SOFT SRL (15208744)/SPV/RECIPISA/r.pdf',
                'extensie' => 'pdf',
                'hash' => 'abcdef',
            ], 200),
            '192.168.1.44:8099/arhiva/copiaza' => Http::response([
                'cale' => 'DIANA SOFT SRL (15208744)/D112/D112_15208744_2026-06_recipisa_912239948.pdf',
            ], 200),
        ]);

        $this->app->make(SpvStorage::class)->aduce($mesaj);

        $this->assertSame(
            'DIANA SOFT SRL (15208744)/D112/D112_15208744_2026-06_recipisa_912239948.pdf',
            $declaratie->fresh()->arhiva_recipisa
        );

        // Copia poarta doar cai, nu si documentul.
        Http::assertSent(function (Request $cerere) {
            if (!str_contains($cerere->url(), '/arhiva/copiaza')) {
                return false;
            }

            return $cerere['cale'] === 'DIANA SOFT SRL (15208744)/SPV/RECIPISA/r.pdf'
                && $cerere['dosar'] === 'D112'
                && $cerere['nume'] === 'D112_15208744_2026-06_recipisa_912239948.pdf';
        });

        $this->assertSame([], Storage::allFiles());
    }

    /** Textul citit la client vine cu raspunsul, ca documentul sa nu urce. */
    public function test_textul_documentului_vine_de_la_client(): void
    {
        Http::fake([
            '192.168.1.44:8099/spv-arhiva*' => Http::response([
                'cale' => 'F/SPV/x.pdf',
                'extensie' => 'pdf',
                'hash' => 'abcdef',
                'text' => 'Nu exista erori de validare.',
            ], 200),
        ]);

        $adus = $this->app->make(SpvStorage::class)->aduce($this->mesaj(), true);

        $this->assertSame('Nu exista erori de validare.', $adus['text']);
        $this->assertSame([], Storage::allFiles());
    }

    /**
     * Instalarile mai vechi nu cunosc capatul nou. Pentru ele lucrarea se face
     * pe drumul dinainte, iar documentul ajunge tot in arhiva clientului.
     */
    public function test_programul_local_vechi_face_lucrarea_pe_drumul_dinainte(): void
    {
        $mesaj = $this->mesaj();

        Http::fake([
            '192.168.1.44:8099/spv-arhiva*' => Http::response(['eroare' => 'Operație necunoscută.'], 404),
            '192.168.1.44:8099/spv/descarcare*' => Http::response('%PDF-1.4 document', 200, [
                'Content-Type' => 'application/pdf',
            ]),
            '192.168.1.44:8099/arhiva' => Http::response(['cale' => 'F/SPV/Situatie Sintetica/x.pdf'], 200),
        ]);

        $adus = $this->app->make(SpvStorage::class)->aduce($mesaj);

        $this->assertSame('F/SPV/Situatie Sintetica/x.pdf', $adus['cale']);

        // Documentul a trecut prin server, dar n-a ramas acolo.
        $this->assertNull($adus['pe_server']);
        $this->assertSame([], Storage::allFiles());
        $this->assertNull($mesaj->fresh()->cale_fisier);
    }

    /**
     * Fara arhiva la client documentul ramane pe server, ca pana acum: altfel
     * s-ar pierde cu totul.
     */
    public function test_fara_arhiva_la_client_documentul_ramane_pe_server(): void
    {
        config(['anaf.arhiva.activa' => false]);

        $mesaj = $this->mesaj();

        Http::fake([
            '192.168.1.44:8099/spv/descarcare*' => Http::response('%PDF-1.4 document', 200, [
                'Content-Type' => 'application/pdf',
            ]),
        ]);

        $adus = $this->app->make(SpvStorage::class)->aduce($mesaj);

        $this->assertNull($adus['cale']);
        $this->assertNotNull($adus['pe_server']);
        $this->assertSame([$adus['pe_server']], Storage::allFiles());
    }
}
