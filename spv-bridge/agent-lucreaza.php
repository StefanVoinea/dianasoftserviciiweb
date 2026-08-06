<?php
/*
 * Duce la capat o singura comanda, in procesul ei.
 *
 * Agentul nu mai face el lucrarea: o da mai departe aici si se intoarce indata
 * la panda. Asa o descarcare lunga din SPV nu mai tine pe loc dosarul urmarit,
 * si nici invers — fiecare lucrare are procesul ei si instanta ei a programului
 * local, iar cele doua nu se mai calca.
 *
 * Se cheama cu un singur argument: fisierul JSON scris de agent, care poarta
 * comanda, adresa locala aleasa si semnul de „lucrez acum".
 */

require __DIR__ . DIRECTORY_SEPARATOR . 'agent-functii.php';

$config = agent_configurare(__DIR__);

if (!isset($argv[1]) || !is_file($argv[1])) {
    fwrite(STDERR, "Lipseste fisierul cu lucrarea de dus la capat.\n");
    exit(1);
}

$date = json_decode((string) @file_get_contents($argv[1]), true);
@unlink($argv[1]);

if (!is_array($date) || empty($date['comanda']['id'])) {
    fwrite(STDERR, "Fisierul lucrarii nu se poate citi.\n");
    exit(1);
}

$comanda = $date['comanda'];
$semn = isset($date['semn']) ? $date['semn'] : null;

// Instanta aleasa de agent; daca nu raspunde, se cade pe cea de baza.
$config['local'] = isset($date['local']) ? $date['local'] : $config['local'];
$baza = isset($date['baza']) ? $date['baza'] : $config['local'];

$raspuns = agent_executa($config, $comanda);

/*
 * Instalarile mai vechi au o singura instanta a programului local. Daca cea
 * aleasa nu asculta (curl 7), se incearca o data pe cea de baza: agentul nou
 * lucreaza atunci ca inainte, in loc sa dea eroare.
 */
if (isset($raspuns['cod']) && (int) $raspuns['cod'] === 7 && $config['local'] !== $baza) {
    agent_scrie($config, 'Instanta ' . $config['local'] . ' nu asculta; incerc pe ' . $baza . '.');

    $config['local'] = $baza;
    $raspuns = agent_executa($config, $comanda);
}

agent_trimite_rezultatul($config, $comanda['id'], $raspuns);

if ($semn) {
    @unlink($semn);
}
