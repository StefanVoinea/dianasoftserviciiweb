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
 *   GET  /spv-arhiva      — aduce documentul din SPV direct în arhiva de aici
 *   POST /spv-arhiva-lot  — aceleași documente, cerute toate deodată (NDJSON)
 *   GET  /pin/fereastra   — stă deschisă acum o fereastră de PIN pe acest calculator?
 *   POST /pin/scrie       — scrie codul în fereastra deschisă și apasă OK
 *   POST /decl/login      — handshake autentificare decl.anaf.mfinante.gov.ro
 *   POST /decl/upload     — depunere PDF semnat (multipart linkdoc)
 *   POST /semnare         — semnează PDF-ul din corpul cererii (PowerShell+iTextSharp)
 *   GET  /imprimante      — imprimantele văzute de acest calculator
 *   POST /concateneaza    — unește mai multe PDF-uri; cu „imprimanta”, le și tipărește
 *   POST /arhiva          — scrie un document în arhiva locală
 *   GET  /arhiva          — citește un document din arhiva locală
 *   POST /arhiva/redenumeste — schimbă numele unui document arhivat
 *   POST /arhiva/copiaza  — încă un exemplar al unui document deja arhivat
 *   POST /arhiva/din-local — aduce în arhivă un fișier de pe acest calculator
 *   POST /arhiva/uneste-dosarul — strânge la un loc cele două dosare ale firmei
 *   GET  /monitorizare    — declarațiile puse în dosarul urmărit
 *   POST /monitorizare/mutat — scoate din dosar fișierul prelucrat
 */

error_reporting(E_ALL);

// Talcul codurilor cu care se opreste curl; stau deoparte, ca sa poata fi probate.
require_once __DIR__ . DIRECTORY_SEPARATOR . 'curl-talcuri.php';

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
 * Fara amprenta nu se cheama ANAF.
 *
 * Pe un calculator pot sta mai multe certificate — unul pentru SPV, altul
 * pentru SEAP — iar curl, primind selectorul gol, il lasa pe Windows sa aleaga.
 * Alegerea lui n-are de unde sa stie care e cel bun, iar ANAF raspunde atunci cu
 * redirectare spre pagina lui de autentificare: legatura pare stricata, desi de
 * fapt nimeni n-a spus cu ce certificat sa se vorbeasca.
 */
function cere_amprenta(array $config)
{
    if (trim((string) $config['thumbprint']) !== '') {
        return;
    }

    raspunde_json(400, array(
        'eroare' => 'Nu mi s-a spus cu ce certificat să vorbesc cu ANAF.',
        'detalii' => 'Cererea nu poartă antetul X-Thumbprint, iar în configurare.env nu e scrisă nicio'
            . ' amprentă. Alegeți certificatul în aplicație, la SPV -> Certificate digitale.',
    ));
}

/** Se scrie langa program, ca sa ramana urma si dupa ce fereastra s-a inchis. */
function scrie_eroarea($text)
{
    @file_put_contents(
        __DIR__ . DIRECTORY_SEPARATOR . 'erori.log',
        date('Y-m-d H:i:s') . ' ' . $text . PHP_EOL,
        FILE_APPEND
    );
}

/*
 * Nicio poticneala nu are voie sa lase cererea fara raspuns.
 *
 * Serverul din PHP tine un singur proces: o eroare fatala nu doar ca lasa
 * cererea in aer, ci opreste programul cu totul, iar sarcina programata il
 * reporneste abia peste un minut — timp in care aplicatia crede ca la client
 * nu ruleaza nimic. Se prinde deci si ce se arunca, si ce se opreste: se scrie
 * in erori.log de langa program si se raspunde cinstit, cu motivul.
 */
set_exception_handler(function ($e) {
    $unde = method_exists($e, 'getFile') ? ' (' . $e->getFile() . ':' . $e->getLine() . ')' : '';

    scrie_eroarea('Neprinsa: ' . $e->getMessage() . $unde);

    raspunde_json(500, array(
        'eroare' => 'Programul local s-a poticnit: ' . $e->getMessage(),
        'detalii' => 'Amanuntele sunt in erori.log, langa program. Lucrarea urmatoare merge mai departe.',
    ));
});

register_shutdown_function(function () {
    $ultima = error_get_last();
    $fatale = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);

    if (!$ultima || !in_array($ultima['type'], $fatale, true)) {
        return;
    }

    scrie_eroarea('Fatala: ' . $ultima['message'] . ' (' . $ultima['file'] . ':' . $ultima['line'] . ')');

    if (!headers_sent()) {
        raspunde_json(500, array(
            'eroare' => 'Programul local s-a oprit din lucrarea aceasta: ' . $ultima['message'],
            'detalii' => 'Amanuntele sunt in erori.log, langa program.',
        ));
    }
});

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
 * Mută tot ce se află într-un dosar în altul, păstrând structura dinăuntru.
 *
 * Fișierul care ar suprascrie ceva primește nume liber — „... (2)" —, la fel ca
 * la scrierea obișnuită: două documente cu același nume nu sunt neapărat același
 * document. Dosarele golite se șterg pe drum, iar cel vechi la sfârșit.
 *
 * Întoarce câte fișiere s-au mutat, sau false dacă vreunul n-a putut fi mutat —
 * atunci nu se șterge nimic, ca omul să vadă ce a rămas.
 */
/**
 * Uneste un dosar in altul: il redenumeste, daca celalalt nu exista, altfel ii
 * muta cuprinsul si il lasa gol.
 *
 * @return int|false cate fisiere s-au mutat, sau false la nereusita
 */
function arhiva_uneste_doua($caleVeche, $caleNoua)
{
    if (!is_dir($caleVeche) || $caleVeche === $caleNoua) {
        return 0;
    }

    if (!is_dir($caleNoua)) {
        return @rename($caleVeche, $caleNoua) ? 0 : false;
    }

    return arhiva_muta_cuprinsul($caleVeche, $caleNoua);
}

function arhiva_muta_cuprinsul($din, $in)
{
    $cuprins = @scandir($din);

    if ($cuprins === false) {
        return false;
    }

    $mutate = 0;

    foreach ($cuprins as $nume) {
        if ($nume === '.' || $nume === '..') {
            continue;
        }

        $sursa = $din . DIRECTORY_SEPARATOR . $nume;
        $tinta = $in . DIRECTORY_SEPARATOR . $nume;

        if (is_dir($sursa)) {
            if (!is_dir($tinta) && !@mkdir($tinta, 0777, true)) {
                return false;
            }

            $adanc = arhiva_muta_cuprinsul($sursa, $tinta);

            if ($adanc === false) {
                return false;
            }

            $mutate += $adanc;

            continue;
        }

        if (!@rename($sursa, arhiva_destinatie($in, $nume))) {
            return false;
        }

        $mutate++;
    }

    // Ramas gol, dosarul vechi nu mai are de ce sa stea.
    @rmdir($din);

    return $mutate;
}

/**
 * Rădăcina arhivei pentru cererea de față, gata de scris.
 *
 * Aplicația o trimite la fiecare cerere, în antet, ca să nu fie umblat prin
 * configurare.env pe fiecare stație; valoarea de acolo rămâne ca rezervă. Dacă
 * ceva nu e în regulă, se răspunde pe loc și execuția nu se mai întoarce aici.
 */
