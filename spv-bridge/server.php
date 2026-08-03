<?php

/**
 * Acces token ANAF — program local care dă aplicației acces la certificatul
 * digital de pe tokenul USB conectat la acest calculator.
 *
 * Pornire:  php -S 127.0.0.1:8099 server.php   (sau porneste-manual.bat)
 *
 * Aplicația nu poate folosi direct certificatul de pe token (cheia privată nu e
 * exportabilă), așa că cererile TLS sunt semnate aici prin curl.exe din Windows
 * (Schannel), care citește certificatul din magazinul de certificate Windows.
 * Configurația se citește din bridge.env, de lângă acest fișier:
 *   SPV_BRIDGE_TOKEN     — codul de acces cerut apelurilor
 *   SPV_CERT_THUMBPRINT  — amprenta certificatului implicit (poate lipsi)
 *   SPV_BASE_URL         — baza REST SPV (implicit webserviced.anaf.ro)
 *   ARHIVA_CALE          — dosarul în care se strâng documentele (implicit ./arhiva)
 *
 * Rute:
 *   GET  /spv/<cale>      — proxy REST SPV (listaMesaje, descarcare, cerere)
 *   POST /decl/login      — handshake autentificare decl.anaf.mfinante.gov.ro
 *   POST /decl/upload     — depunere PDF semnat (multipart linkdoc)
 *   POST /semnare         — semnează PDF-ul din corpul cererii (PowerShell+iTextSharp)
 *   GET  /imprimante      — imprimantele văzute de acest calculator
 *   POST /concateneaza    — unește mai multe PDF-uri; cu „imprimanta”, le și tipărește
 *   POST /arhiva          — scrie un document în arhiva locală
 *   GET  /arhiva          — citește un document din arhiva locală
 *   POST /arhiva/redenumeste — schimbă numele unui document arhivat
 *   GET  /monitorizare    — declarațiile puse în dosarul urmărit
 *   POST /monitorizare/mutat — scoate din dosar fișierul prelucrat
 */

error_reporting(E_ALL);

