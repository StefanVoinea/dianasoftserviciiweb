<?php

namespace Tests\Unit;

use App\Services\Anaf\Bridge\ActualizareBridge;
use App\Services\Anaf\Bridge\Licente;
use Tests\TestCase;

/**
 * Innoirea de la distanta a programului de la client.
 *
 * Fisierele lui sunt text — PHP si PowerShell — deci se pot inlocui fara ca
 * cineva sa se duca acolo. Tot ce trebuie pazit e increderea: clientul primeste
 * pachetul semnat cu cheia serverului si nu inlocuieste nimic pana nu verifica
 * semnatura cu cheia publica pe care o are din kit.
 */
class ActualizareBridgeTest extends TestCase
{
    protected function serviciu(): ActualizareBridge
    {
        return app(ActualizareBridge::class);
    }

    /** Versiunea e amprenta cuprinsului: aceleasi fisiere, aceeasi versiune. */
    public function test_versiunea_nu_se_schimba_de_la_sine()
    {
        $this->assertSame($this->serviciu()->versiunea(), $this->serviciu()->versiunea());
        $this->assertMatchesRegularExpression('/^[a-f0-9]{16}$/', $this->serviciu()->versiunea());
    }

    /**
     * Versiunea se schimba cand se schimba un fisier — si numai atunci. Nimeni
     * n-o creste de mana, deci nu poate fi uitata.
     */
    public function test_versiunea_urmeaza_cuprinsul_fisierelor()
    {
        $dosar = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bridge-proba-' . bin2hex(random_bytes(4));
        mkdir($dosar);
        mkdir($dosar . DIRECTORY_SEPARATOR . 'kit');

        file_put_contents($dosar . DIRECTORY_SEPARATOR . 'server.php', '<?php // unu');

        $serviciu = new ActualizareBridge(app(Licente::class), $dosar);
        $intai = $serviciu->versiunea();

        file_put_contents($dosar . DIRECTORY_SEPARATOR . 'server.php', '<?php // doi');
        $apoi = $serviciu->versiunea();

        $this->assertNotSame($intai, $apoi, 'Versiunea n-a urmat schimbarea fișierului.');

        @unlink($dosar . DIRECTORY_SEPARATOR . 'server.php');
        @rmdir($dosar . DIRECTORY_SEPARATOR . 'kit');
        @rmdir($dosar);
    }

    /** @return array cuprinsul pachetului, asa cum il citeste clientul */
    protected function cuprinsul(string $cale): array
    {
        $pachet = json_decode(file_get_contents($cale), true);

        $this->assertIsArray($pachet, 'pachetul nu se poate citi');
        $this->assertArrayHasKey('fisiere', $pachet);

        return $pachet;
    }

    /** Pachetul poarta fisierele si versiunea, ca sa se stie ce s-a pus. */
    public function test_pachetul_poarta_fisierele_si_versiunea()
    {
        $pachet = $this->serviciu()->pachetul();
        $cuprins = $this->cuprinsul($pachet['arhiva']);

        foreach (['server.php', 'agent.php', 'agent-functii.php', 'versiune.txt'] as $fisier) {
            $this->assertArrayHasKey($fisier, $cuprins['fisiere'], 'Lipsește ' . $fisier . ' din pachet');
        }

        $this->assertSame($pachet['versiune'], trim(base64_decode($cuprins['fisiere']['versiune.txt'])));
        $this->assertSame($pachet['versiune'], $cuprins['versiune']);

        // Fisierele calatoresc intregi: ce se scrie la client e ce e aici.
        $this->assertStringContainsString(
            '<?php',
            base64_decode($cuprins['fisiere']['server.php']),
            'programul n-a ajuns întreg în pachet'
        );

        @unlink($pachet['arhiva']);
    }

    /**
     * Pachetul se citeste fara nicio extensie de PHP.
     *
     * PHP-ul din kit e mic dinadins — mbstring si openssl, atat cat ii trebuie
     * programului. O arhiva ar fi cerut extensia zip, iar innoirea ar fi cazut
     * chiar pe calculatoarele pentru care a fost facuta, cu „Class ZipArchive
     * not found". De aceea pachetul e text.
     */
    public function test_pachetul_nu_cere_nicio_extensie_la_client()
    {
        $pachet = $this->serviciu()->pachetul();

        $this->assertNotNull(
            json_decode(file_get_contents($pachet['arhiva']), true),
            'pachetul trebuie să fie citibil fără extensii'
        );

        $client = file_get_contents(base_path('spv-bridge/agent-actualizare.php'));

        $this->assertStringNotContainsString('new ZipArchive', $client, 'clientul încă cere extensia zip');
        $this->assertStringContainsString('json_decode', $client);

        @unlink($pachet['arhiva']);
    }

    /**
     * Ce NU se innoieste de la distanta: PHP-ul (fisiere in lucru), configurarea
     * clientului si cheia publica — ea e chiar temelia increderii.
     */
    public function test_pachetul_nu_atinge_ce_nu_are_voie()
    {
        $pachet = $this->serviciu()->pachetul();
        $cuprins = $this->cuprinsul($pachet['arhiva']);

        foreach (['configurare.env', 'cheie-publica.pem', 'php/php.exe', 'itextsharp.dll'] as $interzis) {
            $this->assertArrayNotHasKey(
                $interzis,
                $cuprins['fisiere'],
                $interzis . ' n-are ce căuta în pachetul de înnoire'
            );
        }

        @unlink($pachet['arhiva']);
    }

    /**
     * Semnatura se verifica cu cheia publica — chiar cea pe care o are clientul
     * in kit. Daca proba asta trece aici, trece si acolo.
     */
    public function test_semnatura_se_verifica_cu_cheia_publica()
    {
        $licente = app(Licente::class);

        if (!$licente->areChei()) {
            $this->markTestSkipped('Cheile de semnare nu sunt pregătite pe acest server.');
        }

        $pachet = $this->serviciu()->pachetul();

        $publica = openssl_pkey_get_public($licente->cheiePublica());

        $bune = openssl_verify(
            $pachet['versiune'] . ':' . $pachet['amprenta'],
            base64_decode($pachet['semnatura']),
            $publica,
            OPENSSL_ALGO_SHA256
        );

        $this->assertSame(1, $bune, 'Semnătura pachetului nu se verifică cu cheia publică din kit.');

        // Amprenta spusa e chiar a arhivei trimise: altfel semnatura n-ar dovedi nimic.
        $this->assertSame(hash_file('sha256', $pachet['arhiva']), $pachet['amprenta']);

        @unlink($pachet['arhiva']);
    }

    /** Un pachet umblat pe drum nu mai trece de verificare. */
    public function test_pachetul_umblat_nu_mai_trece()
    {
        $licente = app(Licente::class);

        if (!$licente->areChei()) {
            $this->markTestSkipped('Cheile de semnare nu sunt pregătite pe acest server.');
        }

        $pachet = $this->serviciu()->pachetul();

        // Cineva schimba arhiva pe drum: amprenta ei nu mai e cea semnata.
        file_put_contents($pachet['arhiva'], 'altceva', FILE_APPEND);

        $this->assertNotSame(hash_file('sha256', $pachet['arhiva']), $pachet['amprenta']);

        @unlink($pachet['arhiva']);
    }
}
