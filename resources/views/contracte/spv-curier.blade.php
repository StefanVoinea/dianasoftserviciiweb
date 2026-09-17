@php
    /**
     * Contractul de abonament SPV Curier, scos din datele clientului.
     *
     * Ce lipsește se tipărește ca linie punctată, nu ca gol: contractul se
     * poate scoate și neîntreg, ca să fie dus la client și completat de mână,
     * dar cine îl citește trebuie să vadă unde s-a oprit.
     */
    $v = function ($valoare, $latime = 24) {
        $text = trim((string) $valoare);

        return $text !== '' ? e($text) : str_repeat('.', $latime);
    };

    $data = function ($valoare) {
        return $valoare ? $valoare->format('d.m.Y') : '.......................';
    };

    $planAles = $c->plan ? mb_strtoupper($c->plan) : null;
@endphp
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="utf-8">
<title>{{ $c->numeFisier() }}</title>
<style>
    @page { margin: 18mm 16mm 16mm 16mm; }
    body { font-family: "DejaVu Sans", "Segoe UI", Arial, sans-serif; font-size: 10.5pt; line-height: 1.45; color: #17191d; }
    h1 { font-size: 14pt; text-align: center; margin: 0 0 2mm; letter-spacing: .3px; }
    h2 { font-size: 11pt; margin: 6mm 0 2mm; text-transform: uppercase; letter-spacing: .4px; }
    h3 { font-size: 10.5pt; margin: 4mm 0 1.5mm; }
    p { margin: 0 0 2.2mm; text-align: justify; }
    .subtitlu { text-align: center; margin: 0 0 1mm; font-size: 11pt; }
    .numar { text-align: center; margin: 0 0 6mm; font-size: 10pt; }
    table { width: 100%; border-collapse: collapse; margin: 2mm 0 3mm; }
    th, td { border: 1px solid #b9b6ae; padding: 1.6mm 2mm; vertical-align: top; font-size: 9.5pt; }
    th { background: #efede7; text-align: left; font-weight: 600; }
    .fara-chenar td { border: 0; padding: 0 0 1mm; }
    .semnaturi { margin-top: 8mm; }
    .semnaturi td { width: 50%; border: 0; padding: 0 4mm 0 0; vertical-align: top; }
    .rand-semnatura { margin-top: 10mm; }
    .ales { font-weight: 700; }
    .mic { font-size: 9pt; color: #57595c; }
    .pagina-noua { page-break-before: always; }
</style>
</head>
<body>

<h1>CONTRACT DE PRESTĂRI SERVICII INFORMATICE</h1>
<p class="subtitlu">Abonament la serviciul software SPV Curier</p>
<p class="numar">Nr. {{ $v($c->numar, 10) }} din {{ $data($c->data) }}</p>

<h2>I. Părțile contractante</h2>
<p>1.1. <strong>{{ $prestator['denumire'] }}</strong>, persoană juridică română, cu sediul în {{ $prestator['sediu'] }},
înregistrată la Oficiul Registrului Comerțului sub nr. {{ $prestator['reg_com'] }}, CUI {{ $prestator['cui'] }},
cont bancar {{ $v($prestator['iban']) }} deschis la {{ $v($prestator['banca'], 16) }},
e-mail {{ $prestator['email'] }}, telefon {{ $prestator['telefon'] }},
reprezentată legal prin {{ $v($prestator['reprezentant'], 20) }}, în calitate de {{ $v($prestator['functie'], 14) }},
denumită în continuare „Prestatorul",</p>
<p>și</p>
<p>1.2. <strong>{{ $v($c->beneficiar_denumire, 28) }}</strong>, cu sediul în {{ $v($c->beneficiar_adresa, 34) }},
înregistrată la Oficiul Registrului Comerțului sub nr. {{ $v($c->beneficiar_reg_com, 16) }},
CUI {{ $v($c->beneficiar_cui, 12) }}, cont bancar {{ $v($c->beneficiar_iban) }} deschis la {{ $v($c->beneficiar_banca, 16) }},
e-mail {{ $v($c->beneficiar_email, 20) }}, telefon {{ $v($c->beneficiar_telefon, 14) }},
reprezentată legal prin {{ $v($c->beneficiar_reprezentant, 20) }}, în calitate de {{ $v($c->beneficiar_functie, 14) }},
denumită în continuare „Beneficiarul",</p>
<p>denumite împreună „Părțile" și separat „Partea", au convenit încheierea prezentului contract („Contractul")
în următoarele condiții.</p>

<h2>II. Definiții</h2>
<p>2.1. În Contract, termenii de mai jos au următorul înțeles:</p>
<table>
    <tr><th style="width:26%">Termen</th><th>Înțeles</th></tr>
    <tr><td>Serviciul / SPV Curier</td><td>serviciul software oferit de Prestator prin abonament, compus din Aplicația web, Programul local și Aplicația mobilă, împreună cu suportul și actualizările aferente.</td></tr>
    <tr><td>Aplicația web</td><td>componenta găzduită de Prestator, accesibilă prin browser, în care se administrează Entitățile, utilizatorii, declarațiile, solicitările către ANAF, alertele și rapoartele.</td></tr>
    <tr><td>Programul local</td><td>componenta instalată pe stația de lucru a Beneficiarului pe care este conectat Tokenul; descoperă certificatele, urmărește Folderul urmărit, semnează și depune declarațiile de pe stația Beneficiarului.</td></tr>
    <tr><td>Aplicația mobilă</td><td>aplicația SPV Curier pentru Android, prin care se consultă documente, se cer documente de la ANAF și se autorizează semnarea și depunerea.</td></tr>
    <tr><td>SPV</td><td>Spațiul Privat Virtual, serviciul electronic al Agenției Naționale de Administrare Fiscală.</td></tr>
    <tr><td>Token / Certificat</td><td>dispozitivul criptografic al Beneficiarului și certificatul digital calificat stocat pe acesta, emis de un prestator de servicii de încredere.</td></tr>
    <tr><td>Entitate</td><td>o persoană juridică sau fizică identificată prin CIF/CUI/CNP, înrolată în SPV pe un Certificat al Beneficiarului, pentru care Serviciul depune declarații, citește mesaje sau solicită documente.</td></tr>
    <tr><td>Entitate activă</td><td>Entitatea pentru care s-a depus prin Serviciu cel puțin o declarație într-o lună calendaristică; stă la baza încadrării în Plan.</td></tr>
    <tr><td>Plan</td><td>pachetul de abonament ales de Beneficiar, definit prin numărul maxim de Entități active pe lună și prețul aferent, conform Anexei 1.</td></tr>
    <tr><td>Folderul urmărit</td><td>directorul de pe stația Beneficiarului din care Programul local preia automat fișierele de declarații.</td></tr>
    <tr><td>Utilizator autorizat</td><td>persoana desemnată de Beneficiar în Aplicația web, cu drepturi proprii de vizualizare, semnare sau depunere, pe fiecare Entitate.</td></tr>
    <tr><td>Stație autorizată</td><td>calculatorul de pe care Beneficiarul a permis autorizarea operațiunilor de semnare și depunere.</td></tr>
    <tr><td>DUKIntegrator / Validatoare</td><td>programele publicate de ANAF pentru validarea declarațiilor, folosite de Serviciu în forma publicată de ANAF.</td></tr>
    <tr><td>SPV Wizard</td><td>funcția Serviciului care interpretează mesajele de eroare ale Validatoarelor și propune corecții.</td></tr>
    <tr><td>Date ale Beneficiarului</td><td>fișierele declarațiilor, documentele și mesajele din SPV, datele Entităților, jurnalul operațiunilor și orice alte date introduse sau generate prin utilizarea Serviciului de către Beneficiar.</td></tr>
</table>

<h2>III. Obiectul contractului</h2>
<p>3.1. Prestatorul acordă Beneficiarului, pe durata Contractului, dreptul de utilizare a Serviciului SPV Curier,
în limitele Planului din Anexa 1, și prestează serviciile de instalare, suport și actualizare descrise în Anexa 3.</p>
<p>3.2. Serviciul cuprinde: (a) preluarea declarațiilor fiscale generate de programul de contabilitate al
Beneficiarului, în format XML sau PDF; (b) validarea lor cu Validatoarele ANAF; (c) verificările de consistență ale
fișierului SAF-T (D406) și reconcilierea acestuia cu decontul de TVA (D300); (d) generarea decontului de TVA din
fișierul SAF-T; (e) interpretarea erorilor de validare prin SPV Wizard; (f) semnarea declarațiilor cu Certificatul
Beneficiarului, pe Stația cu Tokenul; (g) depunerea în SPV și descărcarea recipiselor; (h) citirea mesajelor din SPV,
descărcarea documentelor și arhivarea lor pe stația Beneficiarului; (i) alerte pe e-mail; (j) solicitarea de documente
de la ANAF; (k) raportul obligațiilor declarative pe Entități; (l) accesul prin Aplicația mobilă.</p>
<p>3.3. Nu fac obiectul Contractului și nu sunt prestate de Prestator: consultanța fiscală, contabilă sau juridică;
ținerea contabilității; întocmirea declarațiilor (cu excepția decontului de TVA generat din SAF-T, la comanda
Beneficiarului); reprezentarea Beneficiarului în fața ANAF; furnizarea sau reînnoirea Certificatului; personalizările
sau dezvoltările la comandă, care se contractează separat, prin act adițional.</p>
<p>3.4. Serviciul este destinat exclusiv profesioniștilor. Beneficiarul declară că utilizează Serviciul în cadrul
activității sale profesionale, pentru Entitățile proprii sau, în cazul cabinetelor de contabilitate, pentru clienții
săi, pentru care deține împuternicirile necesare.</p>

<h2>IV. Durata contractului</h2>
<p>4.1. Contractul intră în vigoare la data semnării și se încheie pe o perioadă {{ $v($c->durata, 30) }}.</p>
<p>4.2. <strong>Perioada de probă.</strong> Primele 90 de zile de la activarea contului sunt gratuite, cu toate
funcțiile Planului deschise și cu instalarea primului Token inclusă. Beneficiarul poate înceta Contractul oricând în
această perioadă, prin notificare scrisă, fără nicio obligație de plată.</p>
<p>4.3. La expirarea perioadei contractuale, Contractul se prelungește automat pe perioade succesive egale, dacă
niciuna dintre Părți nu notifică celeilalte intenția de a nu-l prelungi cu cel puțin 30 de zile înainte de expirare.</p>

<h2>V. Prețul și modalitățile de plată</h2>
<p>5.1. Prețul Serviciului este cel al Planului ales, prevăzut în Anexa 1. Prețurile includ TVA.</p>
<p>5.2. Încadrarea în Plan se face lunar, după numărul de Entități active din luna respectivă. Nu se iau în calcul
Entitățile pentru care s-au citit doar mesaje din SPV sau s-au solicitat doar documente de la ANAF.</p>
<p>5.3. <strong>Depășirea limitei Planului.</strong> Dacă într-o lună numărul Entităților active depășește limita
Planului, Prestatorul notifică Beneficiarul, iar Părțile stabilesc de comun acord trecerea pe Planul potrivit. Nicio
trecere pe un alt Plan și nicio sumă suplimentară nu se aplică fără acordul Beneficiarului.</p>
<p>5.4. Facturarea se face {{ $c->periodicitate === 'anual' ? 'anual, în avans, la începutul perioadei' : 'lunar, în avans' }}.
La plata anuală, douăsprezece luni se facturează la prețul a zece.</p>
<p>5.5. Plata se face prin transfer bancar în contul Prestatorului, în termen de {{ $serviciu['zile_plata'] }} zile
calendaristice de la data facturii. Factura se transmite electronic la adresa de e-mail a Beneficiarului, ceea ce
Beneficiarul acceptă expres, și, acolo unde legea o cere, prin sistemul RO e-Factura.</p>
<p>5.6. Pentru întârzierea la plată, Beneficiarul datorează penalități de {{ $serviciu['penalitati_zi'] }} pe zi de
întârziere din suma neachitată, fără ca totalul penalităților să poată depăși suma datorată. Dacă întârzierea
depășește {{ $serviciu['zile_suspendare'] }} zile de la scadență, Prestatorul poate suspenda Serviciul conform
art. 13.3, după o notificare prealabilă de 5 zile.</p>
<p>5.7. Prestatorul poate modifica prețul Planurilor cu un preaviz scris de 30 de zile. Modificarea se aplică de la
următoarea perioadă de facturare; un abonament anual deja achitat nu se modifică până la expirarea sa. Beneficiarul
care nu acceptă noul preț poate denunța Contractul până la data intrării în vigoare a modificării, fără penalități.</p>
<p>5.8. Sumele achitate în avans pentru abonamentul anual nu se restituie în cazul denunțării Contractului de către
Beneficiar, cu excepția situațiilor prevăzute la art. 5.7 și 13.5.</p>

<h2>VI. Obligațiile Prestatorului</h2>
<p>6.1. Să asigure accesul Beneficiarului la Aplicația web și funcționarea Serviciului în condițiile de
disponibilitate din Anexa 3.</p>
<p>6.2. Să instaleze Programul local și să configureze primul Token, prin acces la distanță sau la sediul
Beneficiarului, în termen de {{ $serviciu['instalare_zile'] }} zile lucrătoare de la semnare, cu încheierea
procesului-verbal din Anexa 4.</p>
<p>6.3. Să actualizeze Serviciul la modificările publicate de ANAF privind structura declarațiilor, Validatoarele și
interfețele SPV, într-un termen rezonabil de la publicarea lor de către ANAF, fără cost suplimentar.</p>
<p>6.4. Să acorde suport tehnic conform Anexei 3.</p>
<p>6.5. Să nu acceseze, să nu copieze și să nu utilizeze Datele Beneficiarului decât în măsura necesară prestării
Serviciului, la cererea Beneficiarului pentru suport sau când legea o impune.</p>
<p>6.6. Să nu dețină, să nu copieze și să nu transmită cheia privată a Certificatului Beneficiarului; semnarea se
execută exclusiv pe Stația cu Tokenul.</p>
<p>6.7. Să anunțe Beneficiarul, cu cel puțin 48 de ore înainte, despre lucrările de mentenanță planificate care
afectează disponibilitatea și, fără întârziere nejustificată, despre orice incident de securitate care afectează
Datele Beneficiarului.</p>
<p>6.8. Să respecte obligațiile de persoană împuternicită prevăzute în Anexa 2.</p>

<h2>VII. Obligațiile Beneficiarului</h2>
<p>7.1. Să plătească prețul la termenele convenite.</p>
<p>7.2. Să asigure infrastructura proprie necesară: o stație de lucru cu sistem de operare
{{ $serviciu['sistem_operare'] }}, conexiune la internet, Tokenul cu Certificat valabil, drivere ale Tokenului și
Validatoarele ANAF instalate sau instalabile, precum și funcționarea continuă a stației pe care rulează Programul
local în intervalele în care dorește prelucrarea automată.</p>
<p>7.3. Să dețină și să mențină valabile Certificatul, drepturile de semnătură și împuternicirile înregistrate la
ANAF pentru fiecare Entitate. Beneficiarul răspunde exclusiv pentru înrolarea Entităților în SPV și pentru
consecințele depunerii pentru o Entitate pentru care nu deține dreptul de reprezentare.</p>
<p>7.4. Să desemneze Utilizatorii autorizați și să le configureze drepturile de semnare și depunere pe fiecare
Entitate; să autorizeze Stațiile; să păstreze confidențialitatea datelor de acces și a PIN-ului Tokenului. Orice
operațiune efectuată din contul unui Utilizator autorizat sau de pe o Stație autorizată se consideră efectuată de
Beneficiar.</p>
<p>7.5. Să verifice conținutul declarațiilor înainte de depunere. Beneficiarul răspunde integral pentru
corectitudinea, completitudinea, legalitatea și depunerea la termen a declarațiilor și a oricăror alte documente
transmise către ANAF prin Serviciu.</p>
<p>7.6. Să configureze fluxul de prelucrare potrivit nevoilor și răspunderii sale. Beneficiarul ia cunoștință că
activarea fluxului complet automat (preluare din Folderul urmărit — validare — semnare — depunere — arhivare)
determină depunerea declarațiilor fără o confirmare suplimentară și își asumă consecințele acestei configurări.</p>
<p>7.7. Să asigure păstrarea, salvarea de siguranță și securitatea arhivei locale de declarații, recipise și
documente, pe termenele prevăzute de legislația fiscală și contabilă.</p>
<p>7.8. Să utilizeze Serviciul cu bună-credință, numai în scopul prevăzut la art. 3, să nu îl revândă sau
sublicențieze, să nu îl decompileze, să nu încerce accesul la datele altor clienți ai Prestatorului și să nu îl
folosească pentru a dezvolta un produs concurent.</p>
<p>7.9. Să informeze persoanele ale căror date le prelucrează prin Serviciu și să dețină temeiul legal pentru această
prelucrare, conform Anexei 2.</p>

<h2>VIII. Certificatul digital, semnarea și depunerea</h2>
<p>8.1. Semnătura electronică aplicată declarațiilor este semnătura Beneficiarului, creată cu Certificatul acestuia,
pe Stația cu Tokenul, la comanda unui Utilizator autorizat sau în cadrul fluxului automat configurat de Beneficiar.
Prestatorul nu creează semnături electronice în numele Beneficiarului și nu este prestator de servicii de încredere.</p>
<p>8.2. Programul local transmite Aplicației web doar datele publice ale Certificatului (titular, emitent, serie,
valabilitate) și lista Entităților înrolate. PIN-ul Tokenului, atunci când Beneficiarul alege să îl trimită din
Aplicația web, se transmite criptat Programului local, se utilizează o singură dată, pentru operațiunea curentă, nu
se scrie pe disc și nu se înregistrează în jurnale.</p>
<p>8.3. Recipisa emisă de ANAF constituie singura dovadă a depunerii. Absența recipisei, indiferent de mesajele
afișate de Serviciu, înseamnă că declarația nu a fost depusă.</p>
<p>8.4. Validarea cu DUKIntegrator, verificările SAF-T, reconcilierea cu decontul de TVA, decontul generat din SAF-T
și explicațiile SPV Wizard sunt instrumente de asistență. Ele nu constituie consultanță fiscală, nu garantează lipsa
erorilor de fond și nu garantează acceptarea declarației de către ANAF.</p>
<p>8.5. Prestatorul nu răspunde pentru declarații nedepuse, respinse sau depuse cu întârziere din cauza:
(a) conținutului fișierelor furnizate de Beneficiar; (b) indisponibilității, erorilor sau modificărilor neanunțate ale
sistemelor sau Validatoarelor ANAF; (c) Certificatului expirat, revocat, fără drepturi sau fără împuternicire pentru
Entitate; (d) opririi Programului local, a stației sau a conexiunii la internet a Beneficiarului; (e) configurării
greșite a fluxului, a Utilizatorilor sau a Folderului urmărit de către Beneficiar; (f) neplății care a dus la
suspendare.</p>

<h2>IX. Nivelul serviciului și suportul</h2>
<p>9.1. Disponibilitatea, timpii de răspuns la solicitările de suport și programul de suport sunt prevăzute în
Anexa 3.</p>
<p>9.2. Prestatorul poate întrerupe temporar Serviciul pentru mentenanță planificată, în afara orelor de program, cu
notificarea prevăzută la art. 6.7. Intervențiile de urgență pentru securitate se pot face fără preaviz, cu informarea
ulterioară a Beneficiarului.</p>
<p>9.3. Suportul acoperă funcționarea Serviciului. Nu acoperă: interpretarea legislației fiscale, corectarea de fond
a declarațiilor, problemele programului de contabilitate al Beneficiarului, ale Tokenului, ale sistemului de operare
sau ale rețelei Beneficiarului, cu excepția asistenței rezonabile la diagnosticare.</p>

<h2>X. Confidențialitatea</h2>
<p>10.1. Fiecare Parte păstrează confidențialitatea informațiilor primite de la cealaltă Parte în legătură cu
Contractul — inclusiv Datele Beneficiarului, informațiile despre Entități, prețurile negociate, documentația tehnică
și modul de funcționare al Serviciului — și nu le divulgă terților fără acordul scris al celeilalte Părți, cu
excepția: angajaților, colaboratorilor și subcontractanților ținuți de obligații echivalente; consultanților juridici
și financiari; autorităților, când legea o impune, cu informarea celeilalte Părți dacă este permis.</p>
<p>10.2. Obligația de confidențialitate durează pe toată perioada Contractului și 3 ani după încetarea acestuia;
pentru Datele Beneficiarului, pe termen nelimitat.</p>
<p>10.3. Prestatorul poate menționa Beneficiarul ca client, prin denumire și logo, în materiale de prezentare, numai
cu acordul scris prealabil al Beneficiarului.</p>

<h2>XI. Protecția datelor cu caracter personal</h2>
<p>11.1. Pentru datele cu caracter personal cuprinse în declarații, în documentele și mesajele din SPV și în
solicitările către ANAF, Beneficiarul (sau, după caz, clientul final al acestuia) este operator, iar Prestatorul este
persoană împuternicită. Raportul dintre Părți este reglementat de Acordul de prelucrare din Anexa 2, care face parte
integrantă din Contract.</p>
<p>11.2. Pentru datele de contact, de cont și de facturare ale reprezentanților și Utilizatorilor Beneficiarului,
Prestatorul este operator și le prelucrează conform Politicii de confidențialitate publicate la spvcurier.ro.</p>

<h2>XII. Proprietatea intelectuală</h2>
<p>12.1. Serviciul, componentele sale, codul sursă și obiect, interfețele, documentația, denumirile SPV Curier și
SPV Wizard și toate drepturile de proprietate intelectuală aferente aparțin Prestatorului. Contractul nu transferă
niciun drept de proprietate intelectuală Beneficiarului.</p>
<p>12.2. Prestatorul acordă Beneficiarului o licență neexclusivă, netransferabilă și limitată la durata Contractului
de a utiliza Serviciul în scopul prevăzut la art. 3, pentru un număr nelimitat de Utilizatori, Tokenuri și Stații ale
Beneficiarului.</p>
<p>12.3. Datele Beneficiarului rămân proprietatea Beneficiarului. Prestatorul primește dreptul de a le prelucra
strict pentru prestarea Serviciului. Arhiva locală de declarații, recipise și documente aparține Beneficiarului și nu
este afectată de încetarea Contractului.</p>
<p>12.4. Validatoarele și DUKIntegrator sunt programe publicate de ANAF și rămân supuse condițiilor stabilite de
ANAF; Prestatorul nu răspunde pentru funcționarea lor.</p>
<p>12.5. Dezvoltările sau personalizările realizate la comanda Beneficiarului rămân proprietatea Prestatorului, care
poate să le includă în Serviciul oferit și altor clienți, cu excepția cazului în care actul adițional prevede altfel.
Beneficiarul primește dreptul de utilizare a acestora pe durata Contractului.</p>

<h2>XIII. Răspunderea contractuală. Suspendarea și încetarea</h2>
<p>13.1. <strong>Limitarea răspunderii.</strong> În limitele permise de lege, răspunderea totală a Prestatorului
față de Beneficiar, din orice cauză legată de Contract sau de Serviciu, este limitată la valoarea sumelor efectiv
plătite de Beneficiar în cele 12 luni anterioare evenimentului care a generat prejudiciul. Prestatorul nu răspunde
pentru beneficiul nerealizat, pierderi indirecte, pierderi de date de pe stațiile Beneficiarului, amenzi, penalități,
dobânzi sau majorări fiscale rezultate din conținutul declarațiilor, din nedepunerea sau depunerea cu întârziere din
cauzele prevăzute la art. 8.5, ori din decizii luate de Beneficiar pe baza verificărilor, rapoartelor sau explicațiilor
generate de Serviciu.</p>
<p>13.2. Limitările de mai sus nu se aplică prejudiciilor cauzate cu intenție sau din culpă gravă și nici încălcării
obligațiilor de confidențialitate sau de protecție a datelor.</p>
<p>13.3. <strong>Suspendarea.</strong> Prestatorul poate suspenda accesul la Serviciu, după notificare prealabilă de
5 zile: (a) pentru neplată, conform art. 5.6; (b) în caz de utilizare cu încălcarea art. 7.8; (c) dacă utilizarea
Serviciului de către Beneficiar pune în pericol securitatea sau funcționarea acestuia pentru alți clienți. Pe durata
suspendării, citirea SPV, semnarea și depunerea sunt oprite; arhiva locală nu este afectată. Suspendarea nu suspendă
obligația de plată.</p>
<p>13.4. <strong>Încetarea.</strong> Contractul încetează: (a) prin acordul Părților; (b) la expirarea duratei, dacă
nu a fost prelungit; (c) prin denunțare unilaterală de către oricare Parte, cu preaviz scris de 30 de zile, cu efect
de la sfârșitul perioadei de facturare în curs; (d) prin reziliere de către Partea neculpabilă, fără intervenția
instanței, dacă cealaltă Parte nu remediază o încălcare esențială în 15 zile de la notificare; pentru neplata care
depășește 30 de zile de la scadență, rezilierea operează de drept, prin simpla notificare; (e) prin dizolvarea,
falimentul sau insolvența oricăreia dintre Părți, în condițiile legii.</p>
<p>13.5. <strong>Încetarea Serviciului de către Prestator.</strong> Dacă Prestatorul decide întreruperea definitivă a
Serviciului, notifică Beneficiarul cu cel puțin 90 de zile înainte și restituie partea neutilizată din abonamentul
plătit în avans.</p>
<p>13.6. <strong>Efectele încetării.</strong> La încetare, accesul la Aplicația web se închide, iar Datele
Beneficiarului din infrastructura Prestatorului se șterg în termen de {{ $serviciu['zile_stergere'] }} de zile, cu
excepția celor a căror păstrare este impusă de lege. Înainte de ștergere, Beneficiarul poate exporta lista
Entităților, jurnalul operațiunilor și rapoartele, în formatele oferite de Aplicația web. Arhiva locală rămâne
intactă la Beneficiar. Încetarea nu afectează obligațiile de plată născute anterior și nici clauzele care, prin
natura lor, supraviețuiesc (confidențialitate, proprietate intelectuală, răspundere, litigii).</p>

<h2>XIV. Forța majoră</h2>
<p>14.1. Niciuna dintre Părți nu răspunde pentru neexecutarea obligațiilor cauzată de un eveniment de forță majoră,
în sensul art. 1351 Cod civil, inclusiv, fără limitare: indisponibilitatea prelungită a sistemelor ANAF, întreruperi
majore ale infrastructurii de internet sau energie, atacuri informatice de amploare, acte ale autorităților. Partea
afectată notifică cealaltă Parte în 5 zile de la apariția evenimentului și ia măsurile rezonabile pentru limitarea
efectelor.</p>
<p>14.2. Dacă evenimentul durează mai mult de 60 de zile, oricare Parte poate înceta Contractul prin notificare, fără
daune, cu regularizarea sumelor plătite în avans.</p>

<h2>XV. Notificări</h2>
<p>15.1. Orice notificare între Părți se face în scris, la adresele de e-mail din Contract, cu confirmare de primire,
sau prin scrisoare recomandată la sediile Părților. Notificările transmise prin Aplicația web au valoare de comunicare
scrisă pentru aspectele operaționale (mentenanță, depășirea Planului, alerte). Schimbarea datelor de contact se
comunică celeilalte Părți în 5 zile; până atunci, comunicările la datele vechi sunt valabile.</p>

<h2>XVI. Legea aplicabilă și litigiile</h2>
<p>16.1. Contractul este guvernat de legea română.</p>
<p>16.2. Părțile vor încerca soluționarea amiabilă a oricărui diferend în termen de 30 de zile de la notificarea
acestuia. În caz de nereușită, litigiul este de competența instanțelor judecătorești de la sediul Prestatorului.</p>

<h2>XVII. Clauze finale</h2>
<p>17.1. Contractul, împreună cu Anexele 1–4, reprezintă întreaga înțelegere a Părților și înlocuiește orice discuții,
oferte sau înțelegeri anterioare privind obiectul său. În caz de neconcordanță, Contractul prevalează asupra Anexelor,
iar acestea prevalează asupra Termenilor și condițiilor publicate la spvcurier.ro.</p>
<p>17.2. Modificarea Contractului se face prin act adițional semnat de ambele Părți, cu excepția modificării prețului
conform art. 5.7 și a actualizărilor Anexei 3 impuse de evoluția Serviciului, care se comunică cu preaviz de 30 de
zile și nu pot reduce nivelul de serviciu convenit.</p>
<p>17.3. Nulitatea unei clauze nu afectează restul Contractului; Părțile o vor înlocui cu o clauză valabilă, cât mai
apropiată de intenția inițială.</p>
<p>17.4. Beneficiarul nu poate cesiona Contractul fără acordul scris al Prestatorului. Prestatorul poate cesiona
Contractul unui succesor al activității sale, cu notificarea Beneficiarului.</p>
<p>17.5. Părțile declară că au negociat și înțeles fiecare clauză, inclusiv cele privind limitarea răspunderii,
suspendarea, rezilierea de drept și competența instanțelor, pe care le acceptă expres în sensul art. 1203 Cod civil.</p>
<p>17.6. Contractul a fost încheiat astăzi, {{ $data($c->data) }}, semnat electronic de ambele Părți, fiecare Parte
primind un exemplar electronic cu aceeași valoare juridică.</p>

<table class="semnaturi">
    <tr>
        <td><strong>PRESTATOR</strong></td>
        <td><strong>BENEFICIAR</strong></td>
    </tr>
    <tr>
        <td>
            {{ $prestator['denumire'] }}<br>
            Reprezentant: {{ $v($prestator['reprezentant'], 18) }}<br>
            Funcția: {{ $v($prestator['functie'], 12) }}
            <div class="rand-semnatura">Semnătura: ______________________<br>Data: ____________</div>
        </td>
        <td>
            {{ $v($c->beneficiar_denumire, 22) }}<br>
            Reprezentant: {{ $v($c->beneficiar_reprezentant, 18) }}<br>
            Funcția: {{ $v($c->beneficiar_functie, 12) }}
            <div class="rand-semnatura">Semnătura: ______________________<br>Data: ____________</div>
        </td>
    </tr>
</table>

<div class="pagina-noua"></div>
<h2>Anexa 1 — Planul și prețul</h2>
<p>1. Planul ales de Beneficiar: <strong>{{ $planAles ?: '............' }}</strong>.</p>
<table>
    <tr><th>Plan</th><th>Entități active / lună</th><th>Preț lunar (TVA inclus)</th><th>Preț anual (TVA inclus)</th></tr>
    @foreach ($planuri as $nume => $plan)
        <tr @if ($planAles === $nume) class="ales" @endif>
            <td>{{ $nume }}</td>
            <td>{{ $plan['entitati'] }}</td>
            <td>{{ $plan['lunar'] }}</td>
            <td>{{ $plan['anual'] }}</td>
        </tr>
    @endforeach
</table>
<p>2. Toate Planurile includ: Utilizatori, Tokenuri și Stații nelimitate; Aplicația web, Programul local și Aplicația
mobilă; actualizările la modificările ANAF; suportul din Anexa 3; instalarea primului Token.</p>
<p>3. Periodicitatea de facturare aleasă:
<strong>{{ $c->periodicitate === 'anual' ? 'anuală (12 luni la prețul a 10)' : ($c->periodicitate === 'lunar' ? 'lunară' : '............') }}</strong>.</p>
<p>4. Data activării contului și a începerii perioadei de probă de 90 de zile: {{ $data($c->data_activare) }}.
Data de la care începe facturarea: {{ $data($c->data_facturare) }}.</p>
<p>5. <strong>Program de recomandare:</strong> pentru fiecare client nou adus de Beneficiar, care încheie un abonament
plătit, atât Beneficiarul, cât și clientul recomandat primesc o lună de abonament gratuită, aplicată la următoarea
factură.</p>
<p>6. Servicii suplimentare, în afara Planului, facturate separat pe bază de ofertă acceptată: instalarea de Tokenuri
suplimentare la sediul Beneficiarului; personalizări și dezvoltări la comandă; instruire suplimentară.</p>

<div class="pagina-noua"></div>
<h2>Anexa 2 — Acord de prelucrare a datelor cu caracter personal</h2>
<p class="mic">încheiat în temeiul art. 28 din Regulamentul (UE) 2016/679 („GDPR")</p>

<h3>1. Rolurile Părților</h3>
<p>Beneficiarul este operator (sau, pentru Entitățile-clienți ale unui cabinet de contabilitate, persoană împuternicită
a acestora, caz în care Prestatorul este persoană împuternicită subsecventă). Prestatorul este persoană împuternicită
și prelucrează datele exclusiv în numele și pe instrucțiunile Beneficiarului.</p>

<h3>2. Obiectul, natura și scopul prelucrării</h3>
<p>Găzduirea Aplicației web; primirea, validarea, verificarea și reconcilierea fișierelor de declarații; transmiterea
comenzilor de semnare și depunere către Programul local; citirea listei mesajelor din SPV și descărcarea documentelor
către stația Beneficiarului; solicitările către ANAF; generarea rapoartelor și alertelor; suportul tehnic. Scopul:
îndeplinirea obligațiilor declarative ale Beneficiarului și ale Entităților sale.</p>

<h3>3. Categoriile de date și de persoane vizate</h3>
<p>Date de identificare (nume, CNP, adresă), date privind veniturile, contribuțiile și raporturile de muncă (de ex.
D112), date ale partenerilor comerciali persoane fizice (SAF-T, D394), date ale reprezentanților legali, conținutul
documentelor emise de ANAF (decizii, somații, fișe pe plătitor), datele Utilizatorilor autorizați. Persoane vizate:
angajați, colaboratori, clienți, furnizori și reprezentanți ai Beneficiarului și ai Entităților sale.</p>

<h3>4. Durata prelucrării</h3>
<p>Pe durata Contractului. La încetare, datele se șterg sau se returnează conform art. 13.6 din Contract.</p>

<h3>5. Obligațiile Prestatorului</h3>
<p>a) prelucrează datele numai pe baza instrucțiunilor documentate ale Beneficiarului, care constau în configurarea
Serviciului și comenzile date prin Aplicația web, Aplicația mobilă și Programul local; informează Beneficiarul dacă o
instrucțiune încalcă, în opinia sa, GDPR;</p>
<p>b) asigură că persoanele autorizate să prelucreze datele s-au angajat să respecte confidențialitatea;</p>
<p>c) implementează măsurile tehnice și organizatorice din secțiunea 7;</p>
<p>d) nu recrutează o altă persoană împuternicită fără autorizarea prealabilă, generală, a Beneficiarului, acordată
prin prezenta pentru subîmputerniciții din secțiunea 8; informează Beneficiarul cu cel puțin 30 de zile înainte despre
orice adăugare sau înlocuire, Beneficiarul putând obiecta în scris, caz în care Părțile caută o soluție sau
Beneficiarul poate denunța Contractul fără penalități;</p>
<p>e) asistă Beneficiarul, prin măsuri tehnice și organizatorice adecvate, în îndeplinirea obligației de a răspunde
cererilor persoanelor vizate;</p>
<p>f) asistă Beneficiarul în asigurarea securității prelucrării, în notificarea încălcărilor și în evaluările de
impact, ținând seama de natura prelucrării și de informațiile de care dispune;</p>
<p>g) notifică Beneficiarul, fără întârziere nejustificată și în cel mult 48 de ore de la luarea la cunoștință,
despre orice încălcare a securității datelor, cu informațiile necesare notificării autorității;</p>
<p>h) la încetarea Serviciului, șterge sau returnează toate datele, la alegerea Beneficiarului, și șterge copiile
existente, cu excepția celor a căror păstrare este impusă de lege;</p>
<p>i) pune la dispoziția Beneficiarului informațiile necesare pentru a demonstra respectarea art. 28 GDPR și permite
audituri, inclusiv inspecții, efectuate de Beneficiar sau de un auditor mandatat, cu preaviz de 15 zile lucrătoare,
cel mult o dată pe an, fără afectarea securității altor clienți; costurile auditului sunt suportate de Beneficiar.</p>

