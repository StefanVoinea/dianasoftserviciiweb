# Pagini de prezentare

Câte una pentru fiecare modul. Structura e aceeași: ce doare, ce face, cum
arată, ce nu promitem, ce urmează.

---

# 1. Declarații ANAF și SPV

## Titlu

**Declarațiile lunii, dintr-o singură apăsare**

Încărcați toate declarațiile deodată. Aplicația le validează cu DUKIntegrator, le
semnează cu certificatul dumneavoastră și le depune. Recipisele le aduce singură.

`{{ buton: Încearcă gratuit }}` · `{{ durata probei }} zile, fără card`

---

## Ce se schimbă

### Nu mai depuneți de șaizeci de ori

Alegeți fișierele — sau un folder întreg — și apăsați o dată. Se validează, se
semnează, se depun. Ce a eșuat rămâne în tabel, cu motivul lângă.

### Erorile, în română

Când validatorul respinge o declarație, nu primiți „R33: atribut cui: valoare
eronata". Primiți ce e greșit, de ce, **pe ce linie și coloană din fișierul XML**,
și chiar linia aceea, cu partea greșită colorată.

Interpretările sunt construite pe catalogul de mesaje al validatoarelor ANAF.

### Recipisele vin singure

Bifați „automat" și alegeți intervalul: 5, 10, 15, 30 sau 60 de minute. Se aduc
când apar, se așază lângă declarația la care răspund și vă arată verdictul ANAF
în tabel.

### Documentele rămân la dumneavoastră

Declarațiile semnate, recipisele și documentele aduse din SPV se scriu pe
calculatorul din birou, în dosarul pe care îl alegeți — inclusiv pe un disc de
rețea. Ordonate singure:

```
D:\Documente fiscale\
└── DIANA SOFT SRL (15208744)\
    ├── D112\
    │   ├── D112_15208744_2026-06_depusa_912239948.pdf
    │   └── D112_15208744_2026-06_recipisa_912239948.pdf
    └── SPV\
        └── Situatie Sintetica\Situatie Sintetica_15208744_2026-07-29_5104283612.pdf
```

Numele spune tipul, CUI-ul, perioada și starea. Dacă plecați de la noi, nu aveți
ce recupera: sunt deja la dumneavoastră, în PDF și XML.

### Certificatul nu pleacă din birou

Aplicația e în cloud, tokenul e la dumneavoastră. Semnarea și depunerea se fac pe
calculatorul unde e băgat, printr-un program local. Noi nu avem cheia privată și
nu putem semna nimic în numele dumneavoastră.

Pe mai multe calculatoare pot sta tokenuri diferite. Fiecare declarație pleacă
singură către calculatorul cu tokenul care a înrolat firma respectivă — nu trebuie
ținut minte de nimeni.

### Fiecare angajat vede ce a depus el

Într-un birou cu mai mulți oameni, fiecare își vede declarațiile și solicitările
lui, plus mesajele SPV ale certificatelor la care i s-a dat acces.
Administratorul biroului vede tot, iar jurnalul arată cine ce a depus și când.

---

## Mai face și

- **Alerte pe email** când intră în SPV un anumit fel de document, pentru o firmă
  anume sau pentru toate firmele unui certificat
- **Un singur PDF pentru imprimantă**, din toate declarațiile sau recipisele
  sesiunii, cu denumirea firmei în filigran
- **Semnătură vizibilă** pe PDF: titular, dată, seria certificatului, emitentul și
  data expirării
- **Entități înrolate** aduse din SPV, cu denumirea oficială a fiecărei firme
- **Vector fiscal**: ce așteptați față de ce scrie ANAF
- **Solicitări SPV** și răspunsurile lor
- **Jurnal de activitate**

---

## Ce nu promitem

**Nu scăpați de PIN.** Tokenul își cere PIN-ul — aceasta e cerința care face
semnătura valabilă juridic. Cât de des îl cere depinde de driver; multe permit o
singură introducere pe sesiunea de Windows, și vă arătăm unde se reglează.

**Nu suntem „agreați de ANAF".** Folosim serviciile publice ANAF, ca orice
aplicație de acest fel.

**Nu înlocuim programul de contabilitate.** Ne ocupăm de partea dinspre ANAF.

---

## Ce urmează

1. Vă faceți cont și primiți `{{ durata probei }}` zile de probă
2. Instalați programul de acces la token — un fișier, o singură dată
3. Trageți în aplicație declarațiile unei firme și vedeți ce se întâmplă

`{{ buton: Începe }}` · sau `{{ link: cere o demonstrație de 20 de minute }}`

---
---

# 2. e-Transport

## Titlu

**Transporturile și UIT-urile, într-un singur tabel**

Declarați transporturile de bunuri cu risc fiscal ridicat și urmăriți starea
fiecăruia la ANAF, fără drumuri prin SPV.

`{{ buton: Încearcă gratuit }}`

---

## Ce se schimbă

### Declarați din aplicație

Transportul se declară direct, cu certificatul digital sau cu autorizare OAuth2.
Mediul se alege: producție sau test.

### UIT-urile, la un loc

Fiecare transport declarat rămâne în tabel, cu UIT-ul și starea lui la ANAF.
Când vine controlul, nu mai căutați prin mailuri.

### Același cont, același certificat

Dacă folosiți deja modulul de declarații, nu instalați nimic în plus și nu
configurați alt certificat.

---

## Ce nu promitem

Schema e-Transport se schimbă des, la deciziile ANAF. Ținem pasul, dar nu vă
promitem că o schimbare publicată azi e implementată azi.

`{{ buton: Încearcă }}` · `{{ durata probei }} zile`

---
---

# 3. Portal Just

## Titlu

**Aflați de termen când apare, nu când e trecut**

Scrieți numerele de dosar sau numele părților. Verificăm din oră în oră și vă
trimitem email când se schimbă ceva.

`{{ buton: Încearcă gratuit }}`

---

## Ce se schimbă

### Nu mai verificați manual

Introduceți dosarele de mână sau încărcați un fișier Excel cu toată lista.
Verificarea rulează singură.

### Urmăriți și după numele părții

Nu doar după numărul dosarului. Utile mai ales când vreți să știți dacă un client
a fost chemat într-un dosar nou.

### Alerta ajunge unde sunteți

Pe email, cu ce anume s-a schimbat: termen nou, soluție, parte adăugată. Și pe
telefon, prin aplicația de Android, de unde vedeți și lista dosarelor urmărite.

### Căutare în ECRIS

Dosare, părți și ședințele instanțelor, din aplicație.

---

## Un amănunt care spune ceva

Am măsurat când actualizează instanțele dosarele: între 08:00 și 16:00, cel mai
des pe la 10:00. De aceea verificarea e din oră în oră, în timpul zilei — nu o
dată pe noapte, cum ar fi fost mai comod de făcut.

---

## Ce nu promitem

Datele vin din portal.just.ro. Când portalul e indisponibil sau întârzie
publicarea, întârziem și noi. Nu putem ști mai devreme decât instanța.

`{{ buton: Începe }}` · `{{ durata probei }} zile, fără card`
