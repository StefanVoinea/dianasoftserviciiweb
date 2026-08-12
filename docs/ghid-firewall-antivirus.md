# Firewall și antivirus pentru programul de acces la token (SPV Curier)

Ghid pentru calculatorul clientului, acela pe care stă tokenul USB și rulează
programul de acces („bridge") împreună cu agentul. Se adresează celui care
administrează acel calculator.

**Simptomul obișnuit:** după instalare merge, iar după un timp — o zi, o
săptămână, o actualizare de antivirus — legătura se oprește. Nu e o pană a
aplicației: e, aproape întotdeauna, filtrarea traficului criptat sau o regulă
de firewall apărută la o actualizare.

---

## 1. Ce comunică, încotro și pe ce port

Sunt trei programe, cu trei drumuri diferite. Cine nu le deosebește ajunge să
deschidă porturi care nu trebuiau deschise și să lase blocat ce conta.

| Cine | Încotro | Port | Fel |
|------|---------|------|-----|
| **Agentul** (`php.exe agent.php` → `curl.exe`) | `app.dianasoft.ro` | 443/TCP | ieșire |
| **Programul local** (`php.exe server.php` → `curl.exe`) | `webserviced.anaf.ro` (SPV) | 443/TCP | ieșire, **cu certificat de client** |
| **Programul local** | `decl.anaf.mfinante.gov.ro` (depunere) | 443/TCP | ieșire, **cu certificat de client** |
| **Programul local** | `webserviceapl.anaf.ro` (e-Transport, dacă se folosește) | 443/TCP | ieșire, **cu certificat de client** |
| **Agentul → programul local** | `127.0.0.1` | 8099/TCP | numai în calculator (loopback) |
| Aplicația → programul local | acest calculator | 8099/TCP | **intrare, numai în modul „direct"** |

### Modul de legătură hotărăște ce se deschide

În aplicație, la fiecare certificat, **SPV → Certificate digitale → Configurează**:

- **„prin tunel"** (recomandat, și cel folosit la instalările noi): agentul
  întreabă singur serverul ce are de făcut, pe 443, ca orice pagină de internet.
  **Nu trebuie deschis niciun port în firewall și niciunul pe router.** Dacă
  cineva vă cere să redirecționați portul 8099, nu e nevoie.
- **„direct"**: serverul aplicației sună calculatorul acesta pe portul 8099.
  Atunci e nevoie de o regulă de **intrare** pe 8099/TCP, limitată la adresa
  serverului.

### Ce NU trece pe la calculatorul clientului

`api.anaf.ro`, `www.anaf.ro`, `stare.anaf.ro`, `logincert.anaf.ro` și `portalquery.just.ro`
(Portal Just, pe **HTTP, portul 80**) sunt chemate de **serverul aplicației**,
nu de calculatorul cu tokenul. Ele contează în firewall doar dacă aplicația e
instalată chiar la client, pe serverul lui. La o instalare obișnuită, unde
aplicația stă la `app.dianasoft.ro`, nu aveți nimic de deschis pentru ele.

---

## 2. Regula de aur: fără inspecție TLS pe adresele ANAF

Programul local nu trimite parole către ANAF: se legitimează cu **certificatul
digital de pe token**, printr-o conexiune TLS în care și clientul își arată
certificatul (TLS mutual). Cheia privată nu poate fi copiată de pe token, deci
conexiunea trebuie să plece chiar de pe acest calculator, cu `curl.exe` din
Windows, care citește certificatul din magazinul Windows.

Orice antivirus cu **scanare HTTPS / filtrare SSL-TLS** se așază la mijlocul
conexiunii, o desface și o reface cu certificatul lui. La o pagină obișnuită nu
se vede nimic. Aici **rupe legitimarea**: antivirusul nu are cheia de pe token,
deci nu o poate arăta mai departe, iar ANAF răspunde cu refuz sau conexiunea
cade fără explicație.

**De aceea, în orice antivirus, adresele de mai jos trebuie scoase de sub
scanarea traficului criptat:**

```
app.dianasoft.ro
webserviced.anaf.ro
decl.anaf.mfinante.gov.ro
webserviceapl.anaf.ro
```

Aceasta este cauza numărul unu a legăturilor care „au mers și apoi s-au oprit":
filtrarea SSL se aprinde singură la o actualizare a antivirusului sau la
reînnoirea abonamentului.

### Cum vedeți în două minute dacă traficul e desfăcut

În PowerShell, pe calculatorul cu tokenul:

```powershell
$c = [Net.HttpWebRequest]::Create('https://app.dianasoft.ro')
$c.GetResponse() | Out-Null
$c.ServicePoint.Certificate.Issuer
```

- Dacă răspunde ceva cu **Let's Encrypt**, **DigiCert**, **Sectigo** ș.a.m.d. —
  conexiunea e curată.
- Dacă răspunde **ESET**, **Kaspersky**, **Bitdefender**, **Avast**, **AVG**,
  **Norton** sau **McAfee** — traficul e desfăcut de antivirus. Aceea e cauza.

---

## 3. Ce trebuie lăsat să ruleze (excepții de scanare)

Dosarul obișnuit de instalare este `C:\DianaSoft_SPV_Curier` (poate fi altul — cel în
care ați dezarhivat kitul).

| Ce | De ce |
|----|-------|
| `C:\DianaSoft_SPV_Curier\` (tot dosarul, cu subdosare) | programul, configurarea, arhiva de documente |
| `C:\DianaSoft_SPV_Curier\php\php.exe` | rulează și programul local, și agentul |
| `C:\Windows\System32\curl.exe` | cu el se vorbește cu ANAF și cu aplicația |
| `powershell.exe` | citirea certificatelor, semnarea, tipărirea (scripturile `.ps1`) |
| `C:\DianaSoft_SPV_Curier\PDFtoPrinter.exe` | tipărirea declarațiilor și a recipiselor |
| `C:\DianaSoft_SPV_Curier\itextsharp.dll` | biblioteca de semnare; unele antivirusuri o pun în carantină |

Cele două sarcini programate care pornesc totul la autentificare se numesc:

```
Acces token ANAF
Acces token ANAF - agent
```

Unele antivirusuri cu modul de „protecție împotriva programelor nedorite" opresc
sarcini programate care pornesc un interpretor (php.exe) fără fereastră. Dacă
sarcinile dispar sau rămân „Ready" fără să pornească, acolo e cauza.

---

## 4. Windows Defender Firewall

### 4.1 Ieșirea (valabil pentru orice mod de legătură)

Firewall-ul Windows lasă implicit tot traficul de ieșire. Dacă în firmă e o
politică prin care ieșirea e blocată implicit, adăugați (în PowerShell, ca
administrator):

```powershell
New-NetFirewallRule -DisplayName "Acces token ANAF - iesire (php)" `
  -Direction Outbound -Action Allow -Protocol TCP -RemotePort 443 `
  -Program "C:\DianaSoft_SPV_Curier\php\php.exe"

New-NetFirewallRule -DisplayName "Acces token ANAF - iesire (curl)" `
  -Direction Outbound -Action Allow -Protocol TCP -RemotePort 443 `
  -Program "C:\Windows\System32\curl.exe"
```

### 4.2 Intrarea pe 8099 — numai în modul „direct"

Instalarea o adaugă singură dacă ați rulat `instaleaza.ps1 -Adresa 0.0.0.0`, sub
numele `Acces token 8099`. Manual, limitată la adresa serverului aplicației:

```powershell
New-NetFirewallRule -DisplayName "Acces token 8099" `
  -Direction Inbound -Action Allow -Protocol TCP -LocalPort 8099 `
  -RemoteAddress 192.0.2.10 -Profile Private,Domain
```

Înlocuiți `192.0.2.10` cu adresa serverului. **Nu lăsați regula deschisă către
oricine** și nu o puneți pe profilul „Public".

### 4.3 Verificarea regulilor existente

```powershell
Get-NetFirewallRule -DisplayName "*token*" | Format-Table DisplayName, Direction, Action, Enabled
Get-NetFirewallProfile | Format-Table Name, Enabled, DefaultOutboundAction
```

Dacă `DefaultOutboundAction` este `Block` pe vreun profil, regulile de la 4.1
sunt obligatorii.

---

## 5. ESET (NOD32 Antivirus, Internet Security, Endpoint Security)

Denumirile diferă puțin de la o versiune la alta; căutați în setări după
cuvântul-cheie scris îngroșat.

Setările stau în **F5** (Configurare avansată).

### 5.1 Filtrarea SSL/TLS — pasul care rezolvă cel mai des problema

Se deschide ESET, se apasă **F5** (Configurare avansată). Denumirile diferă
puțin între versiuni: la cele noi secțiunea se cheamă **Protecții**, la cele mai
vechi **Web și email**.

**Calea cea mai sigură: scoateți programul, nu adresa.**

`Protecții → SSL/TLS → Lista aplicațiilor filtrate SSL/TLS → Editare → Adăugare`

Adăugați, cu acțiunea **„Ignoră"** (Ignore / Nu scana):

```
C:\Windows\System32\curl.exe
C:\DianaSoft_SPV_Curier\php\php.exe
```

Prin ele trece tot ce vorbește cu ANAF. Scoase de sub filtrare, legătura rămâne
cap la cap cu ANAF, iar certificatul de pe token ajunge întreg acolo. Browserul
rămâne mai departe scanat — nu slăbiți protecția pe restul calculatorului.

**Calea a doua, pe adrese** (dacă preferați să nu numiți programe):

`Protecții → SSL/TLS → Lista adreselor excluse din filtrare → Editare → Adăugare`

```
*app.dianasoft.ro*
*webserviced.anaf.ro*
*decl.anaf.mfinante.gov.ro*
*webserviceapl.anaf.ro*
```

**Proba, nu credința:** după salvare, rulați `diagnoza.bat` din dosarul
programului. La pașii 2 și 3, rândul cu emitentul trebuie să arate o autoritate
adevărată (DigiCert, Sectigo, Let's Encrypt și altele asemenea). Dacă mai scrie
`ESET SSL Filter CA`, excluderea nu s-a aplicat — cel mai des pentru că lipsesc
asteriscurile din adresă, sau pentru că setările sunt ținute de o politică ESET
PROTECT, de pe server, care le rescrie pe cele locale.

Dacă tot nu merge, treceți **temporar** filtrarea pe „Nu se scanează protocolul
SSL/TLS" și încercați din nou. Dacă atunci merge, cauza e confirmată: rămâneți pe
excluderi, nu pe filtrarea oprită de tot.

**De ce contează:** ESET desface conexiunea și o reface cu certificatul lui, iar
certificatul de pe token nu mai ajunge la ANAF (vezi capitolul 2).

### 5.2 Protecția accesului web

`Protecții → Protecție acces web → Gestionare adrese URL`

Adăugați aceleași patru adrese în **lista de adrese permise**. Formă acceptată de
ESET: `*.dianasoft.ro`, `*.anaf.ro`, `*.anaf.mfinante.gov.ro`.

### 5.3 Excluderi de performanță (fișiere și procese)

`Detecții → Excluderi → Excluderi de performanță` — adăugați
`C:\DianaSoft_SPV_Curier\` cu tot ce e sub el.

`Detecții → Excluderi → Excluderi de procese` — adăugați:
```
C:\DianaSoft_SPV_Curier\php\php.exe
C:\Windows\System32\curl.exe
```

### 5.4 HIPS

`Protecții → HIPS`

Dacă HIPS e pe „Mod interactiv" sau „Mod bazat pe politici", el poate opri
`php.exe` să pornească `curl.exe` sau `powershell.exe`. Verificați în
`Instrumente → Fișiere jurnal → HIPS` dacă apar opriri pentru `php.exe`. Dacă
da, faceți o regulă care permite `C:\DianaSoft_SPV_Curier\php\php.exe` să pornească
alte aplicații.

### 5.5 Firewall (numai la Internet Security și Endpoint Security)

`Protecții → Acces la rețea → Firewall → Reguli`

- Ieșire: permis pentru `php.exe` și `curl.exe`, TCP, port la distanță 443.
- Intrare: **numai în modul „direct"**, TCP, port local 8099, limitat la adresa
  serverului aplicației.

NOD32 Antivirus (varianta simplă) **nu are firewall** — acolo se aplică doar
regulile Windows de la capitolul 4.

### 5.6 Verificare după modificări

`Instrumente → Fișiere jurnal → Site-uri web filtrate` — dacă mai apar acolo
adresele ANAF sau `app.dianasoft.ro`, excluderea nu s-a aplicat: cel mai des,
pentru că a fost scrisă cu `https://` în față sau cu bară la sfârșit.

---

## 6. Defender și cele cinci antivirusuri cele mai folosite

Defender vine cu Windows, deci e pe orice calculator; după el, cele cinci de mai
jos acoperă aproape tot ce se întâlnește pe calculatoarele din firmele mici, la
noi. Ordinea e orientativă, după cât de des apar.
La toate, pașii sunt aceiași trei: **scoate adresele de sub scanarea HTTPS**,
**exclude dosarul**, **lasă programul prin firewall**.

### 6.1 Microsoft Defender Antivirus

Cel mai des întâlnit, fiind în Windows. Nu desface traficul HTTPS, deci nu
strică legitimarea cu certificat — problemele lui sunt de alt fel: pune în
carantină `itextsharp.dll` sau oprește sarcinile programate.

`Securitate Windows → Protecție împotriva virușilor și amenințărilor →
Gestionare setări → Excluderi → Adăugare excludere`

- Folder: `C:\DianaSoft_SPV_Curier`
- Proces: `php.exe`

Sau din PowerShell, ca administrator:

```powershell
Add-MpPreference -ExclusionPath "C:\DianaSoft_SPV_Curier"
Add-MpPreference -ExclusionProcess "php.exe"
Get-MpPreference | Select-Object -ExpandProperty ExclusionPath
```

Verificați și carantina, dacă semnarea eșuează:

```powershell
Get-MpThreatDetection | Select-Object -Last 10 InitialDetectionTime, Resources
```

### 6.2 Bitdefender (Total Security, Internet Security, GravityZone)

- **Protecție → Online Threat Prevention → Setări → „Scanare SSL"** (la
  GravityZone: politica → `Network Protection → Content Control → Scan SSL`) —
  opriți-o sau adăugați cele patru adrese la excepții.
