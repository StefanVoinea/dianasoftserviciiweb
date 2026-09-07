<?php

/**
 * [2026-09-04] Ce poate atinge accesul MCP (un asistent legat prin „Connectors").
 *
 * Adus din DianaSoft ALPROF, cu o deosebire de fond: acolo era un ERP al unei
 * singure fabrici; aici e un serviciu cu multi clienti, fiecare cu firmele,
 * certificatele si declaratiile lui. De aceea citirea are o regula in plus, pe
 * firma.
 *
 * Sunt doua drepturi diferite, cu doua reguli diferite:
 *
 *   CITIRE  — larga, dar in perimetrul clientului. Se poate citi orice tabela din
 *             baza, in afara celor din `citire.tabele_interzise`, si orice coloana,
 *             in afara celor din `citire.coloane_interzise`.
 *             - tabelele cu `company_id` se filtreaza automat pe firma curenta;
 *             - tabelele FARA `company_id` sunt de doua feluri: nomenclatoare
 *               comune (judete, localitati, curs BNR, registrul comertului...),
 *               listate in `citire.tabele_comune`, pe care le citeste oricine; si
 *               restul (utilizatori, clienti, evidenta de marketing, banci), pe
 *               care le citeste doar administratorul serviciului.
 *
 *   SCRIERE — ingusta. Doar tabelele si coloanele scrise explicit in `tabele`.
 *             Ce nu e listat acolo nu se poate modifica, oricat de citibil ar fi.
 *
 * NU se pot sterge randuri prin acest acces, in nicio situatie. Nu exista
 * operatie de stergere in McpController si nici o cale de a ajunge la `delete()`.
 *
 * Ce se poate CITI, dar nu SCRIE, si de ce:
 *   - declaratiile, mesajele SPV, solicitarile, jurnalul ANAF — sunt urme ale
 *     unor depuneri reale, cu recipise; se scriu doar de procesele care depun.
 *   - certificatele — leaga o firma de un calculator si de un PIN; se
 *     administreaza din aplicatie, cu confirmari.
 *   - `users`, `companies`, permisiuni, meniuri, abonamente.
 */
return [

    // peste cate randuri o modificare in masa cere confirmare explicita
    "prag_confirmare" => 20,

    // limita absoluta pe o singura operatie
    "maxim_randuri" => 2000,

    "citire" => [

        /**
         * Tabele care nu se citesc deloc. Nu sunt secrete de business, sunt chei:
         * tokenuri OAuth si ANAF, resetari de parola, sesiuni, cozi. Nimic din ele
         * nu ajuta la o intrebare despre firme sau declaratii, si tot ce e acolo e
         * material de autentificare.
         */
        "tabele_interzise" => [
            "oauth_access_tokens", "oauth_auth_codes", "oauth_clients",
            "oauth_personal_access_clients", "oauth_refresh_tokens",
            "password_resets", "password_reset_tokens", "sessions",
            "personal_access_tokens", "social_identities",
            "failed_jobs", "jobs", "job_batches", "cache", "cache_locks",
            // tokenurile ANAF (e-Factura, e-Transport, SPV) si cele de push
            "tokenuri", "efacturatokens", "etransporttokens", "dispozitive_notificari",
        ],

        /**
         * Nume de coloane care dispar din rezultate ORIUNDE ar aparea. Asa `users`
         * ramane citibila (cine a depus, cine raspunde) fara ca hash-ul de parola
         * sa iasa vreodata din baza, `anaf_certificate` se vede fara codul de
         * legatura cu puntea, iar `marketing_contacte` fara jetonul de dezabonare.
         *
         * O coloana ascunsa nu se poate nici filtra, nici ordona, nici grupa dupa
         * ea. Altfel ar deveni un oracol: `password incepe_cu "$2y$10$a"`, repetat
         * de destule ori, reconstituie hash-ul caracter cu caracter.
         */
        "coloane_interzise" => [
            "password", "parola", "remember_token", "secret", "client_secret",
            "api_key", "salt",
            "access_token", "refresh_token", "bridge_token", "link_token", "link_revoke_token",
            // jetonul din legatura de dezabonare: cine il stie poate dezabona pe altcineva
            "jeton",
            // date personale care nu au ce cauta intr-o conversatie
            "cnp", "cnp_reprezentant",
            // `mcp_operatii.token` aplica o modificare pregatita. Daca s-ar putea
            // citi, pasul de confirmare in doua faze ar deveni ocolibil.
            "token",
        ],

        /**
         * Tabele fara `company_id` pe care le poate citi ORICE utilizator: sunt
         * nomenclatoare comune, nu date ale vreunui client. Celelalte tabele fara
         * `company_id` raman doar pentru administratorul serviciului.
         */
        "tabele_comune" => [
            "judete", "localitati", "tari", "nombanci", "sarbatorilegale", "cursbnr",
            "datefirmeanaf", "datefirmeregcom", "etransport_coduri_vamale",
            "ordinedeblocareanafhtml", "migrations",
            "dianasoftfield", "dianasoftmodel", "dianasoftmenuoptions", "permissions",
        ],

        "limita_implicita" => 50,
        "limita_maxima"    => 500,
    ],

    "tabele" => [

        /**
         * Firmele clientului, asa cum le stie modulul ANAF. Se pot corecta datele
         * de contact si antetul declaratiilor (declarant, bifele D300). CIF-ul e
         * cheia si nu se schimba; nici certificatul, nici datele trase de la ANAF
         * (`date_identificare`, `vector_la`, `sincronizat_la`).
         */
        "societati" => [
            "model"     => App\Models\AnafSocietate::class,
            "eticheta"  => "Firmele clientului (modulul ANAF)",
            "cheie"     => "cif",
            "cautabile" => ["cif", "denumire", "tip", "caen", "activ", "scos_din_uz", "email", "telefon"],
            "editabile" => [
                "denumire", "adresa", "telefon", "fax", "email", "banca", "cont", "caen",
                "nume_declarant", "prenume_declarant", "functie_declarant", "prin_reprezentant",
                "d300_tip_decont", "d300_pro_rata",
                "d300_bifa_interne", "d300_bifa_cereale", "d300_bifa_mob", "d300_bifa_disp", "d300_bifa_cons",
                "d300_solicit_ramb",
                "activ", "scos_din_uz",
            ],
        ],

        /**
         * Evidenta de marketing: firmele carora li se scrie despre aplicatiile
         * noastre. E lista noastra, nu a vreunui client, deci o poate atinge doar
         * administratorul serviciului. Bifa `abonat` si `dezabonat_la` NU se
         * ating de aici: cine s-a dezabonat ramane dezabonat.
         */
        "contacte_marketing" => [
            "model"              => App\Models\MarketingContact::class,
            "eticheta"           => "Evidenta de marketing (doar administratorul)",
            "cheie"              => "cui",
            "doar_administrator" => true,
            "cautabile"          => ["cui", "denumire", "email", "judet", "tip", "sursa", "abonat", "viza"],
            "editabile"          => ["denumire", "email", "emailuri", "telefon", "judet", "tip", "viza", "membru_din", "sursa"],
        ],
    ],
];