<h3>6. Obligațiile Beneficiarului</h3>
<p>Beneficiarul garantează că deține temeiul legal pentru prelucrarea datelor introduse în Serviciu, că a informat
persoanele vizate și că instrucțiunile sale sunt conforme cu legea. Beneficiarul răspunde pentru configurarea
drepturilor Utilizatorilor și pentru securitatea stațiilor și a arhivei locale.</p>

<h3>7. Măsuri de securitate</h3>
<p>Cheia privată a Certificatului nu părăsește Tokenul; semnarea se face pe Stația Beneficiarului. Comunicațiile
între browser, Aplicația mobilă, Programul local și server sunt criptate (TLS). Legătura Programului local cu serverul
este inițiată de Programul local, prin tunel autentificat, fără porturi deschise la Beneficiar. Documentele descărcate
din SPV se arhivează pe stația Beneficiarului, iar copia de lucru de pe server se șterge după arhivare; fișierele de
lucru ale declarațiilor rămân pe server până la semnare. Accesul la Aplicația web se face pe bază de conturi
individuale și roluri, cu jurnalizarea operațiunilor. Accesul personalului Prestatorului la Datele Beneficiarului se
face numai pentru suport, la cererea Beneficiarului, și este jurnalizat.</p>

<h3>8. Subîmputerniciți autorizați</h3>
<table>
    <tr><th>Subîmputernicit</th><th>Serviciu</th><th>Locația prelucrării</th></tr>
    @foreach ($subimputerniciti as $s)
        <tr><td>{{ $s['nume'] }}</td><td>{{ $s['serviciu'] }}</td><td>{{ $s['locatie'] }}</td></tr>
    @endforeach
