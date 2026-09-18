<?php

namespace App\Services\Anaf\Spv;

/**
 * Legătura securizată cu ANAF s-a stins în mijlocul răspunsului.
 *
 * Așa se poartă un token al cărui driver cere PIN-ul la fiecare folosire, nu o
 * dată pe sesiunea Windows: fereastra lui se deschide chiar în mijlocul
 * strângerii de mână cu ANAF, iar cât omul scrie codul, sesiunea securizată se
 * stinge (SEC_E_CONTEXT_EXPIRED, curl 56). ANAF nu poate fi rugat să aștepte.
 *
 * Nu e o pană adevărată: când răspunsul acesta ajunge la noi, codul e deja
 * scris și tokenul dezlegat — a doua încercare trece. De aceea se cunoaște pe
 * nume, ca apelul să fie reluat în loc să se întoarcă o eroare care sperie
 * omul degeaba.
 *
 * Se cunoaște după vorba programului local, nu după starea tokenului: codul
 * scris în fereastra driverului nu trece prin aplicație, deci „pin_verificat_la"
 * rămâne neatins și nimeni de aici nu află că s-a dezlegat.
 */
class SesiuneStinsa
{
    /**
     * Semnele după care se cunoaște.
     *
     * SEC_E_CONTEXT_EXPIRED e vorba Windows-ului, restul sunt ale curl-ului
     * pentru aceeași pățanie: legătura ruptă în timp ce se primea răspunsul.
     */
    protected const SEMNE = '/SEC_E_CONTEXT_EXPIRED|curl 56|curl: \(56\)|Recv failure|Connection was reset/i';

    /**
     * Despre asta e vorba în răspunsul primit?
     *
     * @param array<string, mixed>|null $payload ce a spus programul local, dacă a spus JSON
     * @param string                    $corp    răspunsul întreg, când n-a fost JSON
     */
    public static function da($payload, string $corp = ''): bool
    {
        $vorba = '';

        if (is_array($payload)) {
            $vorba = trim((string) ($payload['eroare'] ?? '') . ' ' . (string) ($payload['detalii'] ?? ''));
        }

        if ($vorba === '') {
            $vorba = $corp;
        }

        // Taiat: explicatia programului local e lunga, dar semnele stau in ea.
        return $vorba !== '' && preg_match(self::SEMNE, mb_substr($vorba, 0, 4000)) === 1;
    }
}
