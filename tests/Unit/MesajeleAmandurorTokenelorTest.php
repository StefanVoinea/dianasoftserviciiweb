<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * „Descarcă mesaje" intreaba cu fiecare token, nu doar cu cel implicit.
 *
 * Un contabil are des doua tokene pe acelasi calculator: unul al firmei lui si
 * unul al clientului. Programul local stia de mult sa lucreze cu amandoua, iar
 * documentele se aduceau cu tokenul fiecarui mesaj — dar LISTA se cerea de la
 * ANAF cu unul singur, cel implicit.
 *
 * Mesajele celuilalt nu veneau deci niciodata, si nici nu se vedea de ce:
 * singurul chip de a le afla era sa fie schimbat certificatul implicit inainte
 * de fiecare descarcare. Se ajungea la „merge numai daca schimb certificatul".
 *
 * La clientii cu un singur certificat nu se schimba nimic: lista are un rand.
 */
class MesajeleAmandurorTokenelorTest extends TestCase
{
    /** @var string */
    protected $sursa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sursa = file_get_contents(app_path('Http/Controllers/Api/SpvController.php'));
    }

    /** Se intreaba pe rand, nu o singura data. */
    public function test_lista_se_cere_cu_fiecare_certificat(): void
    {
        $inceput = strpos($this->sursa, 'public function index');
        $bucata = substr($this->sursa, $inceput, 2600);

        $this->assertStringContainsString(
            'foreach ($this->certificateDeIntrebat($request) as $certificat)',
            $bucata,
            'lista trebuie cerută cu fiecare token'
        );

        $this->assertStringContainsString('$this->certificate->foloseste($certificat);', $bucata);
        $this->assertStringContainsString('$spvClient->listaMesaje($zile', $bucata);
    }

    /**
     * Un token neconectat nu-i opreste pe ceilalti.
     *
     * E cazul obisnuit, nu unul rar: tokenele se conecteaza pe rand, dupa cum
     * are omul treaba. Daca lipsa unuia ar darama toata citirea, cel de-al
     * doilea token ar fi mai degraba o pacoste decat un ajutor.
     */
    public function test_un_token_care_tace_nu_opreste_citirea(): void
    {
        $inceput = strpos($this->sursa, 'public function index');
        $bucata = substr($this->sursa, $inceput, 3000);

        $this->assertStringContainsString('catch (SpvException $e)', $bucata);
        $this->assertStringContainsString('$tacute[] =', $bucata);
        $this->assertStringContainsString('continue;', $bucata);
    }

    /** Dar daca toate tac, atunci chiar n-a mers nimic si se spune. */
    public function test_cand_toate_tokenele_tac_se_da_eroare(): void
    {
        $this->assertStringContainsString(
            'if ($brute === [] && $tacute !== []) {',
            $this->sursa,
            'tăcerea tuturor nu are voie să treacă drept „nimic nou"'
        );

        $inceput = strpos($this->sursa, 'if ($brute === [] && $tacute !== []) {');
        $bucata = substr($this->sursa, $inceput, 200);

        $this->assertStringContainsString('throw new SpvException', $bucata);
    }

    /** Cand omul a ales anume un certificat, se intreaba doar cu acela. */
    public function test_alegerea_omului_se_respecta(): void
    {
        $inceput = strpos($this->sursa, 'protected function certificateDeIntrebat');
        $bucata = substr($this->sursa, $inceput, 1400);

        $this->assertStringContainsString("\$request->filled('certificat_id')", $bucata);
        $this->assertStringContainsString("\$request->header('X-Certificat-Id')", $bucata);

        // Aceleasi doua cai pe care le citeste si serviciul; altfel s-ar despartit.
        $serviciu = file_get_contents(app_path('Services/Anaf/Spv/CertificatService.php'));

        $this->assertStringContainsString("header('X-Certificat-Id')", $serviciu);
        $this->assertStringContainsString("input('certificat_id')", $serviciu);
    }

    /** Se intreaba numai cu certificatele la care omul are drept. */
    public function test_se_intreaba_doar_cu_certificatele_omului(): void
    {
        $inceput = strpos($this->sursa, 'protected function certificateDeIntrebat');
        $bucata = substr($this->sursa, $inceput, 1400);

        $this->assertStringContainsString('ContextUtilizator::certificateAccesibile()', $bucata);
        $this->assertStringContainsString("where('activ', true)", $bucata);
    }

    /**
     * Fara niciun certificat inregistrat se merge ca pana acum: un singur
     * „null", adica „lasa serviciul sa aleaga" — instalarile care lucreaza din
     * configurare nu au randuri in tabel.
     */
    public function test_fara_certificate_inregistrate_merge_ca_pana_acum(): void
    {
        $inceput = strpos($this->sursa, 'protected function certificateDeIntrebat');
        $bucata = substr($this->sursa, $inceput, 1400);

        $this->assertStringContainsString('return $toate ?: [null];', $bucata);
    }

    /** Iar fila spune care token n-a raspuns: altfel lista pare intreaga. */
    public function test_fila_spune_care_token_a_tacut(): void
    {
        $this->assertStringContainsString("'tacute' => \$tacute,", $this->sursa);

        $fila = file_get_contents(resource_path('js/src/views/app_pages/spv/Mesaje.vue'));

        $this->assertStringContainsString('payload.tacute', $fila);
        $this->assertStringContainsString('Nu s-a putut întreba cu:', $fila);
    }
}