function arhiva_radacina_pregatita(array $config)
{
    $radacina = $config['arhiva'];

    if (!empty($_SERVER['HTTP_X_ARHIVA_CALE'])) {
        $ceruta = arhiva_radacina_ceruta($_SERVER['HTTP_X_ARHIVA_CALE']);

        if ($ceruta === '') {
            raspunde_json(400, array(
                'eroare' => 'Calea arhivei este greșită.',
                'detalii' => 'Se așteaptă o cale completă, de forma D:\\Documente fiscale sau \\\\server\\arhiva.',
            ));
        }

        $radacina = $ceruta;
    }

    if (!is_dir($radacina) && !@mkdir($radacina, 0777, true)) {
        raspunde_json(500, array(
            'eroare' => 'Dosarul de arhivă nu poate fi creat.',
            'detalii' => $radacina,
        ));
    }

    return $radacina;
}

/**
 * Textul scris în paginile unui PDF, citit cu biblioteca de lângă program.
 *
 * Cu el, aplicația află verdictul recipisei sau rândurile vectorului fiscal
 * fără ca documentul să plece de pe calculatorul acesta. Un eșec nu strică
 * nimic: aplicația rămâne fără text și se descurcă altfel.
 */
function pdf_text($dosar, $fisier)
{
    exec(implode(' ', array(
        'powershell', '-NoProfile', '-ExecutionPolicy', 'Bypass',
        '-File', escapeshellarg($dosar . '\\pdf-info.ps1'),
        '-Cale', escapeshellarg($fisier),
        '-Text',
        '2>nul',
    )), $iesire, $cod_iesire);

    if ($cod_iesire !== 0) {
        return null;
    }

    $json = json_decode(implode('', $iesire), true);

    return isset($json['text']) && $json['text'] !== '' ? $json['text'] : null;
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
/**
 * O valoare scrisa in fisierul de configurare al lui curl.
 *
 * Curl taie valoarea la primul spatiu, daca nu e intre ghilimele. Numele de
 * utilizator Windows are insa adesea un spatiu in el - "P.R. Control", "Ion
 * Popescu" -, iar dosarul temporar sta chiar acolo:
 *
 *     output = C:\Users\P.R. Control\AppData\Local\Temp\spvC620.tmp
 *
 * Curl avertiza ("uses unquoted whitespace") si scria raspunsul in "C:\Users\P.R.",
 * adica nicaieri: "curl: (23) Failed writing received data to disk". Toate
 * apelurile la ANAF cadeau, la orice client al carui nume are un spatiu.
 *
 * Intre ghilimele, curl citeste "\" ca inceput de secventa de evitare, asa ca
 * fiecare se dubleaza. La fel face si agentul - vezi agent_pentru_config().
 */
/**
 * Semnele dupa care se stie cu ce s-a lucrat: marginea de TLS ceruta si
 * versiunea programului de aici.
 *
 * Nu se spune ce TLS s-a vorbit de fapt — curl n-are de unde sa-l spuna intr-un
 * „write-out" —, ci ce i s-a cerut. Atat trebuie: din el se vede daca la client
 * ruleaza codul care se tine pe 1.2.
 *
 * Fara ele, doua pene arata la fel oricare le-ar fi pricina, iar dintr-un mesaj
 * lipit intr-un email nu se poate sti nici macar daca indreptarea a ajuns pe
 * calculatorul acela. Cu ele, se vede din prima.
 */
/**
 * Al catelea curl e cel cu care s-a lucrat.
 *
 * Se intreaba o singura data pe cerere, si numai cand ceva a cazut: la fiecare
 * apel ar fi un proces pornit degeaba.
 */
function versiunea_curl(array $rezultat)
{
    static $stiut = null;

    if ($stiut !== null) {
        return $stiut;
    }

    $stiut = '';

    if (empty($GLOBALS['spv_config']['curl'])) {
        return $stiut;
    }

    $iesire = array();
    @exec(escapeshellarg($GLOBALS['spv_config']['curl']) . ' --version 2>&1', $iesire);

    if (isset($iesire[0]) && preg_match('/curl\s+([0-9.]+)/i', $iesire[0], $potrivire)) {
        $stiut = 'curl ' . $potrivire[1];
    }

    return $stiut;
}

/**
 * Sta deschisa pe ecran o fereastra de PIN?
 *
 * Cand legatura cu ANAF cade, pricina cea mai deasa nu e reteaua: e fereastra
 * de PIN a tokenului, deschisa aici si asteptand pe cineva care nu se uita
 * incoace. Din aplicatie asta arata la fel cu un server picat, iar omul cauta
 * vina in retea.
 *
 * Proba nu atinge nimic si nu deschide nimic: numai se uita ce ferestre sunt.
 * De aceea se poate chema dupa fiecare pana, fara grija.
 *
 * @return array{deschisa: bool, titlu: string, proces: string}
 */
function fereastra_de_pin()
{
    $script = __DIR__ . DIRECTORY_SEPARATOR . 'pin-fereastra.ps1';

    if (!is_file($script)) {
        return array('deschisa' => false, 'titlu' => '', 'proces' => '');
    }

    $argumente = array(
        'powershell', '-NoProfile', '-ExecutionPolicy', 'Bypass',
        '-File', escapeshellarg($script),
        '2>&1',
    );

    // Rabdare scurta: e o numaratoare de ferestre, nu o lucrare.
    $rulat = exec_marginit(implode(' ', $argumente), 15);

    $json = json_decode($rulat['iesire'], true);

    if (!is_array($json) || !isset($json['deschisa'])) {
        return array('deschisa' => false, 'titlu' => '', 'proces' => '');
    }

    return array(
        'deschisa' => (bool) $json['deschisa'],
        'titlu' => isset($json['titlu']) ? (string) $json['titlu'] : '',
        'proces' => isset($json['proces']) ? (string) $json['proces'] : '',
    );
}

function semnele_legaturii(array $rezultat)
{
    $bucati = array();

    if (!empty($rezultat['tls'])) {
        $bucati[] = $rezultat['tls'];
    }

    /*
     * Si al catelea curl.
     *
     * A costat o zi de cautat: la un client cu 8.13 legatura cu ANAF cadea, la
     * altul cu 8.21 mergea, iar erorile aratau la fel. Scris aici, se vede din
     * primul rand al mesajului. Se afla cerandu-i-o chiar lui, o data pe pana —
     * curl n-o poate spune intr-un „write-out".
     */
    $alCatelea = versiunea_curl($rezultat);

    if ($alCatelea !== '') {
        $bucati[] = $alCatelea;
    }

    $versiune = __DIR__ . DIRECTORY_SEPARATOR . 'versiune.txt';

    if (is_file($versiune)) {
        $bucati[] = 'program ' . trim((string) @file_get_contents($versiune));
    }

    return $bucati === array() ? '' : ' [' . implode(' | ', $bucati) . ']';
}

function curl_valoare($valoare)
{
    $escapate = str_replace(chr(92), chr(92) . chr(92), $valoare);
    $escapate = str_replace('"', chr(92) . '"', $escapate);

    return '"' . $escapate . '"';
}

function executa_curl(array $config, $url, array $optiuni = array())
{
    $fisier_corp = tempnam(sys_get_temp_dir(), 'spvb');
    $fisier_antete = tempnam(sys_get_temp_dir(), 'spvh');
    $fisier_config = tempnam(sys_get_temp_dir(), 'spvc');

    /*
     * Legatura cu ANAF se tine pe TLS 1.2, nu pe 1.3.
     *
     * Cu certificat de pe token, Schannel-ul Windows si TLS 1.3 se inteleg
     * prost: cheile se schimba in mijlocul raspunsului, iar sesiunea se stinge
     * inainte de capatul lui — SEC_E_CONTEXT_EXPIRED, adica exact eroarea de
     * care ne lovim. Pe 1.2 nu se intampla, iar ANAF il vorbeste de ani de zile.
     *
     * Nu inseamna ca celalalt drum — traficul desfacut de antivirus — nu mai
     * exista; inseamna doar ca se scoate din discutie unul dintre doua, fara sa
     * fie nevoie de nimeni la calculatorul clientului.
     *
     * Se poate schimba din configurare, daca ANAF trece candva numai pe 1.3.
     */
    $tls = isset($config['tls_max']) && $config['tls_max'] === ''
        ? array()
        : array('tlsv1.2', 'tls-max = ' . (isset($config['tls_max']) && $config['tls_max'] !== ''
            ? $config['tls_max']
            : '1.2'));

    /*
     * Fara refolosirea sesiunii TLS.
     *
     * Windows tine minte sesiunile incheiate si le reia, ca sa nu se mai faca
     * strangerea de mana de la capat. Cand cea tinuta minte a expirat intre
     * timp, reluarea se rupe chiar in mijlocul raspunsului — si asta e, cuvant
     * cu cuvant, ce spune SEC_E_CONTEXT_EXPIRED: „contextul a expirat si nu mai
     * poate fi folosit".
     *
     * Se pierde o strangere de mana la fiecare apel; se castiga un apel care nu
     * mai cade. Cu certificat de pe token, tot tokenul face si munca grea, deci
     * diferenta nu se simte.
     */
    if (empty($config['refoloseste_sesiunea'])) {
        $tls[] = 'no-sessionid';
    }

    /*
     * Fara verificarea listelor de revocare.
     *
     * Schannel intreaba emitentul, la fiecare strangere de mana, daca vreun
     * certificat din lant a fost revocat. Intrebarea pleaca la certSIGN, si daca
     * raspunsul intarzie — firewall, retea inceata, serverul lor obosit —
     * strangerea de mana se lungeste, iar sesiunea securizata apuca sa expire
     * inainte de capatul raspunsului: SEC_E_CONTEXT_EXPIRED.
     *
     * Nu se pierde nimic din ce ne apara: certificatul ANAF se verifica mai
     * departe ca lant si ca nume, iar cel de pe token e chiar al nostru. Se
     * pierde doar intrebarea „a fost revocat intre timp?", pusa la fiecare apel.
     */
    if (empty($config['verifica_revocarea'])) {
        $tls[] = 'ssl-no-revoke';
    }

    $linii = array_merge(array(
        'url = ' . curl_valoare($url),
        'cert = ' . curl_valoare('CurrentUser\\MY\\' . $config['thumbprint']),
        'max-time = ' . (int) $config['timeout'],
        'output = ' . curl_valoare($fisier_corp),
        'dump-header = ' . curl_valoare($fisier_antete),
        'silent',
        'show-error',
    ), $tls, $optiuni);

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
        // Ce margine de TLS s-a cerut; dupa ea se stie ce cod ruleaza la client.
        'tls' => implode(', ', array_map(function ($optiune) {
            $talcuri = array('no-sessionid' => 'sesiune noua', 'ssl-no-revoke' => 'fara revocare');

            return isset($talcuri[$optiune]) ? $talcuri[$optiune] : $optiune;
        }, $tls)) ?: 'TLS fara margine',
    );
}

