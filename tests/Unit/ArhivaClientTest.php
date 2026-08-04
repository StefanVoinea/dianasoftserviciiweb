<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Models\AnafSocietate;
use App\Models\SpvMesaj;
use App\Services\Anaf\Arhiva\ArhivaException;
use App\Services\Anaf\Arhiva\ArhivaService;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\SpvFisier;
use App\Services\Anaf\Spv\SpvStorage;
use App\Support\ContextCompanie;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Arhiva de documente de pe calculatorul clientului: cum se numesc dosarele si
 * fisierele si cum ajung acolo, prin programul local.
 */
class ArhivaClientTest extends TestCase
{
    protected const COMPANIE = 991;

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

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /** Valoarea unui câmp din corpul multipart trimis programului local. */
    protected static function camp(string $corp, string $nume): ?string
    {
        $tipar = '/name="' . preg_quote($nume, '/') . '"(?:\r\n[^\r\n]+)*\r\n\r\n(.*?)\r\n--/s';

        return preg_match($tipar, $corp, $potrivire) ? $potrivire[1] : null;
    }

    protected function serviciu(): ArhivaService
    {
        return $this->app->make(ArhivaService::class);
    }

    protected function declaratie(array $atribute = []): AnafDeclaratie
    {
        return AnafDeclaratie::create(array_merge([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'proba.xml',
            'tip' => 'D112',
            'cui' => '15208744',
            'den_firma' => 'DIANA SOFT SRL',
            'luna' => 6,
            'anul' => 2026,
        ], $atribute));
    }

    public function test_dosarul_firmei_poarta_denumirea_si_cui_ul(): void
    {
        $this->assertSame(
            'DIANA SOFT SRL (15208744)',
            ArhivaService::dosarFirma('DIANA SOFT SRL', '15208744')
        );
    }

    /** Fara denumire din Entitati inrolate, dosarul e chiar codul fiscal. */
    public function test_dosarul_fara_denumire_e_codul_fiscal(): void
    {
        $this->assertSame('33486455', ArhivaService::dosarFirma(null, '33486455'));
        $this->assertSame('33486455', ArhivaService::dosarFirma('   ', '33486455'));
    }

    /** Windows nu accepta ":" sau "/" in nume, iar punctul final il taie singur. */
    public function test_denumirea_e_curatata_de_ce_nu_accepta_windows(): void
    {
        $this->assertSame(
            'S.C. ALFA - CONSTRUCT S.R.L (33486455)',
            ArhivaService::dosarFirma('S.C. ALFA / CONSTRUCT S.R.L.', '33486455')
        );
    }

    public function test_numele_cuprinde_tipul_cui_ul_si_perioada(): void
    {
        $declaratie = $this->declaratie();

        $this->assertSame(
            'D112_15208744_2026-06_semnata.pdf',
            ArhivaService::numeDeclaratie($declaratie, 'semnata', 'pdf')
        );

        $this->assertSame(
            'D112_15208744_2026-06.xml',
            ArhivaService::numeDeclaratie($declaratie, '', 'xml')
        );
    }

    /** Declaratiile anuale nu au luna. */
    public function test_declaratia_anuala_are_in_nume_doar_anul(): void
    {
        $declaratie = $this->declaratie(['tip' => 'D101', 'luna' => null, 'anul' => 2025]);

        $this->assertSame(
            'D101_15208744_2025_semnata.pdf',
            ArhivaService::numeDeclaratie($declaratie, 'semnata', 'pdf')
        );
    }

    /** Dupa depunere, numele spune ca s-a depus si cu ce index de incarcare. */
    public function test_numele_declaratiei_depuse_poarta_indicele_de_incarcare(): void
    {
        $declaratie = $this->declaratie(['index_recipisa' => '912239948', 'rectificativa' => true]);

        $this->assertSame(
            'D112_15208744_2026-06_rectificativa_depusa_912239948.pdf',
            ArhivaService::numeDeclaratie($declaratie, 'depusa', 'pdf')
        );

        $this->assertSame(
            'D112_15208744_2026-06_rectificativa_recipisa_912239948.pdf',
            ArhivaService::numeDeclaratie($declaratie, 'recipisa', 'pdf')
        );
    }

    /** Documentul pleaca spre bridge-ul certificatului ales, nu spre server. */
    public function test_documentul_este_trimis_programului_local_cu_dosarul_si_numele_lui(): void
    {
        Http::fake([
            '192.168.1.44:8099/arhiva' => Http::response(
                ['cale' => 'DIANA SOFT SRL (15208744)/D112/D112_15208744_2026-06_semnata.pdf'],
                200
            ),
        ]);

        $cale = $this->serviciu()->pune(
            '%PDF-1.4 proba',
            'DIANA SOFT SRL (15208744)',
            'D112',
            'D112_15208744_2026-06_semnata.pdf'
        );

        $this->assertSame('DIANA SOFT SRL (15208744)/D112/D112_15208744_2026-06_semnata.pdf', $cale);

        Http::assertSent(function (Request $cerere) {
            $corp = $cerere->body();

            // Comanda pleacă cu jetonul semnat al serverului, nu cu codul de instalare.
            $autorizare = $cerere->header('Authorization')[0] ?? '';

            return $cerere->url() === 'http://192.168.1.44:8099/arhiva'
                && (strpos($autorizare, 'Bearer v1.') === 0 || $autorizare === 'Bearer token-contabil')
                && self::camp($corp, 'firma') === 'DIANA SOFT SRL (15208744)'
                && self::camp($corp, 'dosar') === 'D112'
                && self::camp($corp, 'nume') === 'D112_15208744_2026-06_semnata.pdf';
        });
    }

