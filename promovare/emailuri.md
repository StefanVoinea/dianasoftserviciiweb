# Emailuri

Trei secvențe, pe trei feluri de destinatar. Se trimit **doar** către cine s-a
înscris singur sau cu care ai deja o relație (vezi `README.md`).

Regulile de scriere folosite peste tot: subiect sub 60 de caractere, un singur
lucru cerut la sfârșit, fără imagini care ascund mesajul dacă nu se încarcă, și
o cale de dezabonare care chiar funcționează.

---

# A. Birou de contabilitate

## A1 — Primul email (după înscriere)

**Subiect:** Cele 25 de minute dinainte de termen

> Bună ziua,
>
> V-ați înscris pentru DianaSoft. Vă scriu scurt ce face, ca să vedeți dacă merită
> cele douăzeci de minute ale unei demonstrații.
>
> Aplicația se ocupă de partea dinspre ANAF: încărcați declarațiile unei luni
> deodată — sau un folder întreg — iar ea le validează cu DUKIntegrator, le
> semnează cu certificatul dumneavoastră și le depune. Recipisele le aduce
> singură, la intervalul pe care îl alegeți.
>
> Două lucruri pe care le veți remarca de la prima încercare:
>
> **Când validatorul respinge o declarație**, aplicația vă spune în română ce e
> greșit și **pe ce linie din fișierul XML**, nu doar „R33: valoare eronata".
>
> **Certificatul rămâne la dumneavoastră.** Aplicația e în cloud, dar tokenul stă
> în calculatorul din birou; semnarea se face acolo. Nu ne trimiteți nimic din ce
> nu vreți să ne trimiteți.
>
> Aveți `{{ durata probei }}` zile de probă. Fără card, fără obligații.
>
> `{{ buton: Intră în aplicație }}`
>
> O zi bună,
> `{{ nume }}`
> `{{ telefon }}` · `{{ email }}`

## A2 — La 3 zile, dacă n-a intrat

**Subiect:** Ce se întâmplă în primele cinci minute

> Bună ziua,
>
> V-am scris zilele trecute despre DianaSoft. Dacă n-ați apucat să intrați, iată
> ce ar dura cinci minute:
>
> 1. Instalați programul de acces la token pe calculatorul unde e certificatul —
>    un singur fișier, se rulează o dată.
> 2. Apăsați „Citește token-urile conectate". Aplicația vede certificatul.
> 3. Trageți în ea declarațiile unei firme. Le validează și vă arată ce e în
>    regulă și ce nu.
>
> Până aici n-ați depus nimic și n-ați schimbat nimic la felul în care lucrați.
> Vedeți doar dacă vă e de folos.
>
> Dacă preferați să vă arăt eu, sunt `{{ zile disponibile }}` — durează douăzeci
> de minute, pe ecranul dumneavoastră.
>
> `{{ buton: Alege o oră }}`

## A3 — La 10 zile, către cine a încercat dar n-a depus

**Subiect:** V-ați oprit la ceva anume?

> Bună ziua,
>
> Văd că ați încărcat declarații, dar nu ați ajuns la depunere. Nu vă cer nimic —
> vreau doar să știu dacă v-ați lovit de ceva.
>
> Cele trei lucruri de care se împiedică lumea cel mai des:
>
> **Firma nu apare în „Entități înrolate".** Înseamnă că certificatul n-are drept
> de reprezentare pentru acel CUI la ANAF. Aplicația vă atrage atenția în tabel;
> se rezolvă cu formularul 150.
>
> **Tokenul cere PIN la fiecare declarație.** Se rezolvă din driverul tokenului —
> „single logon". Vă spun exact unde, dacă îmi ziceți ce token aveți.
>
> **Nu vă e clar unde ajung documentele.** Ajung la dumneavoastră, în dosarul pe
> care îl alegeți. Vi-l pot configura în două minute.
>
> Răspundeți la acest email cu ce v-a oprit. Citesc eu, personal.

## A4 — Ultimul, cu trei zile înainte de expirarea probei

**Subiect:** Proba se încheie pe `{{ dată }}`

> Bună ziua,
>
> Perioada de probă se încheie pe `{{ dată }}`. După ea, modulele se închid, dar
> **datele rămân** — declarațiile, recipisele și documentele descărcate sunt deja
> în dosarele dumneavoastră, pe calculatorul din birou. Nu aveți ce recupera de la
> noi.
>
> Dacă vreți să continuați: `{{ preț }}` pe lună, per modul.
> Dacă nu: vă mulțumesc că ați încercat, și îmi puteți spune în două rânduri ce
> n-a mers. E singurul fel în care aflu.
>
> `{{ buton: Continuă abonamentul }}`

