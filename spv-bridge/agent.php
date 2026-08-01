<?php
/*
 * Agentul care aduce comenzile de la server.
 *
 * De ce există: serverul stă în cloud, programul local stă în rețeaua
 * clientului, în spatele unui router. Ca serverul să-l poată chema direct ar
 * trebui deschis un port pe routerul acela — lucru pe care niciun administrator
 * cuminte nu-l face de dragul unei aplicații.
 *
 * Așa că nu sună serverul la client, ci clientul întreabă serverul: „ai ceva
 * pentru mine?". Întrebarea pleacă pe 443, ca orice pagină de internet, deci
 * trece prin orice firewall și prin orice proxy. Serverul ține linia deschisă
 * până apare o comandă, iar agentul o duce la programul local de lângă el și
 * trimite răspunsul înapoi. Nimic nu intră niciodată dinspre internet.
 *
 * Pornire:
 *   php agent.php
 *
 * Se oprește singur, curat, la Ctrl+C sau la oprirea serviciului.
 */

$dosar = __DIR__;

require_once $dosar . '/agent-functii.php';

$config = agent_configurare($dosar);

if ($config['server'] === '' || $config['token'] === '') {
    fwrite(STDERR, "Lipsesc datele din configurare.env: PUNTE_SERVER si SPV_BRIDGE_TOKEN.\n");
    exit(1);
}

agent_scrie($config, 'Pornit. Întreb ' . $config['server'] . ' dacă are ceva de lucru.');

/*
 * Întâi ne prezentăm: certificatele de pe tokenul de aici ajung în aplicație
 * fără ca omul să tasteze nimic. Dacă tokenul nu e conectat acum, nu-i nimic —
 * se încearcă din nou la următoarea pornire.
 */
agent_inroleaza($config);

$pauzaLaEroare = 5;

while (true) {
    $comanda = agent_intreaba($config);

    if ($comanda === -1) {
        /*
         * Serverul nu ne recunoaște codul: certificatele de pe tokenul de aici
         * nu sunt încă legate de acest kit. Se încearcă înrolarea din nou —
         * poate tokenul tocmai a fost conectat — și se așteaptă un minut.
         */
        agent_scrie($config, 'Serverul nu-mi recunoaște codul de acces; încerc din nou înrolarea.');
        agent_inroleaza($config);
        sleep(60);

        continue;
    }

    if ($comanda === false) {
        /*
         * Serverul nu răspunde — internet căzut, întreținere, orice. Se așteaptă
         * din ce în ce mai mult, până la un minut, ca să nu batem în ușă la
         * fiecare secundă, dar să revenim repede când se ridică.
         */
        agent_scrie($config, 'Serverul nu răspunde; reîncerc peste ' . $pauzaLaEroare . 's.');
        sleep($pauzaLaEroare);
        $pauzaLaEroare = min($pauzaLaEroare * 2, 60);

        continue;
    }

    $pauzaLaEroare = 5;

    if ($comanda === null) {
        // Pânda s-a împlinit fără nicio comandă: se întreabă din nou, îndată.
        continue;
    }

    agent_scrie($config, 'Comanda ' . $comanda['id'] . ': ' . $comanda['metoda'] . ' ' . $comanda['cale']);

    $raspuns = agent_executa($config, $comanda);

    agent_trimite_rezultatul($config, $comanda['id'], $raspuns);
}
