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

    /**
     * La pana de fel TLS nu se mai insira pricinile cu putinta: se intreaba
     * tokenul si se spune ce a raspuns.
     *
     * Mesajul de dinainte incepea cu „cea mai deasa pricina nu e reteaua, ci
     * tokenul" si trimitea omul sa caute PIN-ul — chiar si la clientii unde
     * PIN-ul era dat de mult, iar traficul era desfacut de antivirus. O
     * banuiala scrisa apasat costa zile de cautat unde nu e nimic.
     */
    public function test_cheia_buna_scoate_pinul_dintre_pricini()
    {
        $talc = talcul_curl(56, 'bun');

        $this->assertStringContainsString('NU e pricina', $talc);
        $this->assertStringContainsString('filtrarea SSL/TLS', $talc, 'trebuie arătat încotro se caută');
    }

    /** Cheia care nu se poate folosi e spusa raspicat, cu ce e de facut. */
    public function test_cheia_blocata_e_spusa_raspicat()
    {
        $talc = talcul_curl(56, 'blocat');

        $this->assertStringContainsString('aceasta este pricina', $talc);
        $this->assertStringContainsString('PIN', $talc);
        $this->assertStringNotContainsString('antivirus', $talc, 'nu se mai însiră pricini care au căzut');
    }

    /** Neintrebat tokenul, se spun pricinile cu putinta — ca inainte. */
    public function test_fara_raspuns_de_la_token_se_spun_pricinile_cu_putinta()
    {
        $talc = talcul_curl(56, 'necunoscut');

        $this->assertStringContainsString('PIN', $talc);
        $this->assertStringContainsString('antivirus', $talc);
        $this->assertStringNotContainsString('a fost întrebat', $talc, 'nu se pretinde ceva ce nu s-a aflat');
    }

    /**
     * Numai la penele in care cheia chiar e in joc. La un DNS cazut, un rand
     * despre token n-ar face decat sa incurce.
     */
    public function test_la_penele_fara_legatura_cu_cheia_nu_se_pomeneste_de_token()
    {
        foreach ([6, 7, 28, 52, 60] as $cod) {
            $this->assertStringNotContainsString('Tokenul a fost întrebat', talcul_curl($cod, 'bun'));
        }
    }

    /** Programul chiar intreaba tokenul cand legatura se rupe. */
    public function test_programul_intreaba_tokenul_la_pana()
    {
        $server = file_get_contents(base_path('spv-bridge/server.php'));

        $this->assertStringContainsString('function starea_cheii', $server);
        $this->assertStringContainsString("array(35, 56)", $server, 'se întreabă doar la penele de fel TLS');
        $this->assertStringContainsString("\$rezultat['cheia']", $server, 'ce s-a aflat merge până la mesaj');
    }
}
