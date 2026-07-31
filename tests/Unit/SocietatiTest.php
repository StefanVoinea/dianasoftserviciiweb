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

    public function test_denumirea_goala_nu_suprascrie(): void
    {
        $societate = new AnafSocietate();
        $societate->setRawAttributes(['cif' => '1', 'denumire' => 'ACME SRL', 'denumire_sursa' => 'vector'], true);

        $this->assertFalse($societate->seteazaDenumire(null, 'date_identificare'));
        $this->assertFalse($societate->seteazaDenumire('   ', 'date_identificare'));
        $this->assertSame('ACME SRL', $societate->denumire);
    }
}
