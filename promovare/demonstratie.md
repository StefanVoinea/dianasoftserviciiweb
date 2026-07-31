# Demonstrația de 20 de minute

Pentru un birou de contabilitate. Scenariul e construit ca obiecția cea mai grea
— *„nu-mi pun certificatul într-un program din internet"* — să fie **arătată**, nu
explicată, și încă din primele trei minute.

Se face pe ecranul lor, cu un certificat de test sau cu al lor, nu pe date reale
ale altui client.

---

## Înainte (5 minute, singur)

- [ ] Un certificat de test conectat și programul local pornit
- [ ] 3–4 declarații pregătite: două valide, **una cu o greșeală adevărată**
      (un CUI cu o cifră schimbată e cel mai bun exemplu — eroarea e clară și
      corectura, evidentă)
- [ ] Dosarul de arhivă gol, ca structura să se vadă cum se naște
- [ ] Un CUI neînrolat, pentru atenționarea din tabel
- [ ] Închise: emailul, notificările, orice altă filă cu date

---

## 0–3 min · Unde stă certificatul

Nu începe cu produsul. Începe cu întrebarea lor.

> „Prima întrebare pe care mi-o pune toată lumea e unde ajunge certificatul.
> Hai să vă arăt, și după aceea ne uităm la restul."

Arată în *Certificate digitale* calculatorul pe care e tokenul. Spune:

> „Aplicația e în cloud. Tokenul e la dumneavoastră. Când semnez, cererea pleacă
> spre calculatorul unde e băgat, el semnează, el urcă la ANAF. Noi n-avem cheia —
> cheia de pe token nu se poate copia, ăsta e rostul lui."

**Scoate tokenul din calculator** și încearcă să semnezi. Aplicația răspunde
„Tokenul cu certificatul cerut nu este conectat la acest calculator". Bagă-l la
loc.

Momentul ăsta valorează cât tot restul demonstrației. Nu-l sări.

---

## 3–8 min · Lotul și eroarea

Trage toate declarațiile deodată. Apasă o dată.

Lasă-le să treacă. Când se oprește la cea greșită:

> „Uitați ce spune validatorul ANAF." — arată eroarea brută.
> „Și uitați ce spunem noi." — deschide interpretarea.

Arată **linia și coloana din XML**, și linia afișată cu partea greșită colorată.

Nu explica mai mult. Un contabil care a pierdut o oră căutând o virgulă înțelege
singur.

---

## 8–12 min · Recipisele și arhiva

Pornește descărcarea automată a recipiselor și arată intervalul.

Apoi deschide **dosarul de pe calculatorul lor**, în Windows Explorer — nu în
aplicație. Asta e important: se vede că sunt fișiere adevărate, nu ceva dintr-un
program.

```
D:\Documente fiscale\DIANA SOFT SRL (15208744)\D112\
    D112_15208744_2026-06_depusa_912239948.pdf
    D112_15208744_2026-06_recipisa_912239948.pdf
```

> „Numele spune tipul, CUI-ul, luna, că s-a depus și cu ce index de încărcare.
> Recipisa stă lângă declarația ei. Dacă plecați mâine de la noi, n-aveți ce
> recupera — sunt deja la dumneavoastră."

Deschide un PDF semnat și arată caseta de semnătură: titular, dată, serie
certificat, emitent, data expirării.

---

## 12–16 min · Ce cere biroul, dar rar întreabă

Trei lucruri de arătat repede, dacă biroul are mai mulți angajați:

**Utilizatorii.** Fiecare vede ce a depus el; administratorul vede tot.
Certificatele se dau pe om.

**Atenționarea de firmă neînrolată.** Arată CUI-ul pregătit dinainte, cu
triunghiul galben, și citește tooltipul.

**Jurnalul.** Cine ce a depus și când.

---

## 16–20 min · Întrebări, și un singur lucru cerut

Nu întreba „ce părere aveți". Întreabă:

> „Care e prima declarație pe care ați vrea s-o încercați dumneavoastră?"

E o întrebare la care răspunsul e o acțiune, nu o politețe.

Apoi, un singur lucru:

> „Vă deschid proba `{{ durata probei }}` zile. Nu cer card. Instalez eu programul
> pe calculatorul cu tokenul, dacă vreți, în zece minute — acum sau când vă e
> comod."

---

## Ce **nu** faci în demonstrație

- **Nu depui o declarație adevărată** a unui client de-al lor. Vorbește-o
  dinainte dacă vor să încerce pe date reale.
- **Nu arăți date ale altui birou.** Nici „acoperite".
- **Nu promiți module care nu sunt gata.** Dacă întreabă de ceva ce nu există,
  spune că nu există și, dacă e cazul, când.
- **Nu spui că scapă de PIN.** Spui că se reglează din driver, și arăți unde.

---

## Dacă întreabă de preț la minutul 4

Spune-l. Nu-l amâna — amânarea îi face să nu asculte restul.

> „`{{ preț }}` pe lună per modul, `{{ durata probei }}` zile de probă fără card.
> Hai să vedem întâi dacă vă e de folos."
