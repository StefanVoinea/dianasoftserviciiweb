# Aplicațiile de telefon puse la îndemâna clienților

Aici stau arhivele `.apk` pe care clientul le ia din fila „Certificate digitale"
și după care telefonul lui se uită, la fiecare pornire, când caută o versiune
mai nouă.

## Cum se numesc

    <aplicatia>-<versiune>+<cod>.apk

de pildă:

    spv_curier-1.1.0+2.apk

Versiunea și codul sunt chiar cele din `pubspec.yaml` al aplicației
(`version: 1.1.0+2`). Numele nu e o podoabă: de acolo își ia serverul versiunea.

Se ține în nume, și nu într-un fișier alăturat, tocmai ca să nu se poată despărți
de program. Un fișier de alături se uită la înlocuire — și atunci telefoanele ar
fi chemate să se înnoiască la o versiune pe care n-o au, sau ar sta cu una veche
crezând că e cea nouă.

## Cum se pune una nouă

Dintr-o singură apăsare, din dosarul aplicațiilor de telefon:

    powershell -ExecutionPolicy Bypass -File publica.ps1 spv_curier -Ridica

`-Ridica` crește întâi versiunea în `pubspec.yaml` (1.1.0+2 → 1.1.1+3), apoi
compilează și așază arhiva aici, cu numele scris cum trebuie. Fără `-Ridica`, se
folosește versiunea care e deja scrisă acolo.

**Codul de după `+` trebuie să crească.** După el se face socoteala, nu după
nume: telefonul se înnoiește numai când codul de pe server e mai mare decât al
lui. Serverul oprește o arhivă cu cod mai mic sau la fel — altfel ea ar sta aici
fără să ajungă vreodată nicăieri, și nimeni n-ar ști de ce.

Cea cu codul cel mai mare e cea care se dă. Celelalte pot rămâne: nu încurcă pe
nimeni, și e bine să ai la ce te întoarce.

## Cum ajunge pe serverul adevărat

Din fila „Certificate digitale", cu unealta de sub butonul de descărcare. Prin
git arhiva nu poate merge — e mare și se schimbă la fiecare compilare —, iar la
desfășurare nimeni nu se atinge de dosarul acesta.

Dreptul de a pune o versiune nu e unul de administrator de firmă: arhiva de aici
ajunge singură pe telefoanele tuturor clienților. El se ține într-o listă de
adrese din `.env`, goală din start:

    MOBIL_PUBLICA=cineva@dianasoft.ro,altcineva@dianasoft.ro

Cât e goală, unealta nici nu se vede în filă.

## Aplicațiile știute

`spv_curier`, `etransport`, `grefier_alert` — vezi
`App\Services\Mobil\ProgrameleDeTelefon::APLICATII`.

## De ce nu prin Google Play

Magazinul ar cere cont de dezvoltator, verificare la fiecare îndreptare și
așteptare. Pentru un program folosit de contabilii noștri, nu de lumea largă,
drumul acesta e și mai scurt, și mai în mâna noastră.
