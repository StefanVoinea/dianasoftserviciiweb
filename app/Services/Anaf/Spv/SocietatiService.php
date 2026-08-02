<?php

namespace App\Services\Anaf\Spv;

use App\Models\AnafCertificat;
use App\Models\AnafSocietate;
use App\Models\SpvSolicitare;

/**
 * Registrul societatilor pentru care certificatul digital are drept de semnatura.
 *
 * Lista vine chiar de la ANAF: raspunsul /listaMesaje contine campul "cui" cu
 * toate CIF-urile accesibile certificatului. Pentru fiecare se solicita apoi din
 * SPV documentele de identificare si vectorul fiscal, din care se preia denumirea
 * folosita mai departe la mesaje si solicitari.
 */
class SocietatiService
{
    protected $client;
    protected $solicitari;
    protected $certificate;

    public function __construct(SpvClient $client, SolicitareService $solicitari, CertificatService $certificate)
    {
        $this->client = $client;
        $this->solicitari = $solicitari;
        $this->certificate = $certificate;
    }

    /**
     * Initializeaza sau actualizeaza lista de societati din certificat.
     *
     * @return array{gasite: int, noi: int, dezactivate: int, cif: array}
     */
    public function sincronizeaza(?AnafCertificat $certificat = null): array
    {
        // Cand certificatul e dat, interogarea SPV merge pe el (si pe bridge-ul lui).
        if ($certificat !== null) {
            $this->certificate->foloseste($certificat);
        }

        $raspuns = $this->client->listaMesajeBrut(1);
        $lista = $this->cifurile($raspuns);

        /*
         * Lista de CIF-uri vine in raspunsul la mesaje, iar cand in ziua cerută
         * nu e niciun mesaj, ANAF raspunde uneori doar cu motivul, fara ea. Se
         * mai intreaba o data, pe fereastra intreaga: acolo se gaseste aproape
         * sigur ceva, iar odata cu mesajele vine si lista.
         */
        if ($lista === '') {
            $raspuns = $this->client->listaMesajeBrut((int) config('anaf.spv.zile_max', 60));
            $lista = $this->cifurile($raspuns);
        }

        if ($lista === '') {
            // Ce a spus ANAF lamureste mai bine decat orice presupunere a noastra.
            $spuseDeAnaf = trim((string) ($raspuns['eroare'] ?? ''));

            throw new SpvException(
                'ANAF nu a returnat lista de CIF-uri pentru acest certificat. '
                . 'Verificați că tokenul este conectat și are drepturi în SPV.'
                . ($spuseDeAnaf !== '' ? ' ANAF a răspuns: „' . $spuseDeAnaf . '”.' : '')
            );
        }

        $cifuri = array_values(array_unique(array_filter(array_map('trim', explode(',', $lista)))));
        $noi = 0;

        // Certificatul care a returnat lista devine cel asociat entitatilor.
        $certificat = $certificat ?: $this->certificate->sincronizeaza([
            'serial' => $raspuns['serial'] ?? null,
            'cnp' => $raspuns['cnp'] ?? null,
        ]);

        foreach ($cifuri as $cif) {
            $societate = AnafSocietate::firstOrNew(['cif' => $cif]);
            $noi += $societate->exists ? 0 : 1;

            $societate->fill([
                'tip' => AnafSocietate::tipDupaCif($cif),
                'activ' => true,
                'cnp_reprezentant' => $raspuns['cnp'] ?? null,
                'serial_certificat' => $raspuns['serial'] ?? null,
                'certificat_id' => $certificat->id,
                'sincronizat_la' => now(),
            ])->save();
        }

        // Entitatile la care ACEST certificat nu mai are drepturi raman in evidenta,
        // dar devin inactive. Cele ale altor certificate nu sunt atinse.
        $dezactivate = AnafSocietate::where('certificat_id', $certificat->id)
            ->whereNotIn('cif', $cifuri)
            ->where('activ', true)
            ->update(['activ' => false]);

        return [
            'certificat' => $certificat->cn,
            'certificat_id' => $certificat->id,
            'gasite' => count($cifuri),
            'noi' => $noi,
            'dezactivate' => $dezactivate,
            'cif' => $cifuri,
        ];
    }

    /**
     * Lista de CIF-uri din raspunsul ANAF, ca text.
     *
     * De obicei vine un sir despartit prin virgule, dar raspunsul poate purta
     * si o lista adevarata; amandoua se citesc la fel de bine.
     */
    protected function cifurile(array $raspuns): string
    {
        $cui = $raspuns['cui'] ?? '';

        if (is_array($cui)) {
            $cui = implode(',', $cui);
        }

        return trim((string) $cui);
    }

    /**
     * Preia entitatile inrolate pe fiecare certificat dat. Un certificat fara
     * drepturi in SPV (sau al carui token nu e conectat) nu opreste restul.
     *
     * @param  AnafCertificat[]  $certificate
     * @return array{rezultate: array, erori: array}
     */
    public function sincronizeazaPentruCertificate(array $certificate): array
    {
        $rezultate = [];
        $erori = [];

        foreach ($certificate as $certificat) {
            try {
                $rezultate[] = $this->sincronizeaza($certificat);
            } catch (SpvException $e) {
                $erori[] = $certificat->cn . ': ' . $e->getMessage();
            }
        }

        return ['rezultate' => $rezultate, 'erori' => $erori];
    }

    /**
     * Trimite solicitarile SPV lipsa (date identificare si vector fiscal) pentru
     * societatile active. Cererile deja trimise si neprimite nu se repeta.
     *
     * @return array{trimise: int, sarite: int, erori: array}
     */
    public function solicitaDocumente(array $tipuri = ['DATE IDENTIFICARE', 'VECTOR FISCAL'], ?int $userId = null): array
    {
        // Documentele deja descarcate completeaza registrul inainte de cereri noi.
        $reinterpretate = $this->solicitari->reinterpreteaza();

        $trimise = 0;
        $sarite = 0;
        $erori = [];

        foreach (AnafSocietate::active()->orderBy('cif')->get() as $societate) {
            // ANAF accepta aceste rapoarte doar pentru persoane juridice.
            if ($societate->tip === 'pf') {
                $sarite += count($tipuri);
                continue;
            }

            foreach ($tipuri as $tip) {
                if ($this->existaSolicitareInCurs($societate->cif, $tip)) {
                    $sarite++;
                    continue;
                }

                try {
                    $this->solicitari->solicita($societate->cif, $tip, [], $userId);
                    $trimise++;
                } catch (SpvException $e) {
                    $erori[] = $societate->cif . ' / ' . $tip . ': ' . $e->getMessage();
                }
            }
        }

        return [
            'trimise' => $trimise,
            'sarite' => $sarite,
            'reinterpretate' => $reinterpretate,
            'erori' => $erori,
        ];
    }

    /** O cerere trimisa azi si inca fara raspuns nu se repeta. */
    protected function existaSolicitareInCurs(string $cif, string $tip): bool
    {
        return SpvSolicitare::where('cif', $cif)
            ->where('tip_document', $tip)
            ->where(function ($query) {
                $query->whereNull('data_afisare')
                    ->orWhere('data_solicitarii', '>=', now()->startOfDay());
            })
            ->exists();
    }

    /** Denumirile cunoscute, pentru afisarea mesajelor si solicitarilor SPV. */
    public static function denumiri(): array
    {
        return AnafSocietate::whereNotNull('denumire')->pluck('denumire', 'cif')->all();
    }
}
