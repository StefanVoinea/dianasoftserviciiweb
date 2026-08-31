<?php

namespace App\Services\Anaf\Declaratii\D300;

/*
 * FIȘIER GENERAT — nu se scrie de mână.
 *
 * Scos din raportul ANAF raport2026.jrxml și din generatorul lui de PDF
 * (d300pdf.java), cu tools/d300/genereaza.php.
 */

/**
 * Randurile decontului: numarul de pe formular, denumirea si atributul
 * din XML-ul D300.
 *
 * Numarul randului nu e acelasi lucru cu numarul din numele atributului:
 * randul 17 sta in „R64”, randul 19 in „R17”. Asa a ramas de cand ANAF a
 * adaugat randuri la mijlocul formularului.
 */
class RanduriD300
{
    /** Campul din decont => randul de pe formular, atributul din XML, denumirea. */
    public const RANDURI = [
        'RD6_TVA' => ['rand' => '6', 'atribut' => 'R6_2', 'denumire' => 'Regularizari privind achizitiile intracomunitare de bunuri pentru care cumparatorul este obligat la plata TVA (taxare inversa)'],
        'RD7_TVA' => ['rand' => '7', 'atribut' => 'R7_2', 'denumire' => 'Achizitii de bunuri, altele decat cele de la rd. 5 si 6 si achizitii de servicii pentru care beneficiarul din Romania este obligat la plata TVA (taxare inversa), din care:'],
        'RD6_BAZA' => ['rand' => '6', 'atribut' => 'R6_1', 'denumire' => 'Regularizari privind achizitiile intracomunitare de bunuri pentru care cumparatorul este obligat la plata TVA (taxare inversa)'],
        'RD3_1_BAZA' => ['rand' => '3.1', 'atribut' => 'R3_1_1', 'denumire' => 'Prestari de servicii intracomunitare care nu beneficiaza de scutire in statul membru in care taxa este datorata'],
        'RD3_BAZA' => ['rand' => '3', 'atribut' => 'R3_1', 'denumire' => 'Livrari de bunuri sau prestari de servicii pentru care locul livrarii/ locul prestarii este in afara Romaniei (in UE sau in afara UE), precum si livrari intracomunitare de bunuri, scutite conform art. 294 alin.(2) lit.b) si c) din Codul fiscal, din care:'],
        'RD5_BAZA' => ['rand' => '5', 'atribut' => 'R5_1', 'denumire' => 'Achizitii intracomunitare de bunuri pentru care cumparatorul este obligat la plata TVA (taxare inversa), din care:'],
        'RD7_BAZA' => ['rand' => '7', 'atribut' => 'R7_1', 'denumire' => 'Achizitii de bunuri, altele decat cele de la rd. 5 si 6 si achizitii de servicii pentru care beneficiarul din Romania este obligat la plata TVA (taxare inversa), din care:'],
        'RD8_BAZA' => ['rand' => '8', 'atribut' => 'R8_1', 'denumire' => 'Regularizari privind achizitii de servicii intracomunitare pentru care beneficiarul este obligat la plata TVA (taxare inversa)'],
        'RD5_1_BAZA' => ['rand' => '5.1', 'atribut' => 'R5_1_1', 'denumire' => 'Achizitii intracomunitare pentru care cumparatorul este obligat la plata TVA (taxare inversa), iar furnizorul este inregistrat in scopuri de TVA in statul membru din care a avut loc livrarea intracomunitara'],
        'RD4_BAZA' => ['rand' => '4', 'atribut' => 'R4_1', 'denumire' => 'Regularizari privind prestarile de servicii intracomunitare care nu beneficiaza de scutire in statul membru in care taxa este datorata'],
        'RD8_TVA' => ['rand' => '8', 'atribut' => 'R8_2', 'denumire' => 'Regularizari privind achizitii de servicii intracomunitare pentru care beneficiarul este obligat la plata TVA (taxare inversa)'],
        'RD1_BAZA' => ['rand' => '1', 'atribut' => 'R1_1', 'denumire' => 'Livrari intracomunitare de bunuri, scutite conform art. 294 alin.(2) lit.a) si d) din Codul fiscal'],
        'RD5_TVA' => ['rand' => '5', 'atribut' => 'R5_2', 'denumire' => 'Achizitii intracomunitare de bunuri pentru care cumparatorul este obligat la plata TVA (taxare inversa), din care:'],
        'RD2_BAZA' => ['rand' => '2', 'atribut' => 'R2_1', 'denumire' => 'Regularizari livrari intracomunitare scutite conform art. 294 alin.(2) lit.a) si d) din Codul fiscal'],
        'RD5_1_TVA' => ['rand' => '5.1', 'atribut' => 'R5_1_2', 'denumire' => 'Achizitii intracomunitare pentru care cumparatorul este obligat la plata TVA (taxare inversa), iar furnizorul este inregistrat in scopuri de TVA in statul membru din care a avut loc livrarea intracomunitara'],
        'RD7_1_BAZA' => ['rand' => '7.1', 'atribut' => 'R7_1_1', 'denumire' => 'Achizitii de servicii intracomunitare pentru care beneficiarul este obligat la plata TVA (taxare inversa)'],
        'RD7_1_TVA' => ['rand' => '7.1', 'atribut' => 'R7_1_2', 'denumire' => 'Achizitii de servicii intracomunitare pentru care beneficiarul este obligat la plata TVA (taxare inversa)'],
        'RD15_BAZA' => ['rand' => '15', 'atribut' => 'R15_1', 'denumire' => 'Livrari de bunuri si prestari de servicii scutite fara drept de deducere'],
        'RD16_TVA' => ['rand' => '16', 'atribut' => 'R16_2', 'denumire' => 'Regularizari taxa colectata'],
        'RD12_1_BAZA' => ['rand' => '12.1', 'atribut' => 'R12_1_1', 'denumire' => 'Achizitii de bunuri si servicii, taxabile cu cota 21%'],
        'RD16_BAZA' => ['rand' => '16', 'atribut' => 'R16_1', 'denumire' => 'Regularizari taxa colectata'],
        'RD12_1_TVA' => ['rand' => '12.1', 'atribut' => 'R12_1_2', 'denumire' => 'Achizitii de bunuri si servicii, taxabile cu cota 21%'],
        'RD12_BAZA' => ['rand' => '12', 'atribut' => 'R12_1', 'denumire' => 'Achizitii de bunuri si servicii supuse masurilor de simplificare pentru care beneficiarul este obligat la plata TVA (taxare inversa), din care'],
        'RD17_BAZA' => ['rand' => '17', 'atribut' => 'R64_1', 'denumire' => 'Prestari de servicii intracomunitare conform art.278 alin.(8) din Codul fiscal pentru care locul prestarii este \în Romania'],
        'RD12_2_BAZA' => ['rand' => '12.2', 'atribut' => 'R12_2_1', 'denumire' => 'Achizitii de bunuri, taxabile cu cota 11%'],
        'RD9_TVA' => ['rand' => '9', 'atribut' => 'R9_2', 'denumire' => 'Livrari de bunuri si prestari de servicii taxabile cu cota 21%'],
        'RD17_TVA' => ['rand' => '17', 'atribut' => 'R64_2', 'denumire' => 'Prestari de servicii intracomunitare conform art.278 alin.(8) din Codul fiscal pentru care locul prestarii este \în Romania'],
        'RD13_BAZA' => ['rand' => '13', 'atribut' => 'R13_1', 'denumire' => 'Livrari de bunuri si prestari de servicii supuse masurilor de simplificare (taxare inversa)'],
        'RD18_BAZA' => ['rand' => '18', 'atribut' => 'R65_1', 'denumire' => 'Regularizari privind prestari de servicii intracomunitare conform art.278 alin.(8) din Codul fiscal pentru care locul prestarii este in Romania'],
        'RD11_BAZA' => ['rand' => '11', 'atribut' => 'R11_1', 'denumire' => 'Livrari de bunuri si prestari de servicii taxabile cu cota 9%'],
        'RD9_BAZA' => ['rand' => '9', 'atribut' => 'R9_1', 'denumire' => 'Livrari de bunuri si prestari de servicii taxabile cu cota 21%'],
        'RD12_TVA' => ['rand' => '12', 'atribut' => 'R12_2', 'denumire' => 'Achizitii de bunuri si servicii supuse masurilor de simplificare pentru care beneficiarul este obligat la plata TVA (taxare inversa), din care'],
        'RD10_TVA' => ['rand' => '10', 'atribut' => 'R10_2', 'denumire' => 'Livrari de bunuri si prestari de servicii taxabile cu cota 11%'],
        'RD10_BAZA' => ['rand' => '10', 'atribut' => 'R10_1', 'denumire' => 'Livrari de bunuri si prestari de servicii taxabile cu cota 11%'],
        'RD11_TVA' => ['rand' => '11', 'atribut' => 'R11_2', 'denumire' => 'Livrari de bunuri si prestari de servicii taxabile cu cota 9%'],
        'RD12_2_TVA' => ['rand' => '12.2', 'atribut' => 'R12_2_2', 'denumire' => 'Achizitii de bunuri, taxabile cu cota 11%'],
        'RD18_TVA' => ['rand' => '18', 'atribut' => 'R65_2', 'denumire' => 'Regularizari privind prestari de servicii intracomunitare conform art.278 alin.(8) din Codul fiscal pentru care locul prestarii este in Romania'],
        'RD14_BAZA' => ['rand' => '14', 'atribut' => 'R14_1', 'denumire' => 'Livrari de bunuri si prestari de servicii scutite cu drept de deducere, altele decat cele de la rd. 1-3, din care'],
        'RD24_BAZA' => ['rand' => '24', 'atribut' => 'R22_1', 'denumire' => 'Achizitii de bunuri si servicii taxabile cu cota de 21%, altele decat cele de la rd. 27'],
        'RD32_TVA' => ['rand' => '31', 'atribut' => 'R28_2', 'denumire' => 'SUB-TOTAL TAXA DEDUSA CONFORM ART. 297 SI ART. 298 SAU ART. 300 SI ART. 298 DIN CODUL FISCAL SI COMPENSATIE IN COTA FORFETARA'],
        'RD29_TVA' => ['rand' => '28', 'atribut' => 'R44_2', 'denumire' => 'Regularizari privind compensatia in cota forfetara'],
        'RD33_TVA' => ['rand' => '32', 'atribut' => 'R29_2', 'denumire' => 'TVA efectiv restituita cumparatorilor straini, inclusiv comisionul unitatilor autorizate'],
        'RD25_TVA' => ['rand' => '25', 'atribut' => 'R23_2', 'denumire' => 'Achizitii de bunuri si servicii taxabile cu cota de 11%'],
        'RD30_BAZA' => ['rand' => '29', 'atribut' => 'R26_1', 'denumire' => 'Achizitii de bunuri si servicii scutite de taxa sau neimpozabile, din care:'],
        'RD24_TVA' => ['rand' => '24', 'atribut' => 'R22_2', 'denumire' => 'Achizitii de bunuri si servicii taxabile cu cota de 21%, altele decat cele de la rd. 27'],
        'RD28_TVA' => ['rand' => '27', 'atribut' => 'R43_2', 'denumire' => 'Compensatia in cota forfetara pentru achizitii de produse si servicii agricole de la furnizori care aplica regimul special pentru agricultori'],
        'RD35_TVA' => ['rand' => '34', 'atribut' => 'R31_2', 'denumire' => 'Ajustari conform pro-rata / ajustari de taxa'],
        'RD30_1_BAZA' => ['rand' => '29.1', 'atribut' => 'R26_1_1', 'denumire' => 'Achizitii de servicii intracomunitare scutite de taxa'],
        'RD34_BAZA' => ['rand' => '33', 'atribut' => 'R30_1', 'denumire' => 'Regularizari taxa dedusa'],
        'RD25_BAZA' => ['rand' => '25', 'atribut' => 'R23_1', 'denumire' => 'Achizitii de bunuri si servicii taxabile cu cota de 11%'],
        'RD34_TVA' => ['rand' => '33', 'atribut' => 'R30_2', 'denumire' => 'Regularizari taxa dedusa'],
        'RD36_TVA' => ['rand' => '35', 'atribut' => 'R32_2', 'denumire' => 'TOTAL TAXA DEDUSA (rd. 31 + rd. 32 + rd. 33 + rd. 34)'],
        'RD37_TVA' => ['rand' => '36', 'atribut' => 'R33_2', 'denumire' => 'Suma negativa a TVA in perioada de raportare (rd. 35 - rd. 19)'],
        'RD38_TVA' => ['rand' => '37', 'atribut' => 'R34_2', 'denumire' => 'Taxa de plata in perioada de raportare (rd. 19 - rd. 35)'],
        'RD39_TVA' => ['rand' => '38', 'atribut' => 'R35_2', 'denumire' => 'Soldul TVA de plata din decontul perioadei fiscale precedente (rd. 45 din decontul perioadei fiscale precedente) neachitate pana la data depunerii decontului de TVA'],
        'RD19_BAZA' => ['rand' => '19', 'atribut' => 'R17_1', 'denumire' => 'TOTAL TAXA COLECTATA (suma de la rd. 1 pana la rd. 18, cu exceptia celor de la rd. 3.1, 5.1 , 7.1, 12.1, 12.2 )'],
        'RD19_TVA' => ['rand' => '19', 'atribut' => 'R17_2', 'denumire' => 'TOTAL TAXA COLECTATA (suma de la rd. 1 pana la rd. 18, cu exceptia celor de la rd. 3.1, 5.1 , 7.1, 12.1, 12.2 )'],
        'RD20_BAZA' => ['rand' => '20', 'atribut' => 'R18_1', 'denumire' => 'Achizitii intracomunitare de bunuri pentru care cumparatorul este obligat la plata TVA (taxare inversa), din care:'],
        'RD20_TVA' => ['rand' => '20', 'atribut' => 'R18_2', 'denumire' => 'Achizitii intracomunitare de bunuri pentru care cumparatorul este obligat la plata TVA (taxare inversa), din care:'],
        'RD20_1_BAZA' => ['rand' => '20.1', 'atribut' => 'R18_1_1', 'denumire' => 'Achizitii intracomunitare pentru care cumparatorul este obligat la plata TVA (taxare inversa), iar furnizorul este inregistrat in scopuri de TVA in statul membru din care a avut loc livrarea'],
        'RD20_1_TVA' => ['rand' => '20.1', 'atribut' => 'R18_1_2', 'denumire' => 'Achizitii intracomunitare pentru care cumparatorul este obligat la plata TVA (taxare inversa), iar furnizorul este inregistrat in scopuri de TVA in statul membru din care a avut loc livrarea'],
        'RD21_BAZA' => ['rand' => '21', 'atribut' => 'R19_1', 'denumire' => 'Regularizari privind achizitiile intracomunitare de bunuri pentru care cumparatorul este obligat la plata TVA (taxare inversa)'],
        'RD21_TVA' => ['rand' => '21', 'atribut' => 'R19_2', 'denumire' => 'Regularizari privind achizitiile intracomunitare de bunuri pentru care cumparatorul este obligat la plata TVA (taxare inversa)'],
        'RD22_BAZA' => ['rand' => '22', 'atribut' => 'R20_1', 'denumire' => 'Achizitii de bunuri, altele decat cele de la rd.20 \şi 21 si achizitii de servicii pentru care beneficiarul din Romania este obligat la plata TVA (taxare inversa), din care:'],
        'RD22_TVA' => ['rand' => '22', 'atribut' => 'R20_2', 'denumire' => 'Achizitii de bunuri, altele decat cele de la rd.20 \şi 21 si achizitii de servicii pentru care beneficiarul din Romania este obligat la plata TVA (taxare inversa), din care:'],
        'RD22_1_BAZA' => ['rand' => '22.1', 'atribut' => 'R20_1_1', 'denumire' => 'Achizitii de servicii intracomunitare pentru care beneficiarul este obligat la plata TVA (taxare inversa)'],
        'RD22_1_TVA' => ['rand' => '22.1', 'atribut' => 'R20_1_2', 'denumire' => 'Achizitii de servicii intracomunitare pentru care beneficiarul este obligat la plata TVA (taxare inversa)'],
        'RD23_BAZA' => ['rand' => '23', 'atribut' => 'R21_1', 'denumire' => 'Regularizari privind achizitii de servicii intracomunitare pentru care beneficiarul este obligat la plata TVA (taxare inversa)'],
        'RD23_TVA' => ['rand' => '23', 'atribut' => 'R21_2', 'denumire' => 'Regularizari privind achizitii de servicii intracomunitare pentru care beneficiarul este obligat la plata TVA (taxare inversa)'],
        'RD26_BAZA' => ['rand' => '26', 'atribut' => 'R25_1', 'denumire' => 'Achizitii de bunuri si servicii supuse masurilor de simplificare pentru care beneficiarul este obligat la plata TVA (taxare inversa), din care:'],
        'RD26_TVA' => ['rand' => '26', 'atribut' => 'R25_2', 'denumire' => 'Achizitii de bunuri si servicii supuse masurilor de simplificare pentru care beneficiarul este obligat la plata TVA (taxare inversa), din care:'],
    ];