/**
 * Cere ceva de la SPV, cu încercări noi pe sesiune curată.
 *
 * Lanțul F5 al ANAF resetează uneori conexiunea, iar sesiunea securizată se mai
 * stinge și în timp ce răspunsul curge. Atunci se aruncă prăjitura de sesiune și
 * se întreabă din nou, cu răgaz între încercări.
 *
 * Un răspuns cu antet, dar cu legătura ruptă la mijloc, nu e răspuns: fișierul
 * ar fi trunchiat, iar cine îl primește n-ar avea de unde să știe. De aceea
 * penele trecătoare se socotesc eșec chiar dacă a apucat să vină un status.
 *
 * Întoarce rezultatul curl, cu corpul scris în fișierul temporar — cine îl
 * primește răspunde de ștergerea lui.
 */
/**
 * Ruleaza o comanda cu rabdare marginita, si o opreste daca trece de ea.
 *
 * „exec" asteapta cat vrea comanda. Pentru cele care pot deschide o fereastra pe
 * ecranul clientului asta nu e o purtare buna: o fereastra de PIN pe care n-o
 * inchide nimeni tine PowerShell-ul pe loc la nesfarsit, iar programul nostru
 * serveste o cerere pe rand — deci se opreste tot, si agentul, si descarcarile,
 * si dosarul urmarit. Un calculator lasat cu fereastra deschisa peste noapte
 * oprea toata legatura cu clientul acela.
 *
 * @return array{iesire: string, cod: int, oprit: bool}
 */
/**
 * Ruleaza o comanda dandu-i ceva pe intrarea standard.
 *
 * Se foloseste pentru PIN: pus in linia de comanda, el s-ar vedea in lista de
 * procese a calculatorului — orice program care se uita acolo l-ar citi. Asa,
 * trece printr-o teava care nu lasa urma nicaieri.
 */
function exec_cu_intrare($comanda, $secunde, $intrare)
{
    $descriptori = array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
    $tevi = array();

    $proces = @proc_open($comanda, $descriptori, $tevi);

    if (!is_resource($proces)) {
        return array('iesire' => '', 'cod' => -1, 'oprit' => false);
    }

    @fwrite($tevi[0], $intrare . PHP_EOL);
    @fclose($tevi[0]);
    unset($tevi[0]);

    foreach ($tevi as $teava) {
        stream_set_blocking($teava, false);
    }

    $iesire = '';
    $pana = microtime(true) + $secunde;
    $oprit = false;

    while (true) {
        $stare = proc_get_status($proces);

        foreach ($tevi as $teava) {
            $bucata = stream_get_contents($teava);

            if ($bucata !== false) {
                $iesire .= $bucata;
            }
        }

        if (!$stare['running']) {
            break;
        }

        if (microtime(true) >= $pana) {
            proc_terminate($proces);
            $oprit = true;
            break;
        }

        usleep(100000);
    }

    foreach ($tevi as $teava) {
        @fclose($teava);
    }

    $cod = proc_close($proces);

    return array('iesire' => $iesire, 'cod' => $oprit ? -1 : $cod, 'oprit' => $oprit);
}

