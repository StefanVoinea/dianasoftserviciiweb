<?php

namespace Tests\Unit;

use App\Services\Anaf\Spv\SolicitareService;
use Tests\TestCase;

/**
 * Textul raspunsului se cere numai cand avem ce citi in el.
 *
 * Raspunsurile la solicitari veneau greu — trei-patru secunde bucata. O parte e
 * pauza ceruta de ANAF si drumul pana la calculatorul clientului, si acelea nu
 * se pot scurta. Dar se cerea si textul documentului, pentru orice tip: el se
 * scoate din PDF acolo, la client, si face drumul inapoi peste retea.
 *
 * Numai ca serverul talcuieste trei tipuri — vectorul fiscal, situatia sintetica
 * si datele de identificare. Pentru toate celelalte textul era aruncat. Iar cand
 * programul local nu izbutea sa-l citeasca, documentul era cerut inapoi din
 * arhiva: inca un drum dus-intors, tot pentru un text de aruncat.
 */
class TextulSeCereDoarCandFolosesteTest extends TestCase
{
    /** @var \ReflectionMethod */
    protected $intrebarea;

    protected function setUp(): void
    {
        parent::setUp();

        $this->intrebarea = new \ReflectionMethod(SolicitareService::class, 'areNevoieDeText');
        $this->intrebarea->setAccessible(true);
    }

    protected function areNevoie(?string $tip): bool
    {
        return $this->intrebarea->invoke(app(SolicitareService::class), $tip);
    }

    /** Tipurile talcuite cer textul. */
    public function test_tipurile_talcuite_cer_textul(): void
    {
        foreach ([
            'VECTOR FISCAL',
            'Vector fiscal',
            'SITUATIE SINTETICA',
            'SITUAȚIE SINTETICĂ',
            'DATE IDENTIFICARE',
            'DATE IDENTIFICARE PERSOANA JURIDICA',
        ] as $tip) {
            $this->assertTrue($this->areNevoie($tip), '„' . $tip . '" trebuie citit');
        }
    }

    /** Restul nu: textul lor s-ar arunca oricum. */
    public function test_celelalte_tipuri_nu_cer_textul(): void
    {
        foreach ([
            'EXTRAS DE CONT',
            'NOTIFICARE',
            'ADEVERINTA',
            'DECIZIE DE IMPUNERE',
            'FISA ROL',
            null,
            '',
        ] as $tip) {
            $this->assertFalse($this->areNevoie($tip), '„' . $tip . '" n-are de ce fi citit');
        }
    }

    /**
     * Cele doua liste nu au voie sa se departeze una de alta.
     *
     * Daca in „interpreteaza" apare un tip nou si nu e trecut si sus, documentul
     * soseste fara text si talcuirea nu mai are pe ce lucra — o pagubă tacuta,
     * care s-ar vedea abia peste luni, intr-o observatie care lipseste.
     */
    public function test_lista_acopera_tot_ce_se_talcuieste(): void
    {
        $sursa = file_get_contents(app_path('Services/Anaf/Spv/SolicitareService.php'));

        $inceput = strpos($sursa, 'protected function interpreteaza');
        $talcuirea = substr($sursa, $inceput, 3000);

        preg_match_all("/strpos\(\\\$tip, '([^']+)'\)/", $talcuirea, $gasite);

        $this->assertNotEmpty($gasite[1], 'nu s-a găsit niciun tip în „interpreteaza"');

        $lista = new \ReflectionClassConstant(SolicitareService::class, 'CU_TEXT');

        foreach ($gasite[1] as $tip) {
            $this->assertContains(
                $tip,
                $lista->getValue(),
                '„' . $tip . '" se tălmăcește, deci textul lui trebuie cerut'
            );
        }
    }

    /** Iar cand textul nu s-a cerut, nu se scrie ca n-a putut fi citit. */
    public function test_nu_se_plange_de_un_text_pe_care_nu_l_a_cerut(): void
    {
        $sursa = file_get_contents(app_path('Services/Anaf/Spv/SolicitareService.php'));

        $inceput = strpos($sursa, 'protected function preia');
        $bucata = substr($sursa, $inceput, 1800);

        $this->assertStringContainsString('$vreaText = $this->areNevoieDeText(', $bucata);
        $this->assertStringContainsString('if ($vreaText) {', $bucata);

        // Plangerea sta inauntrul lui „daca s-a cerut", nu deasupra lui.
        $plangerea = strpos($bucata, 'nu a putut fi citit');
        $conditia = strpos($bucata, 'if ($vreaText) {');

        $this->assertGreaterThan(
            $conditia,
            $plangerea,
            'plângerea trebuie să fie doar pentru documentele al căror text chiar l-am cerut'
        );
    }
}
