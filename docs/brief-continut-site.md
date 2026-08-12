# Brief de conținut — site de prezentare Punte

Document de lucru pentru textele site-ului. Fiecare afirmație are trimitere la
locul din cod care o susține (`fișier:linie`). Ce nu apare aici nu are suport în
cod și nu trebuie scris pe site.

**Surse parcurse:** `routes/api.php`, `routes/api_routes/`, `app/Http/Controllers/Api/`,
`app/Console/Commands/`, `app/Console/Kernel.php`, `app/Services/`, `app/Http/Middleware/`,
`database/migrations/`, `config/anaf.php`, `config/portaljust.php`, `.env.example`,
`README.md`, `ETRANSPORT_README.md`.

**Observație despre `app/Jobs/`:** directorul nu există în proiect. Nu există cozi
de joburi pentru modulele Punte; lucrul periodic se face prin comenzi programate
(`app/Console/Kernel.php:26-59`). Nu scrieți pe site „procesare în cozi”.

---

## 0. Platforma — ce e comun celor trei module

Aceste fapte se aplică tuturor modulelor; se pot folosi în pagina de prezentare
generală, ca să nu fie repetate la fiecare modul.

### 0.1 Capabilități reale

- **Trei module, acordate separat prin abonament**, cu perioadă de probă și dată
  de plată; la expirare modulele se închid, datele rămân — `app/Models/AbonamentClient.php:33-66`,
  `app/Http/Middleware/ModulPermis.php:22-51`. Răspunsul spune și motivul
  (`402 abonament expirat`, `403 modul lipsă`) — `app/Http/Middleware/ModulPermis.php:35-48`.
- **Separare pe clienți (multi-tenant)**: fiecare cerere lucrează în contextul
  societății selectate, iar apartenența utilizatorului la ea se verifică —
  `app/Http/Middleware/CompanieAnaf.php:22-88`, `routes/api.php:97,177,191,201`.
- **Separare pe utilizatori în interiorul firmei**: utilizatorul obișnuit vede
  doar declarațiile și solicitările lui, plus mesajele SPV ale certificatelor la
  care are drept; administratorul firmei vede tot — `app/Support/ContextUtilizator.php:97-141`.
- **Drepturi de semnare și de depunere, date pe fiecare om și pe fiecare firmă**
  — `app/Support/ContextUtilizator.php:64-91`,
  `database/migrations/2026_08_01_100000_add_drepturi_declaratii_to_company_user.php:19-22`.
  Refuzul e pe server, nu doar ascundere în interfață — `app/Http/Controllers/Api/DeclaratiiController.php:461-465,517-521`.
- **Restricționare pe adresă IP per cont**, cu adresă, interval CIDR sau prefix;
  se verifică la autentificare și la fiecare cerere — `app/Services/AccesIp.php:26-186`,
  `app/Http/Controllers/Api/AuthController.php:55-77`, `app/Http/Middleware/CompanieAnaf.php:42-47`.
  La încercarea de la o adresă nepermisă pleacă email către administratorul
  aplicației — `app/Http/Controllers/Api/AuthController.php:79-99`.
- **Jurnal de activitate** pe fiecare operațiune (cine, ce, pentru ce CUI,
  reușit sau nu), cu export — `app/Services/Anaf/Jurnal.php`,
  `app/Http/Controllers/Api/JurnalAnafController.php:17,40`.
- **Autentificare OAuth2 (Laravel Passport)**, cu limitare la 60 de cereri/minut
  pe ruta de login — `routes/api.php:21,24`.
- **Programul local („bridge”) pentru certificatul de pe token USB**, cu:
  - kit de instalare descărcabil din aplicație — `app/Services/Anaf/Spv/KitBridge.php:97`,
    `app/Http/Controllers/Api/CertificateController.php:187`;
  - licență semnată RSA, legată de mașina clientului, valabilă 30 de zile —
    `app/Services/Anaf/Bridge/Licente.php:29-36,113-134`;
  - reînnoire automată cu 10 zile înainte de expirare, numai cât timp abonamentul
    e în regulă — `app/Services/Anaf/Bridge/LicentiereBridge.php:20,41`, rulată
    zilnic la 07:30 — `app/Console/Kernel.php:58`;
  - reînnoire la cerere, din interfață — `routes/api.php:153`,
    `app/Http/Controllers/Api/CertificateController.php:165`.
- **Punte HTTPS către programele locale aflate în spatele routerului**: agentul
  de la client întreabă serverul (long-poll), iar aplicația cheamă puntea ca pe
  un program local — `app/Services/Anaf/Bridge/Punte.php:20-26,42-56,116,174,203`,
  `app/Http/Controllers/Api/PunteController.php:43,106,138,174,194`, `routes/api.php:41-48`.
- **Arhivă de documente pe calculatorul clientului**, în structura
  `<rădăcină>\<Denumire firmă (CUI)>\<TIP>\...`; copia de lucru de pe server se
  poate șterge după arhivare — `config/anaf.php:176-201`, `app/Services/Anaf/Arhiva/ArhivaService.php:59,79`.
- **Notificări în aplicație și pe email** către utilizatori, trimise de
  administratorul serviciului — `app/Http/Controllers/Api/NotificariController.php:30,107,221`,
  `routes/api.php:69-79`.

### 0.2 Beneficiu

