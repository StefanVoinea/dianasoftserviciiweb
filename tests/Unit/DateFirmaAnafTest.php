<?php

namespace Tests\Unit;

use App\Services\Anaf\DateFirma;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Datele firmei, luate de la ANAF după codul fiscal.
 *
 * Serviciul ANAF răspunde cu tot ce știe, într-o formă scrisă pentru program:
 * adresa ruptă în bucăți, totul cu majuscule, cu Ş și Ţ cu sedilă. Aici se
 * verifică drumul invers — cum ajunge răspunsul acela o adresă care se poate
 * pune într-un contract.
 */
class DateFirmaAnafTest extends TestCase
{
    protected function raspuns(array $peste = []): array
    {
        $generale = array_merge([
            'cui' => 15208744,
            'denumire' => 'DIANA SOFT SRL',
            'telefon' => '',
            'codPostal' => '905700',
            'stare_inregistrare' => 'INREGISTRAT din data 12.02.2003',
            'nrRegCom' => 'J13/888/2003',
            'cod_CAEN' => '6201',
        ], $peste['date_generale'] ?? []);

        $sediu = array_merge([
            'sdenumire_Localitate' => 'Orş. Năvodari',
            'sdenumire_Strada' => 'Str. Bradului',
            'snumar_Strada' => '13',
            'sdenumire_Judet' => 'CONSTANŢA',
            'sdetalii_Adresa' => '',
            'scod_Postal' => '905700',
        ], $peste['adresa_sediu_social'] ?? []);

        return [
            'found' => [[
                'date_generale' => $generale,
                'adresa_sediu_social' => $sediu,
                'inregistrare_scop_Tva' => array_merge(['scpTVA' => true], $peste['inregistrare_scop_Tva'] ?? []),
                'stare_inactiv' => array_merge([
                    'dataRadiere' => '',
                    'statusInactivi' => false,
                ], $peste['stare_inactiv'] ?? []),
            ]],
            'notFound' => [],
        ];
    }

    protected function intreaba(array $peste = [], string $cui = 'RO15208744'): ?array
    {
        Http::fake(['*' => Http::response($this->raspuns($peste), 200)]);

        return (new DateFirma())->dupaCui($cui);
    }

    /** @test */
    public function adresa_se_leaga_de_la_strada_spre_judet()
    {
        $firma = $this->intreaba();

        $this->assertSame('Str. Bradului nr. 13, Orș. Năvodari, jud. Constanța', $firma['adresa']);
    }

    /** @test */
    public function sedila_ANAF_se_face_virgula_sub_litera()
    {
        $firma = $this->intreaba();

        $this->assertSame('Constanța', $firma['judet']);
        $this->assertStringNotContainsString('ţ', $firma['adresa']);
        $this->assertStringNotContainsString('ş', $firma['adresa']);
    }

    /** @test */
    public function majusculele_se_aduc_la_scrisul_obisnuit_dar_SRL_ramane_SRL()
    {
        $firma = $this->intreaba();

        $this->assertSame('Diana Soft SRL', $firma['denumire']);
    }

    /** @test */
    public function la_bucuresti_judetul_nu_se_mai_scrie_a_doua_oara()
    {
        $firma = $this->intreaba(['adresa_sediu_social' => [
            'sdenumire_Localitate' => 'Sector 6 Mun. Bucureşti',
            'sdenumire_Strada' => 'Şos. Virtuţii',
            'snumar_Strada' => '148',
            'sdenumire_Judet' => 'Municipiul Bucureşti',
        ]]);

        $this->assertSame('Șos. Virtuții nr. 148, Sector 6 Mun. București', $firma['adresa']);
    }

    /** @test */
    public function detaliile_adresei_intra_intre_numar_si_localitate()
    {
        $firma = $this->intreaba(['adresa_sediu_social' => ['sdetalii_Adresa' => 'Bl. 32EST, Sc. D, Ap. 42']]);

        $this->assertSame(
            'Str. Bradului nr. 13, Bl. 32EST, Sc. D, Ap. 42, Orș. Năvodari, jud. Constanța',
            $firma['adresa']
        );
    }

    /** @test */
    public function firma_radiata_se_vede_si_cand_ANAF_nu_da_data_radierii()
    {
        $firma = $this->intreaba(['date_generale' => ['stare_inregistrare' => 'RADIERE din data 26.06.2002']]);

        $this->assertTrue($firma['radiata']);
    }

    /** @test */
    public function firma_in_regula_nu_e_nici_radiata_nici_inactiva()
    {
        $firma = $this->intreaba();

        $this->assertFalse($firma['radiata']);
        $this->assertFalse($firma['inactiva']);
        $this->assertTrue($firma['platitor_tva']);
    }

    /** @test */
    public function codul_se_ia_si_cu_RO_in_fata_si_cu_spatii()
    {
        foreach (['RO15208744', '  15208744 ', 'ro 15208744'] as $scris) {
            $firma = $this->intreaba([], $scris);

            $this->assertSame('15208744', $firma['cui'], 'scris „' . $scris . '”');
        }
    }

    /** @test */
    public function un_cod_fara_cifre_nu_ajunge_sa_fie_intrebat()
    {
        Http::fake(['*' => Http::response($this->raspuns(), 200)]);

        $this->assertNull((new DateFirma())->dupaCui('fara cifre'));

        Http::assertNothingSent();
    }

    /** @test */
    public function cand_ANAF_nu_gaseste_firma_nu_se_intoarce_nimic()
    {
        Http::fake(['*' => Http::response(['found' => [], 'notFound' => [99999999]], 200)]);

        $this->assertNull((new DateFirma())->dupaCui('99999999'));
    }

    /** @test */
    public function cand_serviciul_ANAF_e_cazut_nu_se_intoarce_nimic()
    {
        Http::fake(['*' => Http::response('bad gateway', 502)]);

        $this->assertNull((new DateFirma())->dupaCui('15208744'));
    }
}
