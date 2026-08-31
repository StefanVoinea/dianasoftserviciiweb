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
 * Multimile de coduri TVA dupa care se impart randurile decontului.
 *
 * 61 multimi, 833 apartenente. Un cod poate sta in mai multe:
 * el duce operatiunea si intr-un rand de amanunt, si in totalul lui.
 */
class CoduriD300
{
    /** 75 coduri. */
    public const COD1 = [
        '320101' => true, '320102' => true, '320103' => true, '320201' => true, '360101' => true, '300101' => true,
        '300102' => true, '300103' => true, '300201' => true, '300202' => true, '300203' => true, '340301' => true,
        '340302' => true, '340303' => true, '340201' => true, '340202' => true, '340203' => true, '360102' => true,
        '360103' => true, '360201' => true, '360202' => true, '360203' => true, '330101' => true, '330102' => true,
        '330103' => true, '330201' => true, '330202' => true, '330203' => true, '350101' => true, '350102' => true,
        '350103' => true, '350201' => true, '350202' => true, '350203' => true, '370101' => true, '370102' => true,
        '370103' => true, '370201' => true, '370202' => true, '370203' => true, '320202' => true, '320203' => true,
        '390301' => true, '300105' => true, '300204' => true, '300205' => true, '320204' => true, '320105' => true,
        '320205' => true, '320104' => true, '390204' => true, '390205' => true, '390304' => true, '390305' => true,
        '350104' => true, '340204' => true, '340205' => true, '340304' => true, '340305' => true, '330104' => true,
        '330105' => true, '330204' => true, '330205' => true, '300104' => true, '370104' => true, '370105' => true,
        '370204' => true, '370205' => true, '350105' => true, '350204' => true, '350205' => true, '360104' => true,
        '360105' => true, '360204' => true, '360205' => true,
    ];

    /** 38 coduri. */
    public const COD2 = [
        '320201' => true, '300201' => true, '300202' => true, '300203' => true, '340301' => true, '340302' => true,
        '340303' => true, '360201' => true, '360202' => true, '360203' => true, '330201' => true, '330202' => true,
        '330203' => true, '350201' => true, '350202' => true, '350203' => true, '370201' => true, '370202' => true,
        '370203' => true, '320202' => true, '320203' => true, '390301' => true, '300204' => true, '300205' => true,
        '320204' => true, '320205' => true, '390304' => true, '390305' => true, '340304' => true, '340305' => true,
        '330204' => true, '330205' => true, '370204' => true, '370205' => true, '350204' => true, '350205' => true,
        '360204' => true, '360205' => true,
    ];

    /** 38 coduri. */
    public const COD3 = [
        '340401' => true, '340402' => true, '340403' => true, '360401' => true, '360402' => true, '360403' => true,
        '330401' => true, '330402' => true, '330403' => true, '300401' => true, '300402' => true, '300403' => true,
        '350401' => true, '350402' => true, '350403' => true, '370401' => true, '370402' => true, '370403' => true,
        '320401' => true, '320402' => true, '320403' => true, '390403' => true, '300404' => true, '300405' => true,
        '320404' => true, '320405' => true, '390404' => true, '390405' => true, '340404' => true, '340405' => true,
        '330404' => true, '330405' => true, '370404' => true, '370405' => true, '350404' => true, '350405' => true,
        '360404' => true, '360405' => true,
    ];

