<?php

namespace App\Support;

use App\Models\CertificatUtilizator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Cine e omul din spatele cererii si cat de mult are voie sa vada.
 *
 * In firma clientului sunt doua feluri de utilizatori. Administratorul vede tot
 * ce s-a lucrat pentru firma lui. Utilizatorul obisnuit vede doar ce a facut el
 * — declaratiile si solicitarile depuse de el — plus mesajele din SPV ale
 * certificatelor la care i s-a dat acces.
 *
 * Fara utilizator autentificat (sarcini programate, comenzi din consola) nu se
 * limiteaza nimic: acolo lucreaza aplicatia, nu o persoana.
 */
class ContextUtilizator
{
    /** Ridicat programatic, pentru operatii interne care trebuie sa vada tot. */
    protected static $faraLimitare = false;

    public static function curent(): ?User
    {
        return Auth::guard('api')->user() ?: Auth::user();
    }

    /** Administratorul serviciului — un singur cont, cel din configuratie. */
    public static function esteSuperAdministrator(): bool
    {
        $user = self::curent();
        $email = config('app.super_admin');

        return $user && $email && strcasecmp((string) $user->email, $email) === 0;
    }

    /** Administratorul din firma clientului curent. */
    public static function esteAdministratorClient(): bool
    {
        $user = self::curent();
        $companie = ContextCompanie::curenta();

        if (!$user || !$companie) {
            return false;
        }

        return DB::table('company_user')
            ->where('user_id', $user->id)
            ->where('company_id', $companie)
            ->where('administrator', true)
            ->exists();
    }

    /**
     * Id-ul utilizatorului ale carui date pot fi vazute, sau null cand nu se
     * limiteaza nimic.
     */
    public static function limitatLa(): ?int
    {
        if (self::$faraLimitare) {
            return null;
        }

        $user = self::curent();

        if (!$user) {
            return null;
        }

        if (self::esteSuperAdministrator() || $user->isOwner() || self::esteAdministratorClient()) {
            return null;
        }

        return (int) $user->id;
    }

    /**
     * Certificatele digitale la care utilizatorul are drept de acces.
     *
     * Legatura se face si dupa adresa de email, pentru ca un certificat poate fi
     * dat unei persoane inainte ca ea sa aiba cont in aplicatie.
     *
     * @return array<int, int>
     */
    public static function certificateAccesibile(): array
    {
        $user = self::curent();

        if (!$user) {
            return [];
        }

        return CertificatUtilizator::where('activ', true)
            ->where(function ($intrebare) use ($user) {
                $intrebare->where('user_id', $user->id)->orWhere('email', $user->email);
            })
            ->pluck('certificat_id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->all();
    }

    /** Executa o operatie interna fara limitarea pe utilizator. */
    public static function faraLimitare(callable $operatie)
    {
        $anterior = self::$faraLimitare;
        self::$faraLimitare = true;

        try {
            return $operatie();
        } finally {
            self::$faraLimitare = $anterior;
        }
    }
}
