<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Ce deosebeste agentul: ziua obisnuita de lucru de pana adevarata.
 *
 * Agentul tine linia deschisa catre server si intreaba „ai ceva pentru mine?".
 * De cele mai multe ori raspunsul e „nu" — asta nu inseamna ca s-a stricat
 * ceva. Multa vreme insemna: raspunsul „comanda": null trecea drept pana de
 * retea, jurnalul se umplea de „Serverul nu raspunde", iar asteptarea crestea
 * pana la un minut, in care comenzile chiar sosite stateau la coada.
 */
class AgentPandaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        require_once base_path('spv-bridge/agent-functii.php');
    }

    /** @return array<string, mixed> raspunsul asa cum il da agent_curl */
    protected function raspuns(int $cod, int $status, string $corp): array
    {
        return ['cod' => $cod, 'status' => $status, 'corp' => $corp];
    }

    /** Panda implinita fara nimic de lucru: cazul cel mai obisnuit din viata agentului. */
    public function test_panda_goala_nu_este_pana()
    {
        $motiv = 'ceva';

        $rezultat = agent_desluseste_panda($this->raspuns(0, 200, '{"comanda":null}'), $motiv);

        $this->assertNull($rezultat, 'Pânda goală trebuie să fie null, nu false.');
        $this->assertSame('', $motiv);
    }

    /** Comanda se intoarce intreaga, ca sa poata fi dusa la indeplinire. */
    public function test_comanda_se_intoarce_intreaga()
    {
        $motiv = '';
        $corp = '{"comanda":{"id":7,"metoda":"GET","cale":"/monitorizare"}}';

        $comanda = agent_desluseste_panda($this->raspuns(0, 200, $corp), $motiv);

        $this->assertSame(7, $comanda['id']);
        $this->assertSame('/monitorizare', $comanda['cale']);
    }

    /** Cand curl nu poate deschide legatura, se spune si unde sa se uite omul. */
    public function test_legatura_neblocata_spune_de_firewall()
    {
        $motiv = '';

        $rezultat = agent_desluseste_panda($this->raspuns(7, 0, ''), $motiv);

        $this->assertFalse($rezultat);
        $this->assertStringContainsString('firewall', $motiv);
        $this->assertStringContainsString('curl 7', $motiv);
    }

    /**
     * La 28, vorba lui curl deosebeste doua pene care se scriu la fel: legatura
     * care nu s-a putut deschide de raspunsul care n-a mai venit.
     */
    public function test_vremea_scursa_spune_si_cat_a_asteptat()
    {
        $motiv = '';
        $iesire = 'curl: (28) Connection timed out after 21014 milliseconds';

        $rezultat = agent_desluseste_panda($this->raspuns(28, 0, $iesire), $motiv);

        $this->assertFalse($rezultat);
        $this->assertStringContainsString('curl 28', $motiv);
        $this->assertStringContainsString('21014', $motiv);
    }

    /**
     * Certificatul neincrezut e semnul traficului desfacut de antivirus — acolo
     * cade si legitimarea cu certificatul de pe token.
     */
    public function test_certificatul_neincrezut_arata_spre_antivirus()
    {
        $motiv = '';

        $rezultat = agent_desluseste_panda($this->raspuns(60, 0, ''), $motiv);

        $this->assertFalse($rezultat);
        $this->assertStringContainsString('desfăcut', $motiv);
    }

    /** Un raspuns care nu seamana cu al aplicatiei vine de la altcineva de pe drum. */
    public function test_raspunsul_strain_se_recunoaste()
    {
        $motiv = '';
        $pagina = '<html><body>Acces blocat de politica de securitate</body></html>';

        $rezultat = agent_desluseste_panda($this->raspuns(0, 200, $pagina), $motiv);

        $this->assertFalse($rezultat);
        $this->assertStringContainsString('proxy', $motiv);
        $this->assertStringContainsString('Acces blocat', $motiv);
    }

    /** Codurile HTTP intalnite aievea isi au talcul lor, nu doar numarul. */
    public function test_codul_http_are_talc()
    {
        $motiv = '';

        $rezultat = agent_desluseste_panda($this->raspuns(0, 503, 'Service Unavailable'), $motiv);

        $this->assertFalse($rezultat);
        $this->assertStringContainsString('oprit sau în întreținere', $motiv);
        $this->assertStringContainsString('503', $motiv);
    }

    /** Randul din jurnal ramane citibil chiar daca raspunsul strain e o pagina intreaga. */
    public function test_inceputul_raspunsului_incape_intr_un_rand()
    {
        $lung = str_repeat('abcdefghij ', 40);

        // 70 de caractere plus semnul de taiere; se numara in caractere, ca „…"
        // ocupa trei octeti.
        $this->assertLessThanOrEqual(71, mb_strlen(agent_inceputul($lung)));
        $this->assertSame('(răspuns gol)', agent_inceputul("  \n\t "));
    }

    /**
     * „Nu ascultă nimeni" si „ascultă, dar tace" nu sunt acelasi lucru.
     *
     * Jurnalul scria doar „(0)" pentru amandoua, iar ele se indreapta cu totul
     * altfel: prima inseamna un program oprit — se porneste; a doua, unul prins
     * in altceva, cel mai des o fereastra de PIN pe care n-o inchide nimeni — se
     * inchide fereastra. Cu „starea 0" scrisa in jurnal, omul cauta unde nu e.
     */
    public function test_programul_oprit_se_deosebeste_de_cel_prins_in_altceva()
    {
        $oprit = agent_talcul_local($this->raspuns(7, 0, ''));
        $prins = agent_talcul_local($this->raspuns(28, 0, ''));

        $this->assertStringContainsString('nu rulează', $oprit);
        $this->assertStringContainsString('prins în altceva', $prins);
        $this->assertStringContainsString('PIN', $prins, 'trebuie spusă pricina cea mai deasă');
        $this->assertNotSame($oprit, $prins);
    }

    /** Un raspuns venit, oricare ar fi el, se spune ca atare. */
    public function test_raspunsul_venit_se_spune_cu_starea_lui()
    {
        $this->assertStringContainsString('403', agent_talcul_local($this->raspuns(0, 403, '')));
    }

    /**
     * Pregatirile bat la usa oricarei instante, nu doar a celei dintai.
     *
     * Programul e pornit in mai multe instante tocmai fiindca serveste o cerere
     * pe rand. Cand cea dintai era prinsa in altceva, agentul se oprea desi
     * vecinele erau libere — si nu se inrola, si nu-si lua licenta.
     */
    public function test_pregatirile_incearca_toate_instantele()
    {
        $functii = file_get_contents(base_path('spv-bridge/agent-functii.php'));

        $this->assertStringContainsString('function agent_intreaba_local', $functii);

        foreach (["'/certificate'", "'/identitate'", "'/licenta'"] as $cale) {
            $this->assertStringContainsString(
                'agent_intreaba_local($config, ' . $cale,
                $functii,
                $cale . ' trebuie cerut pe oricare instanță răspunde'
            );
        }
    }
}