- Fiecare client plătește doar modulele pe care le folosește, iar oprirea la
  neplată e automată — nu cere intervenție manuală.
- Documentele fiscale rămân pe calculatorul clientului, chiar dacă aplicația e
  în cloud (`config/anaf.php:176-201`) — răspuns direct la obiecția „nu-mi urc
  actele la nimeni”.
- Certificatul de pe tokenul USB rămâne la client; semnarea se face acolo, nu pe
  server (`app/Services/Anaf/Declaratii/SemnareService.php:24-51`).
- Dreptul de semnare și cel de depunere pot fi date separat — un operator poate
  pregăti declarațiile fără să le poată trimite la ANAF.
- Jurnalul răspunde la „cine a depus asta și când” fără reconstituiri.

### 0.3 Diferențiator

- Puntea peste HTTPS elimină nevoia de VPN, IP fix sau porturi deschise la
  client: legătura o deschide agentul din rețeaua lui — `app/Services/Anaf/Bridge/Punte.php:35-56`.
  Când agentul n-a mai întrebat de 150 de secunde, cererea e refuzată pe loc, în
  loc să țină omul în așteptare — `app/Services/Anaf/Bridge/Punte.php:42-59`.
- Licențierea semnată RSA, legată de mașină, cu expirare la 30 de zile și
  reînnoire condiționată de abonament: programul local se oprește singur la
  client, fără intervenție — `app/Services/Anaf/Bridge/Licente.php:113-134`,
  `app/Services/Anaf/Bridge/LicentiereBridge.php:41`.
- Kitul de instalare include PHP-ul minimal necesar, generat din aplicație —
  `app/Console/Commands/PregatestePhpBridge.php`, `app/Services/Anaf/Spv/KitBridge.php:16-37`.

### 0.4 Limite

- Nu există procesare în cozi/joburi asincrone; operațiunile lungi se fac în
  cererea HTTP sau în comenzile programate (nu există `app/Jobs/`).
- Drepturile fine pe operațiuni SPV (verificareMesajeSpv, creareSolicitariSpv,
  incarcareDeclaratiiAnaf, vizualizareJurnalAnaf) există ca mecanism, dar sunt
  **dezactivate pe rute**: accesul se dă pe apartenența la societate plus
  drepturile de semnare/depunere — `routes/api.php:99-105`. **[PARȚIAL IMPLEMENTAT]**
- Programul local rulează pe Windows (PowerShell, `.bat`, iTextSharp) —
  `app/Services/Anaf/Spv/KitBridge.php:16-37`.
- Nu există facturare/încasare automată a abonamentului: datele de abonament se
  scriu manual de administratorul serviciului — `routes/api.php:64`,
  `app/Http/Controllers/Api/AdministrareController.php`.

---

## 1. SPV Curier

Modul: `modul:spv` (`routes/api.php:97`). Interfață: „Certificate digitale”,
„Entități înrolate”, „Declarații fiscale”, „Mesaje ANAF”, „Solicitări ANAF”
(`resources/js/src/views/app_pages/Spv.vue:47-59`).

### 1.1 Capabilități reale

**Mesaje SPV**
- Citirea listei de mesaje din SPV pe o fereastră de zile (maximum 60, limită
  ANAF), opțional pe un singur CIF — `app/Services/Anaf/Spv/SpvClient.php:19-27`,
  `config/anaf.php:19`. Endpoint: `https://webserviced.anaf.ro/SPVWS2/rest` +
  `/listaMesaje` — `config/anaf.php:5`, `app/Services/Anaf/Spv/SpvClient.php:27`.
- Mesajele se salvează local și se marchează care sunt cu adevărat noi (lista
  ANAF întoarce toată fereastra la fiecare citire) — `app/Http/Controllers/Api/SpvController.php:29-40`,
  `app/Services/Anaf/Spv/SpvStorage.php:163`.
- Descărcarea automată a fișierelor lipsă, în loturi de maximum 20 pe cerere,
  cu maximum 3 încercări per mesaj și pauză impusă între apeluri —
  `config/anaf.php:28-30`, `app/Http/Controllers/Api/SpvController.php:139-190`,
  `app/Services/Anaf/Spv/SpvClient.php:106`.
- Recipisele și răspunsurile la solicitări nu se descarcă din fila de mesaje:
  se aduc din filele lor, ca să nu se consume de două ori din limita ANAF —
  `config/anaf.php:32-49`, `app/Http/Controllers/Api/SpvController.php:146-152`.
- Reîncărcarea filei nu interoghează ANAF; există rută separată pentru mesajele
  deja stocate — `routes/api.php:108`, `app/Http/Controllers/Api/SpvController.php:94`.
- Documentul descărcat se pune și în arhiva de la client, în dosarul firmei —
  `app/Services/Anaf/Spv/SpvStorage.php:48`.
- **Alerte pe email la intrarea unui document nou în SPV**, filtrate pe tip de
  document și pe certificat; potrivirea e pe bucată de text, fără sensibilitate
  la majuscule — `app/Services/Anaf/Spv/AlerteMesaje.php:27-60`, `config/anaf.php:51-84`,
  `routes/api.php:136-139`.

**Declarații fiscale**
- Încărcare de fișiere XML sau PDF, câte mai multe deodată, maximum 50 MB pe
  fișier — `app/Http/Controllers/Api/DeclaratiiController.php:84-96`, `routes/api.php:123`.
