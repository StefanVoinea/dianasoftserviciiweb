# Grefier Alert — aplicația de telefon

Aplicația pentru Android care ține legătura cu **app.dianasoft.ro** și sună
telefonul când se schimbă ceva la un dosar urmărit.

## Ce este și ce nu este

Aplicația **nu rescrie** ce există deja pe app.dianasoft.ro. Înăuntru rulează
chiar aplicația web, cea pe care o știți din calculator, cu toate filele ei.

Ce aduce în plus sunt tocmai lucrurile pe care o pagină web nu le poate face
singură pe un telefon:

| | |
|---|---|
| **Alerte** | telefonul sună chiar cu ecranul stins și aplicația închisă |
| **Icoană** | stă pe ecranul de start, se deschide dintr-o apăsare |
| **Documente** | PDF-urile se salvează în dosarul de descărcări al telefonului |
| **Autentificare** | se face o dată; nu se cere la fiecare deschidere |

Pentru asta nu s-a schimbat nimic în aplicația web. Legătura se face într-un
singur loc, în [Fereastra.kt](app/src/main/java/ro/dianasoft/grefier/Fereastra.kt):
din când în când se citește jetonul pe care aplicația web îl ține în
`localStorage`, iar când se schimbă, telefonul se anunță la server.

## Instalarea pe telefon

Fișierul gata construit este:

```
app/build/outputs/apk/debug/app-debug.apk
```

**Cu telefonul legat prin cablu USB**, cu „Depanare USB” pornită în Opțiuni
pentru dezvoltatori:

```
adb install -r app/build/outputs/apk/debug/app-debug.apk
```

**Fără cablu:** copiați fișierul `.apk` pe telefon (email, Drive, WhatsApp),
deschideți-l din lista de fișiere și lăsați instalarea din surse necunoscute
pentru programul care îl deschide. Android întreabă o singură dată.

Cere Android **8.0 sau mai nou**.

## Construirea din nou

```
./gradlew assembleDebug
```

Prima construire durează câteva minute; următoarele, sub un minut.

Fișierul `local.properties` spune unde stă Android SDK pe calculatorul acesta.
Pe alt calculator trebuie scris din nou — **cu bară înainte** (`C:/Users/...`),
fiindcă într-un fișier de proprietăți bara înapoi înseamnă altceva, iar calea
ajunge la Gradle stricată și fără nicio lămurire de ce.

## Alertele instantanee

**Aplicația merge și fără ele.** Fără Firebase se vede tot, se lucrează tot —
doar că modificările se află când deschideți aplicația, nu în clipa în care se
petrec. De aceea construirea nu se oprește când fișierul Firebase lipsește.

Ca să sune telefonul, sunt trei lucruri de făcut, fiecare o singură dată.

### 1. În consola Firebase

La [console.firebase.google.com](https://console.firebase.google.com), în
proiectul aplicației (sau într-unul nou):

1. **Project settings → General → Your apps → Add app → Android**
2. La „Android package name" scrieți exact: `ro.dianasoft.grefier`
3. Descărcați **`google-services.json`**

### 2. În kitul acesta

Puneți fișierul descărcat în:

```
mobil-grefier/app/google-services.json
```

și construiți din nou. Atât — restul se leagă singur.

> Fișierul e trecut în `.gitignore`: e cheia proiectului vostru, nu are ce
> căuta în depozit.

### 3. Pe server

În `.env` de pe app.dianasoft.ro:

```
FIREBASE_PROJECT_ID=id-ul-proiectului
FIREBASE_CREDENTIALS=/cale/catre/cheia-contului-de-serviciu.json
```

Cheia se ia din **Project settings → Service accounts → Generate new private
key**. Se ține în afara depozitului.

Verificarea că serverul poate trimite: răspunsul de la `POST /api/dispozitive`
conține `push_activ`. Dacă e `false`, serverul nu are datele, și alertele nu
pleacă oricâte telefoane s-ar înregistra.

## Cum se leagă telefonul de cont

Nu este niciun ecran de conectare al aplicației: vă conectați în pagina web, ca
în calculator. Mai departe:

1. Aplicația citește jetonul aplicației web și societatea aleasă
2. Cere de la Google adresa acestui telefon
3. Le trimite amândouă la `POST /api/dispozitive`
4. La deconectare, `DELETE /api/dispozitive` — telefonul nu mai primește alerte

Se ține minte ce s-a anunțat ultima oară, așa că serverul nu e bătut la ușă
degeaba la fiecare verificare.

## Dacă nu merge

**Nu se poate ajunge la server / conectarea e refuzată de pe telefon.**
Verificați dacă contul are **adrese IP permise** trecute în aplicație. Telefonul
pe date mobile vine de la altă adresă decât biroul și va fi oprit înainte de a
primi jetonul, cu aceeași eroare ca o parolă greșită.

**Alertele nu vin, deși totul pare pus.** În ordine: `push_activ` să fie `true`;
`google-services.json` să fie în kit **și** aplicația reconstruită după aceea;
notificările să fie permise (Android 13+ le cere la prima pornire — dacă s-a
refuzat atunci, se dă din Setări → Aplicații → Grefier Alert → Notificări).

**Aparatul foto nu merge** în filele care citesc documente. Aplicația nu cere
această învoire, fiindcă pentru urmărirea dosarelor nu e nevoie de ea. Se poate
adăuga la nevoie.

## Pentru publicarea în Google Play

APK-ul din `debug` se semnează singur și e bun pentru telefoanele voastre, dar
nu poate fi urcat în magazin. Pentru asta:

1. Faceți o cheie de semnare (`keytool -genkey -v -keystore grefier.jks ...`)
2. Scrieți `semnare.properties` în rădăcina kitului:

```properties
storeFile=grefier.jks
storePassword=...
keyAlias=grefier
keyPassword=...
```

3. `./gradlew assembleRelease`

Amândouă fișierele sunt trecute în `.gitignore`. **Cheia nu se pierde și nu se
schimbă**: fără ea nu se mai poate publica nicio actualizare a aplicației, iar
Google nu o poate reface.