    /**
     * Randurile care se aduna din celelalte, in atribute.
     *
     * Formulele sunt ale validatorului ANAF, nu ale noastre: el le
     * cantareste, iar o declaratie care nu iese dupa ele e respinsa.
     */
    public const TOTALURI = [
        'R17_1' => ['R1_1', 'R2_1', 'R3_1', 'R4_1', 'R5_1', 'R6_1', 'R7_1', 'R8_1', 'R9_1', 'R10_1', 'R11_1', 'R12_1', 'R13_1', 'R14_1', 'R15_1', 'R16_1', 'R64_1', 'R65_1', 'R69_1', 'R70_1', 'R71_1'],
        'R17_2' => ['R5_2', 'R6_2', 'R7_2', 'R8_2', 'R9_2', 'R10_2', 'R11_2', 'R12_2', 'R16_2', 'R64_2', 'R65_2', 'R69_2', 'R70_2', 'R71_2'],
        'R27_1' => ['R18_1', 'R19_1', 'R20_1', 'R21_1', 'R22_1', 'R23_1', 'R24_1', 'R25_1', 'R74_1', 'R75_1'],
        'R27_2' => ['R18_2', 'R19_2', 'R20_2', 'R21_2', 'R22_2', 'R23_2', 'R24_2', 'R25_2', 'R43_2', 'R44_2', 'R74_2', 'R75_2'],
        'R32_2' => ['R28_2', 'R29_2', 'R30_2', 'R31_2'],
        'R37_2' => ['R34_2', 'R35_2', 'R36_2'],
        'R40_2' => ['R33_2', 'R38_2', 'R39_2'],
    ];