- Din PDF-ul de declarație ANAF se extrage XML-ul atașat; dacă PDF-ul e deja
  semnat, trece direct la depunere — `app/Http/Controllers/Api/DeclaratiiController.php:163-186`,
  `app/Services/Anaf/Declaratii/PdfDeclaratie.php:29`.
- Identificarea tipului declarației din namespace, rădăcină sau reguli generice,
  validată față de lista validatoarelor instalate — **174 de tipuri** (D100,
  D112, D300, D390, D394, D406/SAF-T, bilanțuri S1001–S1128, A4200 ș.a.) —
  `app/Services/Anaf/Declaratii/DeclaratieXml.php:21-41,98,179`.
- Validare și generare PDF oficial cu **DUKIntegrator** (programul ANAF), cu
  timeout de 180 s — `app/Services/Anaf/Declaratii/DukIntegrator.php:33-91`,
  `config/anaf.php:205-215`.
- **D406/SAF-T se validează cu perioada raportată** (an, lună, tip L/T/A),
  printr-un lansator separat care cheamă validatorul ANAF cu acești parametri —
  `app/Services/Anaf/Declaratii/DukIntegrator.php:17,50-53,116-160`,
  `app/Services/Anaf/Declaratii/DeclaratieXml.php:353-433`, `config/anaf.php:214`.
- **Explicarea erorilor validatorului** în limbaj obișnuit, cu localizarea
  atributului în XML; până la 300 de probleme explicate per declarație —
  `app/Services/Anaf/Declaratii/InterpretareErori.php:22-68,565-1206`, `routes/api.php:132`.
- **Semnare cu certificatul de pe tokenul USB**, prin programul local; poziția
  casetei vizibile se hotărăște pe server (ultima pagină, jos-dreapta, A4) —
  `app/Services/Anaf/Declaratii/SemnareService.php:24-77`, `config/anaf.php:216-240`.
- **Depunere la ANAF** prin `https://decl.anaf.mfinante.gov.ro` (autentificare
  `/decl/login`, încărcare `/decl/upload`), cu extragerea indexului de încărcare
  din răspunsul HTML — `app/Services/Anaf/Declaratii/DepunereService.php:24-105`,
  `config/anaf.php:241`.
- **Recipise**: se caută mesajul SPV de tip RECIPISA după indexul de încărcare;
  când nu există, se interoghează starea publică pe
  `https://stare.anaf.ro/StareD112/vizualizareStare.do` (fără certificat) —
  `app/Services/Anaf/Declaratii/RecipisaService.php:45-190`, `config/anaf.php:244-245`.
  Verdictul ANAF se citește din textul PDF-ului recipisei —
  `app/Services/Anaf/Declaratii/RecipisaService.php:191`.
- **Tipărire la imprimanta clientului**, prin programul local, cu unirea mai
  multor documente într-un singur PDF și filigran opțional cu denumirea firmei —
  `app/Services/Anaf/Declaratii/ConcatenareService.php:41-119`, `routes/api.php:126`.
- **Dosar urmărit**: declarațiile puse într-un folder de pe calculatorul
  clientului sunt preluate, înregistrate, validate, semnate și arhivate automat,
  **din 5 în 5 minute** — `app/Console/Kernel.php:48-50`,
  `app/Services/Anaf/Declaratii/MonitorizareFolder.php:67-132,150-181`.
  Fișierele abia copiate se lasă pentru rularea următoare, ca să nu fie citite pe
  jumătate — `app/Services/Anaf/Declaratii/MonitorizareFolder.php:128-147`.
  Fișierele reușite se mută în `prelucrate`, cele picate în `erori`, iar pe email
  pleacă înștiințare către oamenii certificatului firmei —
  `app/Services/Anaf/Declaratii/MonitorizareFolder.php:95-121,428-467`.

**Solicitări SPV**
- Cerere de documente prin webserviciul `/cerere`, pentru **35 de tipuri**
  configurate cu parametrii ceruți de ANAF (an, lună, motiv, număr înregistrare,
  CUI punct de lucru): Fișă Rol, Vector Fiscal, Situație Sintetică, Istoric
  declarații, Bilanț anual/semestrial, Adeverințe Venit, Duplicat Recipisă,
  neconcordanțe D112/D394 ș.a. — `config/anaf.php:86-129`,
  `app/Http/Controllers/Api/SpvSolicitariController.php:58-127`.
- Se poate cere același document pentru mai multe CUI-uri dintr-o dată —
  `app/Http/Controllers/Api/SpvSolicitariController.php:63-64,93-101`.
- Parametrii obligatorii pentru tipul ales sunt verificați înainte de a se
  trimite ceva la ANAF — `app/Http/Controllers/Api/SpvSolicitariController.php:75-88`.
- Răspunsurile se caută în SPV și se descarcă automat, apoi se interpretează —
  `app/Services/Anaf/Spv/SolicitareService.php:64-160`, `routes/api.php:117`.

**Certificate, entități, vector fiscal**
- Descoperirea certificatelor de pe tokenul clientului și înrolarea lor —
  `app/Services/Anaf/Spv/CertificatService.php:246-377`, `routes/api.php:148`.
