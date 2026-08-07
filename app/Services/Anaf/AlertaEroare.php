<?php

namespace App\Services\Anaf;

use App\Mail\AlertaEroareSpvEmail;
use App\Models\AnafCertificat;
use App\Models\Company;
use App\Support\ContextCompanie;
use App\Support\ContextUtilizator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Instiintarea celui care tine aplicatia, cand ceva se strica.
 *
 * Trei lucruri o fac de folos, si toate trei lipseau cand ne uitam in jurnalul
 * serverului: cine e clientul, cu ce certificat s-a lucrat si ce e de facut.
 * Fara ele, un mesaj de eroare e doar o vorba fara adresa.
 *
 * Nu opreste niciodata lucrarea pe care o insoteste: o instiintare picata — SMTP
 * cazut, cutie plina — n-are voie sa darame tocmai cererea pe care o raporteaza.
 */
class AlertaEroare
{
    /**
     * Ce e de facut, dupa cum arata eroarea.
     *
     * Se cauta in ordine si se ia prima potrivire, deci tiparele anume trebuie
     * sa stea inaintea celor largi.
     *
     * @var array<int, array{tipar: string, fapt: string}>
     */
    protected const REZOLVARI = [
        [
            'tipar' => '/SEC_E_CONTEXT_EXPIRED|schannel/i',
            'fapt' => 'Legătura cu ANAF s-a rupt în timpul răspunsului. Cea mai deasă pricină nu e'
                . ' rețeaua, ci tokenul: dacă driverul lui cere codul PIN într-un dialog pe care nu-l'
                . ' vede nimeni, legătura moare de la sine. A doua pricină e antivirusul care desface'
                . ' traficul — pe calculatorul clientului, rulați diagnoza.bat și priviți pașii 2, 3 și 5.',
        ],
        [
            'tipar' => '/curl 7|curl 6|nu se poate deschide|dezlegat \(DNS\)/i',
            'fapt' => 'Calculatorul clientului nu poate ieși în internet către adresa cerută.'
                . ' De verificat, în ordine: internetul de acolo, apoi ieșirea pe 443 în firewall.',
        ],
        [
            'tipar' => '/curl 60|certificatul serverului nu este de încredere|SSL Filter/i',
            'fapt' => 'Traficul e desfăcut de antivirus sau de un proxy. Certificatul de pe token nu mai'
                . ' ajunge întreg la ANAF. Adresele ANAF trebuie scoase de sub scanarea HTTPS.',
        ],
        [
            // Scriptul lipsa se plange in doua feluri, dupa cum cade fraza
            'tipar' => '/\.ps1.{0,80}does not exist|does not exist.{0,80}\.ps1|nu a putut fi citit/i',
            'fapt' => 'Lipsește un script din dosarul programului local. Se repară cu un kit nou și'
                . ' instaleaza.bat pe calculatorul clientului.',
        ],
        [
            'tipar' => '/Unknown column|SQLSTATE\[42S22\]/i',
            'fapt' => 'Baza de date nu are coloana pe care codul o cere: a fost pus cod nou fără să se'
                . ' ruleze migrările. Pe server: php artisan migrate --force.',
        ],
        [
            'tipar' => '/429|prea multe cereri|mai rar la ușă/i',
            'fapt' => 'S-a atins limita de cereri. Dacă vine de la aplicație, limita puntii se poate ridica;'
                . ' dacă vine de la ANAF, trebuie lăsat un răgaz mai mare între apeluri.',
        ],
        [
            'tipar' => '/Programul local nu a răspuns|programul de pe calculatorul/i',
            'fapt' => 'Programul de pe calculatorul cu tokenul nu răspunde: sesiunea Windows de acolo e'
                . ' închisă, procesul a fost oprit, ori antivirusul l-a pus în carantină. Diagnoza de la'
                . ' client (diagnoza.bat) spune care dintre ele.',
        ],
        [
            'tipar' => '/certificat expirat|fără drepturi|Autentificare SPV respinsă/i',
            'fapt' => 'ANAF nu primește certificatul: fie a expirat, fie nu mai e înrolat pentru firma'
                . ' cerută. De verificat în SPV, la înrolări.',
        ],
    ];