    /**
     * Soldurile de la sfarsit: o scadere taiata la zero.
     *
     * „R41_2 = max(R37_2 - R40_2, 0)” — tot dupa regulile validatorului:
     * ce iese in plus se cere de la stat, ce iese in minus se plateste.
     */
    public const DIFERENTE = [
        'R33_2' => ['R32_2', 'R17_2'],
        'R34_2' => ['R17_2', 'R32_2'],
        'R41_2' => ['R37_2', 'R40_2'],
        'R42_2' => ['R40_2', 'R37_2'],
    ];

    /**
     * Randurile care trebuie sa fie deopotriva cu altele.
     *
     * Randul 20 e randul 5 vazut din partea deducerii: aceeasi achizitie,
     * o data la taxa datorata, o data la cea de dedus. Validatorul cere sa
     * fie scrise amandoua, si la fel.
     */
    public const EGALITATI = [
        'R25_3_2' => 'R12_3_2',
        'R72_1' => 'R76_1',
        'R72_2' => 'R76_2',
        'R73_1' => 'R77_1',
        'R73_2' => 'R77_2',
        'R18_1' => 'R5_1',
        'R18_2' => 'R5_2',
        'R18_1_1' => 'R5_1_1',
        'R18_1_2' => 'R5_1_2',
        'R19_1' => 'R6_1',
        'R19_2' => 'R6_2',
        'R20_1' => 'R7_1',
        'R20_2' => 'R7_2',
        'R20_1_1' => 'R7_1_1',
        'R20_1_2' => 'R7_1_2',
        'R21_1' => 'R8_1',
        'R21_2' => 'R8_2',
    ];
}
