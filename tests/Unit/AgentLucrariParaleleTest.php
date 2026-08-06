<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Lucrarile agentului, despartite una de alta.
 *
 * Programul local serveste o singura cerere pe rand — asa e serverul din PHP —
 * iar agentul lua si el o singura comanda pe rand. Cele doua la un loc faceau ca
 * o descarcare lunga din SPV sa tina pe loc dosarul urmarit, si invers. De acum
 * instalarea porneste mai multe instante, pe porturi vecine, iar agentul da
 * fiecare comanda uneia libere, in procesul ei.
 */
class AgentLucrariParaleleTest extends TestCase
{
    protected $dosar;

    protected function setUp(): void
    {
        parent::setUp();

        require_once base_path('spv-bridge/agent-functii.php');

        $this->dosar = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'agent-proba-' . bin2hex(random_bytes(4));
        mkdir($this->dosar);
    }

    protected function tearDown(): void
    {
        foreach ((array) glob($this->dosar . DIRECTORY_SEPARATOR . '*') as $fisier) {
            @unlink($fisier);
        }

        @rmdir($this->dosar);

        parent::tearDown();
    }

    protected function config(array $locale): array
    {
        return ['dosar' => $this->dosar, 'local' => $locale[0], 'locale' => $locale];
    }

    protected function semn(string $adresa, string $id, ?int $vechime = null): void
    {
        $cale = $this->dosar . DIRECTORY_SEPARATOR . 'agent_lucru_' . $id . '.tmp';
        file_put_contents($cale, $adresa);

        if ($vechime !== null) {
            touch($cale, time() - $vechime);
        }
    }

    /** Porturile scrise de instalare devin adrese, fara sa se piarda cea de baza. */
    public function test_porturile_devin_adrese()
    {
        $adrese = agent_adresele_locale('http://127.0.0.1:8099', '8099, 8100, 8101');

        $this->assertSame([
            'http://127.0.0.1:8099',
            'http://127.0.0.1:8100',
            'http://127.0.0.1:8101',
        ], $adrese);
    }

    /** Instalarea veche n-are lista: se lucreaza pe una singura, ca inainte. */
    public function test_fara_lista_ramane_o_singura_instanta()
    {
        $this->assertSame(['http://127.0.0.1:8099'], agent_adresele_locale('http://127.0.0.1:8099', ''));
    }

    /** Se alege prima instanta fara lucrare. */
    public function test_se_alege_instanta_libera()
    {
        $config = $this->config([
            'http://127.0.0.1:8099',
            'http://127.0.0.1:8100',
        ]);

        $this->semn('http://127.0.0.1:8099', '11');

        $this->assertSame('http://127.0.0.1:8100', agent_adresa_libera($config));
    }

    /**
     * Cand toate lucreaza, nu se ia comanda: ea ramane la server, unde o poate
     * lua altcineva, in loc sa stea in mana agentului.
     */
    public function test_toate_ocupate_inseamna_asteptare()
    {
        $config = $this->config([
            'http://127.0.0.1:8099',
            'http://127.0.0.1:8100',
        ]);

        $this->semn('http://127.0.0.1:8099', '11');
        $this->semn('http://127.0.0.1:8100', '12');

        $this->assertNull(agent_adresa_libera($config));
    }

    /** Semnul ramas de la un proces cazut nu tine locul ocupat pentru totdeauna. */
    public function test_semnul_vechi_se_curata()
    {
        $config = $this->config(['http://127.0.0.1:8099']);

        $this->semn('http://127.0.0.1:8099', '13', 3600);

        $this->assertSame('http://127.0.0.1:8099', agent_adresa_libera($config));
        $this->assertFileDoesNotExist($this->dosar . DIRECTORY_SEPARATOR . 'agent_lucru_13.tmp');
    }

    /** Doua lucrari pe aceeasi instanta se numara amandoua. */
    public function test_lucrarile_se_numara_pe_instanta()
    {
        $config = $this->config(['http://127.0.0.1:8099', 'http://127.0.0.1:8100']);

        $this->semn('http://127.0.0.1:8099', '21');
        $this->semn('http://127.0.0.1:8099', '22');

        $active = agent_lucrari_active($config);

        $this->assertSame(2, $active['http://127.0.0.1:8099']);
        $this->assertArrayNotHasKey('http://127.0.0.1:8100', $active);
    }
}