- **Protecție → Antivirus → Setări → Gestionare excepții** — adăugați dosarul
  `C:\DianaSoft_SPV_Curier`, bifând și „Aplicare la scanare la accesare".
- **Protecție → Firewall → Setări → Reguli aplicații** — adăugați `php.exe` și
  `curl.exe` cu „Permite".
- **Advanced Threat Defense** — dacă în „Notificări" apare `php.exe` blocat,
  adăugați-l la excepții tot de acolo.

### 6.3 Kaspersky (Standard/Plus/Premium, Endpoint Security)

- **Setări → Setări de securitate a rețelei → „Scanarea conexiunilor
  criptate"** — alegeți „Nu scana conexiunile criptate" sau adăugați cele patru
  adrese la **„Site-uri web de încredere"**. La Endpoint Security: politica →
  `Network settings → Encrypted connections scanning → Trusted addresses`.
- **Setări → Excluderi → Gestionare excluderi** — adăugați `C:\DianaSoft_SPV_Curier\*`.
- **Setări → Excluderi → Aplicații de încredere** — adăugați `php.exe` și
  `curl.exe`, bifând „Nu scana traficul de rețea".
- **Firewall → Reguli pentru aplicații** — `php.exe` în grupul „De încredere".

### 6.4 Avast / AVG (aceeași bază tehnică)

