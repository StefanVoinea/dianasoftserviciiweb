# Acces MCP la DianaSoft servicii web

Dă asistentului tău (Claude) două drepturi diferite, cu reguli diferite:

- **citire pe tot programul, în perimetrul firmei tale** — declarații, mesaje SPV,
  solicitări, certificate, jurnalul ANAF, e-Transport, litigii, nomenclatoare.
  Poate să și grupeze și să însumeze, deci răspunde la întrebări de tipul „câte
  declarații D300 s-au depus luna trecută, pe fiecare firmă".
- **scriere doar pe nomenclatoare** — poate adăuga rânduri noi și poate modifica
  în masă mai multe câmpuri deodată, dar numai acolo.

**Nu poate șterge nimic.** Nu există unealtă de ștergere, iar serverul refuză
oricum orice operație care nu e căutare, adăugare sau modificare.

---

## Ce poate CITI

Practic toată baza de date — aceleași date pe care le vezi oricum în aplicație —
dar **numai ale firmei tale**. Serviciul are mai mulți clienți; fiecare își vede
doar datele lui. `dianasoft_structura` fără argumente îți dă lista tabelelor; cu
numele unei tabele îți dă coloanele ei.

Regulile, pe scurt:

- **tabelele cu `company_id`** (declarații, mesaje, certificate, societăți...) se
  filtrează automat pe firma curentă. Administratorul serviciului poate cere
  `toate_firmele: true` la `dianasoft_cauta`; pentru oricine altcineva cererea e
  ignorată.
- **tabelele fără `company_id`** sunt de două feluri: nomenclatoare comune
  (județe, localități, curs BNR, registrul comerțului, coduri vamale) pe care le
  citește oricine, și tabele de administrare a serviciului (utilizatori, clienți,
  bănci, evidența de marketing) pe care le citește doar administratorul.

Nu se citesc:

- **tabelele de tokenuri** — OAuth, ANAF (SPV, e-Factura, e-Transport), push.
  Nu ajută la nicio întrebare de business și tot ce e acolo e material de acces.
- **coloanele cu nume de secret**, oriunde ar apărea: `password`, `parola`,
  `remember_token`, `secret`, `api_key`, `access_token`, `refresh_token`,
  `bridge_token`, `jeton`, `cnp`, `cnp_reprezentant`. Așa `users` rămâne citibilă
  (cine a depus, cine răspunde) fără ca parolele să iasă vreodată din baza de
  date, iar `anaf_certificate` se vede fără codul de legătură cu puntea.

O coloană ascunsă nu se poate nici filtra, nici ordona după ea — altfel ar deveni
un mod indirect de a o citi.

## Ce poate MODIFICA

| Tabelă | Ce se poate modifica |
|---|---|
| `societati` — firmele clientului (modulul ANAF) | denumire, adresă, telefon, fax, e-mail, bancă, cont, CAEN, declarantul (nume, prenume, funcție, prin reprezentant), antetul D300 (tip decont, pro-rata, bifele), activ, scos din uz |
| `contacte_marketing` — evidența de marketing (**doar administratorul**) | denumire, e-mail, e-mailuri, telefon, județ, tip, viză, membru din, sursă |

**Atât.** Că o tabelă se poate citi nu înseamnă deloc că se poate și scrie în ea.

## Ce se citește, dar nu se scrie, și de ce

- **Declarațiile, mesajele SPV, solicitările, jurnalul ANAF** — sunt urme ale unor
  depuneri reale, cu recipise. Le scriu doar procesele care depun.
- **Certificatele** — leagă o firmă de un calculator și de un PIN; se administrează
  din aplicație, cu confirmări.
- **CIF-ul** unei societăți și datele trase de la ANAF (date de identificare,
  vector fiscal) — nu se corectează de mână.
- **Bifa `abonat`** din evidența de marketing — cine s-a dezabonat rămâne dezabonat.
- **Utilizatori, clienți, permisiuni, meniuri, abonamente.**

---

## Două feluri de a-l porni

**Ca „connector" (recomandat, nimic de instalat).** În Claude, *Settings → Connectors
→ Add custom connector*, și pui adresa:

```
https://app.dianasoft.ro/mcp
```

Claude te trimite la pagina de autentificare DianaSoft, te loghezi cu contul tău
obișnuit, aprobi accesul și gata. Nu ai nevoie de Node, de token și de niciun
fișier de configurare. Vezi datele firmei la care e legat contul tău.

**Ca server local**, dacă vrei să îl rulezi de pe calculatorul tău — instrucțiunile
de mai jos. Rezultatul e același; diferă doar unde rulează procesul.

---

## Instalare (varianta locală)

Ai nevoie de **Node 18 sau mai nou** și de un token pe care ți-l dă Stefan.

Verifică întâi ce versiune ai:

```
node -v
```

