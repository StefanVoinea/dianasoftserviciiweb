# Decontul de TVA scos din SAF-T

ANAF a publicat, în august 2026, aplicația **D300_2026** — scoate decontul de TVA
(D300) dintr-un fișier SAF-T (D406). Ea nu poate fi chemată de pe server, așa cum
e chemat DUKIntegrator: toată prelucrarea stă într-un singur `main` cu fereastră
Swing, iar exportul (PDF, XML, JSON) se face din butoane. Nu există nicio clasă
de apelat, cum erau `validator.Validator` și `pdf.PdfSuperCreator` la D406 (vezi
`tools/duk-d406`).

Regulile ei, în schimb, sunt curat așezate: 61 de mulțimi de coduri TVA și câteva
sute de adunări peste rândurile decontului. Ele sunt mutate în PHP, **mecanic**,
de `genereaza.php`. Nu se scrie nimic de mână: ce iese este exact ce face
aplicația ANAF, linie cu linie.

## Cum se reface

Când ANAF scoate versiunea următoare (D300_2027 și așa mai departe):

```bash
# 1. Prelucrarea și raportul, scoase din jar-ul aplicației
unzip -j D300_2027.jar "anaf/saft/Parsing3.class" -d cls
unzip -j D300_2027.jar "reports/raport2027.jrxml" -d rap

# 2. Generatorul de PDF și validatorul, din instalarea DUKIntegrator: cele mai
#    noi clase (versiunile „P" și „J" din config/versiuniCurente.txt)
unzip -j anaf-tools/dist/lib/D300Pdf.jar "d300/Pdf_v8.class" -d pdf
unzip -j anaf-tools/dist/lib/D300Validator.jar "d300validator/v10/Declaratie300.class" -d val

# 3. Decompilate (CFR — https://www.benf.org/other/cfr/)
java -jar cfr.jar cls/Parsing3.class --outputdir dec
java -jar cfr.jar pdf/Pdf_v8.class > pdf/Pdf_v8.java
java -jar cfr.jar val/Declaratie300.class > val/Declaratie300.java

# 4. Trecute în PHP
php tools/d300/genereaza.php \
    --parsing=dec/anaf/saft/Parsing3.java \
    --raport=rap/raport2027.jrxml \
    --pdf=pdf/Pdf_v8.java \
    --validator=val/Declaratie300.java \
    --nomenclator=RO_SAFT_SchemaDefCod.xlsx
```

Ies patru fișiere în `app/Services/Anaf/Declaratii/D300`:

| Fișier | Din ce | Ce ține |
|---|---|---|
| `CoduriD300.php` | Parsing3 | cele 61 de mulțimi de coduri TVA |
| `ReguliD300.php` | Parsing3 | adunările peste rândurile decontului, în trei momente |
| `RanduriD300.php` | raport + PDF + validator | rândul de pe formular, atributul din XML, totalurile și soldurile |
| `NomenclatorTva.php` | documentația SAF-T | ce înseamnă fiecare cod de taxă și ce cotă are |
| `FormularD300.php` | PDF-ul soft A | unde stă fiecare rând și fiecare câmp în formularul inteligent |

Numai primele două sunt de neapărată trebuință; celelalte adaugă vorbele și
legătura cu XML-ul.

### De ce trebuie și validatorul

Pentru că declarația nu e doar o listă de rânduri: validatorul ANAF cântărește și
legăturile dintre ele, iar una care nu iese e respinsă. Trei feluri de legături,
toate scoase din el:

- **totaluri** — „R17_1 = R1_1 + R2_1 + …" (regulile R65, R66, R99, R100, R108,
  R113, R116);
- **solduri** — „R41_2 = max(R37_2 − R40_2, 0)" (R117, R118): ori iese suma de
  recuperat, ori taxa de plată, niciodată amândouă;
- **egalități** — „R18_1 = R5_1" (V7, V8 și celelalte): rândul 20 e rândul 5
  văzut din partea deducerii, iar declarația le cere scrise deopotrivă.

Aplicația ANAF de decont nu tipărește toate rândurile pe care declarația le cere
(pe cele copiate le desenează cu câmpul celuilalt), așa că fără regulile astea
declarația ar ieși neîntreagă. Cu ele, fișierul scris de `DecontXml` trece de
DUKIntegrator și iese și PDF-ul oficial — asta cântărește și testul
`DecontXmlTest::test_declaratia_trece_de_validatorul_anaf`.

### Cele două fișiere care ies pentru om

Din același decont se scriu două fișiere, pentru două drumuri deosebite:

