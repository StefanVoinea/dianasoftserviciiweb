# Argumentele de fond

Ce se spune, de ce e adevărat, și ce se răspunde la obiecția care vine sigur.
Restul materialelor sunt scrise pornind de aici.

---

## 1. Documentele rămân la client, nu în cloud

**Ce spunem:** aplicația stă în cloud, dar declarațiile semnate, recipisele și
documentele aduse din SPV se scriu pe calculatorul biroului, într-un dosar ales
de el — inclusiv pe un NAS din rețea. Serverul reține doar unde sunt.

**De ce e adevărat:** arhivarea trece prin programul local instalat lângă token.
Structura e făcută de aplicație:

```
D:\Documente fiscale\
└── DIANA SOFT SRL (15208744)\
    ├── D112\D112_15208744_2026-06_depusa_912239948.pdf
    │        D112_15208744_2026-06_recipisa_912239948.pdf
    └── SPV\Situatie Sintetica\Situatie Sintetica_15208744_2026-07-29_5104283612.pdf
```

**De ce contează pentru un contabil:** răspunde singură la întrebarea „unde îmi
sunt documentele dacă mă cert cu furnizorul de software?". Sunt la el, în format
deschis, ordonate — le poate deschide și fără aplicație.

**Obiecția:** *„Și dacă mi se strică calculatorul?"*
**Răspuns:** dosarul poate fi pe un disc de rețea sau într-un folder sincronizat.
Aplicația scrie unde i se spune.

---

## 2. Tokenul nu pleacă nicăieri

**Ce spunem:** certificatul digital rămâne fizic la birou, în calculatorul unde e
băgat. Semnarea și depunerea se fac acolo, nu pe serverul nostru.

**De ce e adevărat:** cheia privată de pe token nu e exportabilă. Aplicația
trimite documentul spre programul local de pe calculatorul cu tokenul, el
semnează și el urcă la ANAF.

**De ce contează:** e cel mai frecvent motiv pentru care birourile refuză
aplicațiile în cloud. Aici obiecția dispare, pentru că premisa ei nu se aplică.

**Bonus real:** pe același calculator pot sta două tokenuri odată, iar pe mai
multe calculatoare din rețea pot sta tokenuri diferite. Aplicația trimite fiecare
declarație către calculatorul unde se află tokenul cu care e **înrolată acea
firmă** — nu trebuie ținut minte de nimeni.

---

## 3. Erorile validatorului, traduse

**Ce spunem:** când DUKIntegrator respinge o declarație, aplicația spune în
română ce e greșit, unde anume în fișierul XML (linia și coloana) și ce trebuie
corectat.

**De ce e adevărat:** interpretarea e construită pe catalogul de mesaje din
validatoarele ANAF, nu pe ghicit. Linia din XML se arată colorată: partea cu
eroarea în roșu, restul în albastru.

**De ce contează:** „R33: atribut cui: valoare eronata" nu ajută pe nimeni la 11
noaptea, pe 25. Un contabil care a pierdut o oră căutând o virgulă înțelege asta
imediat.

---

## 4. Lotul, nu bucata

**Ce spunem:** încarci toate declarațiile lunii deodată — sau un folder întreg —
și aplicația le validează, le semnează și le depune pe rând. Recipisele se aduc
singure, la un interval pe care îl alegi.

**De ce contează:** un birou cu 60 de firme depune de 60 de ori aceeași
succesiune de clicuri. Aici o face o dată.

**De verificat înainte de a promite un număr:** câte declarații pe lot merg fără
să obosească tokenul. Depinde de PIN — vezi punctul următor.

---

## 5. Ce spunem despre PIN

**Ce spunem:** tokenul își cere PIN-ul, așa cum cere legea. Cât de des îl cere
depinde de driverul tokenului; multe permit „single logon", adică o dată pe
sesiunea de Windows.

**Ce nu spunem:** că aplicația „scapă de PIN". Certificatul e calificat, iar
controlul exclusiv asupra semnăturii e chiar temeiul valabilității ei juridice.
Un furnizor care promite altceva fie nu știe ce spune, fie îți creează o
problemă.

**Cum se transformă în avantaj:** e un semn de seriozitate. Contabilii care au
pățit-o apreciază pe cineva care le spune ce nu se poate.

---

## 6. Fiecare vede ce a depus

**Ce spunem:** într-un birou cu mai mulți angajați, fiecare își vede propriile
declarații și solicitări. Mesajele din SPV le vede pe ale certificatelor la care
i s-a dat acces. Administratorul biroului vede tot.

**De ce contează:** birourile cu 5–20 de angajați au nevoie de asta, dar rar o
găsesc. E și un argument de responsabilitate: jurnalul de activitate arată cine
ce a depus și când.

---

## 7. Portal Just: monitorizarea

**Ce spunem:** scrii numerele de dosar sau numele părților — de mână sau dintr-un
fișier Excel — și primești email când apare o modificare: termen nou, soluție,
parte adăugată. Există și aplicație de Android, cu alertă pe telefon.

**De ce e adevărat:** verificarea rulează din oră în oră. Am măsurat când
actualizează instanțele: între 08:00 și 16:00, cu vârf pe la 10:00.

**De ce contează:** avocatul sau juristul care verifică manual 40 de dosare
pierde o oră pe zi. Contabilul care urmărește insolvențele unui client află la
timp.

---

## 8. e-Transport

**Ce spunem:** declararea transporturilor și urmărirea UIT-urilor, cu certificatul
digital sau prin autorizare OAuth2, pe mediul de producție sau de test.

**De verificat înainte de campanie:** starea exactă a modulului față de ultima
schemă ANAF. e-Transport se schimbă des, iar o promisiune depășită se vede din
prima zi.

---

## Obiecțiile care vin sigur

**„Am deja un program de contabilitate."**
Nu înlocuiește programul de contabilitate. Se ocupă de partea dinspre ANAF:
validare, semnare, depunere, recipise, SPV. Multe birouri au programul de
contabilitate și, separat, ore pierdute cu DUKIntegrator și cu descărcatul
recipiselor una câte una.

**„Folosesc DUKIntegrator gratuit."**
Și aplicația îl folosește — e același validator oficial. Diferența e ce se
întâmplă în jurul lui: lotul, traducerea erorilor, semnarea, depunerea,
recipisele, arhiva ordonată.

**„Nu-mi pun eu certificatul într-un program din internet."**
Nici nu se pune. Vezi punctul 2 — merită arătat, nu explicat: se vede în
demonstrație că tokenul rămâne în calculatorul lui.

**„Cât costă?"**
`{{ preț }}` pe lună, cu `{{ durata probei }}` zile de probă, fără card la
înscriere. Modulele se iau separat.

**„Și dacă vreau să plec?"**
Documentele sunt deja la tine, în dosarele tale, în PDF și XML. Nu ai ce
recupera de la noi.