- Sincronizarea societăților pentru care certificatul are drept de semnătură,
  din răspunsul ANAF — `app/Services/Anaf/Spv/SocietatiService.php:35-121`,
  `routes/api.php:162`, comandă: `app/Console/Commands/SincronizeazaSocietati.php:18-23`.
- **Avertizare pe email înainte de expirarea certificatului digital**, zilnic la
  08:00, cu 30 de zile înainte (configurabil) și reamintire la 7 zile —
  `app/Console/Kernel.php:36`, `config/anaf.php:169-174`,
  `app/Console/Commands/AvertizeazaExpirareCertificate.php:25-90`.
- **Vector fiscal**: obligațiile așteptate (editabile) față de cele citite din
  documentul SPV, plus situația pe lună — ce ar fi trebuit depus și ce s-a depus —
  `app/Http/Controllers/Api/VectorFiscalController.php:71-138`,
  `app/Services/Anaf/Spv/VectorFiscalParser.php:21-133`, `routes/api.php:167-172`.
- Citirea automată a denumirii firmei și a obligațiilor restante din documentele
  SPV — `app/Services/Anaf/Spv/VectorFiscalParser.php:121,134`.

### 1.2 Beneficiu

| Capabilitate | Efect pentru client |
|---|---|
| Descărcare automată a mesajelor, în loturi, cu reîncercări (`SpvController.php:139-190`) | Nu se stă la ANAF să se apese pe fiecare mesaj; limita de apeluri nu se consumă degeaba |
| Alerte pe email pe tip de document (`AlerteMesaje.php:27`) | O somație sau o decizie de impunere nu mai stă necitită în SPV până la termen |
| Avertizare la expirarea certificatului (`AvertizeazaExpirareCertificate.php`) | Nu se ajunge în ziua depunerii cu tokenul expirat |
| Validare cu DUKIntegrator înainte de depunere (`DukIntegrator.php:33`) | Declarația respinsă se află înainte de termen, nu după |
| D406 validat cu perioada raportată (`DukIntegrator.php:116`) | Se elimină erorile false de nomenclator, care altfel par respingeri reale |
| Explicarea erorilor cu localizare în XML (`InterpretareErori.php:39`) | Corectarea nu mai cere citirea raportului brut al validatorului |
| Dosar urmărit, din 5 în 5 minute (`Kernel.php:48`) | Declarațiile scoase din programul de contabilitate ajung validate și semnate fără ca cineva să le încarce |
| Email la eșec în dosarul urmărit (`MonitorizareFolder.php:428`) | O declarație picată nu rămâne nedescoperită până la termen |
| Situația vector fiscal pe lună (`VectorFiscalController.php:94`) | Se vede ce obligație lipsește înainte de scadență — risc direct de amendă |
| Recipise verificate automat, inclusiv pe canalul public (`RecipisaService.php:45`) | Confirmarea depunerii nu mai cere căutare manuală în SPV |
| Tipărire la imprimanta clientului, cu filigran (`ConcatenareService.php:41`) | Teancul de hârtie iese sortat pe firme, dintr-o apăsare |
| Documentele rămân în arhiva clientului (`ArhivaService.php:59`) | Nu se schimbă locul unde stau actele firmei |

### 1.3 Diferențiator

- **Lansator propriu pentru D406/SAF-T** (`tools/duk-d406`, apelat din
  `app/Services/Anaf/Declaratii/DukIntegrator.php:116-160`): DUKIntegrator apelat
  din linia de comandă nu primește perioada raportată, iar declarația se compară
  cu nomenclatoarele vechi. Fără această piesă, validarea D406 raportează erori
  care nu există (`.env.example:87-90`).
- **Interpretor de erori cu 6 familii de tipare** (identificatori, valori,
  atribute, structură, reguli, erori fatale) și localizare în XML —
  `app/Services/Anaf/Declaratii/InterpretareErori.php:565-1206` (peste 1200 de
  linii de reguli scrise pentru mesajele validatorului ANAF).
- **Detectarea tipului de declarație** pentru rădăcini inconsecvente
  (`auditfile`→D406, `mReg`→A4200, `msj` ambiguu rezolvat din namespace) —
  `app/Services/Anaf/Declaratii/DeclaratieXml.php:46-70,179-252`.
- **Respectarea limitelor ANAF**: pauză de 1200 ms între apeluri, fereastră
  maximă de 60 de zile, lot de 20 de descărcări, 3 încercări per mesaj, sărirea
  tipurilor care se aduc din alte file — `config/anaf.php:19-49`,
  `app/Services/Anaf/Spv/SpvClient.php:106`.
- **Recipisă pe două căi**: mesaj SPV, iar în lipsa lui pagina publică
  StareD112, cu tratarea explicită a răspunsului „nu este document valid” —
  `app/Services/Anaf/Declaratii/RecipisaService.php:100-190`.
- **Dosarul urmărit prinde orice excepție per fișier**, mută fișierul în `erori`
  și continuă lotul — o declarație picată nu oprește restul —
  `app/Services/Anaf/Declaratii/MonitorizareFolder.php:104-121`.
- **Mesajele de eroare de la ANAF sunt trecute mai departe ca atare**, prin
  toate verigile (punte, agent, program local), în loc de un cod de stare —
  `app/Services/Anaf/Spv/SpvClient.php:108-127`.

### 1.4 Limite