| Fișier | Scris de | Pentru | Formă |
|---|---|---|---|
| `D300_<cui>_<perioada>.xml` | `DecontXml` | validare și depunere | atribute pe schema `v12` — `R5_1="1000"` |
| `D300_formular_<cui>_<perioada>.xml` | `DecontFormular` | PDF-ul inteligent (soft A) | date XFA — `form1/date/comert/r5/c2` |

Cel de-al doilea se încarcă din **Acrobat Reader → Import Data** (ANAF n-a pus
buton de încărcare în formular). Structura lui — unde stă fiecare rând și fiecare
câmp — e citită din chiar PDF-ul publicat de ANAF: el e un formular XFA, iar
fiecare rând e un subformular (`r5`, `r12_1`) cu două căsuțe, `c2` pentru bază și
`c3` pentru taxă. Totalurile nu se scriu acolo: formularul și le face singur.

### Numărul de evidență a plății

Nu e un număr liber: validatorul îl desface cifră cu cifră (regula R25) — poziții
fixe, codul impozitului după felul decontului, perioada raportată, scadența de 25
ale lunii următoare și o cifră de control. Se alcătuiește în `DecontXml`, după
aceeași rețetă.

### De ce trebuie și generatorul de PDF

Pentru că **numărul rândului de pe formular nu e numărul din numele
atributului**. Când ANAF a adăugat rânduri la mijlocul decontului, a păstrat
numele vechi și le-a dat celor noi nume din coadă:

| Rândul de pe formular | Atributul din XML |
|---|---|
| 17 | `R64_1` / `R64_2` |
| 19 — TOTAL TAXĂ COLECTATĂ | `R17_1` / `R17_2` |
| 24 | `R22_1` / `R22_2` |
| 38 | `R35_2` |

Cine ar scrie XML-ul după numărul rândului ar pune cifrele pe alte rânduri decât
cele bune — iar declarația ar trece de validare tocmai așa greșită. Legătura o
știe numai generatorul de PDF al ANAF, care desenează formularul: acolo, fiecare
`drawLine` are și numărul rândului, și atributul, și denumirea.

### Cele două izvoare pentru nomenclator

Corespondența cod → rând din decont se ia din chiar regulile aplicației
(`CoduriD300`), fiindcă în documentația SAF-T coloana „Corespondent rand D300"
e încă „TBD" la achiziții. Din documentație se iau numai denumirile codurilor și
cotele — pentru lămuriri, nu pentru socoteală.

Toate poartă în cap `FIȘIER GENERAT`. Nu se umblă în ele: se generează din nou.

Generatorul se oprește cu eroare la primul lucru pe care nu-l recunoaște — un
nume de variabilă nou, o formă de instrucțiune nemaiîntâlnită. Asta e voit: mai
bine se oprește decât să scape ceva pe tăcute. Când se oprește, se citește ce
spune și se completează tabelele din capul lui (`STEAGURI`, `IN_STARE`,
`ALE_LINIEI`).

După regenerare, testul `DecontDinSaftTest::test_regulile_generate_sunt_intregi`
va pica dacă s-au schimbat numărul de mulțimi, de apartenențe sau de rânduri.
Nu înseamnă că noile reguli sunt greșite — înseamnă că s-au schimbat, și că
cifrele așteptate din celelalte teste trebuie privite încă o dată.

## Ce nu se generează

Citirea fișierului XML: ea e scrisă de mână, în
`app/Services/Anaf/Declaratii/D300/DecontDinSaft.php`, pentru că în aplicația
ANAF stă amestecată cu fereastra și cu raportul Jasper. Acolo sunt și cele două
lucruri de ținut minte:

- steagurile `is4428` și `is35328`, ca și codul de taxă, **nu se sting** la
  sfârșitul liniei în aplicația ANAF; rămân aprinse până la sfârșitul fișierului.
  Are toate semnele unei scăpări, dar se păstrează întocmai: decontul acesta se
  compară cu al lor, pe același fișier, iar o socoteală mai bună decât a lor ar
  fi, pentru omul care depune, o socoteală diferită;
- la un câmp gol, aplicația ANAF cade (`NumberFormatException`); aici se ia drept
  zero.

## Cum se verifică pe fișiere adevărate

Aplicația ANAF exportă decontul în JSON (butonul „Export JSON"), cu aceleași
denumiri de rânduri (`RD5_BAZA`, `RD5_TVA`, …) și cu numerele nerotunjite. Pe un
SAF-T adevărat:

1. se rulează `java -jar D300_2026.jar fisier.xml`, se apasă Export JSON;
2. se rulează același fișier prin `DecontDinSaft`;
3. se compară rând cu rând.

Diferență zero înseamnă mapare corectă. Este singura dovadă care contează, și e
bine ca fiecare fișier pe care s-a făcut comparația să rămână ca fixtură.
