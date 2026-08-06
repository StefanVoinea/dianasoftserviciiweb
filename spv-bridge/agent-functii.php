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
        /*
         * Deschiderea legaturii are vremea ei, mai scurta decat a raspunsului:
         * un pachet de deschidere pierdut pe drum ar tine Windows-ul in loc
         * vreo 21 de secunde, iar agentul ar sta degeaba. Asa se afla mai
         * repede ca s-a stricat ceva, si se incearca din nou.
         */
        'connect-timeout = 15',
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
 * serverul n-a răspuns deloc. În $motiv rămâne, la eșec, pricina spusă pe
 * înțeles — ea ajunge în jurnal, ca omul să nu ghicească ce s-a stricat.
 */
function agent_intreaba($config, &$motiv = null)
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
         * retea — iar daca serverul spune si de ce anume, se scrie in jurnal.
         */
        $date = json_decode($rezultat['corp'], true);

        if (is_array($date) && !empty($date['detalii'])) {
            agent_scrie($config, 'Serverul: ' . $date['detalii']);
        }

        return -1;
    }

    return agent_desluseste_panda($rezultat, $motiv);
}

/**
 * Ce a iesit din panda: comanda, null (nimic de lucru) sau false (pana).
 *
 * Sta deoparte de apelul propriu-zis ca sa poata fi probata: aici se hotaraste
 * daca un raspuns e o zi obisnuita de lucru sau o defectiune, iar deosebirea
 * asta a fost multa vreme gresita (vezi array_key_exists mai jos).
 *
 * @param array $rezultat cod, status si corp, asa cum le da agent_curl
 */
function agent_desluseste_panda($rezultat, &$motiv = null)
{
    $motiv = '';

    if ($rezultat['cod'] !== 0) {
        $motiv = agent_talcul_curl($rezultat['cod'], $rezultat['corp']);

        return false;
    }

    if ($rezultat['status'] !== 200) {
        $motiv = agent_talcul_statusului($rezultat['status']);

        return false;
    }

    $date = json_decode($rezultat['corp'], true);

    if (!is_array($date) || !array_key_exists('comanda', $date)) {
        /*
         * Un raspuns care nu seamana cu al aplicatiei vine, aproape mereu, de la
         * altcineva de pe drum: pagina de oprire a antivirusului, a proxy-ului
         * din firma sau a portalului de retea. Se arata inceputul lui, ca omul
         * sa recunoasca cine i-a raspuns in locul serverului.
         */
        $motiv = 'răspunsul nu vine de la aplicație, ci de la altcineva de pe drum'
            . ' (antivirus, proxy sau portal de rețea): „' . agent_inceputul($rezultat['corp']) . '"';

        return false;
    }

    /*
     * Aici era buba: pânda împlinită fără nimic de lucru întoarce „comanda":
     * null, iar isset() spune „nu există" tocmai pentru valoarea null. Cazul cel
     * mai obișnuit din viața agentului — nimic de făcut — se socotea deci pană
     * de rețea: jurnalul se umplea de „Serverul nu răspunde", iar așteptarea
     * creștea până la un minut, în care comenzile chiar sosite stăteau la coadă.
     */
    return $date['comanda'] ? $date['comanda'] : null;
}

/**
 * Cu ce s-a oprit curl — doar tâlcurile care se întâlnesc aievea.
 *
 * La codul 28 se pune și vorba lui curl, fiindcă tâlcul atârnă de ea: „timed
 * out after 21000 ms" înseamnă că nici legătura nu s-a putut deschide (pachetul
 * de deschidere s-a pierdut pe drum, fără ca cineva să spună „nu"), pe când o
 * vreme mai lungă înseamnă că legătura s-a deschis, dar răspunsul n-a mai venit.
 * Sunt două pricini deosebite, iar omul care caută pana trebuie să știe care.
 */
function agent_talcul_curl($cod, $iesire = '')
{
    $talcuri = array(
        5 => 'proxy-ul scris în setările calculatorului nu poate fi găsit',
        6 => 'numele serverului nu poate fi dezlegat — DNS oprit sau fără internet',
        7 => 'legătura nu se poate deschide — port închis de firewall sau internet căzut',
        28 => 'a trecut vremea fără niciun răspuns — legătura e ținută în loc pe drum',
        35 => 'strângerea de mână TLS a eșuat — cel mai des, traficul e desfăcut de antivirus',
        52 => 'serverul a închis legătura fără să răspundă',
        56 => 'legătura s-a rupt în timpul primirii',
        60 => 'certificatul serverului nu este de încredere — semn limpede că traficul e desfăcut'
            . ' de antivirus sau de proxy',
    );

    $talc = isset($talcuri[$cod])
        ? $talcuri[$cod] . ' [curl ' . $cod . ']'
        : 'curl s-a oprit cu codul ' . $cod;

    // Vorba lui curl, cand a spus-o: „Connection timed out after 21014 ms".
    if (preg_match('/curl:\s*\(\d+\)\s*(.+)/', (string) $iesire, $potrivire)) {
        $talc .= ' — ' . agent_inceputul($potrivire[1]);
    }

    return $talc;
}

/** Ce înseamnă codul cu care a răspuns cineva în locul unui 200. */
function agent_talcul_statusului($status)
{
    if ($status === 0) {
        return 'nu s-a primit niciun răspuns';
    }

    $talcuri = array(
        403 => 'cererea a fost oprită — de obicei de un antivirus sau de un proxy din firmă',
        407 => 'proxy-ul din firmă cere autentificare',
        429 => 'serverul cere să se bată mai rar la ușă',
        502 => 'serverul aplicației nu răspunde în spatele proxy-ului',
        503 => 'serverul aplicației este oprit sau în întreținere',
        504 => 'răspunsul n-a apucat să vină până la capăt',
    );

    return isset($talcuri[$status])
        ? $talcuri[$status] . ' [HTTP ' . $status . ']'
        : 'serverul a răspuns cu codul HTTP ' . $status;
}

/** Începutul unui răspuns străin, adus la o formă care încape într-un rând. */
function agent_inceputul($corp)
{
    $curat = trim(preg_replace('/\s+/', ' ', (string) $corp));

    if ($curat === '') {
        return '(răspuns gol)';
    }

    return strlen($curat) > 70 ? substr($curat, 0, 70) . '…' : $curat;
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

    if ($raspuns['status'] === 200) {
        agent_scrie($config, 'Certificatele de pe acest calculator au fost anunțate aplicației.');

        return;
    }

    // Serverul spune de obicei si de ce anume; motivul lui e mai bun decat JSON-ul brut.
    $date = json_decode($raspuns['corp'], true);
    $motiv = is_array($date) && !empty($date['detalii'])
        ? $date['detalii']
        : mb_substr($raspuns['corp'], 0, 200);

    agent_scrie($config, 'Înrolarea nu a reușit (' . $raspuns['status'] . '): ' . $motiv);
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
