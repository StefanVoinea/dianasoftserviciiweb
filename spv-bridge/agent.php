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
$motiv = '';
// De când ține pana de acum, și de când n-a mai fost nimic de lucru
$deCandNuMerge = 0;
$spusCeEDeFacut = false;
$ultimaVorba = time();

/*
 * Din cât în cât se scrie în jurnal că agentul e viu, chiar dacă n-are ce face.
 * Fără rândul acesta, o zi liniștită și un agent oprit arată la fel: jurnalul
 * tace în amândouă cazurile, iar cine îl citește nu poate spune care e care.
 */
$dinCatInCat = 1800;

while (true) {
    $comanda = agent_intreaba($config, $motiv);

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
         *
         * Pricina se scrie o dată cu mesajul: „nu răspunde" singur nu spune
         * nimănui unde să se uite, iar cine citește jurnalul peste o săptămână
         * n-are de unde să afle dacă a fost internetul, firewall-ul sau
         * antivirusul.
         */
        if (!$deCandNuMerge) {
            $deCandNuMerge = time();
        }

        $deCat = time() - $deCandNuMerge;

        agent_scrie($config, 'Serverul nu răspunde: ' . ($motiv ?: 'pricină necunoscută')
            . '. Reîncerc peste ' . $pauzaLaEroare . 's'
            . ($deCat >= 60 ? ' (ține de ' . round($deCat / 60) . ' min).' : '.'));

        /*
         * Când pana ține de-a binelea, se spune o dată și ce e de căutat. Se
         * spune o singură dată pe pană: repetat la fiecare încercare, ar îneca
         * jurnalul tocmai când el trebuie citit.
         */
        if (!$spusCeEDeFacut && $deCat >= 180) {
            agent_scrie($config, 'De verificat, în ordine: legătura la internet; ieșirea pe 443 către '
                . $config['server'] . ' în firewall; iar în antivirus, ca adresa aceasta să fie scoasă'
                . ' de sub scanarea HTTPS — desfăcută de antivirus, legătura cade fără altă explicație.');
            $spusCeEDeFacut = true;
        }

        sleep($pauzaLaEroare);
        $pauzaLaEroare = min($pauzaLaEroare * 2, 60);

        continue;
    }

    if ($deCandNuMerge) {
        agent_scrie($config, 'Legătura s-a ridicat, după ' . max(1, round((time() - $deCandNuMerge) / 60)) . ' min.');
        $deCandNuMerge = 0;
        $spusCeEDeFacut = false;
        $ultimaVorba = time();
    }

    $pauzaLaEroare = 5;

    if ($comanda === null) {
        /*
         * Pânda s-a împlinit fără nicio comandă — starea cea mai obișnuită. Se
         * întreabă din nou, îndată; din jumătate în jumătate de oră se scrie
         * totuși un rând, ca tăcerea să nu semene cu un agent oprit.
         */
        if (time() - $ultimaVorba >= $dinCatInCat) {
            agent_scrie($config, 'Pândesc mai departe; legătura cu serverul e bună, dar n-a fost nimic de lucru'
                . ' în ultimele ' . round((time() - $ultimaVorba) / 60) . ' min.');
            $ultimaVorba = time();
        }

        continue;
    }

    $ultimaVorba = time();

    agent_scrie($config, 'Comanda ' . $comanda['id'] . ': ' . $comanda['metoda'] . ' ' . $comanda['cale']);

    $raspuns = agent_executa($config, $comanda);

    agent_trimite_rezultatul($config, $comanda['id'], $raspuns);
}
