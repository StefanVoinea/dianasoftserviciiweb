<?php

namespace App\Support;

/**
 * Raspunsul care curge, cate un rand pe masura ce se lucreaza.
 *
 * O descarcare de zeci de documente tine minute: fiecare are pauza ceruta de
 * ANAF si drumul pana la tokenul clientului. Intr-un raspuns obisnuit, omul
 * vede o rotita si atat — nu stie daca merge, unde s-a ajuns, sau daca s-a
 * impotmolit. Asa afla dupa fiecare document cate s-au adus din cate.
 *
 * Formatul e NDJSON: cate un obiect JSON pe rand, citit de fila pe masura ce
 * soseste. Antetul „X-Accel-Buffering: no" opreste tamponarea din serverul care
 * sta in fata aplicatiei — fara el, totul ar ajunge deodata, la sfarsit, si n-ar
 * mai fi nimic de aratat pe drum.
 */
class Flux
{
    /**
     * Un pas, scris asa cum pleaca pe fir: un obiect JSON si trecerea la randul
     * urmator. Diacriticele raman citibile — numele documentelor le poarta.
     */
    public static function rand(array $pas): string
    {
        return json_encode($pas, JSON_UNESCAPED_UNICODE) . "\n";
    }

    /**
     * @param  callable():iterable  $pasi ce se da pe rand, in ordinea lucrului
     */
    public static function raspunde(callable $pasi)
    {
        return response()->stream(function () use ($pasi) {
            // Fara golirea tampoanelor, totul ar ajunge la client abia la final.
            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            foreach ($pasi() as $pas) {
                echo self::rand($pas);
                flush();
            }
        }, 200, [
            'Content-Type' => 'application/x-ndjson; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