    /** 114 coduri. */
    public const COD4 = [
        '340501' => true, '340502' => true, '340503' => true, '340601' => true, '340602' => true, '340603' => true,
        '340701' => true, '340702' => true, '340703' => true, '360501' => true, '360502' => true, '360503' => true,
        '360601' => true, '360602' => true, '360603' => true, '360701' => true, '360702' => true, '360703' => true,
        '300502' => true, '300503' => true, '300601' => true, '300602' => true, '300603' => true, '300701' => true,
        '300702' => true, '300703' => true, '330501' => true, '330502' => true, '330503' => true, '330601' => true,
        '330602' => true, '300501' => true, '350501' => true, '350502' => true, '350503' => true, '350601' => true,
        '350602' => true, '350603' => true, '350701' => true, '350702' => true, '350703' => true, '370501' => true,
        '370502' => true, '370503' => true, '370601' => true, '370602' => true, '370603' => true, '370701' => true,
        '370702' => true, '370703' => true, '320501' => true, '320502' => true, '320503' => true, '320601' => true,
        '320602' => true, '320603' => true, '320701' => true, '320702' => true, '320703' => true, '330603' => true,
        '330701' => true, '330702' => true, '330703' => true, '390501' => true, '390502' => true, '390503' => true,
        '300504' => true, '300505' => true, '300604' => true, '300605' => true, '300704' => true, '300705' => true,
        '360505' => true, '360604' => true, '360605' => true, '360704' => true, '360705' => true, '320504' => true,
        '320605' => true, '320704' => true, '320604' => true, '320705' => true, '320505' => true, '330605' => true,
        '330704' => true, '330705' => true, '340704' => true, '340705' => true, '390504' => true, '390505' => true,
        '390604' => true, '390605' => true, '390704' => true, '390705' => true, '340504' => true, '340505' => true,
        '340604' => true, '340605' => true, '330504' => true, '330505' => true, '330604' => true, '370504' => true,
        '370505' => true, '370604' => true, '370605' => true, '370704' => true, '370705' => true, '350504' => true,
        '350505' => true, '350604' => true, '350605' => true, '350704' => true, '350705' => true, '360504' => true,
    ];

    /** 8 coduri. */
    public const COD5 = [
        '300601' => true, '320601' => true, '330601' => true, '340601' => true, '350601' => true, '360601' => true,
        '370601' => true, '390601' => true,
    ];

    /** 4 coduri. */
    public const COD6 = [
        '300602' => true, '350602' => true, '360602' => true, '370602' => true,
    ];

    /** 4 coduri. */
    public const COD7 = [
        '300603' => true, '350603' => true, '360603' => true, '370603' => true,
    ];

    /** 8 coduri. */
    public const COD8 = [
        '300604' => true, '320604' => true, '330604' => true, '340604' => true, '350604' => true, '360604' => true,
        '370604' => true, '390604' => true,
    ];

    /** 4 coduri. */
    public const COD9 = [
        '300605' => true, '350605' => true, '360605' => true, '370605' => true,
    ];

    /** 37 coduri. */
    public const COD10 = [
        '340701' => true, '340702' => true, '340703' => true, '360701' => true, '360702' => true, '360703' => true,
        '300701' => true, '300702' => true, '300703' => true, '350701' => true, '350702' => true, '350703' => true,
        '370701' => true, '370702' => true, '370703' => true, '320701' => true, '320702' => true, '320703' => true,
        '330701' => true, '330702' => true, '330703' => true, '300704' => true, '300705' => true, '360704' => true,
        '360705' => true, '320704' => true, '320705' => true, '330704' => true, '330705' => true, '340704' => true,
        '340705' => true, '390704' => true, '390705' => true, '370704' => true, '370705' => true, '350704' => true,
        '350705' => true,
    ];

    /** 40 coduri. */
    public const COD11 = [
        '340801' => true, '340802' => true, '340803' => true, '360801' => true, '360802' => true, '360803' => true,
        '300801' => true, '300802' => true, '300803' => true, '350801' => true, '350802' => true, '350803' => true,
        '370801' => true, '370802' => true, '370803' => true, '320801' => true, '320802' => true, '320803' => true,
        '330801' => true, '330802' => true, '330803' => true, '390801' => true, '390802' => true, '390803' => true,
        '300804' => true, '300805' => true, '360804' => true, '360805' => true, '320804' => true, '320805' => true,
        '330804' => true, '330805' => true, '340804' => true, '340805' => true, '390804' => true, '390805' => true,
        '370804' => true, '370805' => true, '350804' => true, '350805' => true,
    ];