- Depunerea la ANAF **nu se face automat** din dosarul urmărit: pipeline-ul se
  oprește la semnare și arhivare — `app/Services/Anaf/Declaratii/MonitorizareFolder.php:150-181`.
  Depunerea e o acțiune cerută explicit, de un utilizator cu acest drept.
- Semnarea și depunerea cer **programul local pornit și tokenul conectat**;
  fără el operațiunea eșuează cu mesaj — `app/Services/Anaf/Declaratii/SemnareService.php:30-46`.
- Fereastra de interogare SPV e limitată la **60 de zile** de ANAF — `config/anaf.php:19`.
- Se prelucrează **XML și PDF**; orice altă extensie e refuzată în dosarul
  urmărit — `app/Services/Anaf/Declaratii/MonitorizareFolder.php:165-169`.
- Lista tipurilor de mesaje SPV **nu e un nomenclator oficial** — ANAF nu publică
  unul; potrivirea alertelor e pe text — `config/anaf.php:51-61`.
- Filigranul se aplică deocamdată **doar pe recipise**, nu și pe declarații —
  `resources/js/src/views/app_pages/spv/Declaratii.vue:1404` (parametrul
  `filigran` e trimis numai pentru tipul `recipisa`).
- Nu există e-Factura în acest modul: rutele `/efacturaparams`, `/gettoken`,
  `/callback` (`routes/api.php:25-27`) țin de altă parte a aplicației, în afara
  celor trei module Punte. **[ÎN AFARA MODULULUI]**

### 1.5 Termeni tehnici de folosit (căutați de clienți)

SPV (Spațiul Privat Virtual), mesaje SPV, `listaMesaje`, `/cerere`, `/descarcare`,
recipisă, index de încărcare, DUKIntegrator, validare XML, D100, D112, D300,
D301, D390, D394, D406, SAF-T, bilanț (S1001–S1128), A4200 (aparate de marcat),
vector fiscal, fișă rol, situație sintetică, certificat de atestare fiscală,
somație, titlu executoriu, poprire, decizie de impunere, certificat digital
calificat, token USB, semnătură electronică, thumbprint, StareD112, CUI/CIF,
entitate înrolată, drept de semnătură.

---

## 2. Dispecer e-Transport

Modul: `modul:etransport` (`routes/api.php:177`). Interfață:
`resources/js/src/views/app_pages/Etransport.vue:384`.

### 2.1 Capabilități reale

- **Depunerea declarației de transport** (fișier XML, maximum 10 MB), pe
  standardul `ETRANSP`, versiunea 1 sau 2 a serviciului — `app/Http/Controllers/Api/EtransportAnafController.php:151-188`,
  `app/Services/Anaf/Etransport/EtransportClient.php:37-49`, `config/anaf.php:146-148`.
- **Două căi de autentificare, comutabile din configurație**:
  - `certificat` — apel prin programul local, cu certificatul de pe token, către
    `https://webserviceapl.anaf.ro/prod/ETRANSPORT/ws/v1` — `config/anaf.php:133,141`,
    `app/Services/Anaf/Etransport/EtransportClient.php:100-119`;
  - `oauth` — apel direct la `https://api.anaf.ro/prod/ETRANSPORT/ws/v1`, cu
    autorizare OAuth2 ANAF — `config/anaf.php:142`,
    `app/Services/Anaf/Etransport/EtransportClient.php:121-154`.
- **Fluxul OAuth2 complet**: URL de autorizare cu `state` semnat, întoarcerea
  browserului pe rută publică, schimbul codului pe token, reînnoirea automată cu
  2 zile înainte de expirare — `app/Services/Anaf/Oauth/OauthAnaf.php:25-136,173-200`,
  `routes/api.php:32,179-180`. Endpoint-uri:
  `https://logincert.anaf.ro/anaf-oauth2/v1/authorize` și `/token` — `config/anaf.php:165-166`.
- **Sincronizarea notificărilor** pe o fereastră de până la 60 de zile, cu
  păstrarea lor locală; reinterogarea nu dublează înregistrările (cheie: UIT +
  tip + index de încărcare) — `app/Services/Anaf/Etransport/EtransportSincronizare.php:30-74`,
  `app/Services/Anaf/Etransport/EtransportClient.php:52-60`, `routes/api.php:181`.
- Se păstrează pentru fiecare notificare: **UIT**, stare, cod și referință
  declarație, tip operațiune, date de transport, punct de trecere a frontierei,
  număr vehicul și remorci, greutate netă/brută, valoare, confirmare —
  `app/Services/Anaf/Etransport/EtransportSincronizare.php:76-108`,
  `database/migrations/2026_07_28_190000_create_etransport_notificari_table.php`.
- **Starea unei declarații depuse**, după indexul de încărcare — `stareMesaj` —
  `app/Services/Anaf/Etransport/EtransportClient.php:63-66`, `routes/api.php:183`.
- **Interogare ca organizator de transport / transportator** (`info`), cu filtre
  pe CUI declarant, UIT și referință — `app/Services/Anaf/Etransport/EtransportClient.php:72-83`,
  `app/Http/Controllers/Api/EtransportAnafController.php:208-222`, `routes/api.php:184`.
- **Căutare locală după UIT** în notificările deja aduse —
  `app/Http/Controllers/Api/EtransportAnafController.php:32-33`.
