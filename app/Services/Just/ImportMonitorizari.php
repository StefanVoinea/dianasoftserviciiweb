<?php

namespace App\Services\Just;

use App\Models\PortalJustMonitorizare;

/**
 * Citeste liniile unui fisier Excel si le transforma in monitorizari.
 *
 * Fisierul poate avea un cap de tabel (Numar dosar / Nume parte / Email /
 * Instanta) sau poate fi o simpla lista pe o coloana: in al doilea caz tipul se
 * deduce din forma valorii, pentru ca numerele de dosar au un format fix.
 */
class ImportMonitorizari
{
    /** Denumirile acceptate pentru fiecare coloana, dupa normalizare. */
    protected const COLOANE = [
        'dosar' => ['numardosar', 'nrdosar', 'dosar', 'numar', 'nr'],
        'parte' => ['numeparte', 'parte', 'nume', 'denumire', 'client'],
        'email' => ['email', 'emails', 'adresaemail', 'adresa'],
        'institutie' => ['instanta', 'institutie', 'instante'],
    ];

    /**
     * @param array $randuri liniile fisierului, ca tablouri de celule
     *
     * @return array{intrari: array<int, array{tip:string, valoare:string, institutie:?string, email:?string}>, ignorate: int}
     */
    public function dinRanduri(array $randuri): array
    {
        $randuri = array_values(array_filter($randuri, function ($rand) {
            return is_array($rand) && $this->areContinut($rand);
        }));

        if ($randuri === []) {
            return ['intrari' => [], 'ignorate' => 0];
        }

        $coloane = $this->capDeTabel($randuri[0]);

        if ($coloane !== []) {
            array_shift($randuri);
        }

        $intrari = [];
        $ignorate = 0;
        $vazute = [];

        foreach ($randuri as $rand) {
            $intrare = $coloane === []
                ? $this->dinRandFaraCap($rand)
                : $this->dinRandCuCap($rand, $coloane);

            if ($intrare === null) {
                $ignorate++;

                continue;
            }

            $cheie = $intrare['tip'] . '|' . mb_strtolower($intrare['valoare']) . '|' . $intrare['institutie'];

            if (isset($vazute[$cheie])) {
                continue;
            }

            $vazute[$cheie] = true;
            $intrari[] = $intrare;
        }

        return ['intrari' => $intrari, 'ignorate' => $ignorate];
    }

    /**
     * Coloanele recunoscute in primul rand. Tabloul gol inseamna ca fisierul nu
     * are cap de tabel.
     *
     * @return array<string, int>
     */
    protected function capDeTabel(array $rand): array
    {
        $coloane = [];

        foreach ($rand as $index => $celula) {
            $nume = $this->normalizeaza((string) $celula);

            if ($nume === '') {
                continue;
            }

            foreach (self::COLOANE as $camp => $denumiri) {
                if (in_array($nume, $denumiri, true) && !isset($coloane[$camp])) {
                    $coloane[$camp] = $index;
                }
            }
        }

        // „Numar” singur nu e suficient: fara o alta coloana recunoscuta, randul
        // e mai probabil date, nu cap de tabel.
        return isset($coloane['dosar']) || isset($coloane['parte']) ? $coloane : [];
    }

    protected function dinRandCuCap(array $rand, array $coloane): ?array
    {
        $dosar = $this->celula($rand, $coloane['dosar'] ?? null);
        $parte = $this->celula($rand, $coloane['parte'] ?? null);

        if ($dosar === null && $parte === null) {
            return null;
        }

        return [
            'tip' => $dosar !== null ? PortalJustMonitorizare::TIP_DOSAR : PortalJustMonitorizare::TIP_PARTE,
            'valoare' => $dosar !== null ? $dosar : $parte,
            'institutie' => $this->celula($rand, $coloane['institutie'] ?? null),
            'email' => $this->celula($rand, $coloane['email'] ?? null),
        ];
    }

    protected function dinRandFaraCap(array $rand): ?array
    {
        $valori = array_values(array_filter(array_map(function ($celula) {
            return $this->text($celula);
        }, $rand), function ($valoare) {
            return $valoare !== null;
        }));

        if ($valori === []) {
            return null;
        }

        $valoare = $valori[0];

        // A doua celula, daca e adresa de email, se ia ca destinatar.
        $email = null;

        foreach (array_slice($valori, 1) as $rest) {
            if (filter_var($rest, FILTER_VALIDATE_EMAIL)) {
                $email = $rest;
                break;
            }
        }

        return [
            'tip' => $this->esteNumarDosar($valoare)
                ? PortalJustMonitorizare::TIP_DOSAR
                : PortalJustMonitorizare::TIP_PARTE,
            'valoare' => $valoare,
            'institutie' => null,
            'email' => $email,
        ];
    }

    /** Numar unic de dosar („1234/3/2024”) sau format vechi („1234/2004”). */
    public function esteNumarDosar(string $valoare): bool
    {
        $valoare = trim($valoare);

        return (bool) preg_match('#^\d+/[^/]+/\d{4}(/[a-z0-9*]+)?$#i', $valoare)
            || (bool) preg_match('#^\d+/\d{4}$#', $valoare);
    }

    protected function celula(array $rand, $index): ?string
    {
        if ($index === null || !array_key_exists($index, $rand)) {
            return null;
        }

        return $this->text($rand[$index]);
    }

    protected function text($celula): ?string
    {
        if (is_array($celula) || is_object($celula)) {
            return null;
        }

        $valoare = trim((string) $celula);

        return $valoare === '' ? null : $valoare;
    }

    protected function areContinut(array $rand): bool
    {
        foreach ($rand as $celula) {
            if ($this->text($celula) !== null) {
                return true;
            }
        }

        return false;
    }

    /** Compara denumirile de coloana fara diacritice, spatii sau semne. */
    protected function normalizeaza(string $text): string
    {
        $text = mb_strtolower(trim($text));

        $diacritice = ['ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ş' => 's', 'ț' => 't', 'ţ' => 't'];
        $text = strtr($text, $diacritice);

        return preg_replace('/[^a-z0-9]/', '', $text);
    }
}