    /** 2 coduri. */
    public const COD12 = [
        '310344' => true, '380305' => true,
    ];

    /** 2 coduri. */
    public const COD13 = [
        '310350' => true, '380106' => true,
    ];

    /** 2 coduri. */
    public const COD14 = [
        '310309' => true, '380301' => true,
    ];

    /** 4 coduri. */
    public const COD15 = [
        '310335' => true, '380101' => true, '380104' => true, '380105' => true,
    ];

    /** 1 coduri. */
    public const COD16 = [
        '310335' => true,
    ];

    /** 1 coduri. */
    public const COD17 = [
        '380101' => true,
    ];

    /** 1 coduri. */
    public const COD18 = [
        '380104' => true,
    ];

    /** 1 coduri. */
    public const COD19 = [
        '380105' => true,
    ];

    /** 2 coduri. */
    public const COD20 = [
        '310351' => true, '380306' => true,
    ];

    /** 1 coduri. */
    public const COD21 = [
        '380107' => true,
    ];

    /** 2 coduri. */
    public const COD22 = [
        '310310' => true, '380302' => true,
    ];

    /** 2 coduri. */
    public const COD23 = [
        '310336' => true, '380102' => true,
    ];

    /** 2 coduri. */
    public const COD24 = [
        '310357' => true, '310358' => true,
    ];

    /** 2 coduri. */
    public const COD25 = [
        '310336' => true, '380102' => true,
    ];

    /** 2 coduri. */
    public const COD26 = [
        '310311' => true, '380303' => true,
    ];

    /** 2 coduri. */
    public const COD27 = [
        '310337' => true, '380103' => true,
    ];

    /** 1 coduri. */
    public const COD28 = [
        '310312' => true,
    ];

    /** 5 coduri. */
    public const COD29 = [
        '310314' => true, '310313' => true, '310341' => true, '310342' => true, '380304' => true,
    ];

    /** 1 coduri. */
    public const COD30 = [
        '310326' => true,
    ];

    /** 9 coduri. */
    public const COD31 = [
        '310315' => true, '310316' => true, '310317' => true, '310318' => true, '310319' => true, '310345' => true,
        '310352' => true, '380006' => true, '380007' => true,
    ];

    /** 1 coduri. */
    public const COD32 = [
        '380001' => true,
    ];

    /** 1 coduri. */
    public const COD33 = [
        '380002' => true,
    ];

    /** 1 coduri. */
    public const COD34 = [
        '380003' => true,
    ];

    /** 1 coduri. */
    public const COD35 = [
        '380004' => true,
    ];

    /** 1 coduri. */
    public const COD36 = [
        '380005' => true,
    ];

    /** 5 coduri. */
    public const COD37 = [
        '310320' => true, '310328' => true, '310343' => true, '310346' => true, '310356' => true,
    ];

    /** 4 coduri. */
    public const COD38 = [
        '310321' => true, '310329' => true, '310347' => true, '310359' => true,
    ];

    /** 24 coduri. */
    public const COD39 = [
        '301104' => true, '301204' => true, '361104' => true, '327306' => true, '321204' => true, '321104' => true,
        '331104' => true, '331204' => true, '341104' => true, '341204' => true, '397306' => true, '391104' => true,
        '391204' => true, '357306' => true, '347306' => true, '337306' => true, '307325' => true, '361204' => true,
        '377306' => true, '371104' => true, '371204' => true, '351104' => true, '351204' => true, '367306' => true,
    ];

    /** 17 coduri. */
    public const COD40 = [
        '380106' => true, '301204' => true, '321204' => true, '331204' => true, '341204' => true, '391204' => true,
        '351204' => true, '361204' => true, '371204' => true, '301305' => true, '321305' => true, '331305' => true,
        '341305' => true, '391305' => true, '351305' => true, '361305' => true, '371305' => true,
    ];

    /** 7 coduri. */
    public const COD41 = [
        '301101' => true, '321101' => true, '331101' => true, '341101' => true, '351101' => true, '361101' => true,
        '371101' => true,
    ];

