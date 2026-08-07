<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Penele de legatura cu ANAF: care trec de la sine si care nu.
 *
 * Programul local vorbeste cu ANAF prin curl.exe, cu certificatul de pe token.
 * Cand legatura se rupe la mijloc — SEC_E_CONTEXT_EXPIRED, adica sesiunea
 * securizata s-a stins inainte de capatul raspunsului — nu s-a stricat nimic la
 * client: se mai incearca o data. Ce nu are voie sa se intample e sa treaca
 * drept izbanda un raspuns venit pe jumatate.
 */
class PeneLegaturaAnafTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        require_once base_path('spv-bridge/curl-talcuri.php');
    }

    /** Ruperea legaturii se mai incearca o data; nu e vina nimanui. */
    public function test_ruperile_de_legatura_se_mai_incearca()
    {
        foreach ([35, 52, 55, 56] as $cod) {
            $this->assertTrue(pana_trecatoare($cod), 'Codul ' . $cod . ' trebuie reîncercat.');
        }
    }

    /** Ce nu tine de legatura nu se reincearca: raspunsul e raspuns. */
    public function test_izbanda_si_greselile_noastre_nu_se_reincearca()
    {
        foreach ([0, 6, 7, 22, 28, 60] as $cod) {
            $this->assertFalse(pana_trecatoare($cod), 'Codul ' . $cod . ' nu are de ce să fie reîncercat.');
        }
    }

    /** Mesajul spune ce s-a intamplat, nu doar un numar. */
    public function test_codul_56_este_talmacit_pentru_om()
    {
        $talc = talcul_curl(56);

        $this->assertStringContainsString('SEC_E_CONTEXT_EXPIRED', $talc);
        $this->assertStringContainsString('se primea răspunsul', $talc);
    }

    /**
     * Cand se repeta, pricina nu e reteaua, ci tokenul: fiecare legatura cu
     * ANAF cere cheia de pe el, iar dialogul de PIN nevazut de nimeni omoara
     * legatura. Se spune, ca sa nu se caute degeaba prin firewall.
     */
    public function test_ruperile_arata_si_spre_codul_pin_al_tokenului()
    {
        $this->assertStringContainsString('PIN', talcul_curl(56));
        $this->assertStringContainsString('PIN', talcul_curl(35));
    }

    /**
     * Codul 58 nu e o pana de retea, ci un certificat negasit — si nu se
     * reincearca: pana nu se conecteaza tokenul, a doua incercare da la fel.
     */
    public function test_certificatul_negasit_se_spune_pe_nume()
    {
        $this->assertFalse(pana_trecatoare(58));
        $this->assertStringContainsString('tokenul nu e conectat', talcul_curl(58));
    }

    /** Certificatul neincrezut arata spre antivirus, unde e si pricina. */
    public function test_codul_60_arata_spre_antivirus()
    {
        $this->assertStringContainsString('desfăcut', talcul_curl(60));
    }

    /** Un cod necunoscut nu ramane fara raspuns: se spune macar care e. */
    public function test_codul_necunoscut_tot_se_spune()
    {
        $this->assertStringContainsString('99', talcul_curl(99));
    }
}
