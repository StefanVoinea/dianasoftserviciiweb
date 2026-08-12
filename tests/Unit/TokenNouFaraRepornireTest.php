<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Al doilea token, conectat dupa pornirea agentului, ajunge singur in aplicatie.
 *
 * Programul local stia de mult sa lucreze cu doua tokene deodata: amprenta vine
 * cu fiecare cerere, iar prajiturile de sesiune stau deoparte pentru fiecare
 * certificat. Ce lipsea era mai devreme, la aflarea lor: certificatele se
 * citeau o singura data, la pornirea agentului.
 *
 * Un contabil cu doua tokene le conecteaza pe rand, dupa cum are treaba. Al
 * doilea nu ajungea niciodata sa fie anuntat, iar singurul leac era repornirea
 * agentului — pe calculatorul clientului, unde nu ajunge nimeni usor.
 *
 * Proba lucreaza pe codul adevarat al agentului, cu vorbitul spre lume astupat.
 * Rulează insa intr-un alt proces: functiile agentului sunt globale, iar o alta
 * proba din suita incarca acelasi fisier — inlocuirile s-ar fi lovit de el si ar
 * fi daramat toata suita.
 */
class TokenNouFaraRepornireTest extends TestCase
{
    /** @var array<string, mixed>|null raportul rularii, facut o singura data */
    protected static $raport;

    protected function setUp(): void
    {
        parent::setUp();

        if (self::$raport === null) {
            self::$raport = $this->ruleazaAgentul();
        }
    }

    /**
     * Codul adevarat, cu trei ferestre spre lume astupate.
     *
     * Se redenumesc in copie functiile care ies din calculator, iar in locul lor
     * se pun altele, sub numele vechi. Asa se cantareste chiar codul care va
     * rula la client, nu o repovestire a lui.
     *
     * @return array<string, mixed>
     */
    protected function ruleazaAgentul(): array
    {
        $sursa = file_get_contents(base_path('spv-bridge/agent-functii.php'));

        foreach (['agent_intreaba_local', 'agent_curl', 'agent_scrie'] as $care) {
            $sursa = str_replace('function ' . $care . '(', 'function nefolosit_' . $care . '(', $sursa);
        }

        $dosar = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'agent-proba-' . bin2hex(random_bytes(4));
        mkdir($dosar);

        file_put_contents($dosar . DIRECTORY_SEPARATOR . 'functii.php', $sursa);
        file_put_contents($dosar . DIRECTORY_SEPARATOR . 'ruleaza.php', $this->scenariile());

        $iesire = shell_exec(escapeshellarg(PHP_BINARY) . ' '
            . escapeshellarg($dosar . DIRECTORY_SEPARATOR . 'ruleaza.php'));

        foreach (['ruleaza.php', 'functii.php'] as $fisier) {
            @unlink($dosar . DIRECTORY_SEPARATOR . $fisier);
        }

        @rmdir($dosar);

        $raport = json_decode((string) $iesire, true);

        $this->assertIsArray($raport, 'agentul de probă n-a dat un raport: ' . mb_substr((string) $iesire, 0, 400));

        return $raport;
    }

    /** Scenariile, jucate pe rand in celalalt proces. */
    protected function scenariile(): string
    {
        return <<<'PHP'
<?php
$GLOBALS['certificate'] = '[{"thumbprint":"AAAA"}]';
$GLOBALS['cereri'] = [];
$GLOBALS['jurnal'] = [];

function agent_intreaba_local($config, $cale, $optiuni = array()) {
    return $GLOBALS['certificate'] === null
        ? array('cod' => 7, 'status' => 0, 'corp' => '')
        : array('cod' => 0, 'status' => 200, 'corp' => $GLOBALS['certificate']);
}
function agent_curl($config, $optiuni) {
    $GLOBALS['cereri'][] = $optiuni['url'];
    return array('status' => 200, 'corp' => '{}', 'cod' => 0);
}
function agent_scrie($config, $vorba) { $GLOBALS['jurnal'][] = $vorba; }

require __DIR__ . '/functii.php';

$config = array(
    'inrolare' => 'jeton-de-inrolare',
    'server' => 'https://app.dianasoft.ro',
    'token' => 'cod-de-instalare',
    'dosar' => sys_get_temp_dir(),
);

function curata() { $GLOBALS['cereri'] = []; $GLOBALS['jurnal'] = []; }

$raport = array();

// 1. La pornire: se anunta.
$amprenta = agent_inroleaza($config);
$raport['pornire'] = array(
    'amprenta' => $amprenta,
    'cereri' => count($GLOBALS['cereri']),
    'jurnal' => $GLOBALS['jurnal'],
);

// 2. Lista neschimbata: nu se mai bate la usa serverului.
curata();
$dinNou = agent_inroleaza($config, $amprenta);
$raport['neschimbat'] = array(
    'aceeasi_amprenta' => $dinNou === $amprenta,
    'cereri' => count($GLOBALS['cereri']),
    'jurnal' => $GLOBALS['jurnal'],
);

// 3. Un token nou, conectat acum.
curata();
$GLOBALS['certificate'] = '[{"thumbprint":"AAAA"},{"thumbprint":"BBBB"}]';
$dupaNou = agent_inroleaza($config, $amprenta);
$raport['token_nou'] = array(
    'alta_amprenta' => $dupaNou !== null && $dupaNou !== $amprenta,
    'cereri' => count($GLOBALS['cereri']),
    'jurnal' => $GLOBALS['jurnal'],
);

// 4. Un token scos se anunta si el.
curata();
$GLOBALS['certificate'] = '[{"thumbprint":"AAAA"}]';
$dupaScos = agent_inroleaza($config, $dupaNou);
$raport['token_scos'] = array(
    'alta_amprenta' => $dupaScos !== null && $dupaScos !== $dupaNou,
    'cereri' => count($GLOBALS['cereri']),
);

// 5. Tokenul lipsa: la recitire se tace, la pornire se spune.
curata();
$GLOBALS['certificate'] = null;
$laRecitire = agent_inroleaza($config, 'amprenta-veche');
$raport['lipsa_la_recitire'] = array(
    'amprenta' => $laRecitire,
    'jurnal' => $GLOBALS['jurnal'],
);

curata();
$laPornire = agent_inroleaza($config);
$raport['lipsa_la_pornire'] = array(
    'amprenta' => $laPornire,
    'jurnal' => $GLOBALS['jurnal'],
);

echo json_encode($raport);
PHP;
    }

