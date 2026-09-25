<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cine mai primeste, in copie ascunsa, fiecare scrisoare trimisa
    |--------------------------------------------------------------------------
    |
    | Adresa casei: asa se vede in cutia noastra ce a plecat si cum arata la
    | destinatar, fara sa fie nevoie sa deschida cineva evidenta. Se poate
    | schimba din .env (MARKETING_BCC), iar goala inseamna ca nu se trimite
    | nimanui in copie.
    |
    | De stiut: la o campanie de cateva mii de firme, tot atatea scrisori ajung
    | si in cutia aceasta.
    |
    */

    'copie_ascunsa' => env('MARKETING_BCC', 'office@dianasoft.ro'),

    /*
    |--------------------------------------------------------------------------
    | Cine scrie, asa cum se vede in cutia destinatarului
    |--------------------------------------------------------------------------
    |
    | Scrisorile plecau cu expeditorul obisnuit al aplicatiei (MAIL_FROM_NAME),
    | care e bun pentru o instiintare tehnica, dar nu si pentru o scrisoare
    | catre cineva care nu ne cunoaste: acolo trebuie sa scrie numele casei, nu
    | al omului care a apasat butonul sau al serverului.
    |
    | Lasate goale, se folosesc valorile din config/mail.php.
    |
    */

    'expeditor' => [
        'adresa' => env('MARKETING_FROM_ADDRESS', ''),
        'nume' => env('MARKETING_FROM_NAME', 'Diana Soft'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cate firme se pot lua deodata la intamplare
    |--------------------------------------------------------------------------
    |
    | Trimiterea la intamplare e facuta ca sa se scrie putin si des — cateva
    | zeci pe zi —, nu ca sa se goleasca lista dintr-o apasare. Plafonul e aici
    | ca o greseala de tastare sa nu ajunga o mie de scrisori.
    |
    */

    'cat_la_intamplare' => 500,

    /*
    |--------------------------------------------------------------------------
    | Sabloanele scrisorilor
    |--------------------------------------------------------------------------
    |
    | De aici porneste omul cand scrie o campanie: alege un sablon, iar subiectul
    | si textul intra in formular, unde se pot schimba inainte de trimitere.
    |
    | Ce trebuie stiut cand se scrie unul nou:
    |
    | Locurile goale se scriu intre acolade — {nume}, {firma}, {cui}, {judet} —
    | si se umplu la trimitere, pentru fiecare destinatar (vezi
    | ScrisoareMarketing::potriveste).
    |
    | Butonul „Solicita o demonstratie" si legatura de dezabonare se pun singure
    | in fiecare scrisoare, de catre sablonul de email. Nu se scriu aici, si mai
    | ales textul trebuie sa se incheie in asa fel incat butonul sa vina firesc
    | dupa el.
    |
    | Si inca ceva, care tine de cinste, nu de cod: in scrisori nu se scrie decat
    | ce aplicatia face cu adevarat. Cifrele de mai jos — 174 de tipuri de
    | declaratii, 35 de documente, 90 de zile de proba — sunt aceleasi cu cele
    | publicate pe spvcurier.ro. Cand se schimba acolo, se schimba si aici.
    |
    */

    'sabloane' => [

        [
            'cheie' => 'prezentare',
            'nume' => 'Prima scrisoare — ce face aplicația',
            'descriere' => 'Pentru cine n-a mai auzit de noi. Spune pe scurt tot, fără să insiste.',
            'campanie' => 'spv-curier-prezentare',
            'subiect' => 'Declarațiile întregului portofoliu, dintr-un singur ecran',
            'text' => "Bună ziua, {nume},\n\n"
                . "Vă scriem de la Diana Soft, din Năvodari. Facem software la comandă din 2003, iar de"
                . " câțiva ani lucrăm aproape numai la un singur lucru: legătura dintre un cabinet de"
                . " contabilitate și ANAF.\n\n"
                . "Se numește SPV Curier. Citește Spațiul Privat Virtual pentru tot portofoliul de firme,"
                . " validează declarațiile cu programul oficial ANAF, le semnează cu certificatul de pe"
                . " token și le depune — apoi aduce recipisa și o pune în arhiva dumneavoastră.\n\n"
                . "174 de tipuri de declarații și 35 de documente care se pot cere din SPV, pentru toate"
                . " firmele deodată.\n\n"
                . "Dacă vi se pare de folos, vă arătăm în 30 de minute cum ar arăta pe firmele"
                . " dumneavoastră, nu pe unele demonstrative. Primele 90 de zile sunt gratuite, fără card.",
        ],

        [
            'cheie' => 'saft-decont',
            'nume' => 'SAF-T ↔ decont de TVA',
            'descriere' => 'Pentru cabinetele cu clienți pe SAF-T. Cea mai bună scrisoare dacă știți că au D406.',
            'campanie' => 'saft-decont',
            'subiect' => 'Neconcordanțele SAF-T ↔ decont TVA, înainte să le vadă ANAF',
            'text' => "Bună ziua, {nume},\n\n"
                . "Dacă aveți clienți care depun SAF-T, știți deja partea neplăcută: fișierul trece de"
                . " validator, decontul de TVA trece și el, iar neconcordanța dintre ele se vede abia"
                . " când întreabă ANAF.\n\n"
                . "SPV Curier le pune față în față înainte de depunere. Unsprezece verificări de"
                . " consistență pe fișierul SAF-T, iar decontul rând cu rând față de jurnale: fiecare"
                . " diferență apare cu suma din SAF-T alături de cea din D300. O vedeți dumneavoastră"
                . " întâi.\n\n"
                . "Tot de acolo se poate genera și decontul, din jurnalele fișierului deja depus — cu"
                . " fiecare rând arătat și cu suma din spatele lui. Aceleași cifre nu se mai introduc de"
                . " două ori.\n\n"
                . "Vă arătăm pe un SAF-T de-al dumneavoastră, în 30 de minute. 90 de zile gratuit, fără"
                . " card.",
        ],

        [
            'cheie' => 'termen-25',
            'nume' => 'Înainte de 25 — ce a mai rămas de depus',
            'descriere' => 'De trimis în prima jumătate a lunii, când grija termenului e proaspătă.',
            'campanie' => 'termen-25',
            'subiect' => 'Ce mai aveți de depus până pe 25, pe tot portofoliul',
            'text' => "Bună ziua, {nume},\n\n"
                . "În preajma lui 25 întrebarea e mereu aceeași: ce a mai rămas de depus și pentru"
                . " cine.\n\n"
                . "SPV Curier o deduce din vectorul fiscal al fiecărei firme: ce se datorează luna"
                . " aceasta, ce s-a depus deja — cu indexul recipisei și data — și ce nu s-a depus încă."
                . " Pentru toate firmele deodată, în PDF sau în Excel.\n\n"
                . "Iar declarațiile puse în folderul urmărit se validează, se semnează, se depun și se"
                . " arhivează automat, dacă așa ați configurat. Recipisa vine înapoi fără să o ceară"
                . " nimeni.\n\n"
                . "Vă arătăm pe firmele dumneavoastră, în 30 de minute. 90 de zile gratuit, fără card.",
        ],

        [
            'cheie' => 'token-stationar',
            'nume' => 'Tokenul nu se mai plimbă între birouri',
            'descriere' => 'Pentru cabinetele cu mai mulți oameni și mai multe tokenuri.',
            'campanie' => 'token-stationar',
            'subiect' => 'Tokenul rămâne unde e, semnați de la orice calculator',
            'text' => "Bună ziua, {nume},\n\n"
                . "Într-un cabinet cu mai mulți salariați, tokenul ajunge să se plimbe de la un birou la"
                . " altul — iar când e nevoie de el, e la cineva acasă.\n\n"
                . "SPV Curier lucrează altfel: certificatul și declarațiile rămân pe calculatorul unde"
                . " stă tokenul, iar semnarea și depunerea se cer de la orice stație autorizată. Nu"
                . " mutați tokenul, autorizați stația.\n\n"
                . "Mai multe tokenuri, mai multe calculatoare, o singură evidență: aplicația ține minte"
                . " ce firme sunt înrolate la ANAF pe fiecare certificat și semnează cu cel potrivit.\n\n"
                . "Utilizatori, tokenuri și stații de lucru nelimitate, în orice plan.\n\n"
                . "Vă arătăm cum se așază la dumneavoastră, în 30 de minute. 90 de zile gratuit, fără"
                . " card.",
        ],

        [
            'cheie' => 'android',
            'nume' => 'Aplicația de telefon',
            'descriere' => 'Scurtă. Bună ca a doua scrisoare, pentru cineva care a citit-o pe prima.',
            'campanie' => 'android',
            'subiect' => 'Recipisele și mesajele din SPV, pe telefon',
            'text' => "Bună ziua, {nume},\n\n"
                . "SPV Curier are și o aplicație de telefon. Declarațiile depuse cu recipisa lor validă,"
                . " mesajele din SPV și solicitările către ANAF — toate în buzunar.\n\n"
                . "Iar când o declarație așteaptă semnătura, o autorizați de acolo, fără să fiți la"
                . " calculatorul pe care stă tokenul.\n\n"
                . "E parte din același abonament, nu se plătește separat.\n\n"
                . "Dacă vreți s-o vedeți lucrând pe firmele dumneavoastră, ne trebuie 30 de minute."
                . " Primele 90 de zile sunt gratuite, fără card.",
        ],

        [
            'cheie' => 'reamintire',
            'nume' => 'Reamintire, pentru cei care n-au răspuns',
            'descriere' => 'De folosit cu filtrul „fără răspuns". Scurtă și fără insistență.',
            'campanie' => 'reamintire',
            'subiect' => 'Vă mai trebuie cele 30 de minute?',
            'text' => "Bună ziua, {nume},\n\n"
                . "V-am scris acum o vreme despre SPV Curier — aplicația cu care un cabinet citește"
                . " Spațiul Privat Virtual, validează, semnează și depune declarațiile pentru tot"
                . " portofoliul, dintr-un singur ecran.\n\n"
                . "Nu insistăm. Dacă nu vă e de folos, o apăsare pe legătura de jos și nu mai primiți"
                . " nimic de la noi.\n\n"
                . "Dacă însă e doar o chestiune de timp: demonstrația ține 30 de minute și se face pe"
                . " firmele dumneavoastră, nu pe unele demonstrative. Primele 90 de zile sunt gratuite,"
                . " fără card.",
        ],

    ],

];
