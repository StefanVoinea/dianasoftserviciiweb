<?php

namespace App\Services\Anaf;

use Carbon\Carbon;

/**
 * Formatarea unitara a datelor in modulul ANAF/SPV:
 * datele calendaristice ca zz.ll.aaaa, iar momentele ca zz.ll.aaaa hh:mm:ss.
 *
 * Formatarea se face pe server, ca toate ecranele sa afiseze identic, indiferent
 * de componenta care le consuma.
 */
class Format
{
    public const DATA = 'd.m.Y';
    public const DATA_ORA = 'd.m.Y H:i:s';

    public static function data($valoare): ?string
    {
        return self::formateaza($valoare, self::DATA);
    }

    public static function dataOra($valoare): ?string
    {
        return self::formateaza($valoare, self::DATA_ORA);
    }

    protected static function formateaza($valoare, string $format): ?string
    {
        if ($valoare === null || $valoare === '') {
            return null;
        }

        if ($valoare instanceof \DateTimeInterface) {
            return Carbon::instance($valoare)->format($format);
        }

        try {
            return Carbon::parse($valoare)->format($format);
        } catch (\Exception $e) {
            // Valoare neinterpretabila — se intoarce ca atare, ca sa nu se piarda.
            return (string) $valoare;
        }
    }
}
