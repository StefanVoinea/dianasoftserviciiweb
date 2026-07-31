<?php

namespace App\Services\Anaf\Bridge;

use App\Models\AnafCertificat;
use Illuminate\Support\Facades\Storage;

/**
 * Licențele programului local și jetoanele cu care i se dau comenzi.
 *
 * Programul de pe calculatorul clientului stă la vedere: oricine îl poate citi
 * și copia. Ce nu se poate copia e dreptul de a-l folosi. Două chei rezolvă
 * asta, amândouă semnate cu cheia noastră privată, pe care o are doar serverul:
 *
 *   - **licența** leagă instalarea de o mașină anume și are o dată de expirare.
 *     Copiată pe alt calculator, nu mai e valabilă; neînnoită de server — pentru
 *     că abonamentul a expirat — programul se oprește singur în câteva zile.
 *   - **jetonul de comandă** ține câteva minute și însoțește fiecare cerere.
 *     Codul de acces din configurare.env rămâne doar pentru instalare, așa că
 *     nici clientul, care îl cunoaște, nu-și poate porni programul din altă
 *     aplicație: n-are cum să semneze un jeton.
 *
 * Se folosește RSA cu SHA-256, prin openssl: programul local are PHP-ul lui, cu
 * extensia openssl în kit, deci verifică semnătura fără să cheme nimic din afară.
 */
class Licente
{
    /** Cât ține un jeton de comandă. Destul pentru o operație, prea puțin pentru altceva. */
    public const JETON_SECUNDE = 300;

    /** Câte zile ține o licență până la reînnoire. */
    public const LICENTA_ZILE = 30;

    protected const DOSAR = 'bridge';
    protected const CHEIE_PRIVATA = 'bridge/cheie-privata.pem';
    protected const CHEIE_PUBLICA = 'bridge/cheie-publica.pem';

    /** Generează perechea de chei, dacă nu există deja. */
    public function pregatesteCheile(bool $forteaza = false): bool
    {
        if (!$forteaza && Storage::exists(self::CHEIE_PRIVATA)) {
            return false;
        }

        $optiuni = array_filter([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            // Pe Windows, openssl nu-si gaseste singur configurarea.
            'config' => $this->configurareOpenssl(),
        ]);

        $pereche = openssl_pkey_new($optiuni);

        if ($pereche === false) {
            throw new \RuntimeException('Cheia nu a putut fi generată: ' . openssl_error_string());
        }

        openssl_pkey_export($pereche, $privata, null, $optiuni);
        $publica = openssl_pkey_get_details($pereche)['key'];

        Storage::put(self::CHEIE_PRIVATA, $privata);
        Storage::put(self::CHEIE_PUBLICA, $publica);

        return true;
    }

    /**
     * Fișierul de configurare al openssl, când sistemul nu-l are la îndemână.
     *
     * Pe Linux e găsit singur; pe Windows, generarea cheii cade cu un „fopen: No
     * such process" până i se arată calea. Se caută unde îl pun instalările
     * obișnuite de PHP pentru Windows.
     */
    protected function configurareOpenssl(): ?string
    {
        if (DIRECTORY_SEPARATOR !== '\\') {
            return null;
        }

        $cai = array_filter([
            getenv('OPENSSL_CONF') ?: null,
            dirname(PHP_BINARY) . '\\extras\\ssl\\openssl.cnf',
            dirname(PHP_BINARY) . '\\extras\\openssl.cnf',
            'C:\\openssl\\openssl.cnf',
        ]);

        foreach ($cai as $cale) {
            if (is_file($cale)) {
                return $cale;
            }
        }

        return null;
    }

    public function areChei(): bool
    {
        return Storage::exists(self::CHEIE_PRIVATA) && Storage::exists(self::CHEIE_PUBLICA);
    }

    public function cheiePublica(): ?string
    {
        return Storage::exists(self::CHEIE_PUBLICA) ? Storage::get(self::CHEIE_PUBLICA) : null;
    }

    /**
     * Licența unei instalări, legată de mașina ei.
     *
     * @param string $masina amprenta calculatorului, citită de la programul local
     *
     * @return array{date: array, semnatura: string}
     */
    public function emite(AnafCertificat $certificat, string $masina, ?int $zile = null): array
    {
        $date = [
            'versiune' => 1,
            'client' => $certificat->company_id,
            'certificat' => $certificat->id,
            'titular' => $certificat->cn,
            'masina' => $masina,
            'emisa' => now()->toIso8601String(),
            'expira' => now()->addDays($zile ?: self::LICENTA_ZILE)->toIso8601String(),
            // Cu licența pusă, comenzile vin numai cu jeton semnat.
            'jeton_semnat' => true,
        ];

        return ['date' => $date, 'semnatura' => $this->semneaza($this->canonic($date))];
    }

    /**
     * Jetonul cu care se dă o comandă programului local.
     *
     * Ține câteva minute: cine îl prinde nu-l poate folosi mâine, iar cine nu
     * are cheia privată nu-l poate face deloc.
     */
    public function jeton(?int $secunde = null): string
    {
        $date = [
            'emis' => time(),
            'expira' => time() + ($secunde ?: self::JETON_SECUNDE),
        ];

        $continut = $this->canonic($date);

        return 'v1.' . $this->base64Url($continut) . '.' . $this->base64Url($this->semneaza($continut, true));
    }

    /** Reprezentare stabilă a datelor: semnătura trebuie să acopere exact ce se trimite. */
    public function canonic(array $date): string
    {
        ksort($date);

        return json_encode($date, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /** @return string semnătura, în base64 (sau brută, când e cerută pentru jeton) */
    protected function semneaza(string $continut, bool $bruta = false): string
    {
        if (!$this->areChei()) {
            throw new \RuntimeException(
                'Cheia de semnare a licențelor lipsește. Rulați „php artisan anaf:chei-bridge".'
            );
        }

        $cheie = openssl_pkey_get_private(Storage::get(self::CHEIE_PRIVATA));

        if ($cheie === false || !openssl_sign($continut, $semnatura, $cheie, OPENSSL_ALGO_SHA256)) {
            throw new \RuntimeException('Semnarea a eșuat: ' . openssl_error_string());
        }

        return $bruta ? $semnatura : base64_encode($semnatura);
    }

    /** Verificarea se face în programul local; aici e doar pentru teste. */
    public function verifica(string $continut, string $semnatura): bool
    {
        $cheie = openssl_pkey_get_public((string) $this->cheiePublica());

        return $cheie !== false
            && openssl_verify($continut, base64_decode($semnatura), $cheie, OPENSSL_ALGO_SHA256) === 1;
    }

    protected function base64Url(string $date): string
    {
        return rtrim(strtr(base64_encode($date), '+/', '-_'), '=');
    }
}
