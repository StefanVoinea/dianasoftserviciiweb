<?php

namespace App\Services\Anaf\Declaratii\D300;

/*
 * FIȘIER GENERAT — nu se scrie de mână.
 *
 * Scos din formularul inteligent al ANAF (d300-softA.pdf),
 * cu tools/d300/genereaza.php.
 */

/**
 * Unde stau datele in formularul inteligent al ANAF („soft A”).
 *
 * Formularul e XFA: fiecare rand al decontului e un subformular cu doua
 * casute — „c2” pentru baza si „c3” pentru taxa —, asezate intr-un arbore.
 * Un fisier de incarcat in el trebuie sa aiba aceeasi asezare.
 */
class FormularD300
{
    /** Randul de pe formular => unde sta si ce casute are. */
    public const RANDURI = [
        '1' => ['cale' => 'form1/date/comert/r1', 'baza' => 'c2', 'tva' => null],
        '2' => ['cale' => 'form1/date/comert/r2', 'baza' => 'c2', 'tva' => null],
        '3' => ['cale' => 'form1/date/comert/r3', 'baza' => 'c2', 'tva' => null],
        '3.1' => ['cale' => 'form1/date/comert/r3_1', 'baza' => 'c2', 'tva' => null],
        '4' => ['cale' => 'form1/date/comert/r4', 'baza' => 'c2', 'tva' => null],
        '5' => ['cale' => 'form1/date/comert/r5', 'baza' => 'c2', 'tva' => 'c3'],
        '5.1' => ['cale' => 'form1/date/comert/r5_1', 'baza' => 'c2', 'tva' => 'c3'],
        '6' => ['cale' => 'form1/date/comert/r6', 'baza' => 'c2', 'tva' => 'c3'],
        '7' => ['cale' => 'form1/date/comert/r7', 'baza' => 'c2', 'tva' => 'c3'],
        '7.1' => ['cale' => 'form1/date/comert/r7_1', 'baza' => 'c2', 'tva' => 'c3'],
        '8' => ['cale' => 'form1/date/comert/r8', 'baza' => 'c2', 'tva' => 'c3'],
        '9' => ['cale' => 'form1/date/livrari/r9', 'baza' => 'c2', 'tva' => 'c3'],
        '10' => ['cale' => 'form1/date/livrari/r10', 'baza' => 'c2', 'tva' => 'c3'],
        '11' => ['cale' => 'form1/date/livrari/r11', 'baza' => 'c2', 'tva' => 'c3'],
        '12' => ['cale' => 'form1/date/livrari/r12', 'baza' => 'c2', 'tva' => 'c3'],
        '12.1' => ['cale' => 'form1/date/livrari/r12_1', 'baza' => 'c2', 'tva' => 'c3'],
        '12.2' => ['cale' => 'form1/date/livrari/r12_2', 'baza' => 'c2', 'tva' => 'c3'],
        '13' => ['cale' => 'form1/date/livrari/r13', 'baza' => 'c2', 'tva' => null],
        '14' => ['cale' => 'form1/date/livrari/r14', 'baza' => 'c2', 'tva' => null],
        '15' => ['cale' => 'form1/date/livrari/r15', 'baza' => 'c2', 'tva' => null],
        '16' => ['cale' => 'form1/date/livrari/r16', 'baza' => 'c2', 'tva' => 'c3'],
        '17' => ['cale' => 'form1/date/livrari/r17', 'baza' => 'c2', 'tva' => 'c3'],
        '18' => ['cale' => 'form1/date/livrari/r18', 'baza' => 'c2', 'tva' => 'c3'],
        '19' => ['cale' => 'form1/date/livrari/r19', 'baza' => 'c2', 'tva' => 'c3'],
        '20' => ['cale' => 'form1/date/achizitiiRO/r20', 'baza' => 'c2', 'tva' => 'c3'],
        '20.1' => ['cale' => 'form1/date/achizitiiRO/r20_1', 'baza' => 'c2', 'tva' => 'c3'],
        '21' => ['cale' => 'form1/date/achizitiiRO/r21', 'baza' => 'c2', 'tva' => 'c3'],
        '22' => ['cale' => 'form1/date/achizitiiRO/r22', 'baza' => 'c2', 'tva' => 'c3'],
        '22.1' => ['cale' => 'form1/date/achizitiiRO/r22_1', 'baza' => 'c2', 'tva' => 'c3'],
        '23' => ['cale' => 'form1/date/achizitiiRO/r23', 'baza' => 'c2', 'tva' => 'c3'],
        '24' => ['cale' => 'form1/date/achizitiiIMP/r24', 'baza' => 'c2', 'tva' => 'c3'],
        '25' => ['cale' => 'form1/date/achizitiiIMP/r25', 'baza' => 'c2', 'tva' => 'c3'],
        '27' => ['cale' => 'form1/date/achizitiiIMP/r27', 'baza' => 'c2', 'tva' => 'c3'],
        '27.1' => ['cale' => 'form1/date/achizitiiIMP/r27_1', 'baza' => 'c2', 'tva' => 'c3'],
        '27.2' => ['cale' => 'form1/date/achizitiiIMP/r27_2', 'baza' => 'c2', 'tva' => 'c3'],
        '28' => ['cale' => 'form1/date/achizitiiIMP/r28', 'baza' => null, 'tva' => 'c3'],
        '29' => ['cale' => 'form1/date/achizitiiIMP/r29', 'baza' => null, 'tva' => 'c3'],
        '30' => ['cale' => 'form1/date/achizitiiIMP/r30', 'baza' => 'c2', 'tva' => null],
        '30.1' => ['cale' => 'form1/date/achizitiiIMP/r30_1', 'baza' => 'c2', 'tva' => null],
        '31' => ['cale' => 'form1/date/achizitiiIMP/r31', 'baza' => 'c2', 'tva' => 'c3'],
        '32' => ['cale' => 'form1/date/achizitiiIMP/r32', 'baza' => null, 'tva' => 'c3'],
        '33' => ['cale' => 'form1/date/achizitiiIMP/r33', 'baza' => null, 'tva' => 'c3'],
        '34' => ['cale' => 'form1/date/achizitiiIMP/r34', 'baza' => 'c2', 'tva' => 'c3'],
        '35' => ['cale' => 'form1/date/achizitiiIMP/r35', 'baza' => null, 'tva' => 'c3'],
        '36' => ['cale' => 'form1/date/achizitiiIMP/r36', 'baza' => null, 'tva' => 'c3'],
        '37' => ['cale' => 'form1/date/regularizari/r37', 'baza' => null, 'tva' => 'c3'],
        '38' => ['cale' => 'form1/date/regularizari/r38', 'baza' => null, 'tva' => 'c3'],
        '39' => ['cale' => 'form1/date/regularizari/r39', 'baza' => null, 'tva' => 'c3'],
        '40' => ['cale' => 'form1/date/regularizari/r40', 'baza' => null, 'tva' => 'c3'],
        '41' => ['cale' => 'form1/date/regularizari/r41', 'baza' => null, 'tva' => 'c3'],
        '42' => ['cale' => 'form1/date/regularizari/r42', 'baza' => null, 'tva' => 'c3'],
        '43' => ['cale' => 'form1/date/regularizari/r43', 'baza' => null, 'tva' => 'c3'],
        '44' => ['cale' => 'form1/date/regularizari/r44', 'baza' => null, 'tva' => 'c3'],
        '45' => ['cale' => 'form1/date/regularizari/r45', 'baza' => null, 'tva' => 'c3'],
        '46' => ['cale' => 'form1/date/regularizari/r46', 'baza' => null, 'tva' => 'c3'],
        '47' => ['cale' => 'form1/date/r47', 'baza' => 'c2', 'tva' => 'c3'],
        '48' => ['cale' => 'form1/date/r48', 'baza' => 'c2', 'tva' => 'c3'],
        '49' => ['cale' => 'form1/date/r49', 'baza' => 'c2', 'tva' => 'c3'],
        '50' => ['cale' => 'form1/date/nedeductibil/r50', 'baza' => 'c2', 'tva' => 'c3'],
        '50.1' => ['cale' => 'form1/date/nedeductibil/r50_1', 'baza' => 'c2', 'tva' => 'c3'],
        '60' => ['cale' => 'form1/date/nedeductibil/r60', 'baza' => 'c2', 'tva' => 'c3'],
        '60.1' => ['cale' => 'form1/date/nedeductibil/r60_1', 'baza' => 'c2', 'tva' => 'c3'],
    ];