function incarca_env($cale)
{
    $env = array();

    if (!is_file($cale)) {
        return $env;
    }

    foreach (file($cale, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linie) {
        $linie = trim($linie);

        if ($linie === '' || $linie[0] === '#' || strpos($linie, '=') === false) {
            continue;
        }

        list($cheie, $valoare) = explode('=', $linie, 2);
        $valoare = trim($valoare);

        if ($valoare !== '' && ($valoare[0] === '"' || $valoare[0] === "'")) {
            $valoare = trim($valoare, $valoare[0]);
        }

        $env[trim($cheie)] = $valoare;
    }

    return $env;
}

function raspunde_json($status, $date)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($date, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Programul cu care se tipăresc PDF-urile.
 *
 * Calea scrisă în bridge.env are întâietate. Fără ea, se caută un program de
 * tipărire chiar lângă acest fișier: kitul îl aduce acolo, deci pe calculatorul
 * clientului nu trebuie configurat nimic de mână.
 *
 * Fără niciunul, tipărirea merge pe verbul Windows — care cere ca PDF-urile să
 * fie asociate cu un program ce știe să tipărească.
 */
function gaseste_program_tiparire(array $env, $dosar)
{
    if (!empty($env['IMPRIMARE_EXE']) && is_file(trim($env['IMPRIMARE_EXE']))) {
        return trim($env['IMPRIMARE_EXE']);
    }

    foreach (array('PDFtoPrinter.exe', 'SumatraPDF.exe') as $nume) {
        $cale = $dosar . DIRECTORY_SEPARATOR . $nume;

        if (is_file($cale)) {
            return $cale;
        }
    }

    return '';
}

/**
 * O bucată de cale primită de la aplicație, adusă la ce acceptă Windows.
 *
 * Separatoarele dispar dinadins: nicio valoare venită prin rețea nu are voie să
 * urce în alt dosar. Punctele de la sfârșit se taie pentru că Windows le
 * înlătură singur, iar dosarul scris data viitoare ar fi altul.
 */
function arhiva_bucata($valoare)
{
    $valoare = str_replace(array('\\', '/'), ' ', (string) $valoare);
    $valoare = preg_replace('/[\x00-\x1F:*?"<>|]/', '', $valoare);
    $valoare = preg_replace('/\s+/', ' ', $valoare);
    $valoare = trim($valoare, " .\t");

    return substr($valoare, 0, 150);
}

/**
 * Rădăcina arhivei cerută de aplicație, dacă e o cale întreagă și cuminte.
 *
 * Se acceptă doar o cale completă — „D:\Documente fiscale" sau un folder din
 * rețea — și niciun „..": aplicația spune unde ține clientul arhiva, nu se plimbă
 * prin discul lui. Întoarce '' dacă valoarea nu e bună de folosit.
 */
function arhiva_radacina_ceruta($valoare)
{
    $valoare = trim(str_replace('/', '\\', (string) $valoare));
    $valoare = rtrim($valoare, '\\');

    if ($valoare === '' || strpos($valoare, '..') !== false) {
        return '';
    }

    // Litera de disc (D:\...) sau cale de rețea (\\server\arhiva)
    if (!preg_match('/^([A-Za-z]:\\\\|\\\\\\\\[^\\\\]+\\\\)/', $valoare)) {
        return '';
    }

    return $valoare;
}

/** Calea absolută a unui document cerut prin calea lui relativă. */
function arhiva_cale_ceruta($radacina, $relativa)
{
    $bucati = array();

    foreach (preg_split('#[\\\\/]+#', (string) $relativa) as $bucata) {
        $curata = arhiva_bucata($bucata);

        if ($curata !== '') {
            $bucati[] = $curata;
        }
    }

    if ($bucati === array()) {
        return '';
    }

    return $radacina . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $bucati);
}

/** Calea pe care o ține minte serverul, mereu cu "/". */
function arhiva_relativa($radacina, $completa)
{
    $relativa = substr($completa, strlen($radacina));

    return str_replace('\\', '/', ltrim($relativa, '\\/'));
}

/**
 * Locul unde se scrie documentul, fără să pierdem ce era acolo.
 *
 * Același nume nu înseamnă neapărat același document: pentru aceeași lună se pot
 * depune mai multe D394, a doua fiind rectificativă fără să fie bifată așa. Un
 * document nou primește deci un nume liber — „... (2)", „... (3)".
 *
 * Singura suprascriere permisă e a propriului fișier: aplicația trimite în
 * $inlocuieste calea documentului scris data trecută pentru aceeași declarație,
 * ca resemnarea să nu lase în urmă un al doilea fișier.
 */
function arhiva_destinatie($dosar, $nume, $inlocuieste = '')
{
    $cale = $dosar . DIRECTORY_SEPARATOR . $nume;

    if ($inlocuieste !== '' && $inlocuieste === $cale) {
        if (is_file($cale)) {
            @unlink($cale);
        }

        return $cale;
    }

    if (!file_exists($cale)) {
        return $cale;
    }

    $extensie = pathinfo($nume, PATHINFO_EXTENSION);
    $trunchi = $extensie !== '' ? substr($nume, 0, -(strlen($extensie) + 1)) : $nume;

    for ($numar = 2; $numar < 1000; $numar++) {
        $candidat = $dosar . DIRECTORY_SEPARATOR . $trunchi . ' (' . $numar . ')'
            . ($extensie !== '' ? '.' . $extensie : '');

        if (!file_exists($candidat)) {
            return $candidat;
        }
    }

    return $cale;
}

/**
 * Rulează curl.exe cu certificatul din store și întoarce
 * array(status, content_type, fisier_corp, iesire, cod_iesire).
 *
 * Opțiunile se transmit printr-un fișier de configurare curl (-K), nu ca
 * argumente de linie de comandă: pe Windows escapeshellarg() înlocuiește '%'
 * cu spațiu, ceea ce ar strica orice URL cu parametri codificați (%20).
 * Valorile din config nu se pun între ghilimele — curl le ia literal.
 *
 * Un 3xx final cu exit code != 0 (conexiune resetată în lanțul F5) e status 0.
 */
function executa_curl(array $config, $url, array $optiuni = array())
{
    $fisier_corp = tempnam(sys_get_temp_dir(), 'spvb');
    $fisier_antete = tempnam(sys_get_temp_dir(), 'spvh');
    $fisier_config = tempnam(sys_get_temp_dir(), 'spvc');

    $linii = array_merge(array(
        'url = ' . $url,
        'cert = CurrentUser\\MY\\' . $config['thumbprint'],
        'max-time = ' . (int) $config['timeout'],
        'output = ' . $fisier_corp,
        'dump-header = ' . $fisier_antete,
        'silent',
        'show-error',
    ), $optiuni);

    file_put_contents($fisier_config, implode("\n", $linii) . "\n");

    $comanda = escapeshellarg($config['curl']) . ' -K ' . escapeshellarg($fisier_config) . ' 2>&1';

    exec($comanda, $iesire, $cod_iesire);
    @unlink($fisier_config);

    $status = 0;
    $tip_continut = 'application/octet-stream';

    foreach (file($fisier_antete, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $antet) {
        if (preg_match('#^HTTP/\S+\s+(\d{3})#', $antet, $m)) {
            $status = (int) $m[1];
            $tip_continut = 'application/octet-stream';
        } elseif (stripos($antet, 'Content-Type:') === 0) {
            $tip_continut = trim(substr($antet, strlen('Content-Type:')));
        }
    }

    if ($cod_iesire !== 0 && $status >= 300 && $status < 400) {
        $status = 0;
    }

    @unlink($fisier_antete);

    return array(
        'status' => $status,
        'content_type' => $tip_continut,
        'fisier_corp' => $fisier_corp,
        'iesire' => implode(' | ', $iesire),
        'cod_iesire' => $cod_iesire,
    );
}

function trimite_fisier($status, $tip_continut, $fisier_corp)
{
    http_response_code($status);
    header('Content-Type: ' . $tip_continut);
    header('Content-Length: ' . filesize($fisier_corp));
    readfile($fisier_corp);
    @unlink($fisier_corp);
    exit;
}

/*
 * Licența și jetoanele de comandă.
 *
 * Programul poate fi citit și copiat — asta nu se poate opri. Se poate însă face
 * copia nefolositoare: licența îl leagă de un calculator anume și expiră, iar
 * comenzile vin cu un jeton semnat de server, valabil câteva minute. Ambele se
 * verifică aici, cu cheia publică din kit; cheia privată rămâne pe server.
 */
function cheie_publica_bridge($dosar)
{
    static $cheie = false;

    if ($cheie === false) {
        $fisier = $dosar . DIRECTORY_SEPARATOR . 'cheie-publica.pem';
        $cheie = is_file($fisier) ? file_get_contents($fisier) : null;
    }

    return $cheie;
}

/** Reprezentarea peste care s-a semnat: aceleași chei, în aceeași ordine. */
function canonic_licenta($date)
{
    ksort($date);

    return json_encode($date, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function semnatura_buna($continut, $semnatura, $dosar)
{
    $cheie = cheie_publica_bridge($dosar);

    if ($cheie === null || !function_exists('openssl_verify')) {
        return false;
    }

    $publica = openssl_pkey_get_public($cheie);

    return $publica !== false && openssl_verify($continut, $semnatura, $publica, OPENSSL_ALGO_SHA256) === 1;
}

/**
 * Amprenta calculatorului: numele lui și seria volumului de sistem.
 *
 * Nu e un secret și nici nu trebuie să fie — rostul ei e ca o licență copiată
 * pe alt calculator să nu se mai potrivească.
 */
function amprenta_masina()
{
    $serie = '';
    $iesire = array();

    @exec('cmd /c vol %SystemDrive% 2>nul', $iesire);

    foreach ($iesire as $linie) {
        if (preg_match('/([0-9A-F]{4}-[0-9A-F]{4})/i', $linie, $potrivire)) {
            $serie = strtoupper($potrivire[1]);
            break;
        }
    }

    $nume = getenv('COMPUTERNAME') ? getenv('COMPUTERNAME') : php_uname('n');

    return strtoupper(substr(hash('sha256', strtoupper($nume) . '|' . $serie), 0, 32));
}

function licenta_curenta($dosar)
{
    $fisier = $dosar . DIRECTORY_SEPARATOR . 'licenta.json';

    if (!is_file($fisier)) {
        return null;
    }

    $licenta = json_decode(file_get_contents($fisier), true);

    return is_array($licenta) && isset($licenta['date']) ? $licenta : null;
}

/**
 * Motivul pentru care licența nu e bună aici, sau "" dacă e în regulă.
 *
 * Fără cheie publică lângă program nu se cere licență: așa merg mai departe
 * instalările făcute înainte de această versiune.
 */
function licenta_refuzata($licenta, $dosar)
{
    if (cheie_publica_bridge($dosar) === null) {
        return '';
    }

    if (!function_exists('openssl_verify')) {
        return 'Lipsește extensia openssl din PHP-ul programului.';
    }

    if ($licenta === null) {
        return 'Programul nu a primit încă licență. Deschideți fila „Certificate digitale" în aplicație.';
    }

    $date = $licenta['date'];

    if (!semnatura_buna(canonic_licenta($date), base64_decode($licenta['semnatura']), $dosar)) {
        return 'Semnătura licenței nu se verifică.';
    }

    if (empty($date['expira']) || strtotime($date['expira']) < time()) {
        return 'Licența a expirat la ' . (isset($date['expira']) ? $date['expira'] : '?') . '.';
    }

    if (empty($date['masina']) || $date['masina'] !== amprenta_masina()) {
        return 'Licența este emisă pentru alt calculator.';
    }

    return '';
}

/** Jetonul de comandă: v1.<date>.<semnatura>, valabil câteva minute. */
function jeton_valid($prezentat, $dosar)
{
    if (cheie_publica_bridge($dosar) === null || strpos($prezentat, 'v1.') !== 0) {
        return false;
    }

    $bucati = explode('.', $prezentat);

    if (count($bucati) !== 3) {
        return false;
    }

    $continut = base64_url_decode($bucati[1]);
    $semnatura = base64_url_decode($bucati[2]);

    if ($continut === false || $semnatura === false || !semnatura_buna($continut, $semnatura, $dosar)) {
        return false;
    }

    $date = json_decode($continut, true);

    if (!is_array($date) || empty($date['expira'])) {
        return false;
    }

    // Un minut de toleranță: ceasurile celor două calculatoare nu bat la fix.
    return $date['expira'] >= time() - 60 && $date['emis'] <= time() + 60;
}

function base64_url_decode($valoare)
{
    $valoare = strtr($valoare, '-_', '+/');

    return base64_decode($valoare . str_repeat('=', (4 - strlen($valoare) % 4) % 4));
}

/*
 * Bridge-ul poate rula independent, pe alt calculator din retea: isi citeste
 * configurarea din bridge.env de langa el, iar in instalarea de dezvoltare
 * cade pe .env-ul aplicatiei.
 */
$env = incarca_env(__DIR__ . '/configurare.env');

if ($env === array()) {
    $env = incarca_env(__DIR__ . '/bridge.env'); // denumirea folosită anterior
}

if ($env === array()) {
    $env = incarca_env(__DIR__ . '/../.env');
}

$config = array(
    'token'      => isset($env['SPV_BRIDGE_TOKEN']) ? $env['SPV_BRIDGE_TOKEN'] : '',
    'thumbprint' => isset($env['SPV_CERT_THUMBPRINT']) ? $env['SPV_CERT_THUMBPRINT'] : '',
    'base_url'   => isset($env['SPV_BASE_URL']) ? $env['SPV_BASE_URL'] : 'https://webserviced.anaf.ro/SPVWS2/rest',
    'decl_url'   => isset($env['ANAF_URL_DEPUNERE']) ? $env['ANAF_URL_DEPUNERE'] : 'https://decl.anaf.mfinante.gov.ro',
    'etransport' => isset($env['ETRANSPORT_URL']) ? $env['ETRANSPORT_URL'] : 'https://webserviceapl.anaf.ro/prod/ETRANSPORT/ws/v1',
    'timeout'    => 120,
    'curl'       => getenv('SystemRoot') . '\\System32\\curl.exe',
    // Program de tiparit PDF-uri; gasit singur daca sta langa acest fisier
    'imprimare_exe' => gaseste_program_tiparire($env, __DIR__),
    // Unde se strang documentele fiscale ale clientului (vezi ruta /arhiva)
    'arhiva'     => rtrim(isset($env['ARHIVA_CALE']) && $env['ARHIVA_CALE'] !== ''
        ? $env['ARHIVA_CALE']
        : __DIR__ . '\\arhiva', '\\/'),
    'cookie_jar' => __DIR__ . '/cookies.txt',
    'decl_jar'   => __DIR__ . '/decl_cookies.txt',
);

if ($config['token'] === '') {
    raspunde_json(500, array('eroare' => 'Configurare incompletă: lipsește codul de acces (SPV_BRIDGE_TOKEN).'));
}

/*
 * Pe acelasi calculator se pot conecta succesiv mai multe tokene, asa ca
 * amprenta certificatului vine cu fiecare cerere (antetul X-Thumbprint).
 * Cea din .env ramane doar ca valoare implicita.
 */
if (!empty($_SERVER['HTTP_X_THUMBPRINT'])) {
    $ceruta = preg_replace('/[^A-Fa-f0-9]/', '', $_SERVER['HTTP_X_THUMBPRINT']);

    if ($ceruta !== '') {
        $config['thumbprint'] = strtoupper($ceruta);
    }
}

// Sesiunile ANAF sunt legate de certificat, deci fiecare token isi are cookie-urile lui.
if ($config['thumbprint'] !== '') {
    $sufix = substr($config['thumbprint'], 0, 16);
    $config['cookie_jar'] = __DIR__ . '/cookies-' . $sufix . '.txt';
    $config['decl_jar'] = __DIR__ . '/decl-cookies-' . $sufix . '.txt';
}

$autorizare = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
$prezentat = strpos($autorizare, 'Bearer ') === 0 ? substr($autorizare, 7) : '';

$calea = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$metoda = $_SERVER['REQUEST_METHOD'];

/*
 * Cine are voie să dea comenzi.
 *
 * Codul din configurare.env deschide doar ușa instalării: cu el se citește
 * amprenta calculatorului și se pune licența. Pentru lucrul propriu-zis e nevoie
 * de un jeton semnat de server, valabil câteva minute — pe care nici clientul,
 * care își știe codul, nu-l poate face singur.
 *
 * Instalările fără licență (cele dinaintea acestei versiuni) merg mai departe
 * cu codul static: altfel s-ar opri singure la actualizare.
 */
$licenta = licenta_curenta(__DIR__);

/*
 * Rutele care merg si fara licenta.
 *
 * Pe langa identitate si licentiere, „/certificate": cu ea se face inrolarea —
 * programul isi spune certificatele de pe token, ca aplicatia sa stie cui sa-i
 * ceara licenta. Fara ea, un calculator nou n-ar avea cum sa intre in evidenta:
 * ar astepta o licenta care nu se poate emite pentru un certificat necunoscut.
 *
 * Ce se afla astfel e numele si seria certificatului propriu, catre cineva care
 * are deja codul de acces al acestui calculator. Semnarea, SPV-ul si depunerea
 * raman inchise pana la licenta.
 */
$rute_de_instalare = array('/identitate', '/licenta', '/certificate');

if (!hash_equals('Bearer ' . $config['token'], $autorizare)
    && !jeton_valid($prezentat, __DIR__)) {
    raspunde_json(401, array('eroare' => 'Cod de acces invalid.'));
}

if (!in_array($calea, $rute_de_instalare, true)) {
    $motiv = licenta_refuzata($licenta, __DIR__);

    if ($motiv !== '') {
        raspunde_json(403, array(
            'eroare' => 'Programul nu are licență validă pe acest calculator.',
            'detalii' => $motiv,
        ));
    }

    // Cu licența pusă, codul static nu mai e de ajuns pentru comenzi.
    if ($licenta !== null && !empty($licenta['date']['jeton_semnat']) && !jeton_valid($prezentat, __DIR__)) {
        raspunde_json(401, array(
            'eroare' => 'Comanda nu este semnată de server.',
            'detalii' => 'Codul de acces deschide doar instalarea și licențierea.',
        ));
    }
}

/*
 * GET /identitate — amprenta calculatorului și starea licenței. Cu ea, serverul
 * emite o licență legată de mașina aceasta.
 */
if ($metoda === 'GET' && $calea === '/identitate') {
    raspunde_json(200, array(
        'masina' => amprenta_masina(),
        'licentiat' => $licenta !== null && licenta_refuzata($licenta, __DIR__) === '',
        'licenta' => $licenta === null ? null : array(
            'client' => isset($licenta['date']['client']) ? $licenta['date']['client'] : null,
            'expira' => isset($licenta['date']['expira']) ? $licenta['date']['expira'] : null,
        ),
    ));
}

/*
 * POST /licenta — licența semnată de server, legată de acest calculator.
 * Se verifică înainte de a fi scrisă: un fișier stricat n-are ce căuta pe disc.
 */
if ($metoda === 'POST' && $calea === '/licenta') {
    $primita = json_decode(file_get_contents('php://input'), true);

    if (!is_array($primita) || !isset($primita['date']) || !isset($primita['semnatura'])) {
        raspunde_json(400, array('eroare' => 'Licența trimisă nu are forma așteptată.'));
    }

    $motiv = licenta_refuzata($primita, __DIR__);

    if ($motiv !== '') {
        raspunde_json(422, array('eroare' => 'Licența nu este valabilă aici.', 'detalii' => $motiv));
    }

    if (@file_put_contents(__DIR__ . '/licenta.json', json_encode($primita)) === false) {
        raspunde_json(500, array('eroare' => 'Licența nu a putut fi scrisă lângă program.'));
    }

    raspunde_json(200, array('primita' => true, 'expira' => $primita['date']['expira']));
}

/*
 * Rutele care folosesc certificatul verifica din start ca tokenul cerut este
 * chiar conectat aici — altfel eroarea ar aparea tarziu, ca esec TLS obscur.
 */
if ($calea !== '/certificate' && preg_match('#^/(spv|decl|semnare|etransport)#', $calea)) {
    if ($config['thumbprint'] === '') {
        raspunde_json(400, array('eroare' => 'Nu s-a indicat certificatul cerut.'));
    }

    exec(implode(' ', array(
        'powershell', '-NoProfile', '-ExecutionPolicy', 'Bypass',
        '-File', escapeshellarg(__DIR__ . '\\cert-info.ps1'),
        '-Thumbprint', escapeshellarg($config['thumbprint']),
        '2>&1',
    )), $verificare, $cod_verificare);

    if ($cod_verificare !== 0) {
        raspunde_json(409, array(
            'eroare' => 'Tokenul cu certificatul cerut nu este conectat la acest calculator.',
            'thumbprint' => $config['thumbprint'],
            'detalii' => mb_substr(implode(' | ', $verificare), 0, 300),
        ));
    }
}

/*
 * GET /spv/<cale> — proxy REST SPV cu sesiune F5 persistentă și o reîncercare
 * cu sesiune nouă la conexiune resetată.
 */
if ($metoda === 'GET' && preg_match('#^/spv/([A-Za-z0-9_\-/.]+)$#', $calea, $potrivire)) {
    $tinta = rtrim($config['base_url'], '/') . '/' . $potrivire[1];

    if (!empty($_SERVER['QUERY_STRING'])) {
        $tinta .= '?' . $_SERVER['QUERY_STRING'];
    }

    $rezultat = array('cod_iesire' => -1, 'iesire' => '');

    for ($incercare = 1; $incercare <= 2; $incercare++) {
        $rezultat = executa_curl($config, $tinta, array(
            'location',
            'cookie-jar = ' . $config['cookie_jar'],
            'cookie = ' . $config['cookie_jar'],
        ));

        if ($rezultat['status'] >= 100) {
            trimite_fisier($rezultat['status'], $rezultat['content_type'], $rezultat['fisier_corp']);
        }

        @unlink($rezultat['fisier_corp']);

        if ($incercare === 1) {
            @unlink($config['cookie_jar']);
            sleep(3);
        }
    }

    raspunde_json(502, array(
        'eroare'  => 'Apelul către ANAF a eșuat (curl exit ' . $rezultat['cod_iesire'] . ')',
        'detalii' => $rezultat['iesire'],
    ));
}

/*
 * GET /certificat — detaliile certificatului configurat, citite din magazinul
 * de certificate Windows (subiect, emitent, serie, perioada de valabilitate).
 */
if ($metoda === 'GET' && ($calea === '/certificat' || $calea === '/certificate')) {
    // /certificat  -> certificatul cerut (X-Thumbprint sau cel din .env)
    // /certificate -> toate tokenele conectate acum la acest calculator
    $argumente = array(
        'powershell', '-NoProfile', '-ExecutionPolicy', 'Bypass',
        '-File', escapeshellarg(__DIR__ . '\\cert-info.ps1'),
    );

    if ($calea === '/certificat') {
        if ($config['thumbprint'] === '') {
            raspunde_json(400, array(
                'eroare' => 'Nu s-a indicat certificatul cerut.',
            ));
        }

        $argumente[] = '-Thumbprint';
        $argumente[] = escapeshellarg($config['thumbprint']);
    }

    $argumente[] = '2>&1';

    exec(implode(' ', $argumente), $iesire, $cod_iesire);
    $json = json_decode(implode('', $iesire), true);

    if ($cod_iesire !== 0 || !is_array($json)) {
        raspunde_json(404, array(
            'eroare' => 'Certificatul nu este disponibil pe acest calculator.',
            'detalii' => mb_substr(implode(' | ', $iesire), 0, 400),
        ));
    }

    raspunde_json(200, $json);
}

/*
 * POST /decl/login — reface sesiunea pe decl.anaf.mfinante.gov.ro:
 * GET / (302 -> my.policy), POST my.policy vhost=standard, eventual "dummy".
 * Succes cand raspunsul contine "displayFile.do".
 */
if ($metoda === 'POST' && $calea === '/decl/login') {
    @unlink($config['decl_jar']);

    $sesiune = array(
        'location',
        'cookie-jar = ' . $config['decl_jar'],
        'cookie = ' . $config['decl_jar'],
    );

    $rezultat = executa_curl($config, $config['decl_url'], $sesiune);
    $corp = $rezultat['status'] >= 100 ? file_get_contents($rezultat['fisier_corp']) : '';
    @unlink($rezultat['fisier_corp']);

    $rezultat = executa_curl($config, $config['decl_url'] . '/my.policy', array_merge($sesiune, array(
        'data = vhost=standard',
    )));
    $corp = $rezultat['status'] >= 100 ? file_get_contents($rezultat['fisier_corp']) : '';
    @unlink($rezultat['fisier_corp']);

    // provocarea "dummy" a gateway-ului F5
    if (strpos($corp, 'dummy') !== false && preg_match('/name="dummy"[^>]*value="([^"]*)"/', $corp, $m)) {
        $rezultat = executa_curl($config, $config['decl_url'] . '/my.policy', array_merge($sesiune, array(
            'data = dummy=' . $m[1],
        )));
        $corp = $rezultat['status'] >= 100 ? file_get_contents($rezultat['fisier_corp']) : '';
        @unlink($rezultat['fisier_corp']);
    }

    if (strpos($corp, 'displayFile.do') !== false) {
        raspunde_json(200, array('success' => true));
    }

    raspunde_json(502, array(
        'eroare' => 'Autentificarea la ANAF a eșuat.',
        'detalii' => mb_substr(strip_tags($corp), 0, 300),
    ));
}

/*
 * POST /decl/upload — corpul cererii este PDF-ul semnat; antetul X-Filename
 * dă numele fișierului. Răspunsul este HTML-ul ANAF (conține indicele de
 * încărcare sau eroarea).
 */
if ($metoda === 'POST' && $calea === '/decl/upload') {
    $pdf = file_get_contents('php://input');

    if ($pdf === '' || $pdf === false) {
        raspunde_json(400, array('eroare' => 'Cererea nu conține niciun fișier.'));
    }

    $nume = isset($_SERVER['HTTP_X_FILENAME']) ? preg_replace('/[^A-Za-z0-9_\-.]/', '_', $_SERVER['HTTP_X_FILENAME']) : 'declaratie.pdf';
    $fisier_pdf = tempnam(sys_get_temp_dir(), 'spvd') . '.pdf';
    file_put_contents($fisier_pdf, $pdf);

    $rezultat = executa_curl($config, $config['decl_url'] . '/WAS6DUS/displayFile.do', array(
        'cookie-jar = ' . $config['decl_jar'],
        'cookie = ' . $config['decl_jar'],
        'form = linkdoc=@' . $fisier_pdf . ';type=application/pdf;filename=' . $nume,
    ));

    @unlink($fisier_pdf);

    if ($rezultat['status'] >= 100) {
        trimite_fisier($rezultat['status'], $rezultat['content_type'], $rezultat['fisier_corp']);
    }

    @unlink($rezultat['fisier_corp']);
    raspunde_json(502, array(
        'eroare'  => 'Trimiterea către ANAF a eșuat (curl exit ' . $rezultat['cod_iesire'] . ')',
        'detalii' => $rezultat['iesire'],
    ));
}

/*
 * /etransport/<cale> — serviciile e-Transport ale ANAF (lista, stareMesaj, info,
 * upload). Calea și parametrii se transmit ca atare; la depunere, corpul cererii
 * este declarația în format XML.
 */
if (preg_match('#^/etransport/(.+)$#', $calea, $potrivire)) {
    $tinta = rtrim($config['etransport'], '/') . '/' . $potrivire[1];

    if (!empty($_SERVER['QUERY_STRING'])) {
        $tinta .= '?' . $_SERVER['QUERY_STRING'];
    }

    $optiuni = array('location');
    $fisier_trimis = null;

    if ($metoda === 'POST') {
        $corp = file_get_contents('php://input');

        if ($corp === '' || $corp === false) {
            raspunde_json(400, array('eroare' => 'Cererea nu conține declarația XML.'));
        }

        $fisier_trimis = tempnam(sys_get_temp_dir(), 'etr') . '.xml';
        file_put_contents($fisier_trimis, $corp);

        $optiuni[] = 'data-binary = @' . $fisier_trimis;
        $optiuni[] = 'header = Content-Type: application/xml';
    }

    $rezultat = executa_curl($config, $tinta, $optiuni);

    if ($fisier_trimis !== null) {
        @unlink($fisier_trimis);
    }

    if ($rezultat['status'] >= 100) {
        trimite_fisier($rezultat['status'], $rezultat['content_type'], $rezultat['fisier_corp']);
    }

    @unlink($rezultat['fisier_corp']);

    raspunde_json(502, array(
        'eroare'  => 'Apelul către e-Transport a eșuat (curl exit ' . $rezultat['cod_iesire'] . ')',
        'detalii' => $rezultat['iesire'],
    ));
}

/*
 * POST /pdf/info — corpul cererii este un PDF de declarație; răspunsul spune
 * dacă este semnat și conține XML-ul atașat în el (necesar pentru validare).
 */
if ($metoda === 'POST' && $calea === '/pdf/info') {
    $pdf = file_get_contents('php://input');

    if ($pdf === '' || $pdf === false) {
        raspunde_json(400, array('eroare' => 'Cererea nu conține niciun fișier.'));
    }

    $fisier = tempnam(sys_get_temp_dir(), 'spvp') . '.pdf';
    file_put_contents($fisier, $pdf);

    $comanda = implode(' ', array(
        'powershell', '-NoProfile', '-ExecutionPolicy', 'Bypass',
        '-File', escapeshellarg(__DIR__ . '\\pdf-info.ps1'),
        '-Cale', escapeshellarg($fisier),
        '2>&1',
    ));

    exec($comanda, $iesire, $cod_iesire);
    @unlink($fisier);

    $json = json_decode(implode('', $iesire), true);

    if ($cod_iesire !== 0 || !is_array($json)) {
        raspunde_json(422, array(
            'eroare' => 'PDF-ul nu a putut fi citit.',
            'detalii' => mb_substr(implode(' | ', $iesire), 0, 400),
        ));
    }

    raspunde_json(200, $json);
}

/*
 * POST /semnare — corpul cererii este PDF-ul de semnat; răspunsul este PDF-ul
 * semnat cu certificatul de pe token (dialogul de PIN apare pe acest calculator).
 */
if ($metoda === 'POST' && $calea === '/semnare') {
    $pdf = file_get_contents('php://input');

    if ($pdf === '' || $pdf === false) {
        raspunde_json(400, array('eroare' => 'Cererea nu conține niciun fișier.'));
    }

    $fisier_in = tempnam(sys_get_temp_dir(), 'spvs') . '.pdf';
    $fisier_out = $fisier_in . '.semnat.pdf';
    file_put_contents($fisier_in, $pdf);

    $argumente = array(
        'powershell', '-NoProfile', '-ExecutionPolicy', 'Bypass',
        '-File', escapeshellarg(__DIR__ . '\\sign-pdf.ps1'),
        '-InPath', escapeshellarg($fisier_in),
        '-OutPath', escapeshellarg($fisier_out),
        '-Thumbprint', escapeshellarg($config['thumbprint']),
    );

    /*
     * Pozitia casetei vizibile vine de la aplicatie: tine de formular, nu de
     * calculatorul cu tokenul. Lipsa lor lasa valorile implicite din script.
     */
    $caseta = array(
        'Pagina'   => 'HTTP_X_SEMNATURA_PAGINA',
        'X'        => 'HTTP_X_SEMNATURA_X',
        'Y'        => 'HTTP_X_SEMNATURA_Y',
        'Latime'   => 'HTTP_X_SEMNATURA_LATIME',
        'Inaltime' => 'HTTP_X_SEMNATURA_INALTIME',
        'Motiv'    => 'HTTP_X_SEMNATURA_MOTIV',
    );

    foreach ($caseta as $parametru => $antet) {
        $valoare = isset($_SERVER[$antet]) ? trim($_SERVER[$antet]) : '';

        // Numerele se verifica, ca sa nu ajunga text arbitrar in linia de comanda.
        if ($valoare === '') {
            continue;
        }

        if (in_array($parametru, array('X', 'Y', 'Latime', 'Inaltime'), true) && !is_numeric($valoare)) {
            continue;
        }

        $argumente[] = '-' . $parametru;
        $argumente[] = escapeshellarg($valoare);
    }

    $argumente[] = '2>&1';

    $comanda = implode(' ', $argumente);

    exec($comanda, $iesire, $cod_iesire);
    @unlink($fisier_in);

    if ($cod_iesire === 0 && is_file($fisier_out) && filesize($fisier_out) > 0) {
        trimite_fisier(200, 'application/pdf', $fisier_out);
    }

    @unlink($fisier_out);
    raspunde_json(500, array(
        'eroare'  => 'Semnarea documentului a eșuat.',
        'detalii' => mb_substr(implode(' | ', $iesire), 0, 500),
    ));
}

/*
 * POST /concateneaza — uneste PDF-urile primite (campuri fisiere[]) intr-unul
 * singur, pentru tiparire. Nu foloseste certificatul, deci nu cere tokenul.
 */
if ($metoda === 'POST' && $calea === '/concateneaza') {
    $primite = isset($_FILES['fisiere']) ? $_FILES['fisiere'] : null;

    if (!$primite || empty($primite['tmp_name'])) {
        raspunde_json(400, array('eroare' => 'Cererea nu conține fișiere de unit.'));
    }

    // Textul de scris in filigran, cate unul pentru fiecare fisier (poate lipsi)
    $filigrane = isset($_POST['watermark']) ? (array) $_POST['watermark'] : array();

    $temporare = array();
    $randuri = array();

    foreach ((array) $primite['tmp_name'] as $index => $temporar) {
        if (!is_uploaded_file($temporar)) {
            continue;
        }

        $destinatie = tempnam(sys_get_temp_dir(), 'spvm') . '-' . $index . '.pdf';
        move_uploaded_file($temporar, $destinatie);

        $temporare[] = $destinatie;

        $filigran = isset($filigrane[$index]) ? trim((string) $filigrane[$index]) : '';
        // Tabul desparte calea de text, iar randurile noi ar rupe lista.
        $filigran = str_replace(array("\t", "\r", "\n"), ' ', $filigran);

        $randuri[] = $destinatie . ($filigran !== '' ? "\t" . $filigran : '');
    }

    if ($randuri === array()) {
        raspunde_json(400, array('eroare' => 'Niciun fișier valid de unit.'));
    }

    // Caile se dau printr-un fisier: sunt multe si linia de comanda are limita.
    $fisier_lista = tempnam(sys_get_temp_dir(), 'spvl');
    file_put_contents($fisier_lista, implode("\r\n", $randuri));

    $fisier_out = tempnam(sys_get_temp_dir(), 'spvo') . '.pdf';

    exec(implode(' ', array(
        'powershell', '-NoProfile', '-ExecutionPolicy', 'Bypass',
        '-File', escapeshellarg(__DIR__ . '\\merge-pdf.ps1'),
        '-ListPath', escapeshellarg($fisier_lista),
        '-OutPath', escapeshellarg($fisier_out),
        '2>&1',
    )), $iesire, $cod_iesire);

    foreach ($temporare as $temporar) {
        @unlink($temporar);
    }

    @unlink($fisier_lista);

    if ($cod_iesire === 0 && is_file($fisier_out) && filesize($fisier_out) > 0) {
        /*
         * Cu imprimantă cerută, documentul unit se tipărește aici și nu mai
         * pleacă spre aplicație: hârtia iese pe imprimanta din birou, nu în
         * dosarul de descărcări al browserului.
         */
        $imprimanta = isset($_POST['imprimanta']) ? trim($_POST['imprimanta']) : '';

        if ($imprimanta !== '' || !empty($_POST['tipareste'])) {
            $argumente = array(
                'powershell', '-NoProfile', '-ExecutionPolicy', 'Bypass',
                '-File', escapeshellarg(__DIR__ . '\\print-pdf.ps1'),
                '-Cale', escapeshellarg($fisier_out),
            );

            if ($imprimanta !== '') {
                $argumente[] = '-Imprimanta';
                $argumente[] = escapeshellarg($imprimanta);
            }

            if ($config['imprimare_exe'] !== '') {
                $argumente[] = '-Program';
                $argumente[] = escapeshellarg($config['imprimare_exe']);
            }

            $argumente[] = '2>&1';

            exec(implode(' ', $argumente), $iesire_print, $cod_print);
            @unlink($fisier_out);

            if ($cod_print !== 0) {
                raspunde_json(500, array(
                    'eroare'  => 'Tipărirea a eșuat.',
                    'detalii' => mb_substr(trim(implode(' ', $iesire_print)), 0, 500),
                ));
            }

            raspunde_json(200, array(
                'tiparit'    => true,
                'imprimanta' => $imprimanta !== '' ? $imprimanta : 'imprimanta implicită',
                'pagini'     => count($randuri),
                'detalii'    => mb_substr(trim(implode(' ', $iesire_print)), 0, 300),
            ));
        }

        trimite_fisier(200, 'application/pdf', $fisier_out);
    }

    @unlink($fisier_out);
    raspunde_json(500, array(
        'eroare'  => 'Unirea documentelor a eșuat.',
        'detalii' => mb_substr(implode(' | ', $iesire), 0, 500),
    ));
}

/*
 * Arhiva locală de documente.
 *
 * Aplicația stă în cloud, dar declarațiile, recipisele și documentele aduse din
 * SPV rămân aici, sub ARHIVA_CALE, în structura:
 *
 *     <rădăcină>\<Denumire firmă (CUI)>\<TIP>\<TIP>_<CUI>_<perioadă>_<stare>.pdf
 *
 * Serverul ține minte doar calea relativă și cere fișierul înapoi când e nevoie.
 */
/*
 * Dosarul urmărit: declarațiile puse acolo se iau singure de aplicație, se
 * validează și se semnează. Rădăcina vine cu fiecare cerere, ca la arhivă.
 *
 *   GET  /monitorizare          — ce fișiere așteaptă
 *   GET  /monitorizare/fisier   — conținutul unuia
 *   POST /monitorizare/mutat    — îl duce în „prelucrate" sau „erori"
 */
if (strpos($calea, '/monitorizare') === 0) {
    $radacina = '';

    if (!empty($_SERVER['HTTP_X_MONITORIZARE_CALE'])) {
        $radacina = arhiva_radacina_ceruta($_SERVER['HTTP_X_MONITORIZARE_CALE']);
    }

    if ($radacina === '') {
        raspunde_json(400, array(
            'eroare' => 'Nu s-a indicat dosarul de urmărit.',
            'detalii' => 'Se așteaptă o cale completă, de forma D:\\Declarații de semnat.',
        ));
    }

    if (!is_dir($radacina) && !@mkdir($radacina, 0777, true)) {
        raspunde_json(500, array('eroare' => 'Dosarul urmărit nu poate fi creat.', 'detalii' => $radacina));
    }

    // GET /monitorizare — fisierele care asteapta, fara subdosare
    if ($metoda === 'GET' && $calea === '/monitorizare') {
        $asteapta = array();

        // XML sau PDF: declarațiile ANAF poartă XML-ul și în interiorul PDF-ului.
        foreach (glob($radacina . DIRECTORY_SEPARATOR . '*.{xml,XML,pdf,PDF}', GLOB_BRACE) as $cale_fisier) {
            if (!is_file($cale_fisier)) {
                continue;
            }

            /*
             * Un fișier abia copiat poate fi încă în curs de scriere. Se lasă
             * deoparte până se liniștește: altfel s-ar trimite spre validare o
             * declarație pe jumătate scrisă.
             */
            $varsta = time() - filemtime($cale_fisier);

            $asteapta[] = array(
                'nume' => basename($cale_fisier),
                'marime' => filesize($cale_fisier),
                'modificat' => date('Y-m-d H:i:s', filemtime($cale_fisier)),
                'gata' => $varsta >= 5,
            );
        }

        raspunde_json(200, array('dosar' => $radacina, 'fisiere' => $asteapta));
    }

    // GET /monitorizare/fisier?nume=... — continutul unui fisier care asteapta
    if ($metoda === 'GET' && $calea === '/monitorizare/fisier') {
        $nume = arhiva_bucata(isset($_GET['nume']) ? $_GET['nume'] : '');
        $cale_fisier = $radacina . DIRECTORY_SEPARATOR . $nume;

        if ($nume === '' || !is_file($cale_fisier)) {
            raspunde_json(404, array('eroare' => 'Fișierul nu se află în dosarul urmărit.'));
        }

        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . filesize($cale_fisier));
        readfile($cale_fisier);
        exit;
    }

    /*
     * POST /monitorizare/mutat — fișierul prelucrat pleacă din dosar, ca să nu
     * fie luat a doua oară. Rămâne pe disc, într-un subdosar, ca omul să poată
     * vedea ce s-a întâmplat cu el.
     */
    if ($metoda === 'POST' && $calea === '/monitorizare/mutat') {
        $nume = arhiva_bucata(isset($_POST['nume']) ? $_POST['nume'] : '');
        $unde = isset($_POST['unde']) && $_POST['unde'] === 'erori' ? 'erori' : 'prelucrate';

        $sursa = $radacina . DIRECTORY_SEPARATOR . $nume;

        if ($nume === '' || !is_file($sursa)) {
            raspunde_json(404, array('eroare' => 'Fișierul nu se află în dosarul urmărit.'));
        }

        $dosar = $radacina . DIRECTORY_SEPARATOR . $unde;

        if (!is_dir($dosar) && !@mkdir($dosar, 0777, true)) {
            raspunde_json(500, array('eroare' => 'Subdosarul nu poate fi creat.', 'detalii' => $dosar));
        }

        // Numele poarta data prelucrarii: acelasi fisier poate veni de mai multe ori.
        $extensie = pathinfo($nume, PATHINFO_EXTENSION);
        $trunchi = $extensie !== '' ? substr($nume, 0, -(strlen($extensie) + 1)) : $nume;
        $destinatie = arhiva_destinatie($dosar, $trunchi . '_' . date('Ymd_His')
            . ($extensie !== '' ? '.' . $extensie : ''));

        if (!@rename($sursa, $destinatie)) {
            raspunde_json(500, array('eroare' => 'Fișierul nu a putut fi mutat.'));
        }

        raspunde_json(200, array('mutat' => arhiva_relativa($radacina, $destinatie)));
    }

    raspunde_json(404, array('eroare' => 'Operație necunoscută pe dosarul urmărit.'));
}

/*
 * GET /imprimante — imprimantele vazute de acest calculator, ca omul sa-si
 * aleaga din aplicatie pe care tipareste.
 */
if ($metoda === 'GET' && $calea === '/imprimante') {
    exec(implode(' ', array(
        'powershell', '-NoProfile', '-ExecutionPolicy', 'Bypass',
        '-File', escapeshellarg(__DIR__ . '\\imprimante.ps1'),
        '2>&1',
    )), $iesire, $cod_iesire);

    $text = trim(implode('', $iesire));
    $lista = json_decode($text, true);

    if ($cod_iesire !== 0 || !is_array($lista)) {
        raspunde_json(500, array(
            'eroare'  => 'Imprimantele nu au putut fi citite.',
            'detalii' => mb_substr($text, 0, 400),
        ));
    }

    // O singura imprimanta vine ca obiect, nu ca lista
    if (isset($lista['nume'])) {
        $lista = array($lista);
    }

    raspunde_json(200, array('imprimante' => $lista));
}

/*
 * GET /arhiva/foldere?cale=... — dosarele de pe acest calculator, ca omul să
 * aleagă din aplicație unde să stea arhiva.
 *
 * Fără „cale" întoarce discurile. Se listează doar dosare, niciodată fișiere:
 * atât e nevoie pentru alegere. Stă înaintea rutei /arhiva pentru că aici se
 * umblă tocmai în afara arhivei — încă nu se știe unde va fi.
 */
if ($metoda === 'GET' && $calea === '/arhiva/foldere') {
    $ceruta = isset($_GET['cale']) ? trim(str_replace('/', '\\', $_GET['cale'])) : '';

    if ($ceruta === '') {
        $discuri = array();

        foreach (range('A', 'Z') as $litera) {
            if (@is_dir($litera . ':\\')) {
                $discuri[] = array('nume' => $litera . ':', 'cale' => $litera . ':\\');
            }
        }

        raspunde_json(200, array('cale' => '', 'parinte' => null, 'foldere' => $discuri));
    }

    if (strpos($ceruta, '..') !== false || !is_dir($ceruta)) {
        raspunde_json(404, array('eroare' => 'Dosarul nu există pe acest calculator.'));
    }

    $ceruta = rtrim($ceruta, '\\') . '\\';
    $foldere = array();
    $continut = @scandir($ceruta);

    if ($continut === false) {
        raspunde_json(403, array('eroare' => 'Dosarul nu poate fi citit.'));
    }

    foreach ($continut as $nume) {
        if ($nume === '.' || $nume === '..' || !is_dir($ceruta . $nume)) {
            continue;
        }

        $foldere[] = array('nume' => $nume, 'cale' => $ceruta . $nume);
    }

    // Alfabetic, nu pe coduri de caractere: altfel „Zeta" ar sta inaintea lui
    // „arhiva". Aplicatia le mai aseaza o data, pentru programele mai vechi.
    usort($foldere, function ($unul, $altul) {
        return strnatcasecmp($unul['nume'], $altul['nume']);
    });

    // Rădăcina unui disc n-are părinte în afară de lista de discuri.
    $parinte = preg_match('/^[A-Za-z]:\\\\$/', $ceruta) ? '' : dirname(rtrim($ceruta, '\\'));

    raspunde_json(200, array(
        'cale' => rtrim($ceruta, '\\'),
        'parinte' => $parinte,
        'foldere' => $foldere,
    ));
}

if (strpos($calea, '/arhiva') === 0) {
    /*
     * Rădăcina arhivei se setează din aplicație, la certificatul care răspunde
     * de acest calculator, și vine cu fiecare cerere. Așa nu mai trebuie umblat
     * prin bridge.env pe fiecare stație; valoarea de acolo rămâne ca rezervă.
     */
    if (!empty($_SERVER['HTTP_X_ARHIVA_CALE'])) {
        $ceruta = arhiva_radacina_ceruta($_SERVER['HTTP_X_ARHIVA_CALE']);

        if ($ceruta === '') {
            raspunde_json(400, array(
                'eroare' => 'Calea arhivei este greșită.',
                'detalii' => 'Se așteaptă o cale completă, de forma D:\\Documente fiscale sau \\\\server\\arhiva.',
            ));
        }

        $config['arhiva'] = $ceruta;
    }

    if (!is_dir($config['arhiva']) && !@mkdir($config['arhiva'], 0777, true)) {
        raspunde_json(500, array(
            'eroare' => 'Dosarul de arhivă nu poate fi creat.',
            'detalii' => $config['arhiva'],
        ));
    }

    // POST /arhiva — scrie un document primit de la aplicație
    if ($metoda === 'POST' && $calea === '/arhiva') {
        if (!isset($_FILES['fisier']) || !is_uploaded_file($_FILES['fisier']['tmp_name'])) {
            raspunde_json(400, array('eroare' => 'Cererea nu conține documentul de arhivat.'));
        }

        $firma = arhiva_bucata(isset($_POST['firma']) ? $_POST['firma'] : '');
        $nume = arhiva_bucata(isset($_POST['nume']) ? $_POST['nume'] : $_FILES['fisier']['name']);

        if ($firma === '' || $nume === '') {
            raspunde_json(400, array('eroare' => 'Lipsește firma sau numele documentului.'));
        }

        // Subdosarul poate avea mai multe niveluri ("SPV/Situatie Sintetica"),
        // fiecare curățat în parte de arhiva_cale_ceruta().
        $dosarul = arhiva_cale_ceruta(
            $config['arhiva'],
            $firma . '/' . (isset($_POST['dosar']) ? $_POST['dosar'] : '')
        );

        if (!is_dir($dosarul) && !@mkdir($dosarul, 0777, true)) {
            raspunde_json(500, array('eroare' => 'Dosarul nu poate fi creat.', 'detalii' => $dosarul));
        }

        // Calea documentului scris data trecută pentru aceeași declarație:
        // singurul fișier pe care aplicația are voie să-l înlocuiască.
        $inlocuieste = isset($_POST['inlocuieste'])
            ? arhiva_cale_ceruta($config['arhiva'], $_POST['inlocuieste'])
            : '';

        $destinatie = arhiva_destinatie($dosarul, $nume, $inlocuieste);

        if (!@move_uploaded_file($_FILES['fisier']['tmp_name'], $destinatie)) {
            raspunde_json(500, array('eroare' => 'Documentul nu a putut fi scris.', 'detalii' => $destinatie));
        }

        raspunde_json(200, array(
            'cale' => arhiva_relativa($config['arhiva'], $destinatie),
            'cale_completa' => $destinatie,
        ));
    }

    // GET /arhiva?cale=... — trimite înapoi un document arhivat
    if ($metoda === 'GET' && $calea === '/arhiva') {
        $fisier = arhiva_cale_ceruta($config['arhiva'], isset($_GET['cale']) ? $_GET['cale'] : '');

        if (!is_file($fisier)) {
            raspunde_json(404, array('eroare' => 'Documentul nu se află în arhivă.'));
        }

        $tipuri = array('pdf' => 'application/pdf', 'xml' => 'application/xml', 'zip' => 'application/zip');
        $extensie = strtolower(pathinfo($fisier, PATHINFO_EXTENSION));

        header('Content-Type: ' . (isset($tipuri[$extensie]) ? $tipuri[$extensie] : 'application/octet-stream'));
        header('Content-Length: ' . filesize($fisier));
        readfile($fisier);
        exit;
    }

    // GET /arhiva/exista?cale=... — verificare fără transfer
    if ($metoda === 'GET' && $calea === '/arhiva/exista') {
        $fisier = arhiva_cale_ceruta($config['arhiva'], isset($_GET['cale']) ? $_GET['cale'] : '');

        raspunde_json(200, array('exista' => is_file($fisier)));
    }

    /*
     * POST /arhiva/redenumeste — după depunere, declarația semnată primește în
     * nume indicele de încărcare, ca să se vadă din dosar că s-a depus.
     */
    if ($metoda === 'POST' && $calea === '/arhiva/redenumeste') {
        $fisier = arhiva_cale_ceruta($config['arhiva'], isset($_POST['cale']) ? $_POST['cale'] : '');
        $nume = arhiva_bucata(isset($_POST['nume']) ? $_POST['nume'] : '');

        if (!is_file($fisier)) {
            raspunde_json(404, array('eroare' => 'Documentul nu se află în arhivă.'));
        }

        if ($nume === '') {
            raspunde_json(400, array('eroare' => 'Lipsește noul nume.'));
        }

        $destinatie = dirname($fisier) . DIRECTORY_SEPARATOR . $nume;

        // Numai daca chiar se schimba numele: altfel ne-am muta peste noi insine.
        if ($destinatie !== $fisier) {
            $destinatie = arhiva_destinatie(dirname($fisier), $nume);

            if (!@rename($fisier, $destinatie)) {
                raspunde_json(500, array('eroare' => 'Documentul nu a putut fi redenumit.'));
            }
        }

        raspunde_json(200, array(
            'cale' => arhiva_relativa($config['arhiva'], $destinatie),
            'cale_completa' => $destinatie,
        ));
    }

    raspunde_json(404, array('eroare' => 'Operație necunoscută pe arhivă.'));
}

raspunde_json(404, array('eroare' => 'Operație necunoscută.'));