- **Meniu → Setări → Protecție → Protecție de bază → Scut web → „Activează
  scanarea HTTPS"** — dezactivați-o, ori adăugați cele patru adrese la
  **„Excepții"**.
- **Meniu → Setări → General → Excepții** — adăugați `C:\DianaSoft_SPV_Curier\**`.
- **Protecție → Firewall → Reguli aplicații** — `php.exe` și `curl.exe` pe
  „Permite".
- **Protecție → Scut comportamental** — dacă a pus programul în carantină, se
  restaurează din **Protecție → Carantină** și se adaugă la excepții.

### 6.5 Norton 360

- **Setări → Firewall → Reguli program** — `php.exe` și `curl.exe` pe
  „Permite".
- **Setări → Antivirus → Scanări și riscuri → Elemente excluse din scanări** —
  adăugați dosarul `C:\DianaSoft_SPV_Curier`.
- **Setări → Antivirus → Scanări și riscuri → Elemente excluse din detectarea
  Auto-Protect, SONAR și Descărcare inteligentă** — același dosar. Fără el,
  SONAR oprește programul după câteva zile de funcționare, ca „comportament
  neobișnuit".
- **Setări → Firewall → Prevenirea intruziunilor → Excluderi** — dacă în jurnal
  apar blocări pentru traficul spre ANAF.

