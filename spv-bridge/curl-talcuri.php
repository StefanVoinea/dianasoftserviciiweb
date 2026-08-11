<?php
/*
 * Ce inseamna codurile cu care se opreste curl, cand vorbeste cu ANAF.
 *
 * Stau deoparte de programul propriu-zis ca sa poata fi probate: de ele atarna
 * si daca se mai incearca o data, si ce scrie omul in raport cand suna.
 */

/**
 * Pene de legatura care trec de la sine: se incearca din nou, nu se da vina.
 *
 *   35 — strangerea de mana TLS a esuat
 *   52 — serverul a inchis fara sa raspunda
 *   55 — trimiterea s-a rupt
 *   56 — primirea s-a rupt; de aici vine si SEC_E_CONTEXT_EXPIRED, adica
 *        sesiunea securizata s-a stins in timp ce ANAF inca raspundea
 */
function pana_trecatoare($cod)
{
    return in_array((int) $cod, array(35, 52, 55, 56), true);
}

/**
 * Ce inseamna codul cu care s-a oprit curl, pe intelesul celui care citeste.
 *
 * La penele de fel TLS, mesajul spunea pana acum toate pricinile cu putinta si
 * lasa omul sa le incerce pe rand — iar cea dintai, tokenul care isi asteapta
 * PIN-ul, il trimitea de multe ori sa caute unde nu era nimic. Acum programul
 * intreaba chiar tokenul inainte sa scrie mesajul, si spune ce a aflat.
 *
 * @param string $cheia 'bun' | 'blocat' | 'necunoscut' — ce a raspuns tokenul
 */
function talcul_curl($cod, $cheia = 'necunoscut')
{
    $talcuri = array(
        6 => 'numele serverului ANAF nu poate fi dezlegat (DNS)',
        7 => 'legătura cu ANAF nu se poate deschide — port închis sau internet căzut',
        28 => 'ANAF nu a răspuns în timpul dat',
        35 => 'strângerea de mână TLS cu ANAF a eșuat',
        52 => 'ANAF a închis legătura fără să răspundă',
        55 => 'legătura s-a rupt în timp ce se trimitea',
        58 => 'certificatul cerut nu s-a găsit în magazinul Windows al contului sub care rulează programul:'
            . ' tokenul nu e conectat, ori amprenta cerută este a altui certificat',
        56 => 'legătura s-a rupt în timp ce se primea răspunsul; sesiunea securizată s-a stins'
            . ' înainte de capătul răspunsului (SEC_E_CONTEXT_EXPIRED)',
        60 => 'certificatul serverului ANAF nu este de încredere — semn că traficul e desfăcut'
            . ' de antivirus sau de proxy',
    );

    $talc = isset($talcuri[(int) $cod])
        ? $talcuri[(int) $cod]
        : 'curl s-a oprit cu codul ' . (int) $cod;

    return $talc . talcul_cheii($cod, $cheia);
}

/**
 * Ce s-a aflat intreband tokenul, adaugat la talcul penei.
 *
 * Numai la penele de fel TLS (35, 56) are rost: acolo cheia chiar e in joc.
 * „Necunoscut" nu adauga nimic — un rand care spune ca nu se stie nimic e mai
 * rau decat tacerea.
 */
function talcul_cheii($cod, $cheia)
{
    if (!in_array((int) $cod, array(35, 56), true)) {
        return '';
    }

    /*
     * Cheia e buna. Pana acum se spunea de-a dreptul „e antivirusul" — si chiar
     * asa era la primul client la care s-a intamplat. La al doilea insa
     * certificatul ANAF venea curat de la DigiCert, adica nimeni nu desfacea
     * nimic, iar omul a umblat degeaba prin setari.
     *
     * Se spune deci ce se stie: nu e nici PIN-ul, nici altceva de la noi, iar
     * mai departe raspunde diagnoza, care se uita la lantul certificatului si
     * cere si vorba lui curl pe dinauntru. Un sfat dat cu siguranta acolo unde
     * nu e costa mai mult decat unul care trimite la proba potrivita.
     */
    if ($cheia === 'bun') {
        return '. Tokenul a fost întrebat chiar acum: cheia a semnat pe loc, fără să ceară nimic,'
            . ' deci driverul ține PIN-ul minte și NU el a rupt legătura. Rămâne strângerea de'
            . ' mână cu ANAF, și cea mai deasă pricină e curl-ul vechi din Windows: pe o stație cu'
            . ' 8.13 legătura cădea, pe alta cu 8.21 mergea. Kitul aduce unul nou — copiați'
            . ' curl.exe din kit în dosarul programului, apoi opriți și porniți-l'
            . ' (opreste-manual.bat, porneste-manual.bat). Dacă nici așa, rulați „diagnoza.bat":'
            . ' ea spune dacă traficul e desfăcut de antivirus și cere și o a doua părere,'
            . ' fără curl';
    }

    /*
     * Cheia merge, dar driverul a cerut PIN-ul din nou.
     *
     * Asta e chiar pricina, si nu se vede din afara: fereastra se deschide si in
     * mijlocul strangerii de mana cu ANAF, iar acolo nimeni nu asteapta dupa om.
     * Cat scrie el PIN-ul, sesiunea securizata se stinge — SEC_E_CONTEXT_EXPIRED.
     * Nu se poate cere ANAF-ului mai multa rabdare; se poate insa spune
     * driverului sa ceara PIN-ul o singura data pe sesiunea Windows.
     */
    if ($cheia === 'bun_dupa_pin') {
        return '. Tokenul a fost întrebat chiar acum: cheia merge, dar driverul a cerut PIN-ul din'
            . ' nou — aceasta este pricina. El îl cere la fiecare folosire, deci îl cere și în'
            . ' mijlocul legăturii cu ANAF, iar cât se scrie PIN-ul, sesiunea securizată se stinge;'
            . ' ANAF nu poate fi rugat să aștepte. Îndreptarea e în driverul tokenului: deschideți'
            . ' „SafeNet Authentication Client" → Vizualizare avansată → Setări token (Client'
            . ' Settings) → bifați „Enable single logon" (Autentificare unică), apoi deconectați și'
            . ' reconectați tokenul. Așa PIN-ul se cere o singură dată pe sesiunea Windows, la'
            . ' intrarea în aplicație, și nu mai ajunge în mijlocul apelurilor';
    }

    if ($cheia === 'blocat') {
        return '. Tokenul a fost întrebat chiar acum și cheia de pe el NU se poate folosi: aceasta'
            . ' este pricina. Mergeți la calculatorul cu tokenul, introduceți codul PIN în fereastra'
            . ' deschisă acolo, apoi încercați din nou';
    }

    /*
     * Tokenul n-a putut fi intrebat (kit vechi, sau proba n-a raspuns). Atunci
     * se spun pricinile cu putinta, ca inainte — dar numai atunci, fiindca
     * altfel omul le cauta pe toate degeaba.
     */
    return '. Fiecare legătură cu ANAF cere cheia de pe token, iar dacă driverul așteaptă codul PIN'
        . ' într-un dialog pe care nu-l vede nimeni, legătura moare de la sine; se întâmplă însă'
        . ' și când traficul e desfăcut de antivirus, la răspunsuri mari, sau când lanțul de'
        . ' servere al ANAF închide legătura pe neașteptate';
}