    /** Calculatorul clientului poate fi inchis: esecul trebuie sa se vada. */
    public function test_esecul_programului_local_este_spus_pe_intelesul_omului(): void
    {
        Http::fake([
            '192.168.1.44:8099/arhiva' => Http::response(['eroare' => 'Dosarul nu poate fi creat.'], 500),
        ]);

        $this->expectException(ArhivaException::class);
        $this->expectExceptionMessage('Dosarul nu poate fi creat.');

        $this->serviciu()->pune('proba', 'DIANA SOFT SRL (15208744)', 'D112', 'proba.pdf');
    }

    /**
     * O resemnare inlocuieste propriul document; o alta declaratie pentru
     * aceeasi luna nu are voie sa il stearga.
     */
    public function test_resemnarea_inlocuieste_doar_propriul_document(): void
    {
        Http::fake([
            '192.168.1.44:8099/arhiva' => Http::response(['cale' => 'F/D394/D394_2026-06_semnata.pdf'], 200),
        ]);

        $this->serviciu()->pune(
            '%PDF nou',
            'DIANA SOFT SRL (15208744)',
            'D394',
            'D394_15208744_2026-06_semnata.pdf',
            'DIANA SOFT SRL (15208744)/D394/D394_15208744_2026-06_semnata.pdf'
        );

        Http::assertSent(function (Request $cerere) {
            return self::camp($cerere->body(), 'inlocuieste')
                === 'DIANA SOFT SRL (15208744)/D394/D394_15208744_2026-06_semnata.pdf';
        });
    }

    /** Un document nou nu trimite nicio cale de inlocuit. */
    public function test_documentul_nou_nu_cere_inlocuirea_niciunui_fisier(): void
    {
        Http::fake(['192.168.1.44:8099/arhiva' => Http::response(['cale' => 'F/D394/x.pdf'], 200)]);

        $this->serviciu()->pune('%PDF', 'DIANA SOFT SRL (15208744)', 'D394', 'x.pdf');

        Http::assertSent(function (Request $cerere) {
            return self::camp($cerere->body(), 'inlocuieste') === null;
        });
    }

    /**
     * Recipisa adusa din fila de mesaje ramane in dosarul SPV, dar primeste si
     * o copie langa declaratia la care raspunde.
     */
    public function test_recipisa_ajunge_si_in_spv_si_langa_declaratie(): void
    {
        $declaratie = $this->declaratie(['index_recipisa' => '912239948']);

        $mesaj = SpvMesaj::create([
            'company_id' => self::COMPANIE,
            'mesaj_id' => '5104283611',
            'cif' => '15208744',
            'tip' => 'RECIPISA',
            'detalii' => 'Recipisa pentru incarcarea cu indexul 912239948',
            'data_creare' => '2026-07-20 10:15:00',
            'descarcat_la' => '2026-07-29 08:30:00',
        ]);

        Http::fakeSequence()
            ->push(['cale' => 'DIANA SOFT SRL (15208744)/SPV/RECIPISA/RECIPISA_15208744_2026-07-20_2026-07-29_5104283611.pdf'], 200)
            ->push(['cale' => 'DIANA SOFT SRL (15208744)/D112/D112_15208744_2026-06_recipisa_912239948.pdf'], 200);

        $cale = $this->app->make(SpvStorage::class)->arhiveazaMesaj(
            $mesaj,
            new SpvFisier('5104283611', '%PDF-1.4 recipisa', 'pdf', 'application/pdf')
        );

        // Documentul mesajului ramane cel din SPV...
        $this->assertSame(
            'DIANA SOFT SRL (15208744)/SPV/RECIPISA/RECIPISA_15208744_2026-07-20_2026-07-29_5104283611.pdf',
            $cale
        );

        // ...iar declaratia stie unde ii sta copia ei.
        $this->assertSame(
            'DIANA SOFT SRL (15208744)/D112/D112_15208744_2026-06_recipisa_912239948.pdf',
            $declaratie->fresh()->arhiva_recipisa
        );

        $trimise = [];

        Http::assertSent(function (Request $cerere) use (&$trimise) {
            $trimise[] = [self::camp($cerere->body(), 'dosar'), self::camp($cerere->body(), 'nume')];

            return true;
        });

        $this->assertSame([
            ['SPV/RECIPISA', 'RECIPISA_15208744_2026-07-20_2026-07-29_5104283611.pdf'],
            ['D112', 'D112_15208744_2026-06_recipisa_912239948.pdf'],
        ], $trimise);
    }