### 6.6 McAfee (Total Protection, LiveSafe)

- **Setări → Firewall → Acces la internet pentru programe** — `php.exe` și
  `curl.exe` pe „Acces complet".
- **Setări → Scanare în timp real → Fișiere excluse** — adăugați dosarul.
- **Setări → Protecție web** — dacă are „Scanare trafic criptat", opriți-o sau
  adăugați adresele.

---

## 7. Imprimantele

Declarațiile și recipisele bifate pentru tipărire nu se descarcă: ies pe hârtie
chiar la calculatorul cu tokenul, pe imprimanta aleasă pentru fiecare om în
aplicație (**SPV → Utilizatori**). Tipărirea o face tot programul local, prin
`powershell.exe`, cu `PDFtoPrinter.exe` din dosarul de instalare — sau, dacă
acesta lipsește, prin programul asociat PDF-urilor (Acrobat Reader, Foxit).

Deci **nu e nevoie de nicio regulă de firewall pentru imprimarea locală**: totul
se petrece în calculatorul acela. Ce trebuie e ca antivirusul să nu stea în
drum, iar imprimanta să fie văzută de contul Windows sub care rulează programul.

### 7.1 Ce blochează antivirusul, de obicei

| Ce se întâmplă | Unde se dezleagă |
|----------------|------------------|
| `PDFtoPrinter.exe` dispare din dosar sau ajunge în carantină — e un program mic, nesemnat, pe care multe antivirusuri îl trec drept „aplicație nedorită" | Excluderile din capitolul 3, plus restaurarea lui din carantină |
| Scripturile `.ps1` nu mai pornesc (modulul de „protecție împotriva scripturilor" / AMSI) | Excluderea de proces pentru `powershell.exe`, la capitolele 5–6 |
| Fișierul PDF nu poate fi scris în dosar („protecție împotriva ransomware", „acces controlat la foldere") | Adăugați `C:\DianaSoft_SPV_Curier` la folderele permise |

La Defender, accesul controlat la foldere se verifică așa:

```powershell
Get-MpPreference | Select-Object EnableControlledFolderAccess
Add-MpPreference -ControlledFolderAccessAllowedApplication "C:\DianaSoft_SPV_Curier\php\php.exe"
Add-MpPreference -ControlledFolderAccessAllowedApplication "C:\DianaSoft_SPV_Curier\PDFtoPrinter.exe"
```

### 7.2 Contul Windows, nu calculatorul, vede imprimantele

Programul rulează ca sarcină programată **sub contul omului care a instalat
kitul**, pentru că numai acel cont vede certificatul de pe token. Prin urmare,
vede exact imprimantele instalate în acel cont.

- O imprimantă de rețea adăugată în alt cont Windows **nu apare** în listă.
  Adăugați-o în contul sub care rulează programul.
- Dacă lista de imprimante din aplicație e goală sau nu se aduce, verificați
  întâi serviciul de tipărire:
  ```powershell
  Get-Service Spooler | Format-Table Name, Status, StartType
  Get-Printer | Format-Table Name, PrinterStatus, DriverName
  ```
  Ce arată `Get-Printer` în acel cont este exact ce va arăta și aplicația.

### 7.3 Imprimante de rețea — aici da, e nevoie de firewall

Imprimanta locală, pe USB, nu cere nimic. Cea din rețea cere ieșire, de la
calculatorul cu tokenul către imprimantă sau către serverul de tipărire:

| Fel de imprimantă | Port de ieșire |
|-------------------|----------------|
| Partajată de pe un server sau alt calculator (`\\server\imprimanta`) | 445/TCP (SMB), plus 135/TCP pentru RPC |
| Direct pe IP, port RAW (cea mai obișnuită la imprimantele de rețea) | 9100/TCP |
| IPP / Internet Printing | 631/TCP |
| LPR/LPD (echipamente mai vechi) | 515/TCP |

Verificarea, cu adresa imprimantei sau a serverului:

```powershell
Test-NetConnection 192.168.1.50 -Port 9100
Test-NetConnection server-print -Port 445
```

### 7.4 Proba tipăririi, fără aplicație

De pe calculatorul cu tokenul, direct cu scriptul folosit de program:

```powershell
cd C:\DianaSoft_SPV_Curier
powershell -ExecutionPolicy Bypass -File .\print-pdf.ps1 `
  -Cale "C:\DianaSoft_SPV_Curier\arhiva\...\document.pdf" `
  -Imprimanta "Numele exact al imprimantei" `
  -Program "C:\DianaSoft_SPV_Curier\PDFtoPrinter.exe"
```

- „Imprimanta '...' nu există pe acest calculator" — numele diferă de cel din
  aplicație, sau imprimanta e în alt cont Windows.
- Comanda trece fără eroare, dar nu iese hârtia — `PDFtoPrinter.exe` a fost pus
  în carantină; căutați-l în jurnalul antivirusului.
- Nu pornește deloc — politica de execuție a scripturilor sau „marca
  internetului" pe fișiere (capitolul 9).

---

## 8. Ordinea în care se caută pana

Rulați pe calculatorul cu tokenul, în ordinea aceasta. Fiecare pas spune ce e
stricat mai departe.

**1. Pornesc programele?**

```powershell
Get-ScheduledTask "Acces token ANAF*" | Format-Table TaskName, State
Get-Process php -ErrorAction SilentlyContinue
```

Trebuie să vedeți două sarcini și cel puțin două procese `php`. Dacă lipsesc,
antivirusul le-a oprit sau le-a șters (capitolul 3).

**2. Răspunde programul local?**

```powershell
curl.exe -sS -o NUL -w "%{http_code}`n" http://127.0.0.1:8099/certificate
```

`401` este răspunsul corect (cere codul de acces). Dacă nu răspunde nimic,
programul nu rulează sau firewall-ul blochează chiar și legătura în interiorul
calculatorului.

**3. Ce spune agentul?**

```powershell
Get-Content C:\DianaSoft_SPV_Curier\agent.log -Tail 30
```

De la versiunea în care agentul spune și pricina, rândul de eroare o poartă cu
el — nu mai trebuie ghicită:

- `Serverul nu răspunde: legătura nu se poate deschide — port închis de firewall
  sau internet căzut [curl 7]` — ieșirea pe 443 e oprită.
- `… certificatul serverului nu este de încredere … [curl 60]` sau `… strângerea
  de mână TLS a eșuat … [curl 35]` — **traficul e desfăcut de antivirus sau de
  proxy: exact problema din capitolul 2.**
- `… răspunsul nu vine de la aplicație, ci de la altcineva de pe drum …`, urmat
  de începutul acelui răspuns — a răspuns pagina de oprire a antivirusului, a
  proxy-ului din firmă sau a portalului de rețea. Textul arătat spune cine.
- După trei minute de pană, agentul scrie o singură dată și ce e de verificat,
  în ordine. Când legătura se ridică, scrie `Legătura s-a ridicat, după N min`.
- `Serverul nu-mi recunoaște codul de acces` — nu e firewall: certificatul nu e
  legat de kit. Se rezolvă din aplicație, cu „Citește token-urile conectate".
- Rânduri cu `Comanda ...` — legătura e bună. Într-o zi liniștită, agentul scrie
  din jumătate în jumătate de oră `Pândesc mai departe; legătura cu serverul e
  bună` — ca tăcerea din jurnal să nu semene cu un agent oprit.

> **La kiturile mai vechi**, jurnalul arată altfel: acolo apare
> `Serverul nu răspunde; reîncerc peste 5s` chiar și când totul merge, la fiecare
> pândă împlinită fără nimic de lucru. Dacă printre acele rânduri apar și rânduri
> `Comanda ...`, **legătura e bună** — mesajul nu înseamnă nimic rău. Semnul
> adevărat al unei pene, la kiturile vechi, este lipsa oricărui rând `Comanda`
> vreme îndelungată. Se lămurește instalând kitul nou.

**4. Se ajunge la server și la ANAF?**

```powershell
Test-NetConnection app.dianasoft.ro -Port 443
Test-NetConnection webserviced.anaf.ro -Port 443
curl.exe -sS -o NUL -w "%{http_code}`n" https://app.dianasoft.ro/api/punte/agent/asteapta
```

Ultima comandă trebuie să răspundă `401` (fără cod de acces). Dacă răspunde
`000` sau dă eroare de certificat, traficul e oprit sau desfăcut.

**5. E desfăcut traficul?** Vezi verificarea din capitolul 2.

**6. Iese hârtia?** Dacă legătura merge, dar tipărirea nu, mergeți la capitolul
7: acolo cauza e aproape întotdeauna alta — `PDFtoPrinter.exe` în carantină, sau
imprimanta instalată în alt cont Windows decât cel sub care rulează programul.

**7. Ultima probă, cea care lămurește tot:** opriți antivirusul cu totul, pentru
două minute, și priviți `agent.log`. Dacă legătura se ridică imediat, cauza e
în antivirus, iar capitolele 5–6 spun unde. Reporniți-l imediat după.

---

## 9. Lucruri care se confundă cu o blocare de firewall

- **`SEC_E_CONTEXT_EXPIRED` la apelurile către ANAF, dar firewall-ul e în
  regulă** — tokenul așteaptă codul PIN. Fiecare legătură cu SPV se face cu
  certificatul de pe token, deci cere cheia privată de pe el; dacă driverul
  deschide dialogul de PIN și nu-l vede nimeni, operația rămâne în așteptare
  până când Windows declară sesiunea securizată expirată. De verificat, în
  ordine:
  1. **Există un dialog de PIN care așteaptă?** Uitați-vă pe calculatorul cu
     tokenul, inclusiv în spatele ferestrelor și pe celelalte sesiuni deschise.
  2. **Este cineva conectat în Windows acolo?** Programul rulează ca sarcină
     programată sub contul omului, pornită la autentificare — cu utilizatorul
     delogat nu rulează nimic, iar cu sesiunea închisă de la distanță (RDP
     deconectat) dialogul n-are unde să apară.
  3. **Porniți „single logon" în driverul tokenului** (la SafeNet Authentication
     Client: *Tools → Advanced*). Atunci PIN-ul se cere o dată pe sesiunea
     Windows, nu la fiecare operație. Nu scăpați de PIN — certificatul e
     calificat, legea îl cere — dar nu vi-l mai cere de zeci de ori pe zi.
  4. **Proba limpede:** rulați `porneste-manual.bat` într-o fereastră vizibilă și
     cereți o operație din aplicație. Dacă dialogul de PIN apare acolo, aceasta
     era pricina.
- **Semnarea eșuează cu `0x80131515`** — nu e antivirusul: fișierele venite din
  arhiva descărcată poartă „marca internetului", iar .NET refuză să încarce
  `itextsharp.dll`. Se rezolvă rulând din nou `instaleaza.bat`, care deblochează
  tot dosarul, sau manual:
  ```powershell
  Get-ChildItem C:\DianaSoft_SPV_Curier -Recurse -File | Unblock-File
  ```
- **„Programul de pe calculatorul cu tokenul nu rulează"**, dar `php` apare în
  procese — de obicei tokenul a fost scos din USB, ori sesiunea Windows a fost
  închisă (sarcina pornește la autentificare și rulează sub contul acelui om;
  cu utilizatorul delogat, nu rulează nimic).
- **Merge la unii utilizatori, la alții nu** — certificatul stă în magazinul
  personal al unui singur cont Windows. Programul trebuie să ruleze sub contul
  acela.
- **A mers și s-a oprit după o reînnoire de abonament** — la reactivare, multe
  antivirusuri repornesc modulele cu setările din fabrică, iar excluderile
  adăugate rămân, dar filtrarea SSL se reaprinde. Recitiți capitolul 2.

---

## 10. Rezumatul de dat administratorului de rețea

> Pe calculatorul cu tokenul USB, programul „Acces token ANAF" are nevoie de:
>
> - **ieșire TCP 443** către `app.dianasoft.ro`, `webserviced.anaf.ro`,
>   `decl.anaf.mfinante.gov.ro` și `webserviceapl.anaf.ro`;
> - aceste patru adrese **scoase de sub inspecția TLS** (proxy, antivirus, UTM) —
>   conexiunea folosește certificat de client de pe token, iar inspecția o rupe;
> - **excluderea din scanare** a dosarului de instalare (`C:\DianaSoft_SPV_Curier`) și
>   a proceselor `php.exe` și `curl.exe`;
> - **intrare TCP 8099 numai** dacă legătura e configurată „direct", limitată la
>   adresa serverului aplicației. Pe legătura „prin tunel" nu se deschide nimic;
> - pentru tipărire, dacă imprimanta e în rețea: ieșire către ea pe **9100/TCP**
>   (port RAW), **445/TCP** (imprimantă partajată de pe un server), **631/TCP**
>   (IPP) sau **515/TCP** (LPR), după cum e instalată. Imprimanta pe USB nu cere
>   nimic. `PDFtoPrinter.exe` și `powershell.exe` trebuie lăsate să ruleze.
>
> Nu e nevoie de redirecționare de porturi pe router și de nicio adresă IP fixă.