    /** 19 coduri. */
    public const COD42 = [
        '380101' => true, '380104' => true, '380105' => true, '301201' => true, '321201' => true, '331201' => true,
        '341201' => true, '391201' => true, '351201' => true, '361201' => true, '371201' => true, '301301' => true,
        '321301' => true, '331301' => true, '341301' => true, '391301' => true, '351301' => true, '361301' => true,
        '371301' => true,
    ];

    /** 24 coduri. */
    public const COD43 = [
        '301105' => true, '301205' => true, '321105' => true, '327307' => true, '321205' => true, '331105' => true,
        '331205' => true, '341105' => true, '341205' => true, '397307' => true, '391105' => true, '391205' => true,
        '357307' => true, '347307' => true, '337307' => true, '307326' => true, '361105' => true, '361205' => true,
        '377307' => true, '371105' => true, '371205' => true, '351105' => true, '351205' => true, '367307' => true,
    ];

    /** 5 coduri. */
    public const COD44 = [
        '301306' => true, '301205' => true, '351205' => true, '361205' => true, '371205' => true,
    ];

    /** 15 coduri. */
    public const COD45 = [
        '341102' => true, '361102' => true, '301102' => true, '331102' => true, '351102' => true, '371102' => true,
        '321102' => true, '307304' => true, '327304' => true, '397304' => true, '337304' => true, '347304' => true,
        '377304' => true, '367304' => true, '357304' => true,
    ];

    /** 5 coduri. */
    public const COD46 = [
        '301302' => true, '301202' => true, '351202' => true, '361202' => true, '371202' => true,
    ];

    /** 15 coduri. */
    public const COD47 = [
        '341103' => true, '361103' => true, '301103' => true, '331103' => true, '351103' => true, '371103' => true,
        '321103' => true, '377305' => true, '337305' => true, '327305' => true, '367305' => true, '347305' => true,
        '307305' => true, '397305' => true, '357305' => true,
    ];

    /** 4 coduri. */
    public const COD48 = [
        '301203' => true, '351203' => true, '361203' => true, '371203' => true,
    ];

    /** 8 coduri. */
    public const COD49 = [
        '300906' => true, '320906' => true, '330906' => true, '340906' => true, '350906' => true, '360906' => true,
        '370906' => true, '390906' => true,
    ];

    /** 8 coduri. */
    public const COD50 = [
        '300907' => true, '320907' => true, '330907' => true, '340907' => true, '350907' => true, '360907' => true,
        '370907' => true, '390907' => true,
    ];

    /** 21 coduri. */
    public const COD51 = [
        '340901' => true, '360901' => true, '300901' => true, '350901' => true, '340905' => true, '340904' => true,
        '320905' => true, '320904' => true, '300905' => true, '300904' => true, '350905' => true, '350904' => true,
        '370901' => true, '320901' => true, '330905' => true, '330904' => true, '330901' => true, '360905' => true,
        '360904' => true, '370905' => true, '370904' => true,
    ];

    /** 7 coduri. */
    public const COD52 = [
        '300902' => true, '320902' => true, '330902' => true, '340902' => true, '350902' => true, '360902' => true,
        '370902' => true,
    ];

    /** 7 coduri. */
    public const COD53 = [
        '300903' => true, '320903' => true, '330903' => true, '340903' => true, '350903' => true, '360903' => true,
        '370903' => true,
    ];

    /** 7 coduri. */
    public const COD54 = [
        '308301' => true, '328301' => true, '338301' => true, '348301' => true, '358301' => true, '368301' => true,
        '378301' => true,
    ];

    /** 8 coduri. */
    public const COD55 = [
        '308307' => true, '328309' => true, '338309' => true, '348309' => true, '358309' => true, '368309' => true,
        '378309' => true, '398309' => true,
    ];