    /** Campurile din antet: ce stim noi => unde sta in formular. */
    public const ANTET = [
        'nr_evid' => 'form1/Antet/nr_evid',
        'an' => 'form1/Antet/metaDate/an_r',
        'total_plata' => 'form1/Antet/metaDate/totalPlata_A',
        'tip_decont' => 'form1/Antet/metaDate/tipDecont',
        'luna' => 'form1/Antet/metaDate/luna_r',
        'temei' => 'form1/Antet/temeiLegal',
        'prin_reprezentant' => 'form1/Antet/d_reprezentant',
        'denumire' => 'form1/identifCntr/denumire/den',
        'cif' => 'form1/identifCntr/denumire/cif',
        'adresa' => 'form1/identifCntr/adresa/str',
        'telefon' => 'form1/identifCntr/contact/telefon',
        'fax' => 'form1/identifCntr/contact/fax',
        'email' => 'form1/identifCntr/contact/email',
        'banca' => 'form1/identifCntr/banca/den',
        'iban' => 'form1/identifCntr/banca/iban',
        'caen' => 'form1/identifCntr/caen',
        'pro_rata' => 'form1/identifCntr/proRata',
        'prenume_declarant' => 'form1/semnatura/prenume',
        'nume_declarant' => 'form1/semnatura/nume',
        'functie_declarant' => 'form1/semnatura/smnFnc',
    ];
}