    /** Recipisa deja pusa langa declaratie nu se mai copiaza inca o data. */
    public function test_recipisa_nu_se_copiaza_de_doua_ori_langa_declaratie(): void
    {
        $this->declaratie([
            'index_recipisa' => '912239948',
            'arhiva_recipisa' => 'DIANA SOFT SRL (15208744)/D112/D112_15208744_2026-06_recipisa_912239948.pdf',
        ]);

        $mesaj = SpvMesaj::create([
            'company_id' => self::COMPANIE,
            'mesaj_id' => '5104283613',
            'cif' => '15208744',
            'tip' => 'RECIPISA',
            'detalii' => 'Recipisa pentru incarcarea cu indexul 912239948',
            'descarcat_la' => '2026-07-29 08:30:00',
        ]);

        Http::fake(['192.168.1.44:8099/arhiva' => Http::response(['cale' => 'F/SPV/RECIPISA/x.pdf'], 200)]);

        $this->app->make(SpvStorage::class)->arhiveazaMesaj(
            $mesaj,
            new SpvFisier('5104283613', '%PDF-1.4 recipisa', 'pdf', 'application/pdf')
        );

        Http::assertSentCount(1);
    }

    /**
     * Dosarul arhivei se seteaza in aplicatie, la certificatul care raspunde de
     * acel calculator, si merge cu fiecare cerere — nimeni nu mai umbla prin
     * bridge.env pe statii.
     */
    public function test_dosarul_arhivei_se_trimite_programului_local(): void
    {
        $this->certificat->update(['arhiva_cale' => 'D:\\Documente fiscale']);
        $this->app->make(CertificatService::class)->foloseste($this->certificat->fresh());

        Http::fake(['192.168.1.44:8099/arhiva' => Http::response(['cale' => 'F/D112/x.pdf'], 200)]);

        $this->serviciu()->pune('%PDF', 'DIANA SOFT SRL (15208744)', 'D112', 'x.pdf');

        Http::assertSent(function (Request $cerere) {
            return $cerere->hasHeader('X-Arhiva-Cale', 'D:\\Documente fiscale');
        });
    }

    /** Nesetat, hotaraste bridge.env de pe acel calculator. */
    public function test_fara_dosar_setat_nu_se_trimite_niciun_antet(): void
    {
        Http::fake(['192.168.1.44:8099/arhiva' => Http::response(['cale' => 'F/D112/x.pdf'], 200)]);

        $this->serviciu()->pune('%PDF', 'DIANA SOFT SRL (15208744)', 'D112', 'x.pdf');

        Http::assertSent(function (Request $cerere) {
            return !$cerere->hasHeader('X-Arhiva-Cale');
        });
    }

    /**
     * Documentele din SPV stau pe firme si pe tipuri, cu amandoua datele in
     * nume: intai ziua in care ANAF a intocmit documentul, apoi cea in care a
     * intrat in arhiva.
     */
    public function test_documentele_spv_stau_pe_tipuri_cu_datele_in_nume(): void
    {
        // Denumirea dosarului vine din Entitati inrolate.
        AnafSocietate::create([
            'company_id' => self::COMPANIE,
            'cif' => '15208744',
            'denumire' => 'DIANA SOFT SRL',
        ]);

        $mesaj = SpvMesaj::create([
            'company_id' => self::COMPANIE,
            'mesaj_id' => '5104283612',
            'cif' => '15208744',
            'tip' => 'Situatie Sintetica',
            'detalii' => 'Situatia sintetica pe iunie',
            'data_creare' => '2026-07-20 10:15:00',
            'descarcat_la' => '2026-07-29 08:30:00',
        ]);

        Http::fake([
            '192.168.1.44:8099/arhiva' => Http::response(['cale' => 'DIANA SOFT SRL (15208744)/SPV/x.pdf'], 200),
        ]);

        $this->app->make(SpvStorage::class)->arhiveazaMesaj(
            $mesaj,
            new SpvFisier('5104283612', '%PDF-1.4 situatie', 'pdf', 'application/pdf')
        );

        Http::assertSent(function (Request $cerere) {
            $corp = $cerere->body();

            return self::camp($corp, 'firma') === 'DIANA SOFT SRL (15208744)'
                && self::camp($corp, 'dosar') === 'SPV/Situatie Sintetica'
                && self::camp($corp, 'nume') === 'Situatie Sintetica_15208744_2026-07-20_2026-07-29_5104283612.pdf';
        });
    }

    public function test_documentul_se_citeste_inapoi_din_arhiva(): void
    {
        Http::fake(['192.168.1.44:8099/arhiva*' => Http::response('%PDF-1.4 continut', 200)]);

        $this->assertSame(
            '%PDF-1.4 continut',
            $this->serviciu()->ia('DIANA SOFT SRL (15208744)/D112/proba.pdf')
        );
    }
}
