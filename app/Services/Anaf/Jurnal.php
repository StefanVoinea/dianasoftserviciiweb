<?php

namespace App\Services\Anaf;

use App\Models\AnafJurnal;
use Illuminate\Support\Facades\Auth;

/**
 * Jurnalul de activitate al modulului ANAF/SPV.
 *
 * Fiecare intrare retine utilizatorul care a actionat si o descriere in limbaj
 * natural, ca jurnalul sa poata fi citit fara sa fie nevoie de context tehnic.
 */
class Jurnal
{
    public static function scrie(
        string $actiune,
        string $descriere,
        array $context = [],
        ?string $cif = null,
        bool $reusit = true
    ): AnafJurnal {
        $user = self::utilizator();

        return AnafJurnal::create([
            'user_id' => optional($user)->id,
            'user_nume' => self::numeUtilizator($user),
            'user_email' => optional($user)->email,
            'actiune' => $actiune,
            'descriere' => mb_substr($descriere, 0, 500),
            'cif' => $cif,
            'certificat_id' => self::certificatCurent(),
            'context' => self::contextScurtat($context),
            'ip' => request()->ip(),
            'reusit' => $reusit,
        ]);
    }

    /** Varianta pentru operatii esuate — apare distinct in jurnal. */
    public static function esec(string $actiune, string $descriere, array $context = [], ?string $cif = null): AnafJurnal
    {
        return self::scrie($actiune, $descriere, $context, $cif, false);
    }

    /** Cat text incape intr-o valoare din context, in caractere. */
    protected const VALOARE_MAXIMA = 2000;

    /**
     * Contextul, adus la o marime rezonabila.
     *
     * In jurnal se tine ce s-a intamplat, nu documentul in sine: o declaratie
     * D406 respinsa aduce zeci de mii de randuri de erori, care oricum stau
     * intregi pe declaratie. Netaiate, ele umpleau coloana si scrierea in
     * jurnal cadea — adica tocmai esecul ramanea neconsemnat.
     *
     * @param array<string, mixed> $context
     * @return array<string, mixed>|null
     */
    protected static function contextScurtat(array $context): ?array
    {
        if ($context === []) {
            return null;
        }

        foreach ($context as $cheie => $valoare) {
            if (!is_string($valoare) || mb_strlen($valoare) <= self::VALOARE_MAXIMA) {
                continue;
            }

            $randuri = preg_split('/\r?\n/', $valoare) ?: [];

            $context[$cheie] = rtrim(mb_substr($valoare, 0, self::VALOARE_MAXIMA))
                . "\n… restul textului nu se ține în jurnal (" . count($randuri)
                . ' rânduri, ' . mb_strlen($valoare) . ' caractere în total).';
        }

        return $context;
    }

    /**
     * Rutele modulului nu trec prin middleware-ul de autentificare, dar tokenul
     * Passport este trimis de aplicatie, deci utilizatorul poate fi identificat.
     */
    protected static function utilizator()
    {
        foreach (['api', null] as $guard) {
            $user = $guard ? Auth::guard($guard)->user() : Auth::user();

            if ($user) {
                return $user;
            }
        }

        return null;
    }

    protected static function numeUtilizator($user): ?string
    {
        if (!$user) {
            return null;
        }

        foreach (['name', 'nume', 'username', 'email'] as $camp) {
            if (!empty($user->$camp)) {
                return $user->$camp;
            }
        }

        return 'utilizator #' . $user->id;
    }

    /** Certificatul folosit acum, ca sa se stie cu ce s-a lucrat. */
    protected static function certificatCurent(): ?int
    {
        try {
            return app(\App\Services\Anaf\Spv\CertificatService::class)->idCurent();
        } catch (\Exception $e) {
            return null;
        }
    }
}