</table>

<h3>9. Transferuri</h3>
<p>Datele sunt prelucrate și stocate în Uniunea Europeană. Niciun transfer în afara Spațiului Economic European nu se
efectuează fără acordul prealabil al Beneficiarului și fără garanțiile prevăzute de Capitolul V GDPR.</p>

<h3>10. Răspunderea</h3>
<p>Fiecare Parte răspunde pentru încălcarea propriilor obligații din prezenta Anexă, în condițiile art. 82 GDPR.
Limitarea răspunderii din art. 13.1 al Contractului nu se aplică sancțiunilor aplicate de autoritatea de supraveghere
pentru încălcări imputabile Prestatorului.</p>

<div class="pagina-noua"></div>
<h2>Anexa 3 — Nivelul serviciului și suportul</h2>
<table>
    <tr><th style="width:34%">Element</th><th>Nivel convenit</th></tr>
    <tr><td>Disponibilitatea Aplicației web</td><td>{{ $serviciu['disponibilitate'] }} lunar, calculată excluzând mentenanța planificată și cauzele externe (ANAF, internet, forță majoră)</td></tr>
    <tr><td>Mentenanță planificată</td><td>În afara intervalului 8:00–20:00 în zilele lucrătoare, cu anunț de minimum 48 de ore</td></tr>
    <tr><td>Program de suport</td><td>Luni–vineri, {{ $serviciu['program_suport'] }}, e-mail {{ $prestator['email'] }} și telefon {{ $prestator['telefon'] }}</td></tr>
    <tr><td>Timp de răspuns — blocant (semnare/depunere imposibilă pentru toate Entitățile)</td><td>{{ $serviciu['raspuns_blocant'] }} lucrătoare de la sesizare; intervenție continuă până la remediere sau soluție temporară</td></tr>
    <tr><td>Timp de răspuns — major (o funcție principală nu merge, există ocolire)</td><td>{{ $serviciu['raspuns_major'] }} lucrătoare</td></tr>
    <tr><td>Timp de răspuns — minor (întrebări, defecte cosmetice, cereri)</td><td>{{ $serviciu['raspuns_minor'] }} lucrătoare</td></tr>
    <tr><td>Actualizări ANAF</td><td>Incluse; la modificarea structurilor de declarații sau a Validatoarelor, Prestatorul actualizează Serviciul într-un termen rezonabil de la publicarea de către ANAF, informând Beneficiarul</td></tr>
    <tr><td>Instalare primul Token</td><td>Inclusă; la distanță sau la sediu; în {{ $serviciu['instalare_zile'] }} zile lucrătoare de la semnare</td></tr>
    <tr><td>Instruire inițială</td><td>O sesiune de {{ $serviciu['instruire_minute'] }} de minute, la distanță, pentru Utilizatorii Beneficiarului</td></tr>
    <tr><td>Remediu la nerespectarea disponibilității</td><td>Pentru fiecare lună în care disponibilitatea scade sub nivelul convenit din cauze imputabile Prestatorului, Beneficiarul primește o reducere de {{ $serviciu['reducere_indisponibilitate'] }} din abonamentul lunii următoare, la cerere scrisă transmisă în 30 de zile; acesta este singurul remediu pentru indisponibilitate</td></tr>