function exec_marginit($comanda, $secunde)
{
    $descriptori = array(1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
    $tevi = array();

    $proces = @proc_open($comanda, $descriptori, $tevi);

    if (!is_resource($proces)) {
        return array('iesire' => '', 'cod' => -1, 'oprit' => false);
    }

    foreach ($tevi as $teava) {
        stream_set_blocking($teava, false);
    }

    $iesire = '';
    $pana = microtime(true) + $secunde;
    $oprit = false;

    while (true) {
        $stare = proc_get_status($proces);

        foreach ($tevi as $teava) {
            $bucata = stream_get_contents($teava);

            if ($bucata !== false) {
                $iesire .= $bucata;
            }
        }

        if (!$stare['running']) {
            break;
        }

        if (microtime(true) >= $pana) {
            proc_terminate($proces);
            $oprit = true;
            break;
        }

        usleep(100000);
    }

    foreach ($tevi as $teava) {
        @fclose($teava);
    }

    $cod = proc_close($proces);

    return array('iesire' => $iesire, 'cod' => $oprit ? -1 : $cod, 'oprit' => $oprit);
}

/**
 * Se poate folosi cheia de pe token chiar acum?
 *
 * Se cheama cand o legatura cu ANAF s-a rupt: acolo, cea mai deasa banuiala e
 * tokenul, dar pana acum ramanea banuiala. Un raspuns de la el o face fapt —
 * si, daca driverul astepta PIN-ul, proba deschide fereastra si urmatoarea
 * incercare are cu ce sa lucreze.
 *
 * Se deosebesc doua feluri de „bun", fiindca se indreapta altfel:
 *
 *   'bun'          — cheia a semnat pe loc, fara sa ceara nimic. Driverul tine
 *                    PIN-ul minte, deci nu el a rupt legatura cu ANAF.
 *   'bun_dupa_pin' — cheia a semnat, dar abia dupa ce s-a deschis fereastra.
 *                    Driverul cere PIN-ul la fiecare folosire, deci l-a cerut si
 *                    in mijlocul strangerii de mana cu ANAF — iar acolo nimeni
 *                    nu asteapta dupa om.
 *
 * @return string 'bun' | 'bun_dupa_pin' | 'blocat' | 'necunoscut'
 */
function starea_cheii($config)
{
    if ($config['thumbprint'] === '') {
        return 'necunoscut';
    }

    $argumente = array(
        'powershell', '-NoProfile', '-ExecutionPolicy', 'Bypass',
        '-File', escapeshellarg(__DIR__ . '\pin-test.ps1'),
        '-Thumbprint', escapeshellarg($config['thumbprint']),
        '2>&1',
    );

    /*
     * Rabdare marginita: cat sa apuce omul sa scrie PIN-ul, nu cat sa ramana
     * fereastra deschisa peste noapte si sa tina programul pe loc.
     */
    $rulat = exec_marginit(implode(' ', $argumente), 90);

    if ($rulat['oprit']) {
        scrie_eroarea('Fereastra de PIN a rămas deschisă peste 90 de secunde; am oprit proba.');

        return 'blocat';
    }

    $date = json_decode($rulat['iesire'], true);

    if (!is_array($date) || !isset($date['gata'])) {
        return 'necunoscut';
    }

    if (empty($date['gata'])) {
        return 'blocat';
    }

    return empty($date['cerut']) ? 'bun' : 'bun_dupa_pin';
}

/**
 * Deschide cheia inainte de apelul la ANAF, nu dupa ce el cade.
 *
 * Strangerea de mana cu ANAF are nevoie de cheia de pe token. Daca driverul
 * cere PIN-ul chiar atunci, fereastra se deschide in mijlocul ei, iar sesiunea
 * securizata se stinge asteptand omul: SEC_E_CONTEXT_EXPIRED. Nu se poate cere
 * ANAF-ului sa aiba mai multa rabdare, dar se poate scoate omul din mijlocul
 * ferestrei aceleia — cerand cheia inainte, cand nimeni nu numara secundele.
 *
 * Nu se face la fiecare apel: ar fi o semnare in plus de fiecare data. Ce s-a
 * aflat se tine langa program cateva minute, si atat.
 *
 * @return string ce s-a aflat despre cheie ('' cand nu s-a intrebat acum)
 */
function asigura_cheia($config, $rastimp = 600)
{
    if ($config['thumbprint'] === '') {
        return '';
    }

    /*
     * Ce s-a aflat se tine minte pentru fiecare token in parte.
     *
     * Pe un calculator cu doua tokene, o singura insemnare le amesteca: deschisa
     * cheia celui dintai, al doilea parea si el deschis, iar apelul lui la ANAF
     * pleca fara ca PIN-ul sa fi fost cerut — asa ca fereastra se deschidea
     * tocmai in mijlocul strangerii de mana, unde nimeni nu asteapta dupa om.
     * Din afara semana cu „merge numai certificatul implicit".
     */
    $fisier = __DIR__ . DIRECTORY_SEPARATOR . 'pin-stare-'
        . substr($config['thumbprint'], 0, 16) . '.json';
    $stiut = is_file($fisier) ? json_decode((string) @file_get_contents($fisier), true) : null;

    /*
     * Ce s-a aflat tine cateva minute, oricare ar fi fost raspunsul.
     *
     * Nu doar cand cheia era deschisa: la un driver care cere PIN-ul la fiecare
     * folosire, o proba noua inaintea fiecarui apel ar deschide o fereastra in
     * plus fata de cea pe care o deschide oricum curl — adica exact necazul, de
     * doua ori. Acolo indreptarea nu e la noi, ci in setarile driverului, si se
     * spune raspicat in mesajul penei.
     */
    if (is_array($stiut) && isset($stiut['la'], $stiut['stare'])
        && (time() - (int) $stiut['la']) < $rastimp) {
        return $stiut['stare'] === 'bun' ? '' : (string) $stiut['stare'];
    }

    $stare = starea_cheii($config);

    @file_put_contents($fisier, json_encode(array('la' => time(), 'stare' => $stare)));

    return $stare;
}

function spv_cere(array $config, $tinta, $incercari = 3)
{
    cere_amprenta($config);

    $rezultat = array(
        'status' => 0,
        'content_type' => '',
        'fisier_corp' => null,
        'iesire' => '',
        'cod_iesire' => -1,
    );

    $racaz = array(1 => 3, 2 => 8);

    /*
     * Cheia se deschide inainte, nu dupa ce apelul cade: asa fereastra de PIN,
     * daca e nevoie de ea, se deschide cand nimeni nu numara secundele.
     */
    $cheia = asigura_cheia($config);

    if ($cheia === '') {
        $cheia = 'necunoscut';
    }

    for ($incercare = 1; $incercare <= $incercari; $incercare++) {
        $rezultat = executa_curl($config, $tinta, array(
            'location',
            'cookie-jar = ' . curl_valoare($config['cookie_jar']),
            'cookie = ' . curl_valoare($config['cookie_jar']),
        ));

        if ($rezultat['status'] >= 100 && !pana_trecatoare($rezultat['cod_iesire'])) {
            $rezultat['cheia'] = $cheia;

            return $rezultat;
        }

        @unlink($rezultat['fisier_corp']);
        $rezultat['fisier_corp'] = null;

        /*
         * Fiecare legatura cu ANAF cere cheia de pe token. Cand strangerea de
         * mana cade (35) sau sesiunea securizata se stinge la mijloc (56), cea
         * mai deasa pricina e tokenul care isi asteapta PIN-ul intr-o fereastra
         * pe care n-o vede nimeni — dar pana acum ramanea o banuiala scrisa in
         * mesaj, si omul o cauta cu zilele.
         *
         * Se intreaba deci chiar el, o singura data. Daca driverul astepta
         * PIN-ul, proba deschide fereastra, iar incercarea urmatoare are cu ce
         * sa lucreze; daca cheia semneaza fara sa clipeasca, banuiala pica si
         * mesajul arata incolo — spre desfacerea traficului de antivirus.
         */
        if ($cheia === 'necunoscut' && in_array((int) $rezultat['cod_iesire'], array(35, 56), true)) {
            $cheia = starea_cheii($config);
            scrie_eroarea('Legătura cu ANAF s-a rupt (curl ' . (int) $rezultat['cod_iesire']
                . '); cheia de pe token: ' . $cheia . '.');
        }

        if (isset($racaz[$incercare])) {
            @unlink($config['cookie_jar']);
            sleep($racaz[$incercare]);
        }
    }

    $rezultat['cheia'] = $cheia;

    /*
     * Dupa toate incercarile: daca si ultima s-a rupt la mijloc, raspunsul nu e
     * bun de dat mai departe, chiar daca a apucat sa vina un antet. Se sterge
     * statusul, ca sa nu treaca drept izbanda pe la cine il primeste.
     */
    if (pana_trecatoare($rezultat['cod_iesire'])) {
        $rezultat['status'] = 0;
    }

    return $rezultat;
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
    /*
     * Curl-ul cu care se vorbeste cu ANAF.
     *
     * Are intaietate cel pus langa program, ca si PHP-ul: cel din Windows e
     * vechi de cati ani are calculatorul, si tocmai in el s-au indreptat, de
     * la o versiune la alta, felurite necazuri ale lui Schannel cu
     * certificatele de pe token. La un client cu 8.13 legatura cadea, la
     * altul cu 8.21 mergea - iar noi nu putem cere nimanui sa-si innoiasca
     * Windows-ul.
     */
    'curl'       => is_file(__DIR__ . DIRECTORY_SEPARATOR . 'curl.exe')
        ? __DIR__ . DIRECTORY_SEPARATOR . 'curl.exe'
        : (isset($env['SPV_CURL']) && trim($env['SPV_CURL']) !== ''
            ? trim($env['SPV_CURL'])
            : getenv('SystemRoot') . DIRECTORY_SEPARATOR . 'System32' . DIRECTORY_SEPARATOR . 'curl.exe'),
    // Program de tiparit PDF-uri; gasit singur daca sta langa acest fisier
    'imprimare_exe' => gaseste_program_tiparire($env, __DIR__),
    // Unde se strang documentele fiscale ale clientului (vezi ruta /arhiva)
    'arhiva'     => rtrim(isset($env['ARHIVA_CALE']) && $env['ARHIVA_CALE'] !== ''
        ? $env['ARHIVA_CALE']
        : __DIR__ . '\\arhiva', '\\/'),
    // Cat de nou poate fi TLS-ul catre ANAF; gol = fara margine (vezi executa_curl)
    'tls_max'    => isset($env['SPV_TLS_MAX']) ? trim($env['SPV_TLS_MAX']) : '1.2',
    // Refolosirea sesiunii TLS; oprita, fiindca reluarea unei sesiuni expirate
    // rupe raspunsul la mijloc (SEC_E_CONTEXT_EXPIRED). Vezi executa_curl().
    'refoloseste_sesiunea' => isset($env['SPV_REFOLOSESTE_SESIUNEA'])
        && trim($env['SPV_REFOLOSESTE_SESIUNEA']) === '1',
    // Verificarea revocarii; oprita, fiindca intrebarea catre emitent poate
    // intarzia strangerea de mana pana la expirarea sesiunii. Vezi executa_curl().
    'verifica_revocarea' => isset($env['SPV_VERIFICA_REVOCAREA'])
        && trim($env['SPV_VERIFICA_REVOCAREA']) === '1',
    'cookie_jar' => __DIR__ . '/cookies.txt',
    'decl_jar'   => __DIR__ . '/decl_cookies.txt',
);

/*
 * Configurarea se lasa la indemana si mesajelor de eroare: ele spun al catelea
 * curl s-a folosit, iar pentru asta trebuie sa stie unde e.
 */
$GLOBALS['spv_config'] = $config;

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

    /*
     * „cerere" inscrie o solicitare la ANAF, deci nu se reincearca: la un
     * timeout, ANAF poate sa fi primit cererea desi raspunsul nu a mai ajuns,
     * iar reincercarea o inregistra inca o data — clientii primeau cate 2-3
     * fise rol la o singura apasare. Citirile (lista, descarcare, stare) raman
     * pe cele 3 incercari: repetate, ele nu strica nimic.
     */
    $incercari = strpos($potrivire[1], 'cerere') === 0 ? 1 : 3;

    $rezultat = spv_cere($config, $tinta, $incercari);

    if ($rezultat['status'] >= 100) {
        trimite_fisier($rezultat['status'], $rezultat['content_type'], $rezultat['fisier_corp']);
    }

    /*
     * Inainte de a da vina pe retea, se vede daca nu cumva tokenul isi asteapta
     * PIN-ul: fereastra lui, deschisa aici, opreste apelul la fel de bine ca un
     * server picat, dar se dezleaga cu totul altfel.
     */
    $fereastra = fereastra_de_pin();

    raspunde_json(502, array(
        'eroare'  => 'Apelul către ANAF a eșuat: ' . talcul_curl($rezultat['cod_iesire'], isset($rezultat['cheia']) ? $rezultat['cheia'] : 'necunoscut')
            . ' [curl ' . $rezultat['cod_iesire'] . ']' . semnele_legaturii($rezultat),
        'detalii' => $rezultat['iesire'],
        'pin_asteapta' => $fereastra['deschisa'],
        'pin_fereastra' => $fereastra['titlu'],
        'pin_proces' => $fereastra['proces'],
    ));
}