- Fiecare depunere intră în jurnal, cu index de încărcare la reușită sau cu
  „respinsă de ANAF” — `app/Http/Controllers/Api/EtransportAnafController.php:172-181`.
- **Mediu de test comutabil** (`prod`/`test`) fără modificări de cod —
  `config/anaf.php:134,145`.

### 2.2 Beneficiu

| Capabilitate | Efect pentru client |
|---|---|
| Depunere din aplicație cu certificatul de pe token (`EtransportClient.php:100`) | Nu mai e nevoie de aplicația ANAF pe stația cu tokenul |
| OAuth2 cu reînnoire automată a tokenului (`OauthAnaf.php:117`) | Autorizarea nu pică în mijlocul zilei de lucru |
| Sincronizare fără dublare pe UIT (`EtransportSincronizare.php:57-67`) | Lista de transporturi rămâne curată la reinterogări repetate |
| Evidența stărilor, inclusiv `ERR` (`EtransportSincronizare.php:69-71`) | Transporturile respinse se văd imediat, nu la control |
| Căutare după UIT (`EtransportAnafController.php:32`) | Se răspunde pe loc la o verificare în trafic |
| Interogarea ca transportator (`EtransportClient.php:72`) | Transportatorul vede declarațiile făcute de alții pe numele lui |
| Jurnal pe fiecare depunere (`EtransportAnafController.php:172`) | Se poate dovedi când și de cine a fost declarat un transport |

### 2.3 Diferențiator

- **Aceeași aplicație merge și cu certificat, și cu OAuth2**, cu comutare din
  configurație; codul de apel e comun — `app/Services/Anaf/Etransport/EtransportClient.php:88-99`.
- **`state` semnat criptografic** la autorizarea OAuth2, verificat la întoarcere —
  `app/Services/Anaf/Oauth/OauthAnaf.php:173-200`.
- **Tratarea răspunsului ANAF „nu există mesaje”**, pe care serviciul îl întoarce
  tot prin `Errors`: nu e eroare, e rezultat gol — `app/Services/Anaf/Etransport/EtransportSincronizare.php:39-52`.
- **Componenta de versiune în calea de upload** cerută de versiunea 2 a
  serviciului — `app/Services/Anaf/Etransport/EtransportClient.php:41-47`.

### 2.4 Limite

- Aplicația **nu generează XML-ul declarației de transport**: el se încarcă gata
  făcut — `app/Http/Controllers/Api/EtransportAnafController.php:154-159`.
- Nu există validator local pentru XML-ul e-Transport; validarea o face ANAF, iar
  erorile se întorc ca atare — `app/Http/Controllers/Api/EtransportAnafController.php:170-186`.
- Nu există alerte pe email sau push pentru transporturi respinse; starea se vede
  la sincronizare (nu există comandă programată pentru e-Transport în
  `app/Console/Kernel.php:26-59`). Sincronizarea e o acțiune cerută de om —
  `routes/api.php:181`.
- Nu se calculează termene de valabilitate a UIT-ului și nu se trimit
  atenționări la expirare.
- În `routes/api_routes/etransport_routes.php:4-14` există un set mai vechi de
  rute (`/etransport/upload`, `/lista`, `/stare`, `/info`) pe alt controller,
  documentat în `ETRANSPORT_README.md`. Modulul Dispecer e-Transport folosește
  rutele `/anaf-etransport/*`. **[CALE PARALELĂ, MAI VECHE]** — a nu se prezenta
  ca funcționalitate separată.

### 2.5 Termeni tehnici de folosit

UIT (cod unic de înregistrare a transportului), e-Transport, RO e-Transport,
ETRANSP, index de încărcare, `stareMesaj`, organizator de transport, declarant,
cod declarație, referință declarație, punct de trecere a frontierei, greutate
netă/brută, bunuri cu risc fiscal ridicat, OAuth2 ANAF, `logincert.anaf.ro`,
mediu test/prod, versiunea 2 a serviciului.

---

## 3. Grefier alert

Modul: `modul:portal_just` (`routes/api.php:191,201`). Interfață:
`resources/js/src/views/app_pages/PortalJust.vue:760`.

### 3.1 Capabilități reale

**Căutare**
- Interogarea serviciului public **Portal Just / ECRIS** (SOAP 1.1, fără
  autentificare) la `http://portalquery.just.ro/Query.asmx` —
  `config/portaljust.php:15-18`, `app/Services/Just/PortalJustClient.php:10-31`.
- Căutare dosare după număr, obiect sau nume parte, cu filtre pe instanță și
  interval de date; există și varianta cu filtru pe data ultimei modificări
  (`CautareDosare2`) — `app/Services/Just/PortalJustClient.php:32-73`,
  `app/Http/Controllers/Api/PortalJustController.php:32-62`.
- Căutare ședințe pe zi și instanță — `app/Services/Just/PortalJustClient.php:74-101`,
  `routes/api.php:194`.
- Lista instanțelor se citește din WSDL și se ține în cache 30 de zile —
  `app/Services/Just/PortalJustClient.php:102-154`, `config/portaljust.php:26`.
- Rezultatele căutărilor se păstrează 10 minute în cache, ca navigarea înainte și
  înapoi să nu reinterogheze serviciul — `config/portaljust.php:32`,
  `app/Http/Controllers/Api/PortalJustController.php:46-49`.