    /** 16 coduri. */
    public const COD56 = [
        '348302' => true, '348303' => true, '358303' => true, '368302' => true, '368303' => true, '308302' => true,
        '308303' => true, '338302' => true, '338303' => true, '358302' => true, '378302' => true, '378303' => true,
        '328302' => true, '328303' => true, '398302' => true, '398303' => true,
    ];

    /** 7 coduri. */
    public const COD57 = [
        '308303' => true, '328303' => true, '338303' => true, '348303' => true, '358303' => true, '368303' => true,
        '378303' => true,
    ];

    /** 1 coduri. */
    public const COD58 = [
        '380200' => true,
    ];

    /** 37 coduri. */
    public const COD59 = [
        '348304' => true, '368304' => true, '308304' => true, '338304' => true, '348305' => true, '348306' => true,
        '308305' => true, '308306' => true, '358304' => true, '378304' => true, '328304' => true, '338305' => true,
        '338306' => true, '378305' => true, '378306' => true, '368305' => true, '368306' => true, '358305' => true,
        '358306' => true, '328305' => true, '328306' => true, '328307' => true, '328308' => true, '398307' => true,
        '398308' => true, '358307' => true, '358308' => true, '348307' => true, '348308' => true, '338307' => true,
        '338308' => true, '308308' => true, '308309' => true, '378307' => true, '378308' => true, '368307' => true,
        '368308' => true,
    ];

    /** 40 coduri. */
    public const COD60 = [
        '349101' => true, '349102' => true, '349103' => true, '369101' => true, '369102' => true, '369103' => true,
        '309101' => true, '309102' => true, '309103' => true, '329101' => true, '329102' => true, '329103' => true,
        '339101' => true, '339102' => true, '339103' => true, '359101' => true, '359102' => true, '359103' => true,
        '379101' => true, '379102' => true, '379103' => true, '329107' => true, '329106' => true, '399106' => true,
        '399107' => true, '359106' => true, '359107' => true, '349106' => true, '349107' => true, '339106' => true,
        '339107' => true, '309106' => true, '309107' => true, '379106' => true, '379107' => true, '369106' => true,
        '369107' => true, '399101' => true, '399102' => true, '399103' => true,
    ];

    /** 102 coduri. */
    public const COD61 = [
        '347301' => true, '347302' => true, '367301' => true, '367302' => true, '307301' => true, '307302' => true,
        '327301' => true, '327302' => true, '337301' => true, '337302' => true, '347314' => true, '347311' => true,
        '347312' => true, '347313' => true, '347324' => true, '347321' => true, '347322' => true, '347323' => true,
        '307314' => true, '307311' => true, '307312' => true, '307313' => true, '307324' => true, '307321' => true,
        '307322' => true, '307323' => true, '357301' => true, '357302' => true, '377301' => true, '377302' => true,
        '337314' => true, '337311' => true, '337312' => true, '337313' => true, '337324' => true, '337321' => true,
        '337322' => true, '337323' => true, '377314' => true, '377311' => true, '377312' => true, '377313' => true,
        '377324' => true, '377321' => true, '377322' => true, '377323' => true, '367314' => true, '367311' => true,
        '367312' => true, '367313' => true, '367324' => true, '367321' => true, '367322' => true, '367323' => true,
        '357314' => true, '357311' => true, '357312' => true, '357313' => true, '357324' => true, '357321' => true,
        '357322' => true, '357323' => true, '327314' => true, '327311' => true, '327312' => true, '327313' => true,
        '327324' => true, '327321' => true, '327322' => true, '327323' => true, '327316' => true, '327315' => true,
        '327325' => true, '327326' => true, '397315' => true, '397316' => true, '397325' => true, '397326' => true,
        '357315' => true, '357316' => true, '357325' => true, '357326' => true, '347315' => true, '347316' => true,
        '347325' => true, '347326' => true, '337315' => true, '337316' => true, '337325' => true, '337326' => true,
        '307327' => true, '307328' => true, '307329' => true, '307330' => true, '377315' => true, '377316' => true,
        '377325' => true, '377326' => true, '367315' => true, '367316' => true, '367325' => true, '367326' => true,
    ];
}
