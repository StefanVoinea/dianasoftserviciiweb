<?php

namespace Tests\Unit;

use App\Models\AnafSocietate;
use App\Services\Anaf\Spv\SocietatiService;
use Tests\TestCase;

class SocietatiTest extends TestCase
{
    public function test_serviciul_este_inregistrat(): void
    {
        $this->assertInstanceOf(SocietatiService::class, $this->app->make(SocietatiService::class));
    }

    /** @dataProvider cifuri */
    public function test_tipul_se_deduce_din_cif(string $cif, string $asteptat): void
    {
        $this->assertSame($asteptat, AnafSocietate::tipDupaCif($cif));
    }

    public function cifuri(): array
    {
        return [
            'CUI societate' => ['15208744', 'pj'],
            'CNP persoană fizică' => ['1720913216197', 'pf'],
            'CUI scurt' => ['123', 'pj'],
        ];
    }

    public function test_denumirea_din_vector_are_prioritate_fata_de_date_identificare(): void
    {
        $societate = new AnafSocietate(['cif' => '15208744']);
        $societate->exists = true;

        // Simulam salvarea fara baza de date
        $societate->setRawAttributes(['cif' => '15208744', 'denumire' => null, 'denumire_sursa' => null], true);

        $prioritati = AnafSocietate::PRIORITATE_SURSE;

        $this->assertGreaterThan($prioritati['date_identificare'], $prioritati['vector']);
        $this->assertGreaterThan($prioritati['vector'], $prioritati['manual']);
    }

    /**
     * Cele doua documente SPV au formate diferite: vectorul are numele in antet,
     * iar documentul de identificare pe un rand "Denumire <nume>".
     *
     * @dataProvider texteDocumente
     */
    public function test_denumirea_se_extrage_din_ambele_documente(string $text, ?string $cui, ?string $asteptat): void
    {
        $parser = new class extends \App\Services\Anaf\Spv\VectorFiscalParser {
            public $textFals = '';

            protected function text(string $calePdf): string
            {
                return $this->textFals;
            }
        };

        $parser->textFals = $text;

        $this->assertSame($asteptat, $parser->citesteDenumire('oricare.pdf', $cui));
    }

    public function texteDocumente(): array
    {
        return [
            'antet vector fiscal' => [
                "DATE PRIVIND SOCIETATEA DIANA SOFT SRL CE ARE CUI-ul 15208744\nLA DATA DE\n",
                '15208744',
                'DIANA SOFT SRL',
            ],
            'rand din date identificare' => [
                "Stare\nCAEN\nDenumire DIANA SOFT SRL\nJUD. CONSTANŢA\n",
                '15208744',
                'DIANA SOFT SRL',
            ],
            'antet amestecat, nume dupa CUI' => [
                "CE ARE CUI-ulDATE PRIVIND SOCIETATEA\t15208744DIANA SOFT SRL\n",
                '15208744',
                'DIANA SOFT SRL',
            ],
            'fara denumire' => ["Document fara nume\n", '15208744', null],
        ];
    }

    /**
     * Fiecare certificat isi are entitatile lui: cand unul pierde drepturile pe
     * un CIF, entitatile celorlalte certificate nu trebuie atinse.
     */
    public function test_dezactivarea_este_limitata_la_certificatul_sincronizat(): void
    {
        $unu = \App\Models\AnafCertificat::create([
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'Certificat unu',
            'valabil_pana_la' => now()->addYear(),
        ]);
        $doi = \App\Models\AnafCertificat::create([
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'Certificat doi',
            'valabil_pana_la' => now()->addYear(),
        ]);

        $pastrat = AnafSocietate::create(['cif' => 'TEST-A', 'certificat_id' => $unu->id, 'activ' => true]);
        $pierdut = AnafSocietate::create(['cif' => 'TEST-B', 'certificat_id' => $unu->id, 'activ' => true]);
        $altul = AnafSocietate::create(['cif' => 'TEST-C', 'certificat_id' => $doi->id, 'activ' => true]);

        // ANAF mai raporteaza doar TEST-A pentru primul certificat.
        $this->mock(\App\Services\Anaf\Spv\SpvClient::class, function ($mock) {
            $mock->shouldReceive('listaMesajeBrut')->andReturn([
                'cui' => 'TEST-A',
                'cnp' => '1234567890123',
                'serial' => 'serie-test',
            ]);
        });

        $rezultat = $this->app->make(SocietatiService::class)->sincronizeaza($unu);

        $this->assertSame(1, $rezultat['gasite']);
        $this->assertSame(1, $rezultat['dezactivate']);

        $this->assertTrue($pastrat->fresh()->activ);
        $this->assertFalse($pierdut->fresh()->activ, 'CIF-ul pierdut trebuia dezactivat');
        $this->assertTrue($altul->fresh()->activ, 'Entitatea altui certificat nu trebuia atinsă');

        AnafSocietate::whereIn('cif', ['TEST-A', 'TEST-B', 'TEST-C'])->delete();
        $unu->delete();
        $doi->delete();
    }