/**
 * Aduce un document din SPV și îl scrie de-a dreptul în arhiva de aici.
 *
 * Documentul nu mai pleacă nicăieri: aplicația primește doar calea sub care a
 * fost pus și, la cerere, textul din el — atât cât să știe ce scrie ANAF în
 * recipisă sau în vectorul fiscal.
 *
 * „nume" vine fără extensie: abia răspunsul ANAF spune dacă e pdf sau zip.
 *
 * Nu răspunde el însuși: întoarce ce a ieșit, ca să poată fi folosit și pentru
 * un document singur, și pentru o transă întreagă.
 *
 * @return array cu „stare" și, după caz, „cale" sau „eroare"
 */
function spv_arhiveaza_unul($config, $radacina, $cerinta)
{
    $id = isset($cerinta['id']) ? trim($cerinta['id']) : '';
    $firma = arhiva_bucata(isset($cerinta['firma']) ? $cerinta['firma'] : '');
    $nume = arhiva_bucata(isset($cerinta['nume']) ? $cerinta['nume'] : '');

    if (!preg_match('/^[A-Za-z0-9_\-]{1,60}$/', $id)) {
        return array('stare' => 400, 'eroare' => 'Lipsește numărul mesajului de descărcat.');
    }

    if ($firma === '' || $nume === '') {
        return array('stare' => 400, 'eroare' => 'Lipsește firma sau numele documentului.');
    }

    $rezultat = spv_cere($config, rtrim($config['base_url'], '/') . '/descarcare?id=' . rawurlencode($id));

    if ($rezultat['status'] < 100) {
        // Inainte de a da vina pe retea: nu cumva tokenul isi asteapta PIN-ul?
        $fereastra = fereastra_de_pin();

        return array(
            'stare' => 502,
            'eroare'  => 'Apelul către ANAF a eșuat: ' . talcul_curl($rezultat['cod_iesire'], isset($rezultat['cheia']) ? $rezultat['cheia'] : 'necunoscut')
                . ' [curl ' . $rezultat['cod_iesire'] . ']' . semnele_legaturii($rezultat),
            'detalii' => $rezultat['iesire'],
            'pin_asteapta' => $fereastra['deschisa'],
            'pin_fereastra' => $fereastra['titlu'],
            'pin_proces' => $fereastra['proces'],
        );
    }

    /*
     * ANAF răspunde cu JSON când nu dă documentul (mesaj inexistent, drepturi
     * lipsă). Se spune mai departe motivul lui, nu un cod de stare.
     */
    $inceput = ltrim((string) @file_get_contents($rezultat['fisier_corp'], false, null, 0, 200));

    if (strpos(strtolower($rezultat['content_type']), 'json') !== false
        || (isset($inceput[0]) && $inceput[0] === '{')) {
        $primit = json_decode((string) @file_get_contents($rezultat['fisier_corp']), true);
        @unlink($rezultat['fisier_corp']);

        return array(
            'stare' => 502,
            'eroare' => isset($primit['eroare']) ? $primit['eroare'] : 'Descărcarea documentului a eșuat.',
            'detalii' => mb_substr($inceput, 0, 300),
        );
    }

    if ($rezultat['status'] !== 200) {
        @unlink($rezultat['fisier_corp']);

        return array(
            'stare' => 502,
            'eroare' => 'ANAF a răspuns cu ' . $rezultat['status'] . ' la descărcarea documentului.',
            'detalii' => mb_substr($inceput, 0, 300),
        );
    }

    $extensie = strpos(strtolower($rezultat['content_type']), 'zip') !== false ? 'zip' : 'pdf';

    $dosarul = arhiva_cale_ceruta($radacina, $firma . '/' . (isset($cerinta['dosar']) ? $cerinta['dosar'] : ''));

    if (!is_dir($dosarul) && !@mkdir($dosarul, 0777, true)) {
        @unlink($rezultat['fisier_corp']);

        return array('stare' => 500, 'eroare' => 'Dosarul nu poate fi creat.', 'detalii' => $dosarul);
    }

    $inlocuieste = isset($cerinta['inlocuieste']) && $cerinta['inlocuieste'] !== ''
        ? arhiva_cale_ceruta($radacina, $cerinta['inlocuieste'])
        : '';

    $destinatie = arhiva_destinatie($dosarul, $nume . '.' . $extensie, $inlocuieste);

    /*
     * Fișierul vine din dosarul temporar al Windows-ului, care poate fi pe alt
     * disc decât arhiva; acolo mutarea dintr-o bucată nu merge, deci se copiază.
     */
    if (!@rename($rezultat['fisier_corp'], $destinatie)) {
        if (!@copy($rezultat['fisier_corp'], $destinatie)) {
            @unlink($rezultat['fisier_corp']);

            return array('stare' => 500, 'eroare' => 'Documentul nu a putut fi scris.', 'detalii' => $destinatie);
        }

        @unlink($rezultat['fisier_corp']);
    }

    $iesit = array(
        'stare' => 200,
        'cale' => arhiva_relativa($radacina, $destinatie),
        'cale_completa' => $destinatie,
        'extensie' => $extensie,
        'marime' => filesize($destinatie),
        'hash' => sha1_file($destinatie),
    );

    if (!empty($cerinta['text']) && $extensie === 'pdf') {
        $text = pdf_text(__DIR__, $destinatie);

        if ($text !== null) {
            $iesit['text'] = $text;
        }
    }

    return $iesit;
}

