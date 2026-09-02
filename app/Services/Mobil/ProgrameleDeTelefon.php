<?php

namespace App\Services\Mobil;

use Illuminate\Support\Facades\Storage;

/**
 * Programele de telefon puse la indemana clientilor, si versiunile lor.
 *
 * Aplicatiile de Android nu trec prin niciun magazin: clientul le ia de aici,
 * din aplicatia web, si tot de aici afla telefonul cand a aparut una mai noua.
 * Magazinul Google ar cere cont de dezvoltator, verificari si asteptare la
 * fiecare indreptare — pentru un program folosit de contabilii nostri, nu de
 * lumea larga, drumul acesta e si mai scurt, si mai in mana noastra.
 *
 * Versiunea nu se tine intr-un fisier alaturat, ci in chiar numele arhivei:
 *
 *     spv_curier-1.4.0+14.apk
 *
 * Asa nu are cum sa se desparta de program. Un fisier de alaturi se uita la
 * inlocuire, si atunci telefoanele ar fi chemate sa se innoiasca la o versiune
 * pe care n-o au, sau ar sta cu una veche crezand ca e cea noua.
 */
class ProgrameleDeTelefon
{
    /** Dosarul in care se pun arhivele, sub „storage/app". */
    public const DOSAR = 'mobil';

    /**
     * Aplicatiile stiute, cu numele lor de aratat.
     *
     * Cheia e si numele fisierului, si ce spune telefonul despre sine cand
     * intreaba de versiune.
     */
    public const APLICATII = [
        'spv_curier' => 'SPV Curier',
        'etransport' => 'Dispecer e-Transport',
        'grefier_alert' => 'Grefier alert',
    ];

    /**
     * Cea mai noua arhiva pentru o aplicatie, sau null cand nu e pusa niciuna.
     *
     * @return array{fisier: string, cale: string, versiune: string, cod: int, marime: int, pusa_la: int}|null
     */
    public function ceaMaiNoua(string $aplicatia): ?array
    {
        if (!isset(self::APLICATII[$aplicatia])) {
            return null;
        }

        $gasite = [];

        foreach (Storage::files(self::DOSAR) as $cale) {
            $fisier = basename($cale);
            $desfacuta = $this->desfaNumele($fisier);

            if ($desfacuta === null || $desfacuta['aplicatia'] !== $aplicatia) {
                continue;
            }

            $gasite[] = [
                'fisier' => $fisier,
                'cale' => $cale,
                'versiune' => $desfacuta['versiune'],
                'cod' => $desfacuta['cod'],
                'marime' => (int) Storage::size($cale),
                'pusa_la' => (int) Storage::lastModified($cale),
            ];
        }

        if ($gasite === []) {
            return null;
        }

        /*
         * Dupa codul versiunii, nu dupa data fisierului: o arhiva copiata din
         * nou pe server capata data de azi fara sa fie mai noua, iar asta ar
         * chema toate telefoanele sa se „innoiasca" inapoi.
         */
        usort($gasite, function ($unul, $altul) {
            return $altul['cod'] <=> $unul['cod'];
        });

        return $gasite[0];
    }

    /**
     * Desface „spv_curier-1.4.0+14.apk" in bucatile lui.
     *
     * @return array{aplicatia: string, versiune: string, cod: int}|null
     */
    public function desfaNumele(string $fisier): ?array
    {
        $tipar = '/^([a-z0-9_]+)-([0-9]+(?:\.[0-9]+)*)\+([0-9]+)\.apk$/i';

        if (!preg_match($tipar, $fisier, $bucati)) {
            return null;
        }

        return [
            'aplicatia' => strtolower($bucati[1]),
            'versiune' => $bucati[2],
            'cod' => (int) $bucati[3],
        ];
    }

    /** Numele sub care se descarca arhiva: fara cod, ca sa se citeasca usor. */
    public function numeDeDescarcare(string $aplicatia, string $versiune): string
    {
        return $aplicatia . '-' . $versiune . '.apk';
    }
}
