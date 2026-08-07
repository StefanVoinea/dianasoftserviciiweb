<?php
/*
 * Innoirea programului de la client, pornita de agent.
 *
 * Merge intr-un proces al ei, ca agentul sa nu stea. Pasii, in ordinea in care
 * conteaza:
 *
 *   1. se aduce pachetul de la server;
 *   2. se verifica semnatura lui cu cheia publica din kit — fara ea nu se
 *      inlocuieste nimic, oricat de bine ar arata pachetul;
 *   3. fisierele de acum se pun deoparte, intr-un dosar de copie;
 *   4. se scriu cele noi si se reporneste programul;
 *   5. daca dupa repornire programul nu raspunde, se pun la loc cele vechi.
 *
 * Se innoiesc numai fisiere de text — program, unelte, scripturi. PHP-ul din
 * kit nu se poate inlocui cat ruleaza, configurare.env e a clientului, iar
 * cheia publica e temelia increderii: ea se schimba doar cu un kit nou.
 */

require __DIR__ . DIRECTORY_SEPARATOR . 'agent-functii.php';

$dosar = __DIR__;
$config = agent_configurare($dosar);

/** Fisierele pe care le primim; orice altceva din arhiva se lasa neatins. */
$ingaduite = array(
    'server.php', 'curl-talcuri.php', 'agent.php', 'agent-functii.php',
    'agent-lucreaza.php', 'agent-actualizare.php', 'versiune.txt',
    'cert-info.ps1', 'sign-pdf.ps1', 'merge-pdf.ps1', 'pdf-info.ps1',
    'imprimante.ps1', 'print-pdf.ps1',
    'diagnoza.bat', 'diagnoza.ps1', 'porneste-manual.bat', 'porneste-agent.bat',
);

function actualizare_spune($config, $mesaj)
{
    agent_scrie($config, 'Innoire: ' . $mesaj);
}

/** Versiunea de acum, scrisa langa program. */
function agent_versiunea($dosar)
{
    $cale = $dosar . DIRECTORY_SEPARATOR . 'versiune.txt';

    return is_file($cale) ? trim((string) @file_get_contents($cale)) : '';
}

/**
 * Se innoieste doar cand nimeni nu lucreaza.
 *
 * O lucrare in curs tine fisiere deschise si asteapta raspuns; inlocuite sub ea,
 * ar cadea tocmai cand omul asteapta un document.
 */
function actualizare_e_liniste($config)
{
    return agent_lucrari_active($config) === array();
}

if (!actualizare_e_liniste($config)) {
    actualizare_spune($config, 'se lucreaza acum; incerc alta data.');
    exit(0);
}

// --- 1. Pachetul -----------------------------------------------------------

$arhiva = $dosar . DIRECTORY_SEPARATOR . 'actualizare.zip';
$antete = $dosar . DIRECTORY_SEPARATOR . 'actualizare_antete.tmp';

$adus = agent_curl($config, array(
    'url' => $config['server'] . '/api/punte/agent/actualizare',
    'antete' => array('Authorization: Bearer ' . $config['token']),
    'antete_in' => $antete,
    'iesire' => $arhiva,
));

if ($adus['cod'] !== 0 || $adus['status'] !== 200) {
    actualizare_spune($config, 'pachetul nu a putut fi adus (' . $adus['status'] . ').');
    @unlink($arhiva);
    @unlink($antete);
    exit(1);
}

$capul = is_file($antete) ? (string) file_get_contents($antete) : '';
@unlink($antete);

function actualizare_antet($capul, $nume)
{
    if (preg_match('/^' . preg_quote($nume, '/') . ':\s*(.+)$/mi', $capul, $m)) {
        return trim($m[1]);
    }

    return '';
}

$versiune = actualizare_antet($capul, 'X-Versiune');
$amprentaSpusa = actualizare_antet($capul, 'X-Amprenta');
$semnatura = actualizare_antet($capul, 'X-Semnatura');

// --- 2. Semnatura ----------------------------------------------------------

$amprenta = hash_file('sha256', $arhiva);

if ($versiune === '' || $amprentaSpusa === '' || $semnatura === '') {
    actualizare_spune($config, 'pachetul a venit fara versiune sau fara semnatura; nu-l folosesc.');
    @unlink($arhiva);
    exit(1);
}

if (!hash_equals($amprentaSpusa, $amprenta)) {
    actualizare_spune($config, 'amprenta pachetului nu se potriveste cu cea spusa; nu-l folosesc.');
    @unlink($arhiva);
    exit(1);
}

