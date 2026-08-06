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

/** Ce inseamna codul cu care s-a oprit curl, pe intelesul celui care citeste. */
function talcul_curl($cod)
{
    $talcuri = array(
        6 => 'numele serverului ANAF nu poate fi dezlegat (DNS)',
        7 => 'legătura cu ANAF nu se poate deschide — port închis sau internet căzut',
        28 => 'ANAF nu a răspuns în timpul dat',
        35 => 'strângerea de mână TLS cu ANAF a eșuat — de obicei, traficul e desfăcut de antivirus',
        52 => 'ANAF a închis legătura fără să răspundă',
        55 => 'legătura s-a rupt în timp ce se trimitea',
        56 => 'legătura s-a rupt în timp ce se primea răspunsul; sesiunea securizată s-a stins'
            . ' înainte de capătul răspunsului (SEC_E_CONTEXT_EXPIRED). Se întâmplă la răspunsuri'
            . ' mari sau când lanțul de servere al ANAF închide legătura pe neașteptate',
        60 => 'certificatul serverului ANAF nu este de încredere — semn că traficul e desfăcut'
            . ' de antivirus sau de proxy',
    );

    return isset($talcuri[(int) $cod])
        ? $talcuri[(int) $cod]
        : 'curl s-a oprit cu codul ' . (int) $cod;
}
