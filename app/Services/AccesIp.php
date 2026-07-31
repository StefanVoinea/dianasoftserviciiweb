<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Adresele IP de la care are voie sa intre fiecare cont.
 *
 * Lista se scrie de mana, cu adrese despartite prin virgula sau rand nou. Se
 * accepta:
 *   - o adresa intreaga        192.168.1.10
 *   - un interval CIDR         192.168.1.0/24
 *   - un inceput de adresa     192.168.1.*
 *
 * Lista goala inseamna „de oriunde". Asta e purtarea implicita si ramane asa
 * dinadins: o coloana noua nu are voie sa inchida pe nimeni afara.
 */
class AccesIp
{
    /** Adrese de la care se intra intotdeauna, oricare ar fi lista contului. */
    public const MEREU_PERMISE = ['127.0.0.1', '::1'];

    /** Are contul o limitare de adrese? */
    public static function esteLimitat(?User $user): bool
    {
        return $user && trim((string) $user->ip_permise) !== '';
    }

    /**
     * Are voie contul sa intre de la aceasta adresa?
     *
     * Fara lista, raspunsul e mereu da.
     */
    public static function arePermisiune(?User $user, ?string $ip): bool
    {
        return !self::esteLimitat($user) || self::potrivesteLista($user->ip_permise, $ip);
    }

    /** Se potriveste adresa cu lista scrisa? O lista goala primeste pe oricine. */
    public static function potrivesteLista(?string $lista, ?string $ip): bool
    {
        $reguli = self::reguli($lista);

        if ($reguli === []) {
            return true;
        }

        $ip = trim((string) $ip);

        if ($ip === '' || in_array($ip, self::MEREU_PERMISE, true)) {
            return true;
        }

        foreach ($reguli as $regula) {
            if (self::sePotriveste($ip, $regula)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Regulile scrise pe cont, curatate.
     *
     * @return array<int, string>
     */
    public static function reguli(?string $lista): array
    {
        $bucati = preg_split('/[\s,;]+/', (string) $lista) ?: [];

        return array_values(array_filter(array_map('trim', $bucati), function ($regula) {
            return $regula !== '';
        }));
    }

    /**
     * De ce nu se poate salva lista asa cum a fost scrisa.
     *
     * Doua opriri, amandoua ca sa nu ramana cineva inchis afara:
     *   - regulile trebuie sa fie scrise intr-o forma pe care o intelegem;
     *   - nimeni nu-si poate pune pe propriul cont o lista care nu cuprinde
     *     adresa de la care lucreaza chiar in clipa aceea.
     *
     * @return string|null motivul, sau null cand se poate salva
     */
    public static function motivRefuz(?string $lista, bool $esteContulMeu, ?string $ipCurent): ?string
    {
        $gresite = self::reguliGresite($lista);

        if ($gresite !== []) {
            return 'Nu înțeleg adresele: ' . implode(', ', $gresite)
                . '. Scrieți o adresă întreagă (192.168.1.10), un interval (192.168.1.0/24) '
                . 'sau un început de adresă (192.168.1.*).';
        }

        if (!$esteContulMeu || self::reguli($lista) === []) {
            return null;
        }

        if (!self::potrivesteLista($lista, $ipCurent)) {
            return 'Lista nu cuprinde adresa de la care lucrați acum (' . ($ipCurent ?: 'necunoscută')
                . '), iar salvarea ei v-ar închide afară din aplicație.';
        }

        return null;
    }

    /** Sunt regulile scrise intr-o forma pe care o intelegem? */
    public static function reguliGresite(?string $lista): array
    {
        $gresite = [];

        foreach (self::reguli($lista) as $regula) {
            if (!self::regulaValida($regula)) {
                $gresite[] = $regula;
            }
        }

        return $gresite;
    }

    protected static function regulaValida(string $regula): bool
    {
        if (strpos($regula, '/') !== false) {
            [$retea, $biti] = explode('/', $regula, 2);

            return filter_var($retea, FILTER_VALIDATE_IP) !== false
                && is_numeric($biti) && $biti >= 0 && $biti <= 32;
        }

        if (strpos($regula, '*') !== false) {
            return (bool) preg_match('/^[0-9.]+\*?$/', $regula);
        }

        return filter_var($regula, FILTER_VALIDATE_IP) !== false;
    }

    protected static function sePotriveste(string $ip, string $regula): bool
    {
        if ($regula === $ip) {
            return true;
        }

        if (strpos($regula, '/') !== false) {
            return self::inRetea($ip, $regula);
        }

        if (strpos($regula, '*') !== false) {
            $inceput = rtrim(substr($regula, 0, strpos($regula, '*')), '');

            return $inceput !== '' && strpos($ip, $inceput) === 0;
        }

        return false;
    }

    /** Intra adresa in reteaua scrisa CIDR? Doar IPv4. */
    protected static function inRetea(string $ip, string $cidr): bool
    {
        [$retea, $biti] = explode('/', $cidr, 2);

        $adresa = ip2long($ip);
        $bazaRetea = ip2long($retea);

        if ($adresa === false || $bazaRetea === false) {
            return false;
        }

        $biti = (int) $biti;

        if ($biti < 0 || $biti > 32) {
            return false;
        }

        if ($biti === 0) {
            return true;
        }

        $masca = -1 << (32 - $biti);

        return ($adresa & $masca) === ($bazaRetea & $masca);
    }

    /**
     * Adresa de la care vine cererea.
     *
     * In spatele unui proxy sau al unui echilibrator, adresa adevarata vine in
     * antet; se ia doar daca aplicatia are proxy-urile declarate ca de incredere,
     * altfel oricine si-ar putea scrie singur adresa.
     */
    public static function adresaCererii($request): ?string
    {
        $ip = $request->ip();

        if (!$ip) {
            Log::warning('Cerere fără adresă IP identificabilă.');
        }

        return $ip;
    }
}