</table>
<p class="mic">Timpii de răspuns se măsoară de la înregistrarea sesizării prin e-mail sau telefon, în programul de
suport. Sesizările din afara programului se consideră primite la începutul următoarei zile lucrătoare.</p>

<div class="pagina-noua"></div>
<h2>Anexa 4 — Proces-verbal de instalare și recepție</h2>
<p>Încheiat astăzi, ............................, între Prestator, prin ............................, și Beneficiar,
prin ............................, cu ocazia instalării Programului local și a configurării primului Token.</p>
<table>
    <tr><th style="width:34%">Element</th><th>Detalii</th></tr>
    <tr><td>Stația de lucru</td><td>&nbsp;</td></tr>
    <tr><td>Token / Certificat</td><td>&nbsp;</td></tr>
    <tr><td>Entități înrolate sincronizate</td><td>&nbsp;</td></tr>
    <tr><td>Folder urmărit</td><td>&nbsp;</td></tr>
    <tr><td>Flux configurat</td><td>&#9744; cu confirmare la semnare/depunere &nbsp;&nbsp; &#9744; complet automat</td></tr>
    <tr><td>Utilizatori creați</td><td>&nbsp;</td></tr>
    <tr><td>Test efectuat</td><td>&nbsp;</td></tr>
    <tr><td>Alerte configurate</td><td>&nbsp;</td></tr>
    <tr><td>Observații</td><td>&nbsp;</td></tr>
</table>
<p>Beneficiarul confirmă că Serviciul a fost instalat și funcționează conform Contractului, că a primit instruirea
inițială și că a ales configurația fluxului de mai sus.</p>

<table class="semnaturi">
    <tr>
        <td><strong>PRESTATOR</strong><div class="rand-semnatura">Nume: ______________________<br>Semnătura: __________________</div></td>
        <td><strong>BENEFICIAR</strong><div class="rand-semnatura">Nume: ______________________<br>Semnătura: __________________</div></td>
    </tr>
</table>

@if ($c->observatii)
    <p class="mic" style="margin-top:6mm">Observații interne: {{ $c->observatii }}</p>
@endif

</body>
</html>
