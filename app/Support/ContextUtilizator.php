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

    /**
     * Omul din spatele cererii, daca e vreunul.
     *
     * Nu orice cerere poarta un token al aplicatiei: agentul de la client vine
     * cu codul lui de instalare, iar Passport, incercand sa-l citeasca drept
     * JWT, se opreste cu „Malformed UTF-8 characters" sau „The JWT string must
     * have two dots" si darama toata cererea.
     *
     * Orice poticnire inseamna aici doar „nimeni conectat". Nu e treaba
     * intrebarii „cine e omul?" sa pice lucrarea — cu atat mai putin cand ea se
     * pune tocmai ca sa se scrie un mesaj de eroare, si atunci poticnirea ia
     * locul erorii adevarate, care nu mai ajunge la nimeni.
     */
    public static function curent(): ?User
    {
        if (self::poateFiJetonulAplicatiei()) {
            try {
                $user = Auth::guard('api')->user();
            } catch (\Throwable $e) {
                $user = null;
            }

            if ($user) {
                return $user;
            }
        }

        try {
            return Auth::user();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Are rost sa fie intrebat Passport pe cererea de acum?
     *
     * Pe caile puntii, niciodata: agentul vine cu codul lui de instalare, iar
     * serverul cu un jeton semnat de el insusi. Niciunul nu e token al
     * aplicatiei.
     *
     * Prinderea poticnirii nu era de ajuns. Passport isi prinde singur exceptia
     * si o RAPORTEAZA inainte s-o inghita — asa ca ea nu ajunge niciodata la
     * „catch"-ul de mai jos, dar ajunge in jurnal: o urma de o suta de randuri
     * despre JWT, la fiecare cerere a fiecarui agent. Erorile adevarate se
     * pierdeau printre ele.
     */
    protected static function poateFiJetonulAplicatiei(): bool
    {
        // Fara cerere — comenzi, sarcini programate — se intreaba ca pana acum.
        if (!app()->bound('request')) {
            return true;
        }

        return !request()->is('api/punte/*');
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
     * Are omul dreptul cerut în firma de acum?
     *
     * Administratorul firmei le are pe toate — el le și dă. Semnarea și
     * depunerea se acordă anume, pentru că una angajează certificatul persoanei,
     * iar cealaltă trimite ceva la ANAF de unde nu se mai poate lua înapoi.
     *
     * Fără utilizator autentificat nu se cere niciun drept, ca peste tot aici:
     * acolo lucrează aplicatia — folderul urmărit, sarcinile programate — nu o
     * persoană care ar trebui să aibă voie.
     */
    public static function areDreptul(string $drept): bool
    {
        $user = self::curent();

        if (!$user) {
            return true;
        }

        if (self::esteSuperAdministrator()) {
            return true;
        }

        $companie = ContextCompanie::curenta();

        if (!$companie) {
            return false;
        }

        if (self::esteAdministratorClient()) {
            return true;
        }

        return (bool) DB::table('company_user')
            ->where('user_id', $user->id)
            ->where('company_id', $companie)
            ->value($drept);
    }

    public static function poateSemna(): bool
    {
        return self::areDreptul('poate_semna');
    }

    public static function poateDepune(): bool
    {
        return self::areDreptul('poate_depune');
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