    /** La pornire se anunta, si se intoarce amprenta listei. */
    public function test_la_pornire_se_anunta_certificatele(): void
    {
        $pas = self::$raport['pornire'];

        $this->assertNotNull($pas['amprenta']);
        $this->assertSame(1, $pas['cereri'], 'trebuie să bată o dată la ușa serverului');
        $this->assertContains('Certificatele de pe acest calculator au fost anunțate aplicației.', $pas['jurnal']);
    }

    /**
     * Miezul dintai: cand lista e neschimbata, nu se mai trimite nimic.
     *
     * La o recitire din trei in trei minute, un apel de fiecare data ar
     * insemna, pe fiecare calculator, sute de cereri degeaba pe zi.
     */
    public function test_lista_neschimbata_nu_mai_bate_la_usa_serverului(): void
    {
        $pas = self::$raport['neschimbat'];

        $this->assertTrue($pas['aceeasi_amprenta']);
        $this->assertSame(0, $pas['cereri'], 'nimic nou de spus, deci nicio cerere');
        $this->assertSame([], $pas['jurnal'], 'nici jurnalul nu se umple degeaba');
    }

    /** Miezul al doilea: un token nou se anunta indata, fara repornire. */
    public function test_tokenul_nou_se_anunta_indata(): void
    {
        $pas = self::$raport['token_nou'];

        $this->assertTrue($pas['alta_amprenta'], 'altă listă, altă amprentă');
        $this->assertSame(1, $pas['cereri']);
        $this->assertContains(
            'S-a schimbat ce tokene sunt conectate; le-am anunțat aplicației.',
            $pas['jurnal']
        );
    }

    /** Un token scos se anunta la fel: aplicatia trebuie sa stie si asta. */
    public function test_tokenul_scos_se_anunta_si_el(): void
    {
        $this->assertTrue(self::$raport['token_scos']['alta_amprenta']);
        $this->assertSame(1, self::$raport['token_scos']['cereri']);
    }

    /**
     * Tokenul scos pentru o clipa nu umple jurnalul de plangeri.
     *
     * La pornire se spune — omul chiar asteapta un raspuns. La recitirile din
     * mers se tace: altfel, un token scos peste noapte ar scrie o plangere la
     * fiecare trei minute, pana dimineata.
     */
    public function test_recitirea_tace_cand_tokenul_lipseste(): void
    {
        $this->assertNull(self::$raport['lipsa_la_recitire']['amprenta']);
        $this->assertSame([], self::$raport['lipsa_la_recitire']['jurnal']);

        $this->assertNull(self::$raport['lipsa_la_pornire']['amprenta']);
        $this->assertNotSame([], self::$raport['lipsa_la_pornire']['jurnal']);
    }

    /** Iar bucla agentului chiar reciteste din cand in cand, si cere licenta. */
    public function test_bucla_reciteste_si_cere_licenta_pentru_tokenul_nou(): void
    {
        $agent = file_get_contents(base_path('spv-bridge/agent.php'));

        $this->assertStringContainsString('$rastimpTokene', $agent, 'lipsește recitirea periodică');

        $inceput = strpos($agent, 'time() - $ultimaCitireTokene >= $rastimpTokene');

        $this->assertNotFalse($inceput, 'recitirea nu e legată de ceas');

        $bucata = substr($agent, $inceput, 900);

        $this->assertStringContainsString('agent_inroleaza($config,', $bucata);
        $this->assertStringContainsString(
            'agent_licentiaza($config);',
            $bucata,
            'un token nou n-are licență, iar fără ea programul local îi refuză comenzile'
        );
    }
}