    /**
     * Lista de CIF-uri vine odata cu mesajele. Cand in ziua ceruta nu e niciun
     * mesaj, ANAF raspunde uneori doar cu motivul, fara lista — atunci se
     * intreaba din nou, pe fereastra intreaga.
     */
    public function test_lista_se_cere_din_nou_pe_fereastra_intreaga(): void
    {
        $certificat = \App\Models\AnafCertificat::create([
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'Certificat fara mesaje azi',
            'valabil_pana_la' => now()->addYear(),
        ]);

        $cerute = [];

        $this->mock(\App\Services\Anaf\Spv\SpvClient::class, function ($mock) use (&$cerute) {
            $mock->shouldReceive('listaMesajeBrut')->andReturnUsing(function ($zile) use (&$cerute) {
                $cerute[] = $zile;

                return $zile <= 1
                    ? ['eroare' => 'Nu exista mesaje in ultimele 1 zile']
                    : ['cui' => 'TEST-D', 'cnp' => '1234567890123', 'serial' => 'serie-test'];
            });
        });

        $rezultat = $this->app->make(SocietatiService::class)->sincronizeaza($certificat);

        $this->assertSame([1, (int) config('anaf.spv.zile_max')], $cerute);
        $this->assertSame(['TEST-D'], $rezultat['cif']);

        AnafSocietate::where('cif', 'TEST-D')->delete();
        $certificat->delete();
    }

    /**
     * Certificatul cu care tocmai s-a vorbit nu se mai cauta pe calculatoare.
     *
     * Altfel se pleca sa fie citit de pe bridge-ul din configuratie — care in
     * cloud nu exista — si cererea se intorcea cu „Could not resolve host".
     */
    public function test_certificatul_de_lucru_nu_se_mai_citeste_de_pe_bridge(): void
    {
        $certificat = \App\Models\AnafCertificat::create([
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'Certificat de lucru',
            'valabil_pana_la' => now()->addYear(),
        ]);

        \Illuminate\Support\Facades\Http::fake();

        $this->mock(\App\Services\Anaf\Spv\SpvClient::class, function ($mock) {
            $mock->shouldReceive('listaMesajeBrut')->andReturn([
                'cui' => 'TEST-E',
                'cnp' => '9876543210123',
                'serial' => 'serie-de-la-anaf',
            ]);
        });

        $certificate = $this->app->make(\App\Services\Anaf\Spv\CertificatService::class);
        $certificate->foloseste($certificat);

        // Fara certificat dat: serviciul trebuie sa-l ia pe cel cu care lucreaza.
        $rezultat = $this->app->make(SocietatiService::class)->sincronizeaza();

        \Illuminate\Support\Facades\Http::assertNothingSent();

        $this->assertSame($certificat->id, $rezultat['certificat_id']);
        $this->assertSame('serie-de-la-anaf', $certificat->fresh()->serie_anaf);
        $this->assertSame('9876543210123', $certificat->fresh()->cnp);

        AnafSocietate::where('cif', 'TEST-E')->delete();
        $certificat->delete();
    }

    /** Fara adresa de calculator se spune ce lipseste, nu se pleaca in gol. */
    public function test_fara_adresa_de_bridge_se_spune_ce_lipseste(): void
    {
        $config = config('anaf.spv');
        $config['bridge']['url'] = '';

        $certificate = new \App\Services\Anaf\Spv\CertificatService($config);

        try {
            $certificate->sincronizeaza(['serial' => 'x']);
            $this->fail('Trebuia să se oprească: nu există adresă de calculator.');
        } catch (\App\Services\Anaf\Spv\SpvException $e) {
            $this->assertStringContainsString('Nu se știe pe ce calculator', $e->getMessage());
        }
    }

    /** Cand nici asa nu vine nimic, se spune si ce a raspuns ANAF. */
    public function test_mesajul_de_la_anaf_ajunge_la_utilizator(): void
    {
        $certificat = \App\Models\AnafCertificat::create([
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'Certificat fara drepturi',
            'valabil_pana_la' => now()->addYear(),
        ]);

        $this->mock(\App\Services\Anaf\Spv\SpvClient::class, function ($mock) {
            $mock->shouldReceive('listaMesajeBrut')->andReturn([
                'eroare' => 'Certificatul nu este inrolat in SPV',
            ]);
        });

        try {
            $this->app->make(SocietatiService::class)->sincronizeaza($certificat);
            $this->fail('Trebuia să se oprească: nu a venit nicio listă de CIF-uri.');
        } catch (\App\Services\Anaf\Spv\SpvException $e) {
            $this->assertStringContainsString('Certificatul nu este inrolat in SPV', $e->getMessage());
        } finally {
            $certificat->delete();
        }
    }

    public function test_denumirea_goala_nu_suprascrie(): void
    {
        $societate = new AnafSocietate();
        $societate->setRawAttributes(['cif' => '1', 'denumire' => 'ACME SRL', 'denumire_sursa' => 'vector'], true);

        $this->assertFalse($societate->seteazaDenumire(null, 'date_identificare'));
        $this->assertFalse($societate->seteazaDenumire('   ', 'date_identificare'));
        $this->assertSame('ACME SRL', $societate->denumire);
    }
}
