<?php

namespace App\Services\Anaf\Declaratii\D300;

use App\Models\AnafSocietate;

/**
 * Antetul decontului de TVA, luat de pe fisa firmei.
 *
 * Cifrele decontului ies din jurnalele SAF-T, dar antetul cere lucruri care nu
 * se afla nicaieri in fisier: adresa, banca si contul, codul CAEN, cine
 * semneaza si in ce calitate, pro-rata, bifele de la inceputul formularului.
 * Ele nu se schimba de la o luna la alta, asa ca stau pe fisa firmei (vezi
 * „Date pentru declarații” din fila Entități) si se iau de acolo.
 *
 * Aici se face si socoteala a ce lipseste: mai bine i se spune omului dinainte
 * ce are de completat decat sa afle de la validatorul ANAF, dupa ce declaratia
 * a fost respinsa.
 */
class AntetD300
{
    /**
     * Ce nu poate lipsi, cu numele pe intelesul omului.
     *
     * Sunt campurile pe care schema D300 le cere („use=required") si care nu se
     * pot scoate din SAF-T. Restul — telefon, fax, mail — sunt de bunavoie.
     */
    protected const CERUTE = [
        'adresa' => 'Adresa',
        'banca' => 'Banca',
        'cont' => 'Contul (IBAN)',
        'caen' => 'Codul CAEN',
        'nume_declarant' => 'Numele declarantului',
        'prenume_declarant' => 'Prenumele declarantului',
        'functie_declarant' => 'Funcția declarantului',
        'd300_tip_decont' => 'Felul decontului',
    ];

    /** Felurile de decont din schema ANAF. */
    public const FELURI_DECONT = [
        'L' => 'lunar',
        'T' => 'trimestrial',
        'S' => 'semestrial',
        'A' => 'anual',
    ];

    /**
     * Antetul, asa cum intra in XML-ul D300.
     *
     * @return array{
     *     atribute: array<string, string>,
     *     lipsesc: array<int, string>,
     *     gata: bool
     * }
     */
    public static function pentru(?AnafSocietate $societate): array
    {
        $lipsesc = [];

        foreach (self::CERUTE as $camp => $nume) {
            if ($societate === null || trim((string) $societate->$camp) === '') {
                $lipsesc[] = $nume;
            }
        }

        if ($societate === null) {
            return ['atribute' => [], 'lipsesc' => $lipsesc, 'gata' => false];
        }

        return [
            'atribute' => self::atribute($societate),
            'lipsesc' => $lipsesc,
            'gata' => $lipsesc === [],
        ];
    }

    /** @return array<string, string> */
    protected static function atribute(AnafSocietate $societate): array
    {
        return array_filter([
            'adresa' => (string) $societate->adresa,
            'telefon' => (string) $societate->telefon,
            'fax' => (string) $societate->fax,
            'mail' => (string) $societate->email,
            'banca' => (string) $societate->banca,
            'cont' => (string) $societate->cont,
            'caen' => (string) $societate->caen,
            'nume_declar' => (string) $societate->nume_declarant,
            'prenume_declar' => (string) $societate->prenume_declarant,
            'functie_declar' => (string) $societate->functie_declarant,
            'tip_decont' => (string) $societate->d300_tip_decont,

            /*
             * Declaratia depusa de imputernicit se insemneaza ca atare, iar
             * atunci temeiul nu mai e „declaratie proprie" (0), ci „prin
             * imputernicit" (2). Cele doua merg impreuna, asa ca se scriu
             * dintr-o singura bifa.
             */
            'depusReprezentant' => $societate->prin_reprezentant ? '1' : '0',
            'temei' => $societate->prin_reprezentant ? '2' : '0',

            // Pro-rata se scrie chiar si cand e 100: schema o cere intotdeauna.
            'pro_rata' => number_format((float) ($societate->d300_pro_rata ?? 100), 2, '.', ''),

            'bifa_interne' => $societate->d300_bifa_interne ? '1' : '0',
            'bifa_cereale' => self::daNu($societate->d300_bifa_cereale),
            'bifa_mob' => self::daNu($societate->d300_bifa_mob),
            'bifa_disp' => self::daNu($societate->d300_bifa_disp),
            'bifa_cons' => self::daNu($societate->d300_bifa_cons),
            'solicit_ramb' => self::daNu($societate->d300_solicit_ramb),
        ], function ($valoare) {
            return $valoare !== '';
        });
    }

    /** Bifele din formular se scriu „D" sau „N". */
    protected static function daNu(?bool $bifat): string
    {
        return $bifat ? 'D' : 'N';
    }
}