> Dacă `node -v` îți arată o versiune sub 18, nu e o problemă — folosește mai jos
> calea completă către Node-ul nou, în loc de `"node"`. Serverul îți spune clar
> dacă a pornit cu versiunea greșită.

```
cd mcp-dianasoft
npm install
npm run verifica
```

`npm run verifica` pornește serverul și confirmă că răspunde. Dacă scrie
„toate verificările: OK", ești gata.

Apoi adaugă serverul în configurarea Claude-ului tău:

```json
{
  "mcpServers": {
    "dianasoft-serviciiweb": {
      "command": "C:/Program Files/nodejs/node.exe",
      "args": ["C:/calea/catre/mcp-dianasoft/bin.cjs"],
      "env": {
        "DIANASOFT_URL": "https://app.dianasoft.ro",
        "DIANASOFT_TOKEN": "token-ul primit de la Stefan"
      }
    }
  }
}
```

Dacă `node -v` îți arată deja 18 sau mai mult, poți lăsa doar `"command": "node"`.

Repornește Claude. Dacă a mers, întrebarea „ce tabele pot edita în DianaSoft?"
îți întoarce lista de mai sus.

**Token-ul e personal.** Tot ce faci apare în istoricul aplicației cu numele tău.
Nu-l pune într-un fișier partajat și nu-l trimite mai departe.

---

## Cum se lucrează

Modificarea în masă are **două faze**, ca să nu se schimbe nimic din greșeală.

**Întâi ceri modificarea.** Serverul nu schimbă nimic — îți spune câte rânduri ar fi
atinse și îți arată exemple de „înainte / după":

> Am găsit 14 societăți fără e-mail de declarant. Iată cum ar arăta după
> modificare. Confirmi?

**Apoi confirmi.** Abia atunci se aplică, exact pe rândurile arătate.

Dacă rezultatul nu e cel dorit, modificarea **se poate anula**: valorile dinainte
se pun la loc pe fiecare rând. Cere-i asistentului istoricul operațiilor și anularea
celei greșite.

### Exemple de întrebat (citire)

> Ce tabele există cu „declaratii" în nume?

> Câte declarații am depus pe fiecare tip, luna trecută?

> Ce mesaje SPV am primit săptămâna asta și pentru ce firme?

> Care certificate expiră în următoarele 30 de zile?

> Ce coloane are tabela `anaf_declaratii`?

### Exemple de cerut (modificare)

> Câte societăți au câmpul `functie_declarant` gol?

> Pune `functie_declarant` = „Administrator" la toate societățile care au acum gol.

> Marchează societatea cu CIF 12345678 ca scoasă din uz.

> Anulează ultima modificare pe societăți.

---

## Ce e bine de știut

- **Citirea întoarce maxim 500 de rânduri.** Pe tabelele mari (mesaje SPV, jurnal,
  registrul comerțului) nu cere rânduri, cere sinteze: grupare și sume. Asistentul
  știe să facă asta.
- **Peste 20 de rânduri**, confirmarea e obligatorie — nu se poate sări peste
  simulare.
- **Peste 2.000 de rânduri** operația e refuzată; restrânge filtrul.
- **Un rând adăugat nu se poate șterge** prin acest acces. Dacă tabela are câmpul
  `activ`, pune-l pe 0 printr-o modificare.
- **Fiecare operație intră în `mcp_operatii`**, cu valoarea dinainte și cea de
  după, rând cu rând. Stefan poate vedea oricând ce s-a schimbat și de cine.
- Dacă token-ul expiră sau e revocat, uneltele întorc „token invalid" — cere unul nou.

---

## Pentru Stefan

Generarea token-ului (varianta locală):

```bash
php artisan mcp:token cineva@client.ro --zile=180
php artisan mcp:token cineva@client.ro --revoca
```

Se afișează o singură dată. Un singur token MCP activ per utilizator — la
regenerare, cel vechi se revocă automat. Conectorul remote nu are nevoie de el:
acolo tokenul iese din fluxul OAuth, după autentificarea în browser.

Ce se poate atinge se schimbă dintr-un singur loc: `config/mcp.php`. Adăugarea unei
tabele la scriere înseamnă un model, o cheie, lista câmpurilor căutabile și lista
celor editabile; `doar_administrator: true` o rezervă administratorului. Lista
`citire.tabele_comune` spune ce tabele fără `company_id` poate citi oricine.

Piesele din backend:

- `routes/mcp.php` — `POST /mcp` (conectorul), documentele de descoperire OAuth și
  `POST /oauth/register` (înregistrare dinamică de client, RFC 7591);
- `routes/api_routes/mcp_routes.php` — `api/mcp/*`, folosite de procesul Node;
- `McpController` — execuția, comună ambelor drumuri; `McpRpcController` — JSON-RPC;
- `AutentificareWebController` + `GET /login` — autentificarea în browser pentru
  pasul de autorizare OAuth (SPA-ul nu are sesiune web);
- `ContextMcp` — pune utilizatorul și firma în sesiune pentru cererile MCP.
