<?php

namespace Tests\Unit;

use App\Models\EtransportDeclaratie;
use App\Services\Anaf\Etransport\DeclaratieXml;
use App\Services\Anaf\Etransport\EtransportException;
use Tests\TestCase;

/**
 * XML-ul declarației e-Transport, verificat pe schema oficială ANAF.
 *
 * Aici se prinde ce ar întoarce ANAF drept cod de eroare: o declarație fără
 * greutate brută, un cod de țară inventat, un traseu fără capăt. Testele merg
 * pe cazul din practică: achiziție intracomunitară din Italia, intrare prin
 * Borș 2 - A3, descărcare la o adresă din țară.
 */
class EtransportDeclaratieXmlTest extends TestCase
{
    /** Declaratia completa trece de schema si poarta toate atributele. */
    public function test_declaratia_completa_trece_de_schema_anaf()
    {
        $xml = (new DeclaratieXml())->construieste($this->declaratia());

        $this->assertStringContainsString('mfp:anaf:dgti:eTransport:declaratie:v2', $xml);
        $this->assertStringContainsString('codDeclarant="15196216"', $xml);
        $this->assertStringContainsString('codTipOperatiune="10"', $xml);
        $this->assertStringContainsString('codTarifar="61046200"', $xml);
        $this->assertStringContainsString('valoareLeiFaraTva="5162.15"', $xml);
        $this->assertStringContainsString('codPtf="38"', $xml);
        $this->assertStringContainsString('codJudet="40"', $xml);
        $this->assertStringContainsString('nrVehicul="BH93BPT"', $xml);
        $this->assertStringContainsString('tipDocument="20"', $xml);
    }

    /** Numarul de inmatriculare se curata de spatii si liniute, cum cere ANAF. */
    public function test_numarul_de_inmatriculare_se_curata()
    {
        $declaratie = $this->declaratia(['nr_vehicul' => 'bh 93 bpt']);

        $xml = (new DeclaratieXml())->construieste($declaratie);

        $this->assertStringContainsString('nrVehicul="BH93BPT"', $xml);
    }

    /** Lipsurile se spun pe romaneste, inainte de a ajunge la ANAF. */
    public function test_lipsurile_se_spun_pe_intelesul_omului()
    {
        $declaratie = $this->declaratia(['nr_vehicul' => null, 'documente' => []]);

        try {
            (new DeclaratieXml())->construieste($declaratie);
            $this->fail('Declarația fără vehicul și documente ar fi trebuit oprită.');
        } catch (EtransportException $e) {
            $this->assertStringContainsString('numărul de înmatriculare', $e->getMessage());
            $this->assertStringContainsString('documentele de transport', $e->getMessage());
        }
    }

    /** Regula ANAF BR-026: documentul „Altele" fara observatii se opreste aici. */
    public function test_documentul_altele_fara_observatii_se_spune_pe_romaneste()
    {
        $declaratie = $this->declaratia([
            'documente' => [['tip' => 9999, 'numar' => '10067639', 'data' => '2026-08-11']],
        ]);

        try {
            (new DeclaratieXml())->construieste($declaratie);
            $this->fail('Documentul „Altele" fără observații ar fi trebuit oprit.');
        } catch (EtransportException $e) {
            $this->assertStringContainsString('Altele', $e->getMessage());
            $this->assertStringContainsString('observații', $e->getMessage());
        }

        // Cu observatiile scrise, trece si le poarta in XML.
        $declaratie = $this->declaratia([
            'documente' => [['tip' => 9999, 'numar' => '10067639', 'data' => '2026-08-11', 'observatii' => 'aviz intern']],
        ]);

        $xml = (new DeclaratieXml())->construieste($declaratie);

        $this->assertStringContainsString('tipDocument="9999"', $xml);
        $this->assertStringContainsString('observatii="aviz intern"', $xml);
    }

    /** O linie fara greutate bruta opreste declaratia cu numarul liniei. */
    public function test_linia_fara_greutate_bruta_se_spune_cu_numarul_ei()
    {
        $declaratie = $this->declaratia();
        $linii = $declaratie->linii;
        $linii[0]['greutate_bruta'] = null;
        $declaratie->linii = $linii;

        try {
            (new DeclaratieXml())->construieste($declaratie);
            $this->fail('Linia fără greutate brută ar fi trebuit oprită.');
        } catch (EtransportException $e) {
            $this->assertStringContainsString('linia 1', $e->getMessage());
        }
    }

    protected function declaratia(array $inLocul = []): EtransportDeclaratie
    {
        return new EtransportDeclaratie(array_merge([
            'cif_declarant' => 'RO15196216',
            'referinta_interna' => 'NIR 10067639',
            'tip_operatiune' => 10,
            'partener_tara' => 'IT',
            'partener_cod' => '00953910403',
            'partener_denumire' => 'TEDDY S.p.A.',
            'nr_vehicul' => 'BH93BPT',
            'transportator_tara' => 'RO',
            'transportator_cod' => '13569610',
            'transportator_denumire' => 'RUTILLI ADOLFO S.R.L.',
            'data_transport' => '2026-08-14',
            'loc_start' => ['tip' => 'ptf', 'cod_ptf' => 38],
            'loc_final' => [
                'tip' => 'adresa',
                'cod_judet' => 40,
                'localitate' => 'bucuresti',
                'strada' => 'bdul magheru',
                'numar' => '33',
            ],
            'documente' => [
                ['tip' => 20, 'numar' => '10038435', 'data' => '2026-05-08'],
            ],
            'linii' => [
                [
                    'cod_tarifar' => '61046200',
                    'denumire' => 'Pantaloni, din bumbac, pentru femei sau fete, tricotate',
                    'scop_operatiune' => 101,
                    'cantitate' => 133,
                    'um' => 'H87',
                    'greutate_neta' => 20.307,
                    'greutate_bruta' => 22.515,
                    'valoare' => 985.23,
                    'valoare_lei' => 5162.15,
                ],
            ],
        ], $inLocul));
    }
}
