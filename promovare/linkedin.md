# Postări LinkedIn

Scurte, una singură idee fiecare, fără hashtaguri înșirate. Cele care merg cel
mai bine la publicul ăsta sunt cele care spun ceva **util în sine**, chiar dacă
cititorul nu cumpără nimic.

Ritm sănătos: două pe săptămână, din care cel mult una despre produs.

---

## 1. Despre PIN (utilă, nu comercială)

> Un lucru pe care mulți contabili nu-l știu și îi costă zeci de minute pe lună:
>
> dacă tokenul vă cere PIN-ul la **fiecare** declarație semnată, de vină e de
> obicei o setare din driverul tokenului, nu programul cu care semnați.
>
> La SafeNet Authentication Client se cheamă „single logon" și înseamnă un PIN
> pe sesiunea de Windows, nu unul pe document. Athena și Gemalto au echivalentul
> lor.
>
> Atenție însă la promisiunile de tipul „scapi complet de PIN". Certificatul
> calificat cere control exclusiv asupra semnăturii — de-aia e valabilă juridic.
> Un furnizor care vă spune că elimină PIN-ul de tot fie nu știe ce vinde, fie vă
> creează o problemă pe care o veți descoperi când nu trebuie.

---

## 2. Despre erorile DUKIntegrator

> „R33: atribut cui: valoare eronata"
>
> Ăsta e tot ce afli de la validatorul ANAF pe 25, la ora 23. Fără linie, fără
> coloană, fără context.
>
> Am petrecut o săptămână citind validatoarele din DUKIntegrator ca să pot traduce
> mesajele în ceva care se poate corecta: ce e greșit, de ce, pe ce linie din XML,
> și linia aceea afișată cu partea greșită colorată.
>
> Nu era muncă spectaculoasă. Dar e diferența dintre zece minute și o oră, de
> fiecare dată.

---

## 3. Despre unde stau documentele

> Întrebarea la care se împiedică orice discuție despre software fiscal în cloud:
>
> *„Și certificatul meu unde ajunge?"*
>
> Răspunsul corect e „nicăieri". Cheia privată de pe un token nu e exportabilă —
> asta e chiar rostul tokenului. Orice aplicație care poate semna în numele tău
> fără ca tokenul să fie băgat în calculatorul tău ar trebui să te pună pe gânduri.
>
> La noi semnarea se face pe calculatorul unde e tokenul, printr-un program local.
> Aplicația e în cloud; semnătura, nu.
>
> Iar documentele semnate se scriu tot la client, în dosarele lui. Dacă pleacă de
> la noi, nu are ce recupera — le are deja.

---

## 4. Despre când actualizează instanțele

> Aveam nevoie să știu cât de des să verific dosarele pe portal.just.ro.
>
> Presupunerea comodă era „peste noapte". Am măsurat, în loc să presupun:
> actualizările apar între 08:00 și 16:00, cu vârf pe la 10:00.
>
> Concluzia practică pentru oricine urmărește dosare: o verificare pe noapte vă
> face să aflați cu o zi întârziere de un termen mutat. Verificarea trebuie să
> ruleze în timpul programului instanțelor.
>
> Măsurați înainte să presupuneți. Chiar și lucrurile care par evidente.

---

## 5. Despre lot

> Un birou de contabilitate cu 60 de firme depune, în fiecare lună, aceeași
> succesiune de clicuri de 60 de ori: deschide, validează, semnează, urcă,
> așteaptă recipisa, salvează, redenumește.
>
> Nu e muncă. E frecare.
>
> Din partea noastră: încarci folderul, apeși o dată, iar recipisele vin singure
> și se așază lângă declarațiile lor, cu nume care spun tipul, CUI-ul, perioada și
> dacă s-a depus.

---

## 6. Anunț de modul nou (când e cazul)

> `{{ modul }}` e disponibil în DianaSoft de azi.
>
> `{{ o propoziție despre ce face }}`
>
> Pentru cine folosește deja aplicația: se deschide din același cont, cu același
> certificat, fără să instalați nimic în plus.
>
> `{{ durata probei }} zile de probă, fără card. Link în primul comentariu. }}`

---

## Ce nu postăm

- Capturi de ecran cu date reale ale unui client, chiar și „acoperite"
- „Cel mai bun program de..." — nu e verificabil și nu convinge pe nimeni
- Comparații nominale cu concurenții
- Termene ANAF prezentate ca noutate proprie (le știe toată lumea)
