<?php

namespace Tests\Unit;

use App\Mail\EroareDeclaratieEmail;
use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Models\AnafSocietate;
use App\Models\CertificatUtilizator;
use App\Services\Anaf\Arhiva\ArhivaService;
use App\Services\Anaf\Declaratii\DeclaratieException;
use App\Services\Anaf\Declaratii\DeclaratieXml;
use App\Services\Anaf\Declaratii\DukIntegrator;
use App\Services\Anaf\Declaratii\MonitorizareFolder;
use App\Services\Anaf\Declaratii\PdfDeclaratie;
use App\Services\Anaf\Declaratii\SemnareService;
use App\Services\Anaf\Spv\CertificatService;
use App\Support\ContextCompanie;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Dosarul urmarit de pe calculatorul clientului: ce se pune acolo se incarca,
 * se valideaza si se semneaza singur, iar ce nu trece de validare nu se pierde
 * — pleaca in „erori" si oamenii firmei afla pe email de ce.
 */
class MonitorizareFolderTest extends TestCase
{
    protected const COMPANIE = 987;

    /** Certificatul care urmareste dosarul. */
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
            'bridge_url' => 'http://192.168.1.77:8099',
            'bridge_token' => 'token-contabil',
            'monitorizare_cale' => 'D:\\Declaratii de semnat',
            'monitorizare_activa' => true,
        ]);

        Mail::fake();

        // Fisierele de lucru raman in disc de proba, nu in storage-ul aplicatiei.
        Storage::fake(config('filesystems.default'));
    }

    protected function tearDown(): void
    {
        AnafDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafSocietate::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        CertificatUtilizator::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected const XML = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<declaratie394 xmlns="mfp:anaf:dgti:d394:declaratie:v4" luna="6" an="2026" cui="RO15208744" den="DIANA SOFT SRL">
  <rezumat1 cuiP="33486455" denP="ALFA CONSTRUCT SRL"/>
</declaratie394>
XML;

    /** Validator care spune ce i se cere, fara sa cheme Java. */
    protected function duk(bool $valid, string $erori = ''): DukIntegrator
    {
        return new class($valid, $erori) extends DukIntegrator {
            protected $valid;
            protected $erori;

            public function __construct(bool $valid, string $erori)
            {
                $this->valid = $valid;
                $this->erori = $erori;
            }

            public function valideazaSiGenereazaPdf(
                string $caleXml,
                string $tip,
                string $calePdf,
                ?string $caleZip = null,
                ?int $an = null,
                ?int $luna = null,
                ?string $tipPerioada = null
            ): array {
                return ['valid' => $this->valid, 'erori' => $this->erori];
            }
        };
    }

    /** Semnatura, fara token conectat. */
    protected function semnare(?string $esec = null): SemnareService
    {
        return new class($esec) extends SemnareService {
            protected $esec;

            public function __construct(?string $esec)
            {
                $this->esec = $esec;
            }

            public function semneaza(string $calePdf, string $calePdfSemnat): string
            {
                if ($this->esec !== null) {
                    throw new DeclaratieException($this->esec);
                }

                return $calePdfSemnat;
            }
        };
    }

    /**
     * Cititorul de PDF, fara programul local: spune ce XML poarta PDF-ul si
     * daca vine deja semnat.
     */
    protected function pdf(?string $xml, bool $semnat = false): PdfDeclaratie
    {
        return new class($xml, $semnat) extends PdfDeclaratie {
            protected $xml;
            protected $semnat;

            public function __construct(?string $xml, bool $semnat)
            {
                $this->xml = $xml;
                $this->semnat = $semnat;
            }

            public function citeste(string $calePdf): array
            {
                return [
                    'semnat' => $this->semnat,
                    'semnatari' => [],
                    'nume_xml' => $this->xml === null ? null : 'declaratie.xml',
                    'xml' => $this->xml,
                ];
            }
        };
    }

    protected function serviciu(
        DukIntegrator $duk = null,
        SemnareService $semnare = null,
        PdfDeclaratie $pdf = null
    ): MonitorizareFolder {
        return new MonitorizareFolder(
            config('anaf.declaratii'),
            $this->app->make(CertificatService::class),
            new DeclaratieXml(),
            $duk ?: $this->duk(true),
            $semnare ?: $this->semnare(),
            $this->app->make(ArhivaService::class),
            $pdf ?: $this->pdf(self::XML)
        );
    }

    /** Programul local raspunde cu un fisier gata de luat si cu continutul lui. */
    protected function bridgeCu(array $fisiere, string $continut = self::XML): void
    {
        Http::fake([
            '192.168.1.77:8099/monitorizare/fisier*' => Http::response($continut, 200),
            '192.168.1.77:8099/monitorizare/mutat' => Http::response(['mutat' => true], 200),
            '192.168.1.77:8099/monitorizare' => Http::response(['fisiere' => $fisiere], 200),
            // Documentele semnate pleaca in arhiva de la client
            '192.168.1.77:8099/arhiva' => Http::response(
                ['cale' => 'DIANA SOFT SRL (15208744)/D394/document.pdf'],
                200
            ),
        ]);
    }

    public function test_declaratia_pusa_in_dosar_este_luata_validata_si_semnata(): void
    {
        $this->bridgeCu([['nume' => 'd394_iunie.xml', 'marime' => 812, 'gata' => true]]);

        $raport = $this->serviciu()->pentruCertificat($this->certificat);

        $this->assertSame(1, $raport['gasite']);
        $this->assertSame(1, $raport['semnate']);
        $this->assertSame(0, $raport['esuate']);

        $declaratie = AnafDeclaratie::where('nume_fisier', 'd394_iunie.xml')->first();

        $this->assertNotNull($declaratie, 'declarația trebuie să apară în listă');
        $this->assertSame('semnat', $declaratie->pas);
        $this->assertSame('D394', $declaratie->tip);
        $this->assertSame('15208744', $declaratie->cui);
        $this->assertTrue((bool) $declaratie->semnat);

        // Prelucrat, fisierul iese din dosar ca sa nu fie luat a doua oara.
        Http::assertSent(function (Request $cerere) {
            return $cerere->url() === 'http://192.168.1.77:8099/monitorizare/mutat'
                && strpos($cerere->body(), 'unde=prelucrate') !== false;
        });
    }

    /** PDF-ul de declarație poartă XML-ul în el: se validează la fel. */
    public function test_pdf_ul_nesemnat_este_validat_pe_xml_ul_din_el_si_semnat(): void
    {
        $this->bridgeCu(
            [['nume' => 'd394_iunie.pdf', 'marime' => 91000, 'gata' => true]],
            '%PDF-1.4 declaratie'
        );

        $raport = $this->serviciu(null, null, $this->pdf(self::XML))->pentruCertificat($this->certificat);

        $this->assertSame(1, $raport['semnate']);

        $declaratie = AnafDeclaratie::where('nume_fisier', 'd394_iunie.pdf')->first();

        $this->assertSame('semnat', $declaratie->pas);
        $this->assertSame('D394', $declaratie->tip);
        $this->assertSame('15208744', $declaratie->cui);

        // Se păstrează PDF-ul primit, nu cel scos de validator.
        $this->assertStringEndsWith('.pdf', $declaratie->cale_pdf);
        $this->assertStringNotContainsString('_duk', $declaratie->cale_pdf);
        $this->assertStringEndsWith('_semnat.pdf', $declaratie->cale_pdf_semnat);
    }

    /** Venit deja semnat, PDF-ul nu se mai semnează încă o dată. */
    public function test_pdf_ul_semnat_trece_direct_in_lista(): void
    {
        $this->bridgeCu(
            [['nume' => 'd394_semnat.pdf', 'marime' => 91000, 'gata' => true]],
            '%PDF-1.4 declaratie semnata'
        );

        $raport = $this->serviciu(
            null,
            $this->semnare('Nu trebuia chemată semnarea.'),
            $this->pdf(self::XML, true)
        )->pentruCertificat($this->certificat);

        $this->assertSame(1, $raport['semnate']);
        $this->assertSame([], $raport['erori']);

        $declaratie = AnafDeclaratie::first();

        $this->assertSame('semnat', $declaratie->pas);
        $this->assertTrue((bool) $declaratie->semnat);

        // Semnătura e chiar în fișierul primit: el pleacă în arhivă ca document semnat.
        Http::assertSent(function (Request $cerere) {
            return $cerere->url() === 'http://192.168.1.77:8099/arhiva'
                && strpos($cerere->body(), '%PDF-1.4 declaratie semnata') !== false
                && strpos($cerere->body(), '_semnata.pdf') !== false;
        });
    }

    /** Un PDF oarecare pus în dosar nu are ce căuta la validare. */
    public function test_pdf_ul_fara_declaratie_in_el_este_raportat(): void
    {
        CertificatUtilizator::create([
            'company_id' => self::COMPANIE,
            'certificat_id' => $this->certificat->id,
            'email' => 'contabil@diana-soft.ro',
            'activ' => true,
        ]);

        $this->bridgeCu(
            [['nume' => 'factura.pdf', 'marime' => 4200, 'gata' => true]],
            '%PDF-1.4 factura'
        );

        $raport = $this->serviciu(null, null, $this->pdf(null))->pentruCertificat($this->certificat);

        $this->assertSame(1, $raport['esuate']);
        $this->assertStringContainsString('nu pare o declarație ANAF', $raport['erori'][0]);
        $this->assertSame(0, AnafDeclaratie::count(), 'nu rămâne nimic pe jumătate în listă');

        Mail::assertSent(EroareDeclaratieEmail::class);
    }

    /**
     * Documentul semnat singur ajunge in arhiva de la client, ca si cel semnat
     * de mana din fila de declaratii.
     */
    public function test_documentul_semnat_pleaca_in_arhiva_clientului(): void
    {
        $this->bridgeCu(
            [['nume' => 'd394_semnat.pdf', 'marime' => 91000, 'gata' => true]],
            '%PDF-1.4 declaratie semnata'
        );

        $this->serviciu(null, null, $this->pdf(self::XML, true))->pentruCertificat($this->certificat);

        $this->assertSame(
            'DIANA SOFT SRL (15208744)/D394/document.pdf',
            AnafDeclaratie::first()->arhiva_semnat
        );

        Http::assertSent(function (Request $cerere) {
            return $cerere->url() === 'http://192.168.1.77:8099/arhiva'
                && strpos($cerere->body(), 'D394_15208744_2026-06_semnata.pdf') !== false;
        });
    }

    /** Arhiva picata nu desface o semnătură reușită. */
    public function test_arhivarea_esuata_lasa_declaratia_semnata(): void
    {
        $this->bridgeCu([['nume' => 'd394_iunie.xml', 'marime' => 812, 'gata' => true]]);

        Http::fake([
            '192.168.1.77:8099/monitorizare/fisier*' => Http::response(self::XML, 200),
            '192.168.1.77:8099/monitorizare/mutat' => Http::response(['mutat' => true], 200),
            '192.168.1.77:8099/monitorizare' => Http::response([
                'fisiere' => [['nume' => 'd394_iunie.xml', 'marime' => 812, 'gata' => true]],
            ], 200),
            '192.168.1.77:8099/arhiva' => Http::response(['eroare' => 'Discul este plin.'], 500),
        ]);

        $raport = $this->serviciu()->pentruCertificat($this->certificat);

        $this->assertSame(1, $raport['semnate']);
        $this->assertSame(0, $raport['esuate']);
        $this->assertSame('semnat', AnafDeclaratie::first()->pas);
    }

    /** Fisierul abia copiat se lasa pentru rularea urmatoare. */
    public function test_fisierul_inca_nescris_pana_la_capat_nu_se_ia(): void
    {
        $this->bridgeCu([['nume' => 'd394_iunie.xml', 'marime' => 12, 'gata' => false]]);

        $raport = $this->serviciu()->pentruCertificat($this->certificat);

        $this->assertSame(0, $raport['gasite']);
        $this->assertSame(0, AnafDeclaratie::count());
    }

    /**
     * Validarea picata muta fisierul in „erori" si instiinteaza oamenii legati
     * de certificatul cu care e inrolata firma din declaratie — nu pe cei ai
     * certificatului care se intampla sa urmareasca dosarul.
     */
    public function test_declaratia_respinsa_ajunge_in_erori_si_se_anunta_oamenii_firmei(): void
    {
        $alFirmei = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'IONESCU MARIA',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
        ]);

        AnafSocietate::create([
            'company_id' => self::COMPANIE,
            'cif' => '15208744',
            'denumire' => 'DIANA SOFT SRL',
            'certificat_id' => $alFirmei->id,
        ]);

        CertificatUtilizator::create([
            'company_id' => self::COMPANIE,
            'certificat_id' => $alFirmei->id,
            'email' => 'contabil@diana-soft.ro',
            'activ' => true,
        ]);

        CertificatUtilizator::create([
            'company_id' => self::COMPANIE,
            'certificat_id' => $this->certificat->id,
            'email' => 'altcineva@diana-soft.ro',
            'activ' => true,
        ]);

        $this->bridgeCu([['nume' => 'd394_iunie.xml', 'marime' => 812, 'gata' => true]]);

        $raport = $this->serviciu($this->duk(false, "E: validari globale\n eroare atribut: cui: CUI invalid"))
            ->pentruCertificat($this->certificat);

        $this->assertSame(1, $raport['esuate']);
        $this->assertSame(0, $raport['semnate']);
        $this->assertStringContainsString('d394_iunie.xml', $raport['erori'][0]);

        $this->assertSame('eroare_validare', AnafDeclaratie::first()->pas);

        Http::assertSent(function (Request $cerere) {
            return $cerere->url() === 'http://192.168.1.77:8099/monitorizare/mutat'
                && strpos($cerere->body(), 'unde=erori') !== false;
        });

        Mail::assertSent(EroareDeclaratieEmail::class, function ($email) {
            return $email->hasTo('contabil@diana-soft.ro');
        });

        Mail::assertNotSent(EroareDeclaratieEmail::class, function ($email) {
            return $email->hasTo('altcineva@diana-soft.ro');
        });
    }

    /**
     * Tokenul blocat opreste doar declaratia lui, nu si restul lotului. Firma
     * nefiind inrolata pe alt certificat, instiintarea pleaca la oamenii
     * certificatului care urmareste dosarul.
     */
    public function test_semnarea_esuata_este_raportata_si_fisierul_pleaca_in_erori(): void
    {
        CertificatUtilizator::create([
            'company_id' => self::COMPANIE,
            'certificat_id' => $this->certificat->id,
            'email' => 'contabil@diana-soft.ro',
            'activ' => true,
        ]);

        $this->bridgeCu([['nume' => 'd394_iunie.xml', 'marime' => 812, 'gata' => true]]);

        $raport = $this->serviciu(null, $this->semnare('Tokenul nu este deblocat.'))
            ->pentruCertificat($this->certificat);

        $this->assertSame(1, $raport['esuate']);
        $this->assertStringContainsString('Tokenul nu este deblocat', $raport['erori'][0]);
        $this->assertSame('eroare_semnare', AnafDeclaratie::first()->pas);

        Mail::assertSent(EroareDeclaratieEmail::class);
    }

    /** Dosarul urmarit se stabileste in aplicatie, nu in bridge.env. */
    public function test_dosarul_urmarit_se_trimite_programului_local(): void
    {
        $this->bridgeCu([]);

        $this->serviciu()->pentruCertificat($this->certificat);

        Http::assertSent(function (Request $cerere) {
            return $cerere->hasHeader('X-Monitorizare-Cale', 'D:\\Declaratii de semnat')
                && $cerere->hasHeader('Authorization', 'Bearer token-contabil');
        });
    }

    /** Nebifata, urmarirea nu deranjeaza calculatorul clientului. */
    public function test_certificatul_fara_urmarire_nu_intreaba_nimic(): void
    {
        Http::fake();

        $this->certificat->update(['monitorizare_activa' => false]);

        $raport = $this->serviciu()->pentruCertificat($this->certificat->fresh());

        $this->assertSame(0, $raport['gasite']);
        Http::assertNothingSent();
    }

    /** Calculatorul inchis se spune pe intelesul omului, nu ca urma de eroare. */
    public function test_dosarul_necitit_este_raportat(): void
    {
        Http::fake([
            '192.168.1.77:8099/monitorizare' => Http::response(['eroare' => 'Dosarul urmărit nu există.'], 500),
        ]);

        $raport = $this->serviciu()->pentruCertificat($this->certificat);

        $this->assertSame(0, $raport['gasite']);
        $this->assertStringContainsString('Dosarul urmărit nu există.', $raport['erori'][0]);
    }
}