    /**
     * Trimite instiintarea, o singura data la racirea ceruta pentru acelasi fel
     * de eroare — altfel o pana care se repeta de o suta de ori ar trimite o
     * suta de emailuri, si tocmai atunci cutia trebuie sa fie citibila.
     *
     * @param string          $unde    unde s-a intamplat, pe intelesul cuiva grabit
     * @param Throwable|string $eroare
     * @param array<string, mixed> $context ce s-a lucrat cand s-a stricat
     */
    public static function trimite(string $unde, $eroare, array $context = []): bool
    {
        try {
            $mesaj = $eroare instanceof Throwable ? $eroare->getMessage() : (string) $eroare;

            if (!self::estePrimaOara($unde, $mesaj)) {
                return false;
            }

            $destinatar = config('anaf.alerte.email');

            if (!$destinatar) {
                return false;
            }

            Mail::to($destinatar)->send(new AlertaEroareSpvEmail([
                'unde' => $unde,
                'mesaj' => $mesaj,
                'cand' => now()->format('d.m.Y H:i:s'),
                'client' => self::clientul($context),
                'certificat' => self::certificatul($context),
                'utilizator' => self::utilizatorul(),
                'context' => self::contextCurat($context),
                'rezolvare' => self::rezolvarea($mesaj),
                'urma' => $eroare instanceof Throwable ? self::urma($eroare) : null,
            ]));

            return true;
        } catch (Throwable $e) {
            // O instiintare picata n-are voie sa darame lucrarea pe care o insoteste.
            Log::warning('Înștiințarea de eroare nu a plecat: ' . $e->getMessage());

            return false;
        }
    }

    /** Ce e de facut, dupa cum arata eroarea. */
    public static function rezolvarea(string $mesaj): string
    {
        foreach (self::REZOLVARI as $regula) {
            if (preg_match($regula['tipar'], $mesaj)) {
                return $regula['fapt'];
            }
        }

        return 'Nu am o rețetă pregătită pentru eroarea aceasta. Jurnalul aplicației și, dacă e vorba de'
            . ' un client, diagnoza de pe calculatorul lui (diagnoza.bat) spun mai departe.';
    }

    /**
     * A mai fost trimisa de curand aceeasi eroare?
     *
     * Se socoteste pe felul erorii, nu pe textul intreg: numerele si id-urile
     * dinauntru se schimba de la o data la alta, iar altfel racirea n-ar prinde
     * niciodata aceeasi pana de doua ori.
     */
    protected static function estePrimaOara(string $unde, string $mesaj): bool
    {
        $felul = preg_replace('/\d+/', '#', mb_substr($mesaj, 0, 200));
        $cheie = 'alerta_eroare_' . md5($unde . '|' . $felul);

        $racire = (int) config('anaf.alerte.racire_minute', 30);

        return Cache::add($cheie, true, now()->addMinutes(max(1, $racire)));
    }

    /** Clientul in numele caruia se lucra. */
    protected static function clientul(array $context): ?string
    {
        $id = $context['company_id'] ?? ContextCompanie::curenta();

        if (!$id) {
            return null;
        }

        $client = Company::find($id);

        return $client ? $client->denumire . ' (#' . $client->id . ')' : '#' . $id;
    }

    /** Certificatul cu care se lucra, cand se stie. */
    protected static function certificatul(array $context): ?string
    {
        $id = $context['certificat_id'] ?? null;

        if (!$id) {
            return null;
        }

        $certificat = AnafCertificat::query()->toateCompaniile()->find($id);

        if (!$certificat) {
            return '#' . $id;
        }

        return $certificat->cn . ' (' . ($certificat->mod_legatura ?: 'direct') . ')';
    }

    protected static function utilizatorul(): ?string
    {
        $om = ContextUtilizator::curent();

        return $om ? $om->name . ' <' . $om->email . '>' : null;
    }

    /**
     * Contextul, adus la o marime care incape intr-un email.
     *
     * @return array<string, string>
     */
    protected static function contextCurat(array $context): array
    {
        $curat = [];

        foreach ($context as $cheie => $valoare) {
            if (in_array($cheie, ['company_id', 'certificat_id'], true)) {
                continue;
            }

            if (is_array($valoare)) {
                $valoare = json_encode($valoare, JSON_UNESCAPED_UNICODE);
            }

            $curat[(string) $cheie] = mb_substr((string) $valoare, 0, 500);
        }

        return $curat;
    }

    /** Primele randuri ale urmei, cat sa se stie de unde a pornit. */
    protected static function urma(Throwable $eroare): string
    {
        $randuri = array_slice(explode("\n", $eroare->getTraceAsString()), 0, 8);

        return $eroare->getFile() . ':' . $eroare->getLine() . "\n" . implode("\n", $randuri);
    }
}