/*
 * POST /spv-arhiva-lot — aceleași documente, dar cerute toate deodată.
 *
 * Corpul e JSON: {"documente":[{...}], "pauza_ms":1200}. Răspunsul curge, câte
 * un obiect JSON pe rând, pe măsură ce fiecare document e scris în arhivă — așa
 * aplicația vede unde s-a ajuns fără să mai bată drumul până aici pentru
 * fiecare document în parte.
 *
 * Pauza cerută de ANAF se ține aici, unde e și apelul. Se socotește de la
 * plecarea apelului dinainte, nu de la întoarcerea lui: altfel s-ar aștepta de
 * două ori — o dată răspunsul, și o dată pauza pusă peste el.
 */
if ($metoda === 'POST' && $calea === '/spv-arhiva-lot') {
    $primita = json_decode(file_get_contents('php://input'), true);
    $documente = isset($primita['documente']) && is_array($primita['documente']) ? $primita['documente'] : array();

    if ($documente === array()) {
        raspunde_json(400, array('eroare' => 'Lotul nu cuprinde niciun document.'));
    }

    $pauza = isset($primita['pauza_ms']) ? (int) $primita['pauza_ms'] : 1200;
    $radacina = arhiva_radacina_pregatita($config);

    header('Content-Type: application/x-ndjson; charset=utf-8');
    header('Cache-Control: no-cache, no-store');
    header('X-Accel-Buffering: no');

    while (ob_get_level() > 0) {
        ob_end_flush();
    }

    $ultimul = 0.0;

    foreach ($documente as $cerinta) {
        if ($pauza > 0 && $ultimul > 0) {
            $trecut = (int) ((microtime(true) - $ultimul) * 1000000);

            if ($trecut < $pauza * 1000) {
                usleep($pauza * 1000 - $trecut);
            }
        }

        $ultimul = microtime(true);

        $iesit = spv_arhiveaza_unul($config, $radacina, $cerinta);
        $iesit['id'] = isset($cerinta['id']) ? $cerinta['id'] : '';

        echo json_encode($iesit, JSON_UNESCAPED_UNICODE) . PHP_EOL;
        flush();
    }

    exit;
}

/*
 * GET /spv-arhiva?id=...&firma=...&dosar=...&nume=... — un singur document.
 *
 * Rămâne pentru aplicațiile care nu cer încă transe, și pentru documentul cerut
 * singur: o recipisă abia sosită n-are cu cine să facă transă.
 */