- Din fiecare dosar se citesc: părțile, ședințele (termene, ora, complet,
  soluție, sumar), căile de atac — `app/Services/Just/PortalJustClient.php:250-337`.

**Monitorizare**
- Urmărirea unui **număr de dosar** sau a unui **nume de parte**, opțional
  limitată la o instanță — `app/Models/PortalJustMonitorizare.php:25-65`,
  `app/Http/Controllers/Api/PortalJustMonitorizareController.php:52-71`.
- **Import în masă din Excel/CSV** (xls, xlsx, csv, txt, maximum 5 MB), cu
  recunoașterea coloanelor din capul de tabel sau, în lipsa lui, din conținut;
  duplicatele sunt sărite — `app/Http/Controllers/Api/PortalJustMonitorizareController.php:73-141`,
  `app/Services/Just/ImportMonitorizari.php:17-153`.
- **Verificare automată din oră în oră**, fără suprapunere între rulări —
  `app/Console/Kernel.php:41`, `app/Console/Commands/MonitorizeazaPortalJust.php:32-70`.
- Comparație pe **amprentă a stării dosarului**: se interoghează, se compară cu
  ce se știa, iar diferențele se înregistrează numite —
  `app/Services/Just/MonitorizarePortalJust.php:30-141`.
- Tipuri de modificări detectate explicit — `app/Services/Just/MonitorizarePortalJust.php:142-324`:
  - dosar nou apărut pe numele părții urmărite (`dosar_nou`);
  - **termen nou** (dată, oră, complet) — `termen_nou`;
  - **soluție** la un termen, inclusiv completată ulterior — `solutie`;
  - schimbarea **stadiului procesual** — `stadiu`;
  - **cale de atac** nouă — `cale_atac`;
  - **parte** nouă în dosar — `parte`;
  - schimbarea obiectului — `obiect`;
  - actualizare nespecificată, când s-a schimbat ceva neexplicitat — `actualizare`.
- **La prima verificare nu se trimit alerte** pentru dosarele găsite: ele sunt
  starea inițială, nu noutăți — `app/Services/Just/MonitorizarePortalJust.php:31,55-57`.
- **Înștiințare pe email** pentru modificările găsite, la adresa configurată pe
  monitorizare — `app/Console/Commands/MonitorizeazaPortalJust.php:227`,
  `app/Http/Controllers/Api/PortalJustMonitorizareController.php:52-71`.
- **Alertă instantanee pe telefon (push, Firebase Cloud Messaging)**, pe lângă
  email, grupată pe utilizator — `app/Console/Commands/MonitorizeazaPortalJust.php:120-205`,
  `app/Services/Notificari/Fcm.php:23-97`.
- Înregistrarea telefoanelor care primesc alerte; tokenul se actualizează singur
  la reinstalare — `app/Http/Controllers/Api/DispozitiveController.php:19-46`,
  `routes/api.php:216-217`.
- Istoricul modificărilor se păstrează 365 de zile (configurabil) —
  `config/portaljust.php:43`; se poate consulta din aplicație — `routes/api.php:208`.
- Pauză de 500 ms între două interogări succesive, ca serviciul public să nu fie
  împovărat — `config/portaljust.php:39`, `app/Console/Commands/MonitorizeazaPortalJust.php:87-107`.

### 3.2 Beneficiu

| Capabilitate | Efect pentru client |
|---|---|
| Verificare din oră în oră (`Kernel.php:41`) | Termenul nou se află în aceeași zi, nu la următoarea verificare manuală |
| Alertă la termen nou și la soluție (`MonitorizarePortalJust.php:191-231`) | Nu se pierde un termen procedural din neatenție — riscul e decăderea din drepturi |
| Push pe telefon (`MonitorizeazaPortalJust.php:127`) | Anunțul ajunge și când omul nu e la calculator |
| Monitorizare pe nume de parte (`PortalJustMonitorizare.php:56-58`) | Se află de dosarele noi deschise pe numele clientului, fără să le știe cineva numărul |
| Import din Excel (`PortalJustMonitorizareController.php:73`) | Un portofoliu de sute de dosare se pune sub urmărire dintr-un fișier |
| Prima verificare nu alertează (`MonitorizarePortalJust.php:55`) | Nu se primesc zeci de alerte false la punerea sub urmărire |
| Istoric 365 de zile (`portaljust.php:43`) | Se poate reconstitui ce s-a schimbat și când |
| Căutare cu cache (`PortalJustController.php:46`) | Navigarea în rezultate e imediată |

### 3.3 Diferențiator

- **Plic SOAP compus manual**, pentru că elementele opționale sunt declarate
  `nillable` cu `minOccurs=1` și trebuie trimise explicit ca `xsi:nil` — cu
  `SoapClient` din PHP ar fi omise, iar serviciul ar refuza cererea —
  `app/Services/Just/PortalJustClient.php:10-31,191-217`.
- **Detectarea numită a modificărilor**, nu doar „ceva s-a schimbat”: termen nou,
  soluție, stadiu, cale de atac, parte, obiect, cu text gata de trimis —
  `app/Services/Just/MonitorizarePortalJust.php:142-260`.
- **Soluția completată ulterior** e tratată separat de termenul nou: ea apare de
  regulă după ce termenul a fost deja anunțat — `app/Services/Just/MonitorizarePortalJust.php:214-231`.
