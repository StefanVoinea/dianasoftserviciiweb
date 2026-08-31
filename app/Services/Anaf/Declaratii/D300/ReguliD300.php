<?php

namespace App\Services\Anaf\Declaratii\D300;

/*
 * FIȘIER GENERAT — nu se scrie de mână.
 *
 * Scos din aplicația ANAF D300 (clasa anaf.saft.Parsing3), cu
 * tools/d300/genereaza.php. Când ANAF scoate versiunea următoare, se
 * generează din nou; vezi tools/d300/README.md.
 */

/**
 * Randurile decontului, socotite din liniile jurnalelor SAF-T.
 *
 * Trei momente: la citirea codului de taxa (randurile fara TVA), la
 * citirea sumei taxei (tot restul) si la sfarsit (totalurile).
 */
class ReguliD300
{
    /** Randurile decontului, toate cele atinse de reguli. */
    public const RANDURI = [
        'RD1_BAZA', 'RD2_BAZA', 'RD3_1_BAZA', 'RD3_BAZA', 'RD4_BAZA',
        'RD5_1_BAZA', 'RD5_1_TVA', 'RD5_BAZA', 'RD5_TVA', 'RD6_BAZA',
        'RD6_TVA', 'RD7_1_BAZA', 'RD7_1_TVA', 'RD7_BAZA', 'RD7_TVA',
        'RD8_BAZA', 'RD8_TVA', 'RD9_1_BAZA', 'RD9_1_TVA', 'RD9_BAZA',
        'RD9_TVA', 'RD10_1_BAZA', 'RD10_1_TVA', 'RD10_BAZA', 'RD10_TVA',
        'RD11_1_BAZA', 'RD11_1_TVA', 'RD11_BAZA', 'RD11_TVA', 'RD12_1_BAZA',
        'RD12_1_TVA', 'RD12_2_BAZA', 'RD12_2_TVA', 'RD12_3_BAZA', 'RD12_3_TVA',
        'RD12_4_BAZA', 'RD12_4_TVA', 'RD12_5_BAZA', 'RD12_5_TVA', 'RD12_BAZA',
        'RD12_TVA', 'RD13_BAZA', 'RD14_BAZA', 'RD15_BAZA', 'RD16_BAZA',
        'RD16_TVA', 'RD17_BAZA', 'RD17_TVA', 'RD18_BAZA', 'RD18_TVA',
        'RD19_BAZA', 'RD19_TVA', 'RD20_1_BAZA', 'RD20_1_TVA', 'RD20_BAZA',
        'RD20_TVA', 'RD21_BAZA', 'RD21_TVA', 'RD22_1_BAZA', 'RD22_1_TVA',
        'RD22_BAZA', 'RD22_TVA', 'RD23_BAZA', 'RD23_TVA', 'RD24_1_BAZA',
        'RD24_1_TVA', 'RD24_BAZA', 'RD24_BAZA_P', 'RD24_TVA', 'RD24_TVA_P',
        'RD25_1_BAZA', 'RD25_1_TVA', 'RD25_BAZA', 'RD25_BAZA_P', 'RD25_TVA',
        'RD25_TVA_P', 'RD26_BAZA', 'RD26_TVA', 'RD27_1_BAZA', 'RD27_1_TVA',
        'RD27_2_BAZA', 'RD27_2_TVA', 'RD27_3_BAZA', 'RD27_3_TVA', 'RD27_4_BAZA',
        'RD27_4_TVA', 'RD27_5_BAZA', 'RD27_5_TVA', 'RD27_BAZA', 'RD27_TVA',
        'RD28_TVA', 'RD29_TVA', 'RD30_1_BAZA', 'RD30_BAZA', 'RD32_TVA',
        'RD33_TVA', 'RD34_BAZA', 'RD34_TVA', 'RD35_TVA', 'RD36_TVA',
        'RD37_TVA', 'RD38_TVA', 'RD39_TVA',
    ];

    /** Codurile fara taxa, cele din „laCodTaxa”. */
    public const CODURI_LA_COD_TAXA = [
        '310301' => true, '310302' => true, '310303' => true, '310304' => true, '310305' => true, '310306' => true,
        '310307' => true, '310308' => true,
    ];

