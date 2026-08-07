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

/*
 * Si licenta si-o cere tot el. Fara ea, programul de langa noi refuza orice
 * comanda, iar omul vede in aplicatie „Programul nu are licenta valida pe acest
 * calculator" desi totul e pornit si legat.
 */
agent_licentiaza($config);

$pauzaLaEroare = 5;
$motiv = '';
// De când ține pana de acum, și de când n-a mai fost nimic de lucru
$deCandNuMerge = 0;
$spusCeEDeFacut = false;
$ultimaVorba = time();
// Cand s-a incercat ultima innoire: nu se reia la fiecare panda
$ultimaInnoire = 0;
// Cand s-a uitat ultima data daca licenta mai e buna
$ultimaLicenta = time();
// Licenta tine luni de zile; se intreaba de ea o data la sase ceasuri
$rastimpLicenta = 21600;

/*
 * Din cât în cât se scrie în jurnal că agentul e viu, chiar dacă n-are ce face.
 * Fără rândul acesta, o zi liniștită și un agent oprit arată la fel: jurnalul
 * tace în amândouă cazurile, iar cine îl citește nu poate spune care e care.
 */
$dinCatInCat = 1800;

agent_scrie($config, 'Lucrez pe ' . count($config['locale']) . ' instanță(e) a programului local: '
    . implode(', ', $config['locale']) . '.');

while (true) {
    /*
     * Nu se ia comanda decât când e cine s-o ducă la capăt.
     *
     * Fiecare instanță a programului local servește o singură cerere pe rând,
     * așa că numărul lor e chiar numărul lucrărilor care pot merge deodată.
     * Luată fără loc liber, comanda ar sta în mâna agentului în loc să aștepte
     * la server, unde o poate lua altcineva.
     */
    $adresa = agent_adresa_libera($config);

    if ($adresa === null) {
        usleep(300000);

        continue;
    }

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

        /*
         * Se spune si care server: aici e vorba de aplicatie, nu de ANAF. Fara
         * numele lui, „serverul nu raspunde" trimitea cautarea in partea
         * gresita — omul se ducea sa vada ce-i cu ANAF.
         */
        agent_scrie($config, 'Aplicația (' . $config['server'] . ') nu răspunde: '
            . ($motiv ?: 'pricină necunoscută')
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
         * Panda goala e clipa potrivita pentru innoire: nu e nimic de lucru,
         * iar innoirea repornaste programul. Se incearca o singura data la un
         * ceas — o innoire care nu izbuteste n-are de ce sa fie reluata la
         * fiecare panda.
         */
        $innoireCeruta = isset($GLOBALS['agent_innoire']) ? $GLOBALS['agent_innoire'] : null;

        if ($innoireCeruta && (time() - $ultimaInnoire) > 3600) {
            $ultimaInnoire = time();

            agent_scrie($config, 'Serverul are versiunea ' . $innoireCeruta . ', eu am '
                . agent_versiunea_locala($config['dosar']) . '. Pornesc innoirea.');

            agent_porneste_innoirea($config);
        }

        /*
         * Tot pe panda goala se uita si la licenta. Ea tine luni de zile, dar
         * cand se apropie de capat programul local o spune la /identitate, si
         * atunci se cere alta — fara sa mai astepte cineva sa salveze ceva in
         * aplicatie.
         */
        if (time() - $ultimaLicenta >= $rastimpLicenta) {
            $ultimaLicenta = time();
            agent_licentiaza($config);
        }

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

    agent_scrie($config, 'Comanda ' . $comanda['id'] . ': ' . $comanda['metoda'] . ' ' . $comanda['cale']
        . ' -> ' . $adresa);

    /*
     * Lucrarea pleacă într-un proces al ei, iar agentul se întoarce pe loc la
     * pândă. Dacă procesul nu poate fi pornit, se face aici, ca înainte: mai
     * bine încet decât deloc.
     */
    if (!agent_porneste_lucrul($config, $comanda, $adresa)) {
        agent_scrie($config, 'Nu am putut porni un proces pentru comanda ' . $comanda['id']
            . '; o duc eu la capăt, iar celelalte așteaptă.');

        $config['local'] = $adresa;
        agent_trimite_rezultatul($config, $comanda['id'], agent_executa($config, $comanda));
    }
}