- **Amprentă pe starea dosarului**, ca dosarele neschimbate să nu fie recitite în
  amănunt — `app/Services/Just/MonitorizarePortalJust.php:52-83,128-141`.
- **Telefoanele moarte se curăță singure**: tokenul invalid raportat de Firebase
  șterge dispozitivul, iar eșecurile repetate îl elimină după un prag —
  `app/Console/Commands/MonitorizeazaPortalJust.php:177-192`, `app/Services/Notificari/Fcm.php:98-111`.
- **Alertele nu se reîncearcă la nesfârșit**: se marchează ca trimise chiar dacă
  niciun dispozitiv n-a răspuns — `app/Console/Commands/MonitorizeazaPortalJust.php:194-197`.
- **Recuperarea restanțelor**: la rulare se iau și clienții cu înștiințări
  netrimise, chiar dacă monitorizarea a fost oprită între timp —
  `app/Console/Commands/MonitorizeazaPortalJust.php:43-51`.
- **Importul recunoaște fișiere fără cap de tabel**, după forma numărului de
  dosar — `app/Services/Just/ImportMonitorizari.php:119-160`.

### 3.4 Limite

- Datele vin din serviciul public al Ministerului Justiției; **nu se depune și nu
  se comunică nimic către instanță**. Documentele din dosar nu sunt accesibile —
  serviciul întoarce doar date de dosar, părți, termene, soluții și căi de atac
  (`app/Services/Just/PortalJustClient.php:250-337`).
- Datele de căutare **nu se salvează local** — `app/Http/Controllers/Api/PortalJustController.php:11-17`.
  Se păstrează doar dosarele puse sub monitorizare.
- Serviciul întoarce **cel mult 1000 de dosare** per căutare; peste acest număr,
  lista e incompletă și interfața o marchează — `config/portaljust.php:36`,
  `app/Http/Controllers/Api/PortalJustController.php:56-61`.
- Căutarea cere cel puțin numărul dosarului, obiectul sau numele părții —
  `app/Services/Just/PortalJustClient.php:36-41`.
- Cadența de verificare e **orară**, nu în timp real — `app/Console/Kernel.php:41`.
- Fără Firebase configurat, **alertele push nu funcționează**; rămâne emailul —
  `app/Services/Notificari/Fcm.php:30-43`, `.env.example:80-85`.
- **Aplicația mobilă Android nu face parte din acest depozit** (aici există doar
  API-ul: înregistrarea dispozitivului și trimiterea alertei) —
  `app/Http/Controllers/Api/DispozitiveController.php:10-16`. Capabilitățile ei
  nu pot fi susținute din acest cod. **[ÎN AFARA DEPOZITULUI]**
- Serviciul Portal Just se apelează pe **HTTP**, nu HTTPS — `config/portaljust.php:15`.
  A nu se scrie „conexiune securizată către instanțe”.

### 3.5 Termeni tehnici de folosit

Portal Just, portal.just.ro, ECRIS, `portalquery.just.ro`, dosar, număr dosar,
parte, obiectul dosarului, stadiu procesual, termen (de judecată), termen
procedural, complet de judecată, soluție, soluție pe scurt, cale de atac, apel,
recurs, instanță, ședință de judecată, monitorizare dosar, alertă termen,
notificare push.

---

## 4. Reguli de scriere pentru site

1. **Nu scrieți „depune automat declarațiile”.** Dosarul urmărit se oprește la
   semnare și arhivare (`app/Services/Anaf/Declaratii/MonitorizareFolder.php:150-181`).
   Formularea corectă: „preia, validează, semnează și arhivează automat”.
2. **Nu scrieți „în timp real”** pentru Grefier alert: verificarea e orară
   (`app/Console/Kernel.php:41`). Corect: „verificare din oră în oră”.
3. **Nu scrieți „fără instalare”** pentru SPV Curier și pentru modul `certificat`
   al e-Transport: semnarea și apelurile cu certificat cer programul local pe
   stația cu tokenul (`app/Services/Anaf/Spv/KitBridge.php:97`).
4. **Nu promiteți e-Factura.** Nu există în cele trei module.
5. **Nu promiteți generarea declarațiilor** (nici fiscale, nici e-Transport):
   aplicația le primește gata făcute.
6. **Numerele care se pot scrie ca atare:** 174 de tipuri de declarații
   recunoscute (`DeclaratieXml.php:21-41`), 35 de tipuri de documente
   solicitabile din SPV (`config/anaf.php:88-129`), 60 de zile fereastră SPV și
   e-Transport (`config/anaf.php:19,149`), 5 minute cadența dosarului urmărit
   (`Kernel.php:49`), o oră cadența monitorizării dosarelor (`Kernel.php:41`),
   30 de zile avertizare expirare certificat (`config/anaf.php:171`), 365 de zile
   istoric modificări dosare (`config/portaljust.php:43`), 1000 de dosare maxim
   per căutare (`config/portaljust.php:36`).
7. **Marcajele din acest document** — [PARȚIAL IMPLEMENTAT], [CALE PARALELĂ, MAI
   VECHE], [ÎN AFARA MODULULUI], [ÎN AFARA DEPOZITULUI] — nu se traduc în text de
   site; ele arată unde nu există acoperire în cod.
