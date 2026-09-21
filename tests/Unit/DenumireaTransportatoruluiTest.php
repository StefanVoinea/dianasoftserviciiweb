<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\EtransportDeclaratiiController;
use App\Services\Anaf\DateFirma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Denumirea transportatorului, luată de la ANAF după CIF.
 *
 * Nu e doar o comoditate: la e-Transport, denumirea scrisă altfel decât o are
 * ANAF întoarce declarația. Scrisă de mână, ea se scrie de fiecare dată puțin
 * altfel — „SC Ceva Trans SRL", „CEVA TRANS S.R.L." —, iar omul află abia la
 * depunere.
 */
class DenumireaTransportatoruluiTest extends TestCase
{
    protected function raspunsAnaf(array $peste = []): array
    {
        return [
            'found' => [[
                'date_generale' => array_merge([
                    'cui' => 15208744,
                    'denumire' => 'DIANA SOFT SRL',
                    'nrRegCom' => 'J13/888/2003',
                    'stare_inregistrare' => 'INREGISTRAT din data 12.02.2003',
                ], $peste),
                'adresa_sediu_social' => [
                    'sdenumire_Localitate' => 'Orş. Năvodari',
                    'sdenumire_Strada' => 'Str. Bradului',
                    'snumar_Strada' => '13',
                    'sdenumire_Judet' => 'CONSTANŢA',
                ],
                'inregistrare_scop_Tva' => ['scpTVA' => true],
                'stare_inactiv' => ['dataRadiere' => '', 'statusInactivi' => false],
            ]],
            'notFound' => [],
        ];
    }

    protected function intreaba(string $cui)
    {
        return (new EtransportDeclaratiiController())->firma(
            Request::create('/api/anaf-etransport/declaratii/firma', 'GET', ['cui' => $cui]),
            new DateFirma()
        );
    }

    /** @test */
    public function dupa_cif_vine_denumirea()
    {
        Http::fake(['*' => Http::response($this->raspunsAnaf(), 200)]);

        $raspuns = $this->intreaba('RO15208744');
        $date = $raspuns->getData(true);

        $this->assertSame(200, $raspuns->status());
        $this->assertTrue($date['success']);
        $this->assertSame('Diana Soft SRL', $date['data']['denumire']);
        $this->assertSame('15208744', $date['data']['cui']);
    }

    /** @test */
    public function un_cod_pe_care_ANAF_nu_l_cunoaste_se_spune_pe_fata()
    {
        Http::fake(['*' => Http::response(['found' => [], 'notFound' => [99999999]], 200)]);

        $raspuns = $this->intreaba('99999999');

        $this->assertSame(404, $raspuns->status());
        $this->assertStringContainsString('nicio firmă', $raspuns->getData(true)['message']);
    }

    /** @test */
    public function serviciul_ANAF_cazut_nu_arunca_exceptie()
    {
        Http::fake(['*' => Http::response('bad gateway', 502)]);

        $this->assertSame(404, $this->intreaba('15208744')->status());
    }

    /**
     * Un transportator radiat se vede din răspuns, ca omul să fie prevenit
     * înainte de depunere, nu după.
     *
     * @test
     */
    public function firma_radiata_se_cunoaste_din_raspuns()
    {
        Http::fake(['*' => Http::response(
            $this->raspunsAnaf(['stare_inregistrare' => 'RADIERE din data 26.06.2002']),
            200
        )]);

        $date = $this->intreaba('15208744')->getData(true);

        $this->assertTrue($date['data']['radiata']);
    }

    /** @test */
    public function codul_se_ia_si_cu_RO_in_fata()
    {
        Http::fake(['*' => Http::response($this->raspunsAnaf(), 200)]);

        $this->assertSame('15208744', $this->intreaba('  RO 15208744 ')->getData(true)['data']['cui']);
    }
}
