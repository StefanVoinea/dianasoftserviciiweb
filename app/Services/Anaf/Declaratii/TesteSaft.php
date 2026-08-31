<?php

namespace App\Services\Anaf\Declaratii;

/**
 * Ce inseamna fiecare NOK din verificarea de consistenta a D406.
 *
 * Aplicatia ANAF scrie in fisierul de erori doar codul testului picat:
 * „NOK-07". Omul care primeste raportul nu are de unde sa stie ce inseamna, iar
 * ANAF nu publica lista. Ea e scoasa din chiar testele aplicatiei
 * (ro.anaf.saft.tests.Test01..Test13), asa ca aici scrie ce compara fiecare si
 * ce e de cautat in contabilitate.
 *
 * Conturile se compara pe inceputul lor: „4426" prinde si 4426.01, „35326"
 * prinde conturile de TVA din regimul special. Testele 08 si 09 nu exista in
 * aplicatie — numerotarea ANAF sare peste ele.
 */
class TesteSaft
{
    /**
     * Fiecare test, cu ce verifica si de unde se apuca omul sa indrepte.
     */
    protected const TESTE = [
        'NOK-01' => [
            'titlu' => 'TVA cu cod generic, pe un cont care nu e de TVA',
            'verifica' => 'Linia are TVA (TaxAmount ≠ 0), tip taxă 300 și codul TVA 000000, dar contul ei nu începe cu 4426, 4427 sau 4428.',
            'de_facut' => 'Codul 000000 înseamnă „fără cod". Pune pe linie codul TVA real din nomenclatorul ANAF, cel care duce operațiunea în rândul potrivit din decont.',
        ],
        'NOK-02' => [
            'titlu' => 'TVA-ul nu iese din bază și cotă',
            'verifica' => 'TVA-ul declarat pe linie se compară cu baza × cota (iar dacă baza lipsește, cu suma din debit sau credit × cota). Se semnalează doar când diferența trece și de 10 lei, și de 10% din valoarea calculată.',
            'de_facut' => 'Uită-te la cota trecută în TaxPercentage și la baza din TaxBase: de obicei una din ele e greșită sau baza e pusă cu tot cu TVA.',
        ],
        'NOK-03' => [
            'titlu' => 'TVA fără tip de taxă, pe un cont care nu e de TVA',
            'verifica' => 'Linia are TVA peste 0,99 lei, tip taxă 000 și codul 000000, dar contul nu începe cu 4426, 4427 sau 4428.',
            'de_facut' => 'Completează tipul de taxă (300 pentru TVA) și codul TVA din nomenclator.',
        ],
        'NOK-04' => [
            'titlu' => 'Informația de taxă stă pe linia contului de TVA deductibilă',
            'verifica' => 'Linia e pe 4426 sau 35326, are sumă în debit sau credit și poartă și TaxAmount.',
            'de_facut' => 'Informația de taxă se pune pe linia de bază — cea de cheltuială sau de furnizor —, nu pe linia contului de TVA. Programul de contabilitate o duce, de obicei, dintr-o setare a exportului SAF-T.',
        ],
        'NOK-05' => [
            'titlu' => 'Informația de taxă stă pe linia contului de TVA colectată',
            'verifica' => 'Linia e pe 4427 sau 35327, are sumă în debit sau credit și poartă și TaxAmount.',
            'de_facut' => 'La fel ca la 4426: taxa se declară pe linia de venit sau de client, nu pe linia contului de TVA.',
        ],
        'NOK-06' => [
            'titlu' => 'Informația de taxă stă pe linia contului de TVA neexigibilă',
            'verifica' => 'Linia e pe 4428 sau 35328, are sumă în debit sau credit și poartă și TaxAmount.',
            'de_facut' => 'La TVA la încasare, taxa se declară pe linia de bază; contul 4428 rămâne doar cu suma.',
        ],
        'NOK-07' => [
            'titlu' => 'Tip de taxă care nu e TVA',
            'verifica' => 'Tipul taxei de pe linie este unul din 301, 302, 303, 304, 305, 307, 344 sau 390 — accize și alte impozite, nu TVA.',
            'de_facut' => 'Dacă operațiunea e de TVA, tipul trebuie să fie 300. Dacă nu e, verifică de ce linia a ajuns cu informație de taxă în jurnalul de TVA.',
        ],
        'NOK-10' => [
            'titlu' => 'TVA pe o operațiune care merge într-un rând fără TVA',
            'verifica' => 'Codul TVA de pe linie duce operațiunea în rândurile 1, 2, 3, 3.1, 4, 13, 14 sau 15 din decont — rânduri fără taxă —, dar linia are TVA.',
            'de_facut' => 'Ori codul TVA e ales greșit, ori operațiunea nu trebuia să poarte taxă. Alege codul care corespunde regimului real.',
        ],
        'NOK-11' => [
            'titlu' => 'Operațiune taxabilă înregistrată fără TVA',
            'verifica' => 'Codul TVA duce operațiunea în rândurile taxabile ale decontului (5/20, 6/21, 7/22, 8/23, 12/27 și subrândurile lor), suma trece de 2 lei, contul nu e de TVA sau de decontare, iar TVA-ul de pe linie este zero.',
            'de_facut' => 'Caută factura: fie i-a rămas TVA-ul necompletat la înregistrare, fie codul TVA trebuia să fie unul de operațiune scutită sau cu taxare inversă.',
        ],
        'NOK-12' => [
            'titlu' => 'TVA pe o achiziție dusă în rândurile 30 sau 31',
            'verifica' => 'Codul TVA duce operațiunea în rândul 30 sau 31 din decont — achiziții fără taxă —, dar linia are TVA, pe un cont care nu e de TVA.',
            'de_facut' => 'Verifică regimul achiziției: rândurile 30 și 31 nu primesc taxă deductibilă.',
        ],
        'NOK-13' => [
            'titlu' => 'TVA pe un cod de operațiune fără taxă',
            'verifica' => 'Codul TVA e unul din 380001–380005 (iar de la iulie 2025 și 380006, 380007), coduri fără taxă, dar linia are TVA pe un cont care nu e de TVA.',
            'de_facut' => 'Schimbă codul TVA cu cel al regimului real al operațiunii sau scoate taxa de pe linie.',
        ],
        'NOK - an sau luna eronate' => [
            'titlu' => 'Perioada tranzacției e în afara intervalului',
            'verifica' => 'Luna tranzacției nu e între 1 și 12, anul e mai mic decât 2022 sau perioada ei trece de perioada raportată în antet.',
            'de_facut' => 'Verifică ce perioadă a exportat programul de contabilitate: în declarație au intrat note contabile din altă lună.',
        ],
    ];

    /** Testul, asa cum se arata omului. Codurile necunoscute nu se inventeaza. */
    public static function descrie(?string $stare): array
    {
        $stare = trim((string) $stare);

        if (isset(self::TESTE[$stare])) {
            return ['cod' => $stare] + self::TESTE[$stare];
        }

        return [
            'cod' => $stare !== '' ? $stare : 'NOK',
            'titlu' => 'Neconcordanță semnalată de ANAF',
            'verifica' => 'Aplicația ANAF a marcat linia, fără un test cunoscut aici.',
            'de_facut' => 'Compară linia cu factura din care vine.',
        ];
    }

    /** Toate testele, pentru legenda de sub tabel. */
    public static function toate(): array
    {
        $lista = [];

        foreach (self::TESTE as $cod => $test) {
            $lista[] = ['cod' => $cod] + $test;
        }

        return $lista;
    }
}
