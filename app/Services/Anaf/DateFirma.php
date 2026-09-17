<?php

namespace App\Services\Anaf;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Datele unei firme, luate de la ANAF după codul fiscal.
 *
 * ANAF ține un serviciu public — „PlatitorTvaRest" — care, întrebat cu un CUI,
 * răspunde cu denumirea, sediul, numărul de la Registrul Comerțului, starea
 * înregistrării și situația TVA. Așa se completează un client nou dintr-un
 * singur câmp, fără să se scrie de mână ce ANAF știe deja.
 *
 * Serviciul nu cere autentificare, dar are limită de apeluri, așa că se
 * întreabă numai la cererea omului, nu la fiecare tastă.
 */
class DateFirma
{
    protected const URL = 'https://webservicesp.anaf.ro/api/PlatitorTvaRest/v9/tva';

    /** @var int secunde */
    protected $asteptare;

    public function __construct(int $asteptare = 15)
    {
        $this->asteptare = $asteptare;
    }

    /**
     * Ce știe ANAF despre firma cu acest CUI.
     *
     * Întoarce null când codul nu e al niciunei firme sau când serviciul nu
     * răspunde; cine cheamă hotărăște ce spune omului, fiindcă cele două
     * situații se deosebesc doar din jurnal.
     *
     * @return array<string, mixed>|null
     */
    public function dupaCui(string $cui): ?array
    {
        $cifre = $this->doarCifre($cui);

        if ($cifre === '') {
            return null;
        }

        try {
            $raspuns = Http::timeout($this->asteptare)
                ->asJson()
                ->post(self::URL, [
                    ['cui' => (int) $cifre, 'data' => now()->toDateString()],
                ]);
        } catch (\Throwable $eroare) {
            Log::warning('ANAF nu a răspuns la interogarea CUI ' . $cifre . ': ' . $eroare->getMessage());

            return null;
        }

        if (! $raspuns->successful()) {
            Log::warning('ANAF a refuzat interogarea CUI ' . $cifre . ' (HTTP ' . $raspuns->status() . ')');

            return null;
        }

        $gasite = $raspuns->json('found');

        if (! is_array($gasite) || $gasite === []) {
            return null;
        }

        return $this->limpezeste($gasite[0]);
    }

    /**
     * Din răspunsul ANAF, numai ce se scrie într-o fișă de client.
     *
     * @param array<string, mixed> $firma
     *
     * @return array<string, mixed>
     */
    protected function limpezeste(array $firma): array
    {
        $generale = $firma['date_generale'] ?? [];
        $sediu = $firma['adresa_sediu_social'] ?? [];
        $inactiv = $firma['stare_inactiv'] ?? [];
        $tva = $firma['inregistrare_scop_Tva'] ?? [];

        $localitate = $this->numePropriu($this->fara($sediu, 'sdenumire_Localitate'));
        $judet = $this->numePropriu($this->fara($sediu, 'sdenumire_Judet'));

        return [
            'cui' => (string) $this->fara($generale, 'cui'),
            'denumire' => $this->numePropriu($this->fara($generale, 'denumire')),
            'reg_com' => $this->fara($generale, 'nrRegCom'),
            'adresa' => $this->sediul($sediu),
            'localitate' => $localitate,
            'judet' => $judet,
            'telefon' => $this->fara($generale, 'telefon'),
            'cod_caen' => $this->fara($generale, 'cod_CAEN'),
            'cod_postal' => $this->fara($sediu, 'scod_Postal') ?: $this->fara($generale, 'codPostal'),
            'platitor_tva' => (bool) ($tva['scpTVA'] ?? false),
            'stare' => $this->fara($generale, 'stare_inregistrare'),
            'inactiva' => (bool) ($inactiv['statusInactivi'] ?? false),
            // Data radierii vine goală și pentru firme radiate; atunci o spune starea.
            'radiata' => $this->fara($inactiv, 'dataRadiere') !== ''
                || mb_stripos($this->fara($generale, 'stare_inregistrare'), 'RADIERE') !== false,
        ];
    }

    /**
     * Sediul social, scris cum se scrie într-un contract.
     *
     * ANAF dă adresa și întreagă, într-un singur câmp, dar cu totul cu
     * majuscule și cu județul în față. Aici se leagă din bucăți, de la stradă
     * spre județ, cum e obiceiul.
     *
     * @param array<string, mixed> $sediu
     */
    protected function sediul(array $sediu): string
    {
        $strada = $this->numePropriu($this->fara($sediu, 'sdenumire_Strada'));
        $numar = $this->fara($sediu, 'snumar_Strada');
        $detalii = $this->numePropriu($this->fara($sediu, 'sdetalii_Adresa'));
        $localitate = $this->numePropriu($this->fara($sediu, 'sdenumire_Localitate'));
        $judet = $this->numePropriu($this->fara($sediu, 'sdenumire_Judet'));

        $bucati = [];

        if ($strada !== '') {
            $bucati[] = $numar !== '' ? $strada . ' nr. ' . $numar : $strada;
        }

        foreach ([$detalii, $localitate] as $bucata) {
            if ($bucata !== '') {
                $bucati[] = $bucata;
            }
        }

        /*
         * La București sectorul e deja în localitate, iar județul se cheamă
         * „Municipiul București": pus după, ar ieși de două ori același oraș.
         */
        if ($judet !== '' && mb_stripos($localitate, $judet) === false
            && mb_stripos($judet, 'Bucureș') === false) {
            $bucati[] = 'jud. ' . $judet;
        }

        return implode(', ', $bucati);
    }

    /**
     * Majusculele ANAF, aduse la scrisul obișnuit.
     *
     * „ORŞ. NĂVODARI" se citește greu într-un contract; „Orş. Năvodari" se
     * citește. Prescurtările scurte — SRL, SA, PFA — rămân cum sunt, fiindcă
     * așa se scriu.
     */
    protected function numePropriu(string $text): string
    {
        $text = $this->virguleSubLitere(trim(preg_replace('/\s+/u', ' ', $text)));

        if ($text === '' || $text !== mb_strtoupper($text, 'UTF-8')) {
            return $text;
        }

        $intregi = ['SRL', 'SA', 'SNC', 'SCS', 'SCA', 'PFA', 'II', 'IF', 'ONG', 'RA', 'CT', 'SC'];

        $cuvinte = array_map(function ($cuvant) use ($intregi) {
            $curat = rtrim($cuvant, '.,');

            if (in_array($curat, $intregi, true) || mb_strlen($curat, 'UTF-8') <= 1) {
                return $cuvant;
            }

            return mb_convert_case($cuvant, MB_CASE_TITLE, 'UTF-8');
        }, explode(' ', $text));

        return implode(' ', $cuvinte);
    }

    /**
     * Ș și Ț cu sedilă, scrise cum se scriu în românește: cu virgulă dedesubt.
     *
     * ANAF le trimite cu sedilă — semnele turcești —, iar „Constanţa" pus într-un
     * contract lângă un „Constanța" scris de om se vede că sunt două litere
     * diferite.
     */
    protected function virguleSubLitere(string $text): string
    {
        return strtr($text, ['ş' => 'ș', 'Ş' => 'Ș', 'ţ' => 'ț', 'Ţ' => 'Ț']);
    }

    /**
     * @param array<string, mixed> $unde
     */
    protected function fara(array $unde, string $cheie): string
    {
        return trim((string) ($unde[$cheie] ?? ''));
    }

    protected function doarCifre(string $cui): string
    {
        return preg_replace('/\D+/', '', $cui);
    }
}
