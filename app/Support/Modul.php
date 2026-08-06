<?php

namespace App\Support;

use App\Models\AbonamentClient;
use Illuminate\Support\Facades\DB;

/**
 * Modulele vandute: SPV Curier, Dispecer e-Transport si Grefier alert.
 *
 * Accesul la un modul tine de doua lucruri, si trebuie sa fie amandoua:
 *
 *   - abonamentul clientului — ce a cumparat firma;
 *   - darea catre om — cine dintre oamenii firmei lucreaza cu modulul acela.
 *
 * Al doilea se tine in coloana „module" din company_user, ca si celelalte
 * drepturi de firma. Gol inseamna „tot ce e in abonament": asa conturile de
 * pana acum raman neschimbate, fara sa umble cineva pe fiecare in parte.
 */
class Modul
{
    /**
     * Cheia fiecarui modul e chiar cea din abonament si din middleware („modul:spv"),
     * ca sa nu fie doua nume pentru acelasi lucru.
     */
    public const CATALOG = [
        'spv' => [
            'nume' => 'SPV Curier',
            'descriere' => 'Declarații, mesaje și solicitări în Spațiul Privat Virtual',
            'ruta' => 'spv',
            // Intrarile din meniul din stanga care tin de modul
            'meniu' => ['spv', 'vector-fiscal'],
        ],
        'etransport' => [
            'nume' => 'Dispecer e-Transport',
            'descriere' => 'Declararea transporturilor și urmărirea UIT-urilor',
            'ruta' => 'etransport',
            'meniu' => ['etransport-anaf'],
        ],
        'portal_just' => [
            'nume' => 'Grefier alert',
            'descriere' => 'Dosare, termene și monitorizare în Portal Just',
            'ruta' => 'portal-just',
            'meniu' => ['portal-just'],
        ],
    ];

    /** @return array<int, string> */
    public static function chei(): array
    {
        return array_keys(self::CATALOG);
    }

    /** Catalogul, asa cum il asteapta interfata. */
    public static function lista(): array
    {
        $lista = [];

        foreach (self::CATALOG as $cheie => $modul) {
            $lista[] = [
                'cheie' => $cheie,
                'nume' => $modul['nume'],
                'descriere' => $modul['descriere'],
            ];
        }

        return $lista;
    }

    /**
     * Modulele date contului in firma aceasta.
     *
     * @return array<int, string>|null null = toate cele din abonament
     */
    public static function aleContului($userId, $companieId): ?array
    {
        if (!$userId || !$companieId) {
            return null;
        }

        $scris = DB::table('company_user')
            ->where('user_id', $userId)
            ->where('company_id', $companieId)
            ->value('module');

        if ($scris === null || $scris === '') {
            return null;
        }

        $chei = json_decode($scris, true);

        return is_array($chei) ? array_values(array_intersect($chei, self::chei())) : null;
    }

    /**
     * Scrie modulele date contului. Null sterge darea anume, adica il lasa cu
     * tot ce cuprinde abonamentul.
     *
     * @param array<int, string>|null $chei
     */
    public static function scrie($userId, $companieId, ?array $chei): void
    {
        DB::table('company_user')
            ->where('user_id', $userId)
            ->where('company_id', $companieId)
            ->update([
                'module' => $chei === null
                    ? null
                    : json_encode(array_values(array_intersect($chei, self::chei()))),
            ]);
    }

    /**
     * Ce vede omul cu adevarat: si in abonament, si dat lui.
     *
     * Un client fara abonament scris lucreaza ca pana acum, cu tot — asa
     * instalarile vechi nu se opresc singure.
     *
     * @return array<int, string>
     */
    public static function vazuteDe($userId, $companieId): array
    {
        $abonament = AbonamentClient::alClientului($companieId);
        $aleLui = self::aleContului($userId, $companieId);

        return array_values(array_filter(self::chei(), function ($cheie) use ($abonament, $aleLui) {
            $inAbonament = $abonament === null || $abonament->areModul($cheie);
            $datLui = $aleLui === null || in_array($cheie, $aleLui, true);

            return $inAbonament && $datLui;
        }));
    }

    /**
     * Intrarile de meniu care tin de modulele acestea.
     *
     * @param array<int, string> $chei
     * @return array<int, string> slug-uri de optiuni de meniu
     */
    public static function slugurileMeniului(array $chei): array
    {
        $sluguri = [];

        foreach ($chei as $cheie) {
            foreach (self::CATALOG[$cheie]['meniu'] ?? [] as $slug) {
                $sluguri[] = $slug;
            }
        }

        return array_values(array_unique($sluguri));
    }
}
