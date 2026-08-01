<?php

return [
    'spv' => [
        'base_url' => env('SPV_BASE_URL', 'https://webserviced.anaf.ro/SPVWS2/rest'),
        'driver' => env('SPV_DRIVER', 'bridge'),

        'cert' => [
            'path' => env('SPV_CERT_PATH'),
            'password' => env('SPV_CERT_PASSWORD'),
            'ca' => env('SPV_CA_BUNDLE'),
        ],

        'bridge' => [
            'url' => env('SPV_BRIDGE_URL', 'http://127.0.0.1:8099'),
            'token' => env('SPV_BRIDGE_TOKEN'),
        ],

        'zile_max' => 60,
        'throttle_ms' => 1200,
        'timeout' => 60,
        'storage_disk' => env('SPV_DISK', 'local'),
        'storage_path' => 'spv',

        // La citirea listei de mesaje se descarca automat si fisierele lipsa.
        // ANAF impune o pauza intre apeluri, asa ca numarul e limitat pe cerere;
        // mesajele ramase se preiau la urmatoarea citire.
        'descarcare_automata' => env('SPV_DESCARCARE_AUTOMATA', true),
        'limita_descarcari' => (int) env('SPV_LIMITA_DESCARCARI', 20),
        'incercari_max' => 3,

        /*
         * Feluri de documente ale caror fisiere se aduc din alta fila, nu de la
         * „Descarcă mesaje".
         *
         * Recipisele se descarca odata cu declaratiile, iar raspunsurile odata
         * cu solicitarile — acolo sunt si legate de documentul lor. Aduse si de
         * aici, ar insemna acelasi fisier cerut de doua ori de la ANAF, adica
         * apeluri consumate degeaba din limita zilnica.
         *
         * Cheia e o bucata din denumire (potrivirea nu tine cont de litere mari
         * sau mici), iar valoarea e fila din care vine documentul.
         */
        'tipuri_din_alte_file' => [
            'RECIPISA' => 'Declarații fiscale',
            'RASPUNS SOLICITARE' => 'Solicitări ANAF',
        ],

        /*
         * Felurile de documente pe care ANAF le pune in SPV, pentru alertele pe
         * email. Lista e un punct de plecare, nu una oficiala: ANAF nu publica
         * un nomenclator al valorilor din campul „tip" al mesajelor.
         *
         * De aceea potrivirea alertelor se face pe bucata de text si fara sa
         * tina cont de litere mari sau mici („somatie" prinde si „SOMATIE", si
         * „Somatie de plata"), iar in interfata campul ramane liber: daca ANAF
         * trimite un fel nou, se poate scrie de mana.
         *
         * Tipurile intalnite chiar in mesajele clientului se adauga automat
         * peste aceasta lista.
         */
        'tipuri_mesaje' => [
            'RECIPISA',
            'RASPUNS SOLICITARE',
            'EXTRAS DE CONT',
            'SOMATIE',
            'DECIZIE',
            'DECIZIE DE IMPUNERE',
            'NOTIFICARE',
            'INSTIINTARE',
            'ADRESA',
            'PROCES VERBAL',
            'TITLU EXECUTORIU',
            'POPRIRE',
            'CERTIFICAT DE ATESTARE FISCALA',
            'FISA ROL',
            'SITUATIE SINTETICA',
            'VECTOR FISCAL',
            'DATE IDENTIFICARE',
        ],

        /*
         * Tipurile de documente care pot fi solicitate prin webserviciul SPV
         * (/cerere) si parametrii suplimentari ceruti de ANAF pentru fiecare.
         * Denumirile trebuie scrise exact asa — ANAF le compara literal.
         * Sursa: https://github.com/MfpAnaf/ClientSPV
         */
        'tipuri_documente' => [
            'DATE IDENTIFICARE' => [],
            'VECTOR FISCAL' => [],
            'Situatie Sintetica' => [],
            'Fisa Rol' => ['cui_pui'],
            'Obligatii de plata' => [],
            'Nota obligatiilor de plata' => [],
            'Istoric Spatiu Virtual' => [],
            'Istoric declaratii' => ['an'],
            'Istoric bilant' => [],
            'Registru intrari-iesiri' => [],
            'InterogariBanci' => [],
            'Bilant anual' => ['an'],
            'Bilant semestrial' => ['an'],
            'Adeverinte Venit' => ['an', 'motiv'],
            'Duplicat Recipisa' => ['numar_inregistrare'],
            'NeconcordanteD112CNP' => [],
            'NeconcordanteD394' => ['an', 'luna'],
            'D112Contrib' => [],
            'D100' => ['an', 'luna'],
            'D101' => ['an'],
            'D106' => ['an'],
            'D112' => ['an', 'luna'],
            'D120' => ['an'],
            'D130' => ['an'],
            'D180' => ['an', 'luna'],
            'D205' => ['an'],
            'D208' => ['an', 'luna'],
            'D212' => ['an'],
            'D300' => ['an', 'luna'],
            'D301' => ['an', 'luna'],
            'D311' => ['an', 'luna'],
            'D390' => ['an', 'luna'],
            'D392' => ['an'],
            'D393' => ['an'],
            'D394' => ['an', 'luna'],
        ],
    ],

    /*
     * e-Transport — declararea transporturilor de bunuri cu risc fiscal ridicat.
     * Se folosesc endpoint-urile cu certificat digital (webserviceapl.anaf.ro),
     * apelate prin programul local, nu cele cu OAuth2.
     */
    'etransport' => [
        'base_url' => env('ETRANSPORT_CERT_PROD_BASE_URL', 'https://webserviceapl.anaf.ro/prod/ETRANSPORT/ws/v1'),
        'test_url' => env('ETRANSPORT_CERT_TEST_BASE_URL', 'https://webserviceapl.anaf.ro/test/ETRANSPORT/ws/v1'),

        /*
         * Modul de autentificare:
         *   certificat — apel prin programul local, cu certificatul de pe token
         *   oauth      — apel direct, cu autorizare OAuth2 (api.anaf.ro)
         */
        'mod' => env('ETRANSPORT_MOD', 'certificat'),
        'oauth_url' => env('ETRANSPORT_PROD_BASE_URL', 'https://api.anaf.ro/prod/ETRANSPORT/ws/v1'),
        'oauth_test_url' => env('ETRANSPORT_TEST_BASE_URL', 'https://api.anaf.ro/test/ETRANSPORT/ws/v1'),

        // Mediul folosit implicit: prod sau test
        'mediu' => env('ETRANSPORT_MEDIU', 'prod'),
        'standard' => 'ETRANSP',
        'versiune' => (int) env('ETRANSPORT_VERSIUNE', 2),
        'zile_max' => 60,
        'timeout' => 120,
        'storage_path' => 'etransport',
    ],

    /*
     * Autorizarea OAuth2 la ANAF (comuna serviciilor web: e-Transport, e-Factura).
     * Datele de client se obtin din SPV, la inregistrarea aplicatiei.
     */
    'oauth' => [
        'client_id' => env('CLIENT_ANAF_ID'),
        'client_secret' => env('CLIENT_ANAF_SECRET'),

        // Adresa la care ANAF intoarce browserul; trebuie sa coincida cu cea declarata la ANAF.
        'redirect' => env('ANAF_OAUTH_REDIRECT'),

        'url_autorizare' => env('ANAF_OAUTH_AUTHORIZE', 'https://logincert.anaf.ro/anaf-oauth2/v1/authorize'),
        'url_token' => env('ANAF_OAUTH_TOKEN', 'https://logincert.anaf.ro/anaf-oauth2/v1/token'),
    ],

    'certificate' => [
        // Cu cate zile inainte de expirare se trimite avertizarea pe email
        'zile_avertizare' => (int) env('ANAF_CERT_ZILE_AVERTIZARE', 30),
        // Cat de des se repeta avertizarea cat timp certificatul e in fereastra
        'reamintire_zile' => (int) env('ANAF_CERT_REAMINTIRE_ZILE', 7),
    ],

    /*
     * Arhiva de documente de pe calculatorul clientului.
     *
     * Aplicatia sta in cloud, dar documentele fiscale raman la client: sunt
     * scrise prin programul local, in structura
     *
     *     <radacina>\<Denumire firma (CUI)>\<TIP>\<TIP>_<CUI>_<perioada>_<stare>.pdf
     *
     * Radacina se alege pe calculatorul clientului, in bridge.env (ARHIVA_CALE).
     * Serverul pastreaza doar calea relativa, ca sa stie de unde sa ceara
     * fisierul cand omul apasa pe el.
     */
    'arhiva' => [
        'activa' => (bool) env('ANAF_ARHIVA_ACTIVA', true),

        /*
         * Dupa ce documentul a ajuns in arhiva clientului, copia de lucru de pe
         * server se sterge. Fisierele de lucru (XML-ul de validat, PDF-ul
         * nesemnat) raman pana la semnare, ca sa poata fi validate si semnate.
         */
        'sterge_de_pe_server' => (bool) env('ANAF_ARHIVA_STERGE_SERVER', true),

        // Subdosarul in care intra documentele descarcate din SPV
        'dosar_spv' => 'SPV',
        'timeout' => 120,
    ],

    'declaratii' => [
        // Validare XML si generare PDF cu DUKIntegrator (ANAF)
        'duk' => [
            'java' => env('ANAF_JAVA_BIN', 'java'),
            'jar' => env('ANAF_DUK_JAR', 'C:\\dianasoft_serviciiweb\\anaf-tools\\dist\\DUKIntegrator.jar'),

            /*
             * Lansatorul pentru D406/SAF-T, care cheama validatorul ANAF cu
             * perioada raportata (vezi tools/duk-d406). Gol = se cauta
             * DukD406.jar langa DUKIntegrator.jar.
             */
            'jar_d406' => env('ANAF_DUK_JAR_D406'),
            'timeout' => 180,
        ],

        /*
         * Caseta vizibila a semnaturii pe PDF-ul declaratiei.
         *
         * Coordonatele sunt in puncte, cu originea in coltul din stanga-jos al
         * paginii (A4 = 595 x 842). Caseta sta pe ultima pagina, jos in dreapta:
         * acolo e locul semnaturii pe orice act, si acolo formularele ANAF au
         * spatiu liber, oricat de lunga ar fi declaratia. Cu y = 45 si inaltime
         * 78, ea tine intre 45 si 123 de puncte de la marginea de jos.
         *
         * Daca pe o anumita declaratie caseta se suprapune peste text, se muta
         * de aici, fara sa fie nevoie de modificari in cod.
         */
        'semnatura' => [
            // Numarul paginii sau "ultima"
            'pagina' => env('ANAF_SEMNATURA_PAGINA', 'ultima'),
            'x' => (float) env('ANAF_SEMNATURA_X', 330),
            'y' => (float) env('ANAF_SEMNATURA_Y', 45),
            'latime' => (float) env('ANAF_SEMNATURA_LATIME', 235),
            'inaltime' => (float) env('ANAF_SEMNATURA_INALTIME', 78),
            'motiv' => env('ANAF_SEMNATURA_MOTIV', 'Semnatura declaratie'),
        ],

        // Depunere prin bridge (mTLS cu certificatul de pe token)
        'url_depunere' => env('ANAF_URL_DEPUNERE', 'https://decl.anaf.mfinante.gov.ro'),

        // Verificare stare + descarcare recipisa (fara certificat)
        'url_stare' => env('ANAF_URL_STARE', 'https://www.anaf.ro/StareD112/vizualizareStare.do'),
        'url_recipisa' => env('ANAF_URL_RECIPISA', 'https://www.anaf.ro/StareD112/ObtineRecipisa?numefisier='),

        'storage_path' => 'declaratii',
        'timeout' => 120,
    ],
];