    /**
     * Randurile 1—4, care se aduna la citirea codului de taxa.
     *
     * Ele tin operatiunile fara TVA: acolo nu vine niciun TaxAmount dupa care
     * sa se ia decizia, asa ca suma se ia din chiar linia notei contabile.
     */
    public static function laCodTaxa(array &$s, string $cod, float $ca, float $da): void
    {
        switch ($cod) {
            case '310301': {
                $s['RD1_BAZA'] = $s['RD1_BAZA'] + $ca - $da;
                break;
            }
            case '310302': {
                $s['RD2_BAZA'] = $s['RD2_BAZA'] + $ca - $da;
                break;
            }
            case '310307': {
                $s['RD3_1_BAZA'] = $s['RD3_1_BAZA'] + $ca - $da;
                $s['RD3_BAZA'] = $s['RD3_BAZA'] + $ca - $da;
                break;
            }
            case '310303':
            case '310304':
            case '310305':
            case '310306': {
                $s['RD3_BAZA'] = $s['RD3_BAZA'] + $ca - $da;
                break;
            }
            case '310308': {
                $s['RD4_BAZA'] = $s['RD4_BAZA'] + $ca - $da;
            }
        }
    }

    /**
     * Ce aduce linia in decont, socotit cand i se citeste suma taxei.
     *
     * Semnul il da partea in care sta suma: ce e in debit intra cu plus, ce e
     * in credit cu minus (sau pe dos, la randurile de livrari). De aceea apare
     * peste tot abs(...) * (abs($da) / $da) — adica marimea sumei, cu semnul
     * partii din care vine.
     *
     * @param array $s     starea decontului, purtata de la o linie la alta
     * @param array $cont  steagurile conturilor liniei (vezi DecontDinSaft)
     */
    public static function laSumaTaxa(array &$s, string $cod, float $ca, float $da, float $ta, float $baza, bool $areBaza, array $cont): void
    {
        $not442 = $cont['not442'];
        $not3532 = $cont['not3532'];
        $is4426 = $cont['is4426'];
        $is4427 = $cont['is4427'];
        $is4428 = $cont['is4428'];
        $is35326 = $cont['is35326'];
        $is35327 = $cont['is35327'];
        $is35328 = $cont['is35328'];
        $rezultat = 0.0;

        if (isset(CoduriD300::COD1[$cod]) && $not442 && $not3532 && $ta != 0.0 && $baza == 0.0) {
            $s['RD5_BAZA'] = $s['RD5_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD1[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD5_BAZA'] += $rezultat;
        } elseif (isset(CoduriD300::COD1[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD5_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD1[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD5_TVA'] += $rezultat;
        } elseif (isset(CoduriD300::COD1[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD5_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD2[$cod]) && $not442 && $not3532 && $ta != 0.0 && $baza == 0.0) {
            $s['RD5_1_BAZA'] = $s['RD5_1_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD2[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD5_1_BAZA'] += $rezultat;
        } elseif (isset(CoduriD300::COD2[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD5_1_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD2[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD5_1_TVA'] += $rezultat;
        } elseif (isset(CoduriD300::COD2[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD5_1_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD3[$cod]) && $not442 && $not3532 && $ta != 0.0) {
            $s['RD6_BAZA'] = $s['RD6_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD3[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD6_TVA'] += $rezultat;
        } elseif (isset(CoduriD300::COD3[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD6_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD4[$cod]) && $not442 && $not3532 && $ta != 0.0 && $baza == 0.0) {
            $s['RD7_BAZA'] = $s['RD7_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD4[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD7_BAZA'] += $rezultat;
        } elseif (isset(CoduriD300::COD4[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD7_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD5[$cod]) && ($is4426 || $is35326) && $ta == 0.0) {
            $s['RD7_BAZA'] += ($da - $ca) / 0.19;
        }
        if (isset(CoduriD300::COD6[$cod]) && ($is4426 || $is35326) && $ta == 0.0) {
            $s['RD7_BAZA'] += ($da - $ca) / 0.09;
        }
        if (isset(CoduriD300::COD7[$cod]) && ($is4426 || $is35326) && $ta == 0.0) {
            $s['RD7_BAZA'] += ($da - $ca) / 0.05;
        }
        if (isset(CoduriD300::COD8[$cod]) && ($is4426 || $is35326) && $ta == 0.0) {
            $s['RD7_BAZA'] += ($da - $ca) / 0.21;
        }
        if (isset(CoduriD300::COD9[$cod]) && ($is4426 || $is35326) && $ta == 0.0) {
            $s['RD7_BAZA'] += ($da - $ca) / 0.11;
        }
        if (isset(CoduriD300::COD4[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD7_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD4[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD7_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD5[$cod]) && ($is4426 || $is35326) && $ta == 0.0) {
            $s['RD7_TVA'] += $da - $ca;
        }
        if (isset(CoduriD300::COD6[$cod]) && ($is4426 || $is35326) && $ta == 0.0) {
            $s['RD7_TVA'] += $da - $ca;
        }
        if (isset(CoduriD300::COD7[$cod]) && ($is4426 || $is35326) && $ta == 0.0) {
            $s['RD7_TVA'] += $da - $ca;
        }
        if (isset(CoduriD300::COD8[$cod]) && ($is4426 || $is35326) && $ta == 0.0) {
            $s['RD7_TVA'] += $da - $ca;
        }
        if (isset(CoduriD300::COD9[$cod]) && ($is4426 || $is35326) && $ta == 0.0) {
            $s['RD7_TVA'] += $da - $ca;
        }
        if (isset(CoduriD300::COD10[$cod]) && $not442 && $not3532 && $ta != 0.0 && $baza == 0.0) {
            $s['RD7_1_BAZA'] = $s['RD7_1_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD10[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD7_1_BAZA'] += $rezultat;
        } elseif (isset(CoduriD300::COD10[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD7_1_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD10[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD7_1_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD10[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD7_1_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD11[$cod]) && $not442 && $not3532 && $ta != 0.0) {
            $s['RD8_BAZA'] = $s['RD8_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD11[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD8_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD11[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD8_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD12[$cod]) && $not442 && $not3532 && $baza == 0.0 && $ta != 0.0) {
            $s['RD9_BAZA'] = $s['RD9_BAZA'] + $ca - $da;
        }
        if (isset(CoduriD300::COD13[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD9_BAZA'] += ($ca - $da) / 0.21;
        }
        if (isset(CoduriD300::COD12[$cod]) && $not442 && $not3532 && $baza != 0.0 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD9_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD12[$cod]) && $not442 && $not3532 && $baza != 0.0 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD9_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD12[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD9_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD12[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD9_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD13[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD9_TVA'] = $s['RD9_TVA'] + $ca - $da;
        }
        if (isset(CoduriD300::COD14[$cod]) && $not442 && $not3532 && $baza == 0.0 && $ta != 0.0) {
            $s['RD9_1_BAZA'] = $s['RD9_1_BAZA'] + $ca - $da;
        }
        if (isset(CoduriD300::COD16[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD9_1_BAZA'] += ($ca - $da) / 0.19;
        }
        if (isset(CoduriD300::COD17[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD9_1_BAZA'] += ($ca - $da) / 0.19;
        }
        if (isset(CoduriD300::COD18[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD9_1_BAZA'] += ($ca - $da) / 0.2;
        }
        if (isset(CoduriD300::COD19[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD9_1_BAZA'] += ($ca - $da) / 0.24;
        }
        if (isset(CoduriD300::COD14[$cod]) && $not442 && $not3532 && $baza != 0.0 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD9_1_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD14[$cod]) && $not442 && $not3532 && $baza != 0.0 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD9_1_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD14[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD9_1_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD14[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD9_1_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD15[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD9_1_TVA'] = $s['RD9_1_TVA'] + $ca - $da;
        }
        if (isset(CoduriD300::COD20[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD10_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD20[$cod]) && $not442 && $not3532 && $baza == 0.0 && $ta != 0.0) {
            $s['RD10_BAZA'] = $s['RD10_BAZA'] + $ca - $da;
        }
        if (isset(CoduriD300::COD20[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD10_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD21[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD10_BAZA'] += ($ca - $da) / 0.11;
        }
        if (isset(CoduriD300::COD20[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD10_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD20[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD10_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD21[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD10_TVA'] += $ca - $da;
        }
        if (isset(CoduriD300::COD22[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD10_1_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD22[$cod]) && $not442 && $not3532 && $baza == 0.0 && $ta != 0.0) {
            $s['RD10_1_BAZA'] = $s['RD10_1_BAZA'] + $ca - $da;
        }
        if (isset(CoduriD300::COD22[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD10_1_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD23[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD10_1_BAZA'] += ($ca - $da) / 0.09;
        }
        if (isset(CoduriD300::COD22[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD10_1_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD22[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD10_1_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD23[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD10_1_TVA'] += $ca - $da;
        }
        if (isset(CoduriD300::COD24[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD11_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD24[$cod]) && $not442 && $not3532 && $baza == 0.0 && $ta != 0.0) {
            $s['RD11_BAZA'] = $s['RD11_BAZA'] + $ca - $da;
        }
        if (isset(CoduriD300::COD24[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD11_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD25[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD11_BAZA'] += ($ca - $da) / 0.09;
        }
        if (isset(CoduriD300::COD24[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD11_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD24[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD11_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD25[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD11_TVA'] += $ca - $da;
        }
        if (isset(CoduriD300::COD26[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD11_1_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD26[$cod]) && $not442 && $not3532 && $baza == 0.0 && $ta != 0.0) {
            $s['RD11_1_BAZA'] = $s['RD11_1_BAZA'] + $ca - $da;
        }
        if (isset(CoduriD300::COD26[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD11_1_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD27[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD11_1_BAZA'] += ($ca - $da) / 0.05;
        }
        if (isset(CoduriD300::COD26[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD11_1_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD26[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD11_1_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD27[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD11_1_TVA'] += $ca - $da;
        }
        if (isset(CoduriD300::COD28[$cod]) && $not442 && $not3532) {
            $s['RD13_BAZA'] = $s['RD13_BAZA'] + $ca - $da;
        }
        if (isset(CoduriD300::COD29[$cod])) {
            $s['RD14_BAZA'] = $s['RD14_BAZA'] + $ca - $da;
        }
        if (isset(CoduriD300::COD30[$cod])) {
            $s['RD15_BAZA'] = $s['RD15_BAZA'] + $ca - $da;
        }
        if (isset(CoduriD300::COD31[$cod]) && $not442 && $not3532 && $ta != 0.0) {
            $s['RD16_BAZA'] = $s['RD16_BAZA'] + $ca - $da;
        }
        if (isset(CoduriD300::COD32[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD16_BAZA'] += ($ca - $da) / 0.19;
        }
        if (isset(CoduriD300::COD33[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD16_BAZA'] += ($ca - $da) / 0.09;
        }
        if (isset(CoduriD300::COD34[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD16_BAZA'] += ($ca - $da) / 0.05;
        }
        if (isset(CoduriD300::COD35[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD16_BAZA'] += ($ca - $da) / 0.2;
        }
        if (isset(CoduriD300::COD36[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD16_BAZA'] += ($ca - $da) / 0.24;
        }
        if (isset(CoduriD300::COD31[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD16_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD31[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD16_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD32[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD16_TVA'] += $ca - $da;
        }
        if (isset(CoduriD300::COD33[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD16_TVA'] += $ca - $da;
        }
        if (isset(CoduriD300::COD34[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD16_TVA'] += $ca - $da;
        }
        if (isset(CoduriD300::COD35[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD16_TVA'] += $ca - $da;
        }
        if (isset(CoduriD300::COD36[$cod]) && ($is4427 || $is35327) && $ta == 0.0) {
            $s['RD16_TVA'] += $ca - $da;
        }
        if (isset(CoduriD300::COD37[$cod]) && $not442 && $not3532 && $ta != 0.0) {
            $s['RD17_BAZA'] = $s['RD17_BAZA'] + $ca - $da;
        }
        if (isset(CoduriD300::COD37[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD17_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD37[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD17_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD38[$cod]) && $not442 && $not3532 && $ta != 0.0) {
            $s['RD18_BAZA'] = $s['RD18_BAZA'] + $ca - $da;
        }
        if (isset(CoduriD300::COD38[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD18_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD38[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD18_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD39[$cod]) && $not442 && $not3532 && $baza == 0.0 && $ta != 0.0) {
            $s['RD24_BAZA'] = $s['RD24_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD39[$cod]) && $not442 && $not3532 && $baza != 0.0 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD24_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD39[$cod]) && $not442 && $not3532 && $baza != 0.0 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD24_BAZA'] -= $rezultat;
        }
        if ((isset(CoduriD300::COD40[$cod]) || preg_match('/^3.120.$/', $cod) === 1) && ($is4426 || $is35326)) {
            $s['RD24_BAZA'] += ($da - $ca) / 0.21;
        }
        if (($is4426 || $is35326) && $ta == 0.0) {
            $s['RD24_TVA_P'] = $s['RD24_TVA_P'] + $da - $ca;
            $s['RD24_BAZA_P'] += ($da - $ca) / 0.21;
        }
        if (preg_match('/^301104$/', $cod) === 1 && ($is4426 || $is35326) && $ta == 0.0) {
            $s['conditie1'] = true;
        }
        if (($is4428 || $is35328) && $ta == 0.0) {
            $s['conditie2'] = true;
        }
        if ($ta != 0.0) {
            $s['isTa'] = true;
        }
        if (isset(CoduriD300::COD39[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD24_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD39[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD24_TVA'] -= $rezultat;
        }
        if ((isset(CoduriD300::COD40[$cod]) || preg_match('/^3.120.$/', $cod) === 1) && ($is4426 || $is35326)) {
            $s['RD24_TVA'] += $da - $ca;
        }
        if (isset(CoduriD300::COD41[$cod]) && $not442 && $not3532 && $baza == 0.0 && $ta != 0.0) {
            $s['RD24_1_BAZA'] = $s['RD24_1_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD41[$cod]) && $not442 && $not3532 && $baza != 0.0 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD24_1_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD41[$cod]) && $not442 && $not3532 && $baza != 0.0 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD24_1_BAZA'] -= $rezultat;
        }
        if ((isset(CoduriD300::COD42[$cod]) || preg_match('/^3.120.$/', $cod) === 1) && ($is4426 || $is35326)) {
            $s['RD24_1_BAZA'] += ($da - $ca) / 0.19;
        }
        if (isset(CoduriD300::COD41[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD24_1_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD41[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD24_1_TVA'] -= $rezultat;
        }
        if ((isset(CoduriD300::COD42[$cod]) || preg_match('/^3.120.$/', $cod) === 1) && ($is4426 || $is35326)) {
            $s['RD24_1_TVA'] += $da - $ca;
        }
        if (($is4426 || $is35326) && $ta == 0.0) {
            $s['RD25_TVA_P'] = $s['RD25_TVA_P'] + $da - $ca;
            $s['RD25_BAZA_P'] += ($da - $ca) / 0.11;
        }
        if (preg_match('/^301105$/', $cod) === 1 && ($is4426 || $is35326) && $ta == 0.0) {
            $s['conditie3'] = true;
        }
        if (isset(CoduriD300::COD43[$cod]) && $not442 && $not3532 && $baza == 0.0 && $ta != 0.0) {
            $s['RD25_BAZA'] = $s['RD25_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD43[$cod]) && $not442 && $not3532 && $baza != 0.0 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD25_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD43[$cod]) && $not442 && $not3532 && $baza != 0.0 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD25_BAZA'] -= $rezultat;
        }
        if ((isset(CoduriD300::COD43[$cod]) || preg_match('/^3.120.$/', $cod) === 1) && ($is4426 || $is35326) && $ta != 0.0) {
            $s['RD25_BAZA'] += ($da - $ca) / 0.09;
        }
        if ((isset(CoduriD300::COD44[$cod]) || preg_match('/^3.120.$/', $cod) === 1) && ($is4426 || $is35326)) {
            $s['RD25_BAZA'] += ($da - $ca) / 0.11;
        }
        if (isset(CoduriD300::COD43[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD25_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD43[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD25_TVA'] -= $rezultat;
        }
        if ((isset(CoduriD300::COD43[$cod]) || preg_match('/^3.120.$/', $cod) === 1) && ($is4426 || $is35326) && $ta != 0.0) {
            $s['RD25_TVA'] += $da - $ca;
        }
        if ((isset(CoduriD300::COD44[$cod]) || preg_match('/^3.120.$/', $cod) === 1) && ($is4426 || $is35326)) {
            $s['RD25_TVA'] += $da - $ca;
        }
        if (isset(CoduriD300::COD45[$cod]) && $not442 && $not3532 && $baza == 0.0 && $ta != 0.0) {
            $s['RD25_1_BAZA'] = $s['RD25_1_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD45[$cod]) && $not442 && $not3532 && $baza != 0.0 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD25_1_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD45[$cod]) && $not442 && $not3532 && $baza != 0.0 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD25_1_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD45[$cod]) && ($is4426 || $is35326) && $ta != 0.0) {
            $s['RD25_1_BAZA'] += ($da - $ca) / 0.09;
        }
        if (isset(CoduriD300::COD46[$cod]) && ($is4426 || $is35326)) {
            $s['RD25_1_BAZA'] += ($da - $ca) / 0.09;
        }
        if (isset(CoduriD300::COD45[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD25_1_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD45[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD25_1_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD45[$cod]) && ($is4426 || $is35326) && $ta != 0.0) {
            $s['RD25_1_TVA'] += $da - $ca;
        }
        if (isset(CoduriD300::COD46[$cod]) && ($is4426 || $is35326)) {
            $s['RD25_1_TVA'] += $da - $ca;
        }
        if (isset(CoduriD300::COD47[$cod]) && $not442 && $not3532 && !$areBaza && $ta != 0.0) {
            $s['RD26_BAZA'] = $s['RD26_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD47[$cod]) && $not442 && $not3532 && $areBaza && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD26_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD47[$cod]) && $not442 && $not3532 && $areBaza && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD26_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD47[$cod]) && ($is4426 || $is35326) && $ta != 0.0) {
            $s['RD26_BAZA'] += ($da - $ca) / 0.05;
        }
        if (isset(CoduriD300::COD48[$cod]) && ($is4426 || $is35326)) {
            $s['RD26_BAZA'] += ($da - $ca) / 0.05;
        }
        if (isset(CoduriD300::COD47[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD26_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD47[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD26_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD47[$cod]) && ($is4426 || $is35326) && $ta != 0.0) {
            $s['RD26_TVA'] += $da - $ca;
        }
        if (isset(CoduriD300::COD48[$cod]) && ($is4426 || $is35326)) {
            $s['RD26_TVA'] += $da - $ca;
        }
        if (isset(CoduriD300::COD49[$cod]) && $not442 && $not3532 && $ta != 0.0 && $baza == 0.0) {
            $s['RD27_1_BAZA'] = $s['RD27_1_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD49[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD27_1_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD49[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD27_1_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD49[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD27_1_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD49[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD27_1_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD50[$cod]) && $not442 && $not3532 && $ta != 0.0 && $baza == 0.0) {
            $s['RD27_2_BAZA'] = $s['RD27_2_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD50[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD27_2_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD50[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD27_2_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD50[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD27_2_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD50[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD27_2_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD51[$cod]) && $not442 && $not3532 && $ta != 0.0 && $baza == 0.0) {
            $s['RD27_3_BAZA'] = $s['RD27_3_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD51[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD27_3_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD51[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD27_3_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD51[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD27_3_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD51[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD27_3_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD52[$cod]) && $not442 && $not3532 && $ta != 0.0 && $baza == 0.0) {
            $s['RD27_4_BAZA'] = $s['RD27_4_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD52[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD27_4_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD52[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD27_4_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD52[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD27_4_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD52[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD27_4_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD53[$cod]) && $not442 && $not3532 && $ta != 0.0 && $baza == 0.0) {
            $s['RD27_5_BAZA'] = $s['RD27_5_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD53[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($baza) * (abs($da) / $da);
            $s['RD27_5_BAZA'] += $rezultat;
        }
        if (isset(CoduriD300::COD53[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($baza) * (abs($ca) / $ca);
            $s['RD27_5_BAZA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD53[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD27_5_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD53[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD27_5_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD54[$cod]) && $not442 && $not3532 && $ta == 0.0) {
            $s['RD28_TVA'] = $s['RD28_TVA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD55[$cod]) && $not442 && $not3532 && $ta == 0.0) {
            $s['RD29_TVA'] = $s['RD29_TVA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD56[$cod]) && $not442 && $not3532 && $ta == 0.0) {
            $s['RD30_BAZA'] = $s['RD30_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD57[$cod]) && $not442 && $not3532 && $ta == 0.0) {
            $s['RD30_1_BAZA'] = $s['RD30_1_BAZA'] + $da - $ca;
        }
        if (isset(CoduriD300::COD58[$cod]) && ($is4426 || $is35326)) {
            $s['RD32_TVA'] += $ca - $da;
        }
        if (isset(CoduriD300::COD59[$cod]) && ($is4426 || $is35326)) {
            $s['RD32_TVA'] -= $da - $ca;
        }
        if (isset(CoduriD300::COD60[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD32_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD60[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = -abs($ta) * (abs($ca) / $ca);
            $s['RD32_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD61[$cod]) && ($is4426 || $is35326)) {
            $s['RD32_TVA'] += $da - $ca;
        }
        if (isset(CoduriD300::COD59[$cod]) && ($is4426 || $is35326)) {
            $s['RD33_TVA'] += $da - $ca;
        }
        if (isset(CoduriD300::COD60[$cod]) && $not442 && $not3532 && $ta != 0.0) {
            $s['RD34_BAZA'] += $da - $ca;
        }
        if (isset(CoduriD300::COD60[$cod]) && $not442 && $not3532 && $ta != 0.0 && $da != 0.0) {
            $rezultat = abs($ta) * (abs($da) / $da);
            $s['RD34_TVA'] += $rezultat;
        }
        if (isset(CoduriD300::COD60[$cod]) && $not442 && $not3532 && $ta != 0.0 && $ca != 0.0) {
            $rezultat = abs($ta) * (abs($ca) / $ca);
            $s['RD34_TVA'] -= $rezultat;
        }
        if (isset(CoduriD300::COD61[$cod]) && ($is4426 || $is35326)) {
            $s['RD35_TVA'] += $da - $ca;
        }
        if (isset(CoduriD300::COD58[$cod]) && ($is4426 || $is35326)) {
            $s['RD36_TVA'] += $ca - $da;
        }
    }

    /**
     * Randurile care ies din celelalte, dupa ce s-a citit tot fisierul.
     *
     * Totalurile, randurile care se copiaza (20 = 5, 21 = 6 …) si soldul de
     * TVA de la sfarsit. Unele tin de an: nomenclatorul din 2026 a adus cotele
     * de 21% si 11%, cu randurile lor.
     */
    public static function laFinal(array &$s, string $an): void
    {
        $s['RD12_BAZA'] = $s['RD27_1_BAZA'] + $s['RD27_2_BAZA'] + $s['RD27_3_BAZA'] + $s['RD27_4_BAZA'] + $s['RD27_5_BAZA'];
        $s['RD12_TVA'] = $s['RD27_1_TVA'] + $s['RD27_2_TVA'] + $s['RD27_3_TVA'] + $s['RD27_4_TVA'] + $s['RD27_5_TVA'];
        $s['RD12_1_BAZA'] = $s['RD27_1_BAZA'];
        $s['RD12_1_TVA'] = $s['RD27_1_TVA'];
        $s['RD12_2_BAZA'] = $s['RD27_2_BAZA'];
        $s['RD12_2_TVA'] = $s['RD27_2_TVA'];
        $s['RD12_3_BAZA'] = $s['RD27_3_BAZA'];
        $s['RD12_3_TVA'] = $s['RD27_3_TVA'];
        $s['RD12_4_BAZA'] = $s['RD27_4_BAZA'];
        $s['RD12_4_TVA'] = $s['RD27_4_TVA'];
        $s['RD12_5_BAZA'] = $s['RD27_5_BAZA'];
        $s['RD12_5_TVA'] = $s['RD27_5_TVA'];
        if ($an === '2026') {
            $s['RD16_BAZA'] = $s['RD16_BAZA'] + $s['RD9_1_BAZA'] + $s['RD10_1_BAZA'] + $s['RD11_1_BAZA'];
            $s['RD16_TVA'] = $s['RD16_TVA'] + $s['RD9_1_TVA'] + $s['RD10_1_TVA'] + $s['RD11_1_TVA'];
        }
        $s['RD19_BAZA'] = $s['RD1_BAZA'] + $s['RD2_BAZA'] + $s['RD3_BAZA'] + $s['RD3_1_BAZA'] + $s['RD4_BAZA'] + $s['RD5_BAZA'] + $s['RD6_BAZA'] + $s['RD7_BAZA'] + $s['RD8_BAZA'] + $s['RD9_BAZA'] + $s['RD9_1_BAZA'] + $s['RD10_BAZA'] + $s['RD10_1_BAZA'] + $s['RD11_BAZA'] + $s['RD11_1_BAZA'] + $s['RD12_BAZA'] + $s['RD13_BAZA'] + $s['RD14_BAZA'] + $s['RD15_BAZA'] + $s['RD16_BAZA'] + $s['RD17_BAZA'] + $s['RD18_BAZA'];
        $s['RD19_TVA'] = $s['RD5_TVA'] + $s['RD6_TVA'] + $s['RD7_TVA'] + $s['RD8_TVA'] + $s['RD9_TVA'] + $s['RD9_1_TVA'] + $s['RD10_TVA'] + $s['RD10_1_TVA'] + $s['RD11_TVA'] + $s['RD11_1_TVA'] + $s['RD12_TVA'] + $s['RD16_TVA'] + $s['RD17_TVA'] + $s['RD18_TVA'];
        $s['RD20_BAZA'] = $s['RD5_BAZA'];
        $s['RD20_TVA'] = $s['RD5_TVA'];
        $s['RD20_1_BAZA'] = $s['RD5_1_BAZA'];
        $s['RD20_1_TVA'] = $s['RD5_1_TVA'];
        $s['RD21_BAZA'] = $s['RD6_BAZA'];
        $s['RD21_TVA'] = $s['RD6_TVA'];
        $s['RD22_BAZA'] = $s['RD7_BAZA'];
        $s['RD22_TVA'] = $s['RD7_TVA'];
        $s['RD22_1_BAZA'] = $s['RD7_1_BAZA'];
        $s['RD22_1_TVA'] = $s['RD7_1_TVA'];
        $s['RD23_BAZA'] = $s['RD8_BAZA'];
        $s['RD23_TVA'] = $s['RD8_TVA'];
        $s['RD27_BAZA'] = $s['RD27_1_BAZA'] + $s['RD27_2_BAZA'] + $s['RD27_3_BAZA'] + $s['RD27_4_BAZA'] + $s['RD27_5_BAZA'];
        $s['RD27_TVA'] = $s['RD27_1_TVA'] + $s['RD27_2_TVA'] + $s['RD27_3_TVA'] + $s['RD27_4_TVA'] + $s['RD27_5_TVA'];
        if ($an === '2026') {
            $s['RD34_BAZA'] = $s['RD34_BAZA'] + $s['RD12_3_BAZA'] + $s['RD12_4_BAZA'] + $s['RD12_5_BAZA'] + $s['RD24_1_BAZA'] + $s['RD25_1_BAZA'] + $s['RD26_BAZA'] + $s['RD27_3_BAZA'] + $s['RD27_4_BAZA'] + $s['RD27_5_BAZA'];
            $s['RD34_TVA'] = $s['RD34_TVA'] + $s['RD12_3_TVA'] + $s['RD12_4_TVA'] + $s['RD12_5_TVA'] + $s['RD24_1_TVA'] + $s['RD25_1_TVA'] + $s['RD26_TVA'] + $s['RD27_3_TVA'] + $s['RD27_4_TVA'] + $s['RD27_5_TVA'];
        }
        if ($s['RD36_TVA'] - $s['RD19_TVA'] > 0.0) {
            $s['RD37_TVA'] = $s['RD36_TVA'] - $s['RD19_TVA'];
        }
        if ($s['RD19_TVA'] - $s['RD36_TVA'] > 0.0) {
            $s['RD38_TVA'] = $s['RD19_TVA'] - $s['RD36_TVA'];
        }
        if ($s['OpeningCreditBalance'] - $s['OpeningDebitBalance'] > 0.0) {
            $s['RD39_TVA'] = $s['OpeningCreditBalance'] - $s['OpeningDebitBalance'] - $s['FinalPayment4423'];
        }
    }
}
