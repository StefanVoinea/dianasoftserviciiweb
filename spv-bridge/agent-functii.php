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

/** Rulează curl și întoarce array(cod, iesire, status). */
function agent_curl($config, $argumente, $fisierIesire = null)
{
    $comanda = escapeshellarg($config['curl']) . ' -sS --max-time 300 -w "\n%{http_code}"';

    foreach ($argumente as $argument) {
        $comanda .= ' ' . $argument;
    }

    if ($fisierIesire !== null) {
        $comanda .= ' -o ' . escapeshellarg($fisierIesire);
    }

    $iesire = array();
    $cod = 0;
    exec($comanda . ' 2>&1', $iesire, $cod);

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
 * „Ai ceva pentru mine?"
 *
 * Întoarce comanda, null dacă pânda s-a împlinit fără nimic, sau false dacă
 * serverul n-a răspuns deloc.
 */
function agent_intreaba($config)
{
    $rezultat = agent_curl($config, array(
        '-X POST',
        '-H ' . escapeshellarg('Authorization: Bearer ' . $config['token']),
        escapeshellarg($config['server'] . '/api/punte/agent/asteapta'),
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
        '-H ' . escapeshellarg('Authorization: Bearer ' . $config['token']),
        escapeshellarg($config['local'] . '/certificate'),
    ));

    if ($local['cod'] !== 0 || $local['status'] !== 200) {
        agent_scrie($config, 'Nu am putut citi certificatele de pe token (' . $local['status'] . ').');

        return;
    }

    $fisier = $config['dosar'] . '/agent_inrolare.tmp';
    @file_put_contents($fisier, '{"certificate":' . $local['corp'] . '}');

    $raspuns = agent_curl($config, array(
        '-X POST',
        '-H ' . escapeshellarg('Authorization: Bearer ' . $config['token']),
        '-H ' . escapeshellarg('X-Inrolare: ' . $config['inrolare']),
        '-H ' . escapeshellarg('Content-Type: application/json'),
        '--data-binary ' . escapeshellarg('@' . $fisier),
        escapeshellarg($config['server'] . '/api/punte/agent/inrolare'),
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
            '-H ' . escapeshellarg('Authorization: Bearer ' . $config['token']),
            escapeshellarg($config['server'] . '/api/punte/agent/corp/' . $comanda['id']),
        ), $corp);

        if ($adus['cod'] !== 0) {
            @unlink($corp);

            return array('eroare' => 'Corpul comenzii nu a putut fi adus de la server.');
        }
    }

    $argumente = array('-X ' . escapeshellarg($comanda['metoda']));

    foreach ((array) $comanda['antete'] as $nume => $valoare) {
        $argumente[] = '-H ' . escapeshellarg($nume . ': ' . $valoare);
    }

    if ($corp !== null) {
        $argumente[] = '--data-binary ' . escapeshellarg('@' . $corp);
    }

    $argumente[] = '-D ' . escapeshellarg($config['dosar'] . '/agent_antete_' . $comanda['id'] . '.tmp');
    $argumente[] = escapeshellarg($config['local'] . $comanda['cale']);

    $iesire = $config['dosar'] . '/agent_raspuns_' . $comanda['id'] . '.tmp';

    $rezultat = agent_curl($config, $argumente, $iesire);

    if ($corp !== null) {
        @unlink($corp);
    }

    if ($rezultat['cod'] !== 0) {
        @unlink($iesire);

        return array('eroare' => 'Programul local nu a răspuns: curl ' . $rezultat['cod']);
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

    $argumente = array(
        '-X POST',
        '-H ' . escapeshellarg('Authorization: Bearer ' . $config['token']),
    );

    if (isset($raspuns['eroare'])) {
        $argumente[] = '-H ' . escapeshellarg('X-Eroare: ' . $raspuns['eroare']);
        $argumente[] = escapeshellarg($adresa);

        agent_curl($config, $argumente);

        return;
    }

    $argumente[] = '-H ' . escapeshellarg('X-Status: ' . $raspuns['status']);
    $argumente[] = '-H ' . escapeshellarg('X-Tip-Continut: ' . $raspuns['tip']);
    $argumente[] = '-H ' . escapeshellarg('Content-Type: application/octet-stream');
    $argumente[] = '--data-binary ' . escapeshellarg('@' . $raspuns['fisier']);
    $argumente[] = escapeshellarg($adresa);

    agent_curl($config, $argumente);

    @unlink($raspuns['fisier']);
}