$cheie = @file_get_contents($dosar . DIRECTORY_SEPARATOR . 'cheie-publica.pem');
$publica = $cheie ? openssl_pkey_get_public($cheie) : false;

if ($publica === false || !function_exists('openssl_verify')) {
    actualizare_spune($config, 'nu am cu ce verifica semnatura (lipseste cheia publica); nu innoiesc.');
    @unlink($arhiva);
    exit(1);
}

$bunaSemnatura = openssl_verify(
    $versiune . ':' . $amprenta,
    base64_decode($semnatura),
    $publica,
    OPENSSL_ALGO_SHA256
) === 1;

if (!$bunaSemnatura) {
    actualizare_spune($config, 'SEMNATURA NU E BUNA — pachetul nu vine de la serverul nostru. Nu inlocuiesc nimic.');
    @unlink($arhiva);
    exit(1);
}

// --- 3. Copia de siguranta -------------------------------------------------

$zip = new ZipArchive();

if ($zip->open($arhiva) !== true) {
    actualizare_spune($config, 'arhiva nu se poate deschide.');
    @unlink($arhiva);
    exit(1);
}

$copie = $dosar . DIRECTORY_SEPARATOR . 'copie-' . date('Ymd-His');
@mkdir($copie);

$deScris = array();

for ($i = 0; $i < $zip->numFiles; $i++) {
    $nume = $zip->getNameIndex($i);

    if (!in_array($nume, $ingaduite, true)) {
        continue;
    }

    $cuprins = $zip->getFromIndex($i);

    if ($cuprins === false) {
        continue;
    }

    $deScris[$nume] = $cuprins;
}

$zip->close();

if ($deScris === array()) {
    actualizare_spune($config, 'arhiva n-a adus niciun fisier cunoscut.');
    @unlink($arhiva);
    exit(1);
}

foreach (array_keys($deScris) as $nume) {
    $vechi = $dosar . DIRECTORY_SEPARATOR . $nume;

    if (is_file($vechi)) {
        @copy($vechi, $copie . DIRECTORY_SEPARATOR . $nume);
    }
}

// --- 4. Scrierea si repornirea ---------------------------------------------

$scrise = 0;

foreach ($deScris as $nume => $cuprins) {
    if (@file_put_contents($dosar . DIRECTORY_SEPARATOR . $nume, $cuprins) !== false) {
        $scrise++;
    }
}

@unlink($arhiva);

actualizare_spune($config, 'am scris ' . $scrise . ' fisier(e), versiunea ' . $versiune . '. Repornesc programul.');

/*
 * Programul local citeste fisierul la fiecare cerere, deci ar merge si fara
 * repornire — dar agentul, nu: el a fost incarcat o data, la pornire. Se
 * repornesc sarcinile, iar agentul de acum iese: sarcina lui il porneste la loc,
 * cu fisierul nou.
 */
foreach (array('Acces token ANAF', 'Acces token ANAF - lucrator 8100', 'Acces token ANAF - lucrator 8101') as $sarcina) {
    @exec('schtasks /End /TN ' . escapeshellarg($sarcina) . ' 2>&1');
    @exec('schtasks /Run /TN ' . escapeshellarg($sarcina) . ' 2>&1');
}

sleep(4);

// --- 5. A mers? ------------------------------------------------------------

$proba = agent_curl($config, array('url' => $config['local'] . '/certificate'));

if ($proba['cod'] === 0 && $proba['status'] >= 100) {
    actualizare_spune($config, 'programul raspunde dupa innoire. Ies, ca sa fiu pornit din nou cu fisierul nou.');
    exit(0);
}

/*
 * Nu raspunde: se pun la loc fisierele vechi si se reporneste. Mai bine un
 * program vechi care merge decat unul nou care nu porneste — clientul nu
 * trebuie sa ramana fara SPV pentru o innoire picata.
 */
actualizare_spune($config, 'programul NU raspunde dupa innoire; pun la loc fisierele vechi.');

foreach (glob($copie . DIRECTORY_SEPARATOR . '*') as $salvat) {
    @copy($salvat, $dosar . DIRECTORY_SEPARATOR . basename($salvat));
}

foreach (array('Acces token ANAF', 'Acces token ANAF - lucrator 8100', 'Acces token ANAF - lucrator 8101') as $sarcina) {
    @exec('schtasks /End /TN ' . escapeshellarg($sarcina) . ' 2>&1');
    @exec('schtasks /Run /TN ' . escapeshellarg($sarcina) . ' 2>&1');
}

actualizare_spune($config, 'fisierele vechi au fost puse la loc. Copia ramane in ' . basename($copie) . '.');

exit(1);
