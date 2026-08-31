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
    /**
     * Cine a facut lucrarea, cand n-a facut-o un om.
     *
     * Programul de pe calculatorul clientului lucreaza singur — aduce fisiere
     * din dosarul urmarit, semneaza cu tokenul, isi cere licenta — si nu se
     * legitimeaza cu contul nimanui. In jurnal, randurile lui apareau drept
     * „necunoscut", desi se stie foarte bine cine le-a facut.
     */
    public const BRIDGE = 'Bridge local';

    /**
     * @param ?string $autor cine a facut lucrarea, cand n-a facut-o un om
     *                       (vezi Jurnal::BRIDGE)
     */
    public static function scrie(
        string $actiune,
        string $descriere,
        array $context = [],
        ?string $cif = null,
        bool $reusit = true,
        ?string $autor = null
    ): AnafJurnal {
        $user = self::utilizator();

        return AnafJurnal::create([
            'user_id' => optional($user)->id,
            'user_nume' => self::autorul($user, $autor),
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
    public static function esec(
        string $actiune,
        string $descriere,
        array $context = [],
        ?string $cif = null,
        ?string $autor = null
    ): AnafJurnal {
        return self::scrie($actiune, $descriere, $context, $cif, false, $autor);
    }

    /**
     * Cine a facut lucrarea.
     *
     * Intai omul autentificat; apoi cine s-a spus anume (lucrarile pornite din
     * program, care se fac prin bridge-ul de la client); apoi, daca cererea vine
     * de la programul local, chiar el.
     *
     * Randul ramane fara nume numai cand nu se stie cu adevarat cine a lucrat.
     */
    protected static function autorul($user, ?string $autor): ?string
    {
        return self::numeUtilizator($user)
            ?: $autor
            ?: (self::vineDeLaProgramulLocal() ? self::BRIDGE : null);
    }

    /**
     * Cererea vine de la programul de pe calculatorul clientului?
     *
     * El se legitimeaza cu codul lui de instalare, pus tot pe „Authorization:
     * Bearer" — dar codul acela nu e un jeton al aplicatiei, si tocmai de aici
     * se cunoaste: jetoanele aplicatiei au trei bucati despartite de puncte.
     */
    protected static function vineDeLaProgramulLocal(): bool
    {
        if (!app()->bound('request')) {
            return false;
        }

        return (string) request()->bearerToken() !== '' && !self::pareTokenulAplicatiei();
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
        /*
         * Cand cererea poarta altceva decat un token al aplicatiei — codul de
         * instalare al agentului, de pilda — nici nu se mai intreaba gardul.
         * Altfel Passport incearca sa-l citeasca drept JWT, se opreste, si
         * scrie el singur in jurnalul serverului cate o eroare la fiecare
         * cerere a agentului, desi nu s-a stricat nimic.
         */
        if (!self::pareTokenulAplicatiei()) {
            return null;
        }

        foreach (['api', null] as $guard) {
            try {
                $user = $guard ? Auth::guard($guard)->user() : Auth::user();
            } catch (\Throwable $e) {
                /*
                 * Nu toate cererile poarta un token al aplicatiei. Agentul de la
                 * client se legitimeaza cu codul lui de instalare, iar Passport,
                 * incercand sa-l citeasca drept JWT, se opreste cu „The JWT
                 * string must have two dots". O insemnare in jurnal n-are voie
                 * sa dea peste cap lucrarea pe care doar o consemneaza: acolo
                 * unde nu e nimeni autentificat, randul ramane fara nume.
                 */
                continue;
            }

            if ($user) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Poarta cererea un token al aplicatiei?
     *
     * Un token Passport e un JWT: trei parti despartite prin doua puncte. Codul
     * de instalare al agentului nu seamana cu asa ceva, iar fara cerere (in
     * comenzile din consola) nu e nimeni de cautat.
     */
    protected static function pareTokenulAplicatiei(): bool
    {
        if (!app()->bound('request')) {
            return false;
        }

        $token = (string) request()->bearerToken();

        return $token !== '' && substr_count($token, '.') === 2;
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
        } catch (\Throwable $e) {
            // Insemnarea nu are voie sa darame lucrarea pe care o consemneaza.
            return null;
        }
    }
}