---

# B. Firmă care declară transporturi (e-Transport)

## B1 — Primul email

**Subiect:** UIT-urile, dintr-un singur loc

> Bună ziua,
>
> Dacă declarați transporturi de bunuri cu risc fiscal ridicat, știți deja partea
> neplăcută: fiecare transport înseamnă un drum prin SPV, iar când vine controlul,
> căutarea UIT-ului potrivit ia mai mult decât ar trebui.
>
> Modulul e-Transport din DianaSoft declară transporturile și vă ține evidența
> UIT-urilor și a stării fiecăruia la ANAF, într-un singur tabel. Merge cu
> certificatul digital sau cu autorizare OAuth2, pe producție sau pe mediul de
> test.
>
> Aveți `{{ durata probei }}` zile de probă, fără card.
>
> `{{ buton: Încearcă }}`

## B2 — La 4 zile

**Subiect:** Certificatul rămâne la dumneavoastră

> Bună ziua,
>
> Cea mai frecventă întrebare la modulul e-Transport: *„unde ajunge certificatul
> meu?"*
>
> Nicăieri. Rămâne în calculatorul dumneavoastră. Aplicația e în cloud, dar
> semnarea și trimiterea către ANAF se fac pe calculatorul unde e băgat tokenul,
> printr-un program local. Noi nu avem cheia și nu putem semna nimic în numele
> dumneavoastră.
>
> Dacă lucrați cu autorizare OAuth2, nici token nu vă trebuie — se face din
> aplicație.
>
> `{{ buton: Vezi cum arată }}`

---

# C. Avocat, jurist, birou care urmărește dosare (Portal Just)

## C1 — Primul email

**Subiect:** Nu mai verificați dosarele manual

> Bună ziua,
>
> Dacă urmăriți dosare pe portal.just.ro, verificarea manuală vă ia probabil o oră
> pe zi — și tot se întâmplă să aflați târziu de un termen mutat.
>
> Modulul Portal Just din DianaSoft vă lasă să scrieți numerele de dosar sau
> numele părților — de mână sau dintr-un fișier Excel — și verifică singur, din
> oră în oră. Când apare o modificare (termen nou, soluție, parte adăugată),
> primiți email cu ce anume s-a schimbat.
>
> Există și aplicație de Android: alerta vă ajunge pe telefon, și vedeți dosarele
> urmărite de acolo.
>
> Un amănunt care spune ceva despre cum lucrăm: am măsurat când actualizează
> instanțele dosarele. Între 08:00 și 16:00, cel mai des pe la 10:00. De aceea
> verificarea e din oră în oră, nu o dată pe noapte.
>
> `{{ buton: Încearcă gratuit }}`

## C2 — La 5 zile

**Subiect:** Importați lista dintr-un Excel

> Bună ziua,
>
> Dacă v-a oprit gândul că trebuie introduse dosarele unul câte unul: nu trebuie.
> Încărcați un fișier Excel cu numerele de dosar sau cu numele părților și
> aplicația le preia pe toate.
>
> Puteți urmări și **după numele părții**, nu doar după numărul dosarului — util
> când vreți să știți când cineva e chemat într-un dosar nou.
>
> `{{ buton: Importă lista }}`

---

# D. Către clienții actuali, pentru un modul pe care nu-l au

**Subiect:** Aveți acces la trei module. Folosiți unul.

> Bună ziua,
>
> Folosiți DianaSoft pentru declarațiile ANAF. Poate vă e de folos să știți că
> același cont deschide și `{{ modul }}`:
>
> `{{ una din descrierile de mai jos }}`
>
> **Portal Just** — urmărește dosarele clienților dumneavoastră și vă anunță pe
> email când apare un termen sau o soluție. Util mai ales pentru insolvențe și
> litigii de muncă, unde aflați de obicei ultimul.
>
> **e-Transport** — dacă vreunul dintre clienți transportă bunuri cu risc fiscal
> ridicat, declarațiile și UIT-urile se fac din același loc, cu același
> certificat.
>
> Vi-l pot deschide pe `{{ durata probei }}` zile, fără nicio obligație. Un
> răspuns la acest email e de ajuns.
