<?php
/*
 * Uneltele agentului. Stau separat de bucla lui ca să poată fi probate.
 *
 * Se folosește curl.exe din Windows, nu extensia curl din PHP: PHP-ul din kit e
 * unul mic, cu doar două extensii, iar curl.exe există pe orice Windows 10 sau
 * mai nou. Același lucru îl face și programul local pentru apelurile la ANAF.
 */

function agent_configurare($dosar)
{
    $env = array();

    foreach (array('/configurare.env', '/bridge.env') as $nume) {
        if (is_file($dosar . $nume)) {
            foreach (file($dosar . $nume, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linie) {
                if (strpos($linie, '#') === 0 || strpos($linie, '=') === false) {
                    continue;
                }

                list($cheie, $valoare) = explode('=', $linie, 2);
                $env[trim($cheie)] = trim(trim($valoare), "\"'");
            }

            break;
        }
    }

    return array(
        'server' => isset($env['PUNTE_SERVER']) ? rtrim($env['PUNTE_SERVER'], '/') : '',
        'token' => isset($env['SPV_BRIDGE_TOKEN']) ? $env['SPV_BRIDGE_TOKEN'] : '',
        'inrolare' => isset($env['PUNTE_INROLARE']) ? $env['PUNTE_INROLARE'] : '',
        'local' => isset($env['PUNTE_LOCAL']) ? rtrim($env['PUNTE_LOCAL'], '/') : 'http://127.0.0.1:8099',
        'curl' => getenv('SystemRoot') . '\\System32\\curl.exe',
        'jurnal' => $dosar . '/agent.log',
        'dosar' => $dosar,
    );
}

function agent_scrie($config, $mesaj)
{
    $linie = date('Y-m-d H:i:s') . ' ' . $mesaj . PHP_EOL;

    echo $linie;
    @file_put_contents($config['jurnal'], $linie, FILE_APPEND);
}

/**
 * Rulează curl și întoarce array(cod, status, corp).
 *
 * Adresa, antetele și corpul se dau printr-un fișier de configurare al lui
 * curl, nu prin linia de comandă. Sunt două pricini, amândouă însemnate:
 *
 *   - pe Windows, escapeshellarg() înlocuiește fiecare „%" cu un spațiu, ca să
 *     nu se expandeze variabilele. Adresele către SPV au parametri codificați
 *     procentual (%3A, %2F), deci ajungeau la curl ciopârțite: „URL rejected:
 *     Malformed input to a URL function".
 *   - codul de acces nu mai apare în lista de procese a calculatorului.
 *
 * @param array $optiuni url, antete, metoda, corp_fisier, iesire
 */
function agent_curl($config, $optiuni)
{
    $linii = array(
        'silent',
        'show-error',
        'max-time = 300',
        'write-out = "\n%{http_code}"',
        'url = "' . agent_pentru_config($optiuni['url']) . '"',
    );

    if (!empty($optiuni['metoda'])) {
        $linii[] = 'request = "' . $optiuni['metoda'] . '"';
    }

    foreach (isset($optiuni['antete']) ? $optiuni['antete'] : array() as $antet) {
        $linii[] = 'header = "' . agent_pentru_config($antet) . '"';
    }

    if (!empty($optiuni['corp_fisier'])) {
        $linii[] = 'data-binary = "@' . agent_pentru_config($optiuni['corp_fisier']) . '"';
    }

    if (!empty($optiuni['antete_in'])) {
        $linii[] = 'dump-header = "' . agent_pentru_config($optiuni['antete_in']) . '"';
    }

    if (!empty($optiuni['iesire'])) {
        $linii[] = 'output = "' . agent_pentru_config($optiuni['iesire']) . '"';
    }

    $fisierConfig = $config['dosar'] . '/agent_curl_' . getmypid() . '.cfg';
    @file_put_contents($fisierConfig, implode("\n", $linii) . "\n");

    $iesire = array();
    $cod = 0;
    exec(escapeshellarg($config['curl']) . ' --config ' . escapeshellarg($fisierConfig) . ' 2>&1', $iesire, $cod);

    @unlink($fisierConfig);

    $tot = implode("\n", $iesire);
    $taiat = strrpos($tot, "\n");
    $status = $taiat === false ? (int) $tot : (int) substr($tot, $taiat + 1);

    return array(
        'cod' => $cod,
        'status' => $status,
        'corp' => $taiat === false ? '' : substr($tot, 0, $taiat),
    );
}

/**
 * Pregătește o valoare pentru fișierul de configurare al lui curl.
 *
 * Între ghilimele, curl citește „\" ca început de secvență de evitare, așa că
 * despărțitorul de dosare din Windows trebuie dublat, iar ghilimelele scăpate.
 */
function agent_pentru_config($valoare)
{
    return str_replace(array('\\', '"'), array('\\\\', '\\"'), $valoare);
}

/**
 * „Ai ceva pentru mine?"
 *
 * Întoarce comanda, null dacă pânda s-a împlinit fără nimic, sau false dacă
 * serverul n-a răspuns deloc.
 */
function agent_intreaba($config)
{
    $rezultat = agent_curl($config, array(
        'metoda' => 'POST',
        'url' => $config['server'] . '/api/punte/agent/asteapta',
        'antete' => array('Authorization: Bearer ' . $config['token']),
    ));

    if ($rezultat['status'] === 401) {
        /*
         * Serverul nu ne recunoaste codul de acces. De obicei inseamna ca
         * inrolarea nu s-a facut inca: certificatele de pe tokenul de aici nu
         * sunt legate de acest kit. Se spune limpede, ca sa nu para o pana de
         * retea.
         */
        return -1;
    }

    if ($rezultat['cod'] !== 0 || $rezultat['status'] !== 200) {
        return false;
    }

    $date = json_decode($rezultat['corp'], true);

    if (!is_array($date) || !isset($date['comanda'])) {
        return false;
    }

    return $date['comanda'] ? $date['comanda'] : null;
}

/**
 * Se prezintă serverului: „aici sunt eu, cu certificatele astea".
 *
 * Prin tunel, serverul n-are cum să ne caute, deci intrarea în evidență pornește
 * de aici: se citesc certificatele de pe tokenul de lângă noi și se trimit, cu
 * jetonul de înrolare din kit. Omul nu tastează nimic în aplicație — instalează
 * kitul, iar certificatele apar acolo singure.
 *
 * Se încearcă la fiecare pornire: dacă se schimbă tokenul din USB, noul
 * certificat intră în evidență la următoarea repornire a programului.
 */
function agent_inroleaza($config)
{
    if ($config['inrolare'] === '') {
        return;
    }

    $local = agent_curl($config, array(
        'url' => $config['local'] . '/certificate',
        'antete' => array('Authorization: Bearer ' . $config['token']),
    ));

    if ($local['cod'] !== 0 || $local['status'] !== 200) {
        agent_scrie($config, 'Nu am putut citi certificatele de pe token (' . $local['status'] . ').');

        return;
    }

    $fisier = $config['dosar'] . '/agent_inrolare.tmp';
    @file_put_contents($fisier, '{"certificate":' . $local['corp'] . '}');

    $raspuns = agent_curl($config, array(
        'metoda' => 'POST',
        'url' => $config['server'] . '/api/punte/agent/inrolare',
        'antete' => array(
            'Authorization: Bearer ' . $config['token'],
            'X-Inrolare: ' . $config['inrolare'],
            'Content-Type: application/json',
        ),
        'corp_fisier' => $fisier,
    ));

    @unlink($fisier);

    agent_scrie($config, $raspuns['status'] === 200
        ? 'Certificatele de pe acest calculator au fost anunțate aplicației.'
        : 'Înrolarea nu a reușit (' . $raspuns['status'] . '): ' . mb_substr($raspuns['corp'], 0, 200));
}

/** Duce comanda la programul local de lângă agent și adună răspunsul lui. */
function agent_executa($config, $comanda)
{
    $corp = null;

    if (!empty($comanda['are_corp'])) {
        $corp = $config['dosar'] . '/agent_corp_' . $comanda['id'] . '.tmp';

        $adus = agent_curl($config, array(
            'url' => $config['server'] . '/api/punte/agent/corp/' . $comanda['id'],
            'antete' => array('Authorization: Bearer ' . $config['token']),
            'iesire' => $corp,
        ));

        if ($adus['cod'] !== 0) {
            @unlink($corp);

            return array('eroare' => 'Corpul comenzii nu a putut fi adus de la server.');
        }
    }

    $antete = array();

    foreach ((array) $comanda['antete'] as $nume => $valoare) {
        $antete[] = $nume . ': ' . $valoare;
    }

    $iesire = $config['dosar'] . '/agent_raspuns_' . $comanda['id'] . '.tmp';

    $rezultat = agent_curl($config, array(
        'metoda' => $comanda['metoda'],
        // Calea vine cu tot cu întrebare, codificată procentual: de aceea nu
        // trece prin linia de comandă, ci prin fișierul de configurare.
        'url' => $config['local'] . $comanda['cale'],
        'antete' => $antete,
        'corp_fisier' => $corp,
        'antete_in' => $config['dosar'] . '/agent_antete_' . $comanda['id'] . '.tmp',
        'iesire' => $iesire,
    ));

    if ($corp !== null) {
        @unlink($corp);
    }

    if ($rezultat['cod'] !== 0) {
        @unlink($iesire);

        /*
         * Se scrie și în jurnalul de lângă program: aplicația primește doar o
         * frază, iar când ceva nu merge, omul care se uită aici trebuie să vadă
         * ce anume a pățit curl-ul.
         */
        $motiv = 'Programul local nu a răspuns (curl ' . $rezultat['cod'] . '): '
            . mb_substr(trim($rezultat['corp']), 0, 200);

        agent_scrie($config, $motiv);

        return array('eroare' => $motiv);
    }

    return array(
        'status' => $rezultat['status'],
        'tip' => agent_tip_continut($config['dosar'] . '/agent_antete_' . $comanda['id'] . '.tmp'),
        'fisier' => $iesire,
    );
}

function agent_tip_continut($fisierAntete)
{
    $tip = 'application/octet-stream';

    if (is_file($fisierAntete)) {
        foreach (file($fisierAntete) as $linie) {
            if (stripos($linie, 'content-type:') === 0) {
                $tip = trim(substr($linie, 13));
            }
        }

        @unlink($fisierAntete);
    }

    return $tip;
}

/** Trimite răspunsul înapoi, ca aplicația care așteaptă să-l primească. */
function agent_trimite_rezultatul($config, $id, $raspuns)
{
    $adresa = $config['server'] . '/api/punte/agent/rezultat/' . $id;

    if (isset($raspuns['eroare'])) {
        agent_curl($config, array(
            'metoda' => 'POST',
            'url' => $adresa,
            'antete' => array(
                'Authorization: Bearer ' . $config['token'],
                // Antetul nu poate purta rânduri noi; motivul se pune pe o linie.
                'X-Eroare: ' . str_replace(array("\r", "\n"), ' ', $raspuns['eroare']),
            ),
        ));

        return;
    }

    agent_curl($config, array(
        'metoda' => 'POST',
        'url' => $adresa,
        'antete' => array(
            'Authorization: Bearer ' . $config['token'],
            'X-Status: ' . $raspuns['status'],
            'X-Tip-Continut: ' . $raspuns['tip'],
            'Content-Type: application/octet-stream',
        ),
        'corp_fisier' => $raspuns['fisier'],
    ));

    @unlink($raspuns['fisier']);
}