if ($metoda === 'GET' && $calea === '/spv-arhiva') {
    $iesit = spv_arhiveaza_unul($config, arhiva_radacina_pregatita($config), $_GET);

    $stare = $iesit['stare'];
    unset($iesit['stare']);

    raspunde_json($stare, $iesit);
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
 * GET /pin — se poate folosi acum cheia de pe token, si a cerut PIN-ul?
 *
 * Citirea certificatului nu atinge cheia privata, deci nu cere niciodata PIN.
 * PIN-ul se cere abia cand cheia e chiar folosita: la semnare, sau la intrarea
 * in SPV cu certificat. Pana acum, primul lucru care avea nevoie de el se
 * impiedica de fereastra deschisa pe calculatorul clientului — adesea in
 * mijlocul unei descarcari de zeci de documente, si adesea pe alt ecran decat
 * al omului care apasase.
 *
 * Aici se cere o semnatura mica, dinadins: daca driverul are PIN-ul in minte,
 * ea se face pe loc si nu se vede nimic; daca nu-l are, se deschide fereastra
 * si omul il scrie atunci, cand nu asteapta nimic dupa el. Proba e deci si
 * declansatorul — nu se poate afla fara sa se forteze.
 */
/*
 * GET /pin/fereastra — sta deschisa acum o fereastra de PIN?
 *
 * Se cheama dupa o pana, si apoi din cand in cand: aplicatia asteapta sa se
 * inchida fereastra — adica omul sa fi scris PIN-ul — si reia singura apelul
 * care a cazut, fara sa mai fie nevoie de o apasare.
 *
 * PIN-ul nu trece pe aici. El se scrie in fereastra lui, de omul care tine
 * tokenul; aici se afla doar daca fereastra mai e pe ecran.
 */
/*
 * POST /pin/scrie — scrie codul in fereastra care il asteapta si apasa OK.
 *
 * Codul vine in corpul cererii, nu in adresa: adresele ajung in jurnalele
 * serverelor de web, iar acolo n-are ce cauta un PIN. De aici mai departe el
 * merge pe intrarea standard a scriptului — nici in linia de comanda, unde
 * l-ar vedea orice program care se uita in lista de procese.
 *
 * Nu se scrie nicaieri pe disc, nu intra in niciun jurnal si nu se intoarce
 * inapoi — nici macar in mesajul de eroare. Se foloseste o data si se uita.
 *
 * Merge numai cand fereastra e deja deschisa: aici nu se forteaza nimic, ci se
 * raspunde la o cerere pe care a facut-o tokenul singur.
 */
if ($metoda === 'POST' && $calea === '/pin/scrie') {
    cere_amprenta($config);

    $primita = json_decode(file_get_contents('php://input'), true);
    $pin = isset($primita['pin']) ? (string) $primita['pin'] : '';

    if ($pin === '') {
        raspunde_json(400, array('scris' => false, 'motiv' => 'Nu a venit niciun cod.'));
    }

    $script = __DIR__ . DIRECTORY_SEPARATOR . 'pin-scrie.ps1';

    if (!is_file($script)) {
        raspunde_json(501, array('scris' => false, 'motiv' => 'Programul local nu cunoaște scrierea PIN-ului.'));
    }

    $argumente = array(
        'powershell', '-NoProfile', '-ExecutionPolicy', 'Bypass',
        '-File', escapeshellarg($script),
    );

    $rulat = exec_cu_intrare(implode(' ', $argumente), 45, $pin);

    // Codul nu mai are ce cauta in memorie de aici incolo.
    $pin = null;
    unset($primita);

    $json = json_decode($rulat['iesire'], true);

    if (!is_array($json) || !isset($json['scris'])) {
        raspunde_json(500, array(
            'scris' => false,
            'motiv' => 'Scrierea PIN-ului nu a putut fi făcută.',
        ));
    }

    raspunde_json(200, array(
        'scris' => (bool) $json['scris'],
        'motiv' => isset($json['motiv']) ? (string) $json['motiv'] : '',
    ));
}

if ($metoda === 'GET' && $calea === '/pin/fereastra') {
    cere_amprenta($config);

    raspunde_json(200, fereastra_de_pin());
}

if ($metoda === 'GET' && $calea === '/pin') {
    cere_amprenta($config);

    $argumente = array(
        'powershell', '-NoProfile', '-ExecutionPolicy', 'Bypass',
        '-File', escapeshellarg(__DIR__ . '\pin-test.ps1'),
        '-Thumbprint', escapeshellarg($config['thumbprint']),
        '2>&1',
    );

    $rulat = exec_marginit(implode(' ', $argumente), 90);

    if ($rulat['oprit']) {
        raspunde_json(200, array(
            'gata' => false,
            'cerut' => true,
            'motiv' => 'fereastra de PIN a rămas deschisă peste 90 de secunde, fără să scrie nimeni codul',
            'secunde' => 90,
        ));
    }

    $json = json_decode($rulat['iesire'], true);

    if (!is_array($json)) {
        raspunde_json(500, array(
            'eroare' => 'Proba PIN-ului nu a putut fi facuta.',
            'detalii' => mb_substr($rulat['iesire'], 0, 400),
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
    cere_amprenta($config);

    @unlink($config['decl_jar']);

    $sesiune = array(
        'location',
        'cookie-jar = ' . curl_valoare($config['decl_jar']),
        'cookie = ' . curl_valoare($config['decl_jar']),
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
        'cookie-jar = ' . curl_valoare($config['decl_jar']),
        'cookie = ' . curl_valoare($config['decl_jar']),
        'form = ' . curl_valoare('linkdoc=@' . $fisier_pdf . ';type=application/pdf;filename=' . $nume),
    ));

    @unlink($fisier_pdf);

    if ($rezultat['status'] >= 100) {
        trimite_fisier($rezultat['status'], $rezultat['content_type'], $rezultat['fisier_corp']);
    }

    @unlink($rezultat['fisier_corp']);
    raspunde_json(502, array(
        'eroare'  => 'Trimiterea către ANAF a eșuat: ' . talcul_curl($rezultat['cod_iesire'], isset($rezultat['cheia']) ? $rezultat['cheia'] : 'necunoscut')
            . ' [curl ' . $rezultat['cod_iesire'] . ']' . semnele_legaturii($rezultat),
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

        $optiuni[] = 'data-binary = ' . curl_valoare('@' . $fisier_trimis);
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
        'eroare'  => 'Apelul către e-Transport a eșuat: ' . talcul_curl($rezultat['cod_iesire'], isset($rezultat['cheia']) ? $rezultat['cheia'] : 'necunoscut')
            . ' [curl ' . $rezultat['cod_iesire'] . ']' . semnele_legaturii($rezultat),
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
    $config['arhiva'] = arhiva_radacina_pregatita($config);

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

    /*
     * POST /arhiva/copiaza — încă un exemplar al unui document deja arhivat.
     *
     * Recipisa stă și lângă declarația la care răspunde, acolo unde o caută
     * omul. Copia se face aici, între două dosare de pe același calculator, ca
     * documentul să nu mai facă drumul până la aplicație și înapoi.
     */
    if ($metoda === 'POST' && $calea === '/arhiva/copiaza') {
        $sursa = arhiva_cale_ceruta($config['arhiva'], isset($_POST['cale']) ? $_POST['cale'] : '');
        $firma = arhiva_bucata(isset($_POST['firma']) ? $_POST['firma'] : '');
        $nume = arhiva_bucata(isset($_POST['nume']) ? $_POST['nume'] : '');

        if ($sursa === '' || !is_file($sursa)) {
            raspunde_json(404, array('eroare' => 'Documentul de copiat nu se află în arhivă.'));
        }

        if ($firma === '' || $nume === '') {
            raspunde_json(400, array('eroare' => 'Lipsește firma sau numele copiei.'));
        }

        $dosarul = arhiva_cale_ceruta(
            $config['arhiva'],
            $firma . '/' . (isset($_POST['dosar']) ? $_POST['dosar'] : '')
        );

        if (!is_dir($dosarul) && !@mkdir($dosarul, 0777, true)) {
            raspunde_json(500, array('eroare' => 'Dosarul nu poate fi creat.', 'detalii' => $dosarul));
        }

        $destinatie = arhiva_destinatie($dosarul, $nume);

        if (!@copy($sursa, $destinatie)) {
            raspunde_json(500, array('eroare' => 'Copia nu a putut fi scrisă.', 'detalii' => $destinatie));
        }

        raspunde_json(200, array(
            'cale' => arhiva_relativa($config['arhiva'], $destinatie),
            'cale_completa' => $destinatie,
        ));
    }

    /*
     * POST /arhiva/din-local — aduce în arhivă un fișier aflat pe acest
     * calculator, în afara arhivei.
     *
     * Migrarea de la programul vechi de depunere lasă declarațiile și
     * recipisele în dosarele lui (de pildă C:\Program Files\AutomaticIT\Depuse).
     * Aplicația știe căile din tabelul „depuneri" și cere de aici copierea
     * fiecăreia în arhivă, fără ca documentele să facă drumul până la server
     * și înapoi. Originalul rămâne neatins.
     */
    if ($metoda === 'POST' && $calea === '/arhiva/din-local') {
        $sursa = isset($_POST['sursa']) ? trim($_POST['sursa']) : '';
        $firma = arhiva_bucata(isset($_POST['firma']) ? $_POST['firma'] : '');
        $nume = arhiva_bucata(isset($_POST['nume']) ? $_POST['nume'] : '');

        // Doar o cale completă (C:\... sau \\server\...), fără „..": se aduce
        // un fișier anume, nu se umblă prin calculator.
        $completa = preg_match('/^([A-Za-z]:[\\\\\\/]|\\\\\\\\)/', $sursa) && strpos($sursa, '..') === false;

        if (!$completa || !is_file($sursa)) {
            raspunde_json(404, array(
                'eroare' => 'Fișierul de adus nu există pe acest calculator.',
                'detalii' => $sursa,
            ));
        }

        if ($firma === '' || $nume === '') {
            raspunde_json(400, array('eroare' => 'Lipsește firma sau numele documentului.'));
        }

        $dosarul = arhiva_cale_ceruta(
            $config['arhiva'],
            $firma . '\\' . (isset($_POST['dosar']) ? $_POST['dosar'] : '')
        );

        if (!is_dir($dosarul) && !@mkdir($dosarul, 0777, true)) {
            raspunde_json(500, array('eroare' => 'Dosarul nu poate fi creat.', 'detalii' => $dosarul));
        }

        $destinatie = arhiva_destinatie($dosarul, $nume);

        if (!@copy($sursa, $destinatie)) {
            raspunde_json(500, array('eroare' => 'Copia nu a putut fi scrisă.', 'detalii' => $destinatie));
        }

        raspunde_json(200, array(
            'cale' => arhiva_relativa($config['arhiva'], $destinatie),
            'cale_completa' => $destinatie,
        ));
    }

    /*
     * POST /arhiva/uneste-dosarul — muta cuprinsul unui dosar de firmă în altul.
     *
     * Dosarul firmei poartă denumirea ei, cu CUI-ul în paranteză; până când
     * denumirea e știută, el poartă doar codul. Așa o firmă ajunge cu documentele
     * împărțite în două dosare — „15208744" și „ALFA SRL (15208744)" — care
     * arată a dezordine, deși nu s-a pierdut nimic.
     *
     * Aici se strâng la un loc: dacă dosarul nou nu există încă, cel vechi e doar
     * redenumit; altfel fișierele se mută unul câte unul, iar cele care s-ar
     * suprascrie primesc nume liber, ca peste tot în arhivă.
     */
    if ($metoda === 'POST' && $calea === '/arhiva/uneste-dosarul') {
        $vechi = arhiva_bucata(isset($_POST['din']) ? $_POST['din'] : '');
        $nou = arhiva_bucata(isset($_POST['in']) ? $_POST['in'] : '');
        $cui = arhiva_bucata(isset($_POST['cui']) ? $_POST['cui'] : '');

        /*
         * Cu codul fiscal dat, se strang toate dosarele firmei, oricum s-ar
         * chema.
         *
         * Dosarele poarta numele „DENUMIRE (CUI)", iar denumirea se afla pe
         * parcurs: din vectorul fiscal, din datele de identificare, sau scrisa
         * de om. Pana atunci, documentele au apucat sa intre in dosare cu numele
         * de-atunci — inclusiv unul citit gresit, „SRL (22489650)". Ramaneau
         * asa, imprastiate, iar omul le cauta prin trei locuri.
         *
         * Codul nu se schimba niciodata, deci dupa el se recunosc.
         */
        if ($cui !== '' && $nou !== '') {
            $deUnit = array();

            foreach ((array) @scandir($config['arhiva']) as $intrare) {
                if ($intrare === '.' || $intrare === '..' || $intrare === $nou) {
                    continue;
                }

                if (!is_dir($config['arhiva'] . DIRECTORY_SEPARATOR . $intrare)) {
                    continue;
                }

                // Dosarul codului singur, sau oricare „<ceva> (CUI)".
                if ($intrare === $cui || substr($intrare, -strlen('(' . $cui . ')')) === '(' . $cui . ')') {
                    $deUnit[] = $intrare;
                }
            }

            $mutateTot = 0;
            $uniteTot = 0;

            foreach ($deUnit as $dosarVechi) {
                $rezultat = arhiva_uneste_doua(
                    $config['arhiva'] . DIRECTORY_SEPARATOR . $dosarVechi,
                    $config['arhiva'] . DIRECTORY_SEPARATOR . $nou
                );

                if ($rezultat !== false) {
                    $mutateTot += $rezultat;
                    $uniteTot++;
                }
            }

            raspunde_json(200, array(
                'mutate' => $mutateTot,
                'unit' => $uniteTot > 0,
                'dosare' => $uniteTot,
            ));
        }

        if ($vechi === '' || $nou === '' || $vechi === $nou) {
            raspunde_json(400, array('eroare' => 'Lipsește dosarul de unit sau cel în care se unește.'));
        }

        $caleVeche = $config['arhiva'] . DIRECTORY_SEPARATOR . $vechi;
        $caleNoua = $config['arhiva'] . DIRECTORY_SEPARATOR . $nou;

        // Nimic de unit: raspuns cuminte, ca aplicatia sa nu trateze asta ca esec.
        if (!is_dir($caleVeche)) {
            raspunde_json(200, array('mutate' => 0, 'unit' => false));
        }

        if (!is_dir($caleNoua)) {
            if (!@rename($caleVeche, $caleNoua)) {
                raspunde_json(500, array('eroare' => 'Dosarul nu a putut fi redenumit.', 'detalii' => $caleNoua));
            }

            raspunde_json(200, array('mutate' => 0, 'unit' => true, 'redenumit' => true));
        }

        $mutate = arhiva_muta_cuprinsul($caleVeche, $caleNoua);

        if ($mutate === false) {
            raspunde_json(500, array('eroare' => 'Cuprinsul dosarului nu a putut fi mutat.'));
        }

        raspunde_json(200, array('mutate' => $mutate, 'unit' => true, 'redenumit' => false));
    }

    raspunde_json(404, array('eroare' => 'Operație necunoscută pe arhivă.'));
}

raspunde_json(404, array('eroare' => 'Operație necunoscută.'));
