<?php

namespace App\Services\Anaf\Spv;

use App\Models\AnafSocietate;
use App\Models\SpvSolicitare;
use App\Models\VectorFiscal;
use Illuminate\Support\Facades\Storage;


/**
 * Solicitarea de documente din SPV si preluarea raspunsurilor: cererea se trimite
 * prin webserviciul ANAF, iar raspunsul apare ca mesaj in lista SPV, de unde e
 * descarcat si interpretat (vector fiscal, situatie sintetica, date identificare).
 */
class SolicitareService
{
    protected $client;
    protected $storage;
    protected $parser;
    protected $certificate;

    public function __construct(
        SpvClient $client,
        SpvStorage $storage,
        VectorFiscalParser $parser,
        CertificatService $certificate
    ) {
        $this->client = $client;
        $this->storage = $storage;
        $this->parser = $parser;
        $this->certificate = $certificate;
    }

    public function solicita(string $cui, string $tipDocument, array $optiuni = [], ?int $userId = null): SpvSolicitare
    {
        $raspuns = $this->client->cerere($tipDocument, $cui, $optiuni);

        return SpvSolicitare::create([
            'cif' => $cui,
            'den_firma' => optional(AnafSocietate::where('cif', $cui)->first())->denumire
                ?: optional(VectorFiscal::where('cui', $cui)->first())->denumire,
            'tip_document' => $tipDocument,
            'an' => $optiuni['an'] ?? null,
            'luna' => $optiuni['luna'] ?? null,
            'motiv' => $optiuni['motiv'] ?? null,
            'numar_inregistrare' => $optiuni['numar_inregistrare'] ?? null,
            'cui_pui' => $optiuni['cui_pui'] ?? null,
            'id_solicitare' => $raspuns['id_solicitare'] ?? ($raspuns['idSolicitare'] ?? null),
            'data_solicitarii' => now(),
            'detalii' => $raspuns['titlu'] ?? null,
            'stare' => 'trimisa',
            'certificat_id' => $this->certificate->idCurent(),
            'user_id' => $userId,
        ]);
    }

    /**
     * Cauta in mesajele SPV raspunsurile la solicitarile in asteptare, le descarca
     * si le interpreteaza.
     *
     * Cu o limita data se preiau cel mult atatea raspunsuri, iar restul raman
     * pentru chemarea urmatoare: fiecare descarcare are pauza ei impusa de ANAF,
     * si o suta de raspunsuri nu incap intr-o singura cerere web.
     *
     * @return array{verificate: int, preluate: int, ramase: int, erori: array}
     */
    public function preiaRaspunsuri(int $zile = 60, ?int $limita = null): array
    {
        $solicitari = SpvSolicitare::inAsteptare()->get();

        if ($solicitari->isEmpty()) {
            return ['verificate' => 0, 'preluate' => 0, 'ramase' => 0, 'erori' => []];
        }

        $erori = [];

        try {
            $lista = $this->client->listaMesaje($zile);
            $mesaje = isset($lista['mesaje']) && is_array($lista['mesaje']) ? $lista['mesaje'] : [];
        } catch (SpvException $e) {
            return [
                'verificate' => $solicitari->count(),
                'preluate' => 0,
                'ramase' => $solicitari->count(),
                'erori' => ['SPV: ' . $e->getMessage()],
            ];
        }

        $preluate = 0;
        $incercate = 0;

        foreach ($solicitari as $solicitare) {
            $mesaj = $this->potrivesteMesaj($solicitare, $mesaje);

            if ($mesaj === null) {
                continue;
            }

            $incercate++;

            try {
                $this->preia($solicitare, $mesaj);
                $preluate++;
            } catch (\Exception $e) {
                $erori[] = $solicitare->cif . ' / ' . $solicitare->tip_document . ': ' . $e->getMessage();
            }

            /*
             * Se numara incercarile, nu izbanzile: si o descarcare picata a
             * costat drumul pana la ANAF. Altfel, cand raspunsurile nu vin, lotul
             * s-ar intinde peste toata lista si am ajunge iar la o cerere web
             * care tine minute.
             */
            if ($limita !== null && $incercate >= $limita) {
                break;
            }
        }

        return [
            'verificate' => $solicitari->count(),
            'preluate' => $preluate,
            // Cate mai asteapta raspuns dupa lotul acesta
            'ramase' => SpvSolicitare::inAsteptare()->count(),
            'erori' => $erori,
        ];
    }

    /**
     * Reinterpreteaza documentele deja descarcate — utila cand registrul de
     * societati e populat dupa ce raspunsurile au fost preluate.
     *
     * @return int numarul de documente reinterpretate
     */
    public function reinterpreteaza(): int
    {
        $solicitari = SpvSolicitare::whereNotNull('cale_fisier')->get();
        $procesate = 0;

        foreach ($solicitari as $solicitare) {
            if (!Storage::exists($solicitare->cale_fisier)) {
                continue;
            }

            $obs = $this->interpreteaza($solicitare, Storage::path($solicitare->cale_fisier));

            if ($obs !== null) {
                $solicitare->update(['obs' => $obs]);
            }

            $procesate++;
        }

        return $procesate;
    }

    protected function potrivesteMesaj(SpvSolicitare $solicitare, array $mesaje): ?array
    {
        foreach ($mesaje as $mesaj) {
            $idSolicitare = (string) ($mesaj['id_solicitare'] ?? '');

            if ($solicitare->id_solicitare && $idSolicitare === (string) $solicitare->id_solicitare) {
                return $mesaj;
            }
        }

        return null;
    }

    protected function preia(SpvSolicitare $solicitare, array $mesaj): void
    {
        $fisier = $this->client->descarcare($mesaj['id']);
        $salvat = $this->storage->saveFile($fisier, 'solicitari');
        $this->storage->saveMessage($mesaj, $solicitare->cif);

        $obs = $this->interpreteaza($solicitare, Storage::path($salvat['path']));

        $solicitare->update([
            'mesaj_id' => $mesaj['id'],
            'cale_fisier' => $salvat['path'],
            'data_afisare' => now(),
            'detalii' => $mesaj['detalii'] ?? $solicitare->detalii,
            'obs' => $obs,
            'stare' => 'preluata',
        ]);
    }

    /** Interpretarea documentului descarcat, in functie de tip. */
    protected function interpreteaza(SpvSolicitare $solicitare, string $calePdf): ?string
    {
        $tip = mb_strtoupper($solicitare->tip_document);

        try {
            if (strpos($tip, 'VECTOR FISCAL') !== false) {
                $rezultat = $this->parser->citesteVectorFiscal($calePdf, $solicitare->cif);
                $numar = count($rezultat['randuri']);

                // Antetul vectorului contine denumirea oficiala a contribuabilului.
                $this->actualizeazaSocietatea($solicitare->cif, [
                    'denumire' => $this->parser->citesteDenumire($calePdf, $solicitare->cif),
                    'sursa' => 'vector',
                    'campuri' => ['vector_la' => now()],
                ]);

                if ($numar === 0) {
                    return 'Vectorul fiscal nu a putut fi interpretat (0 obligații citite).';
                }

                if ($rezultat['prima_preluare']) {
                    return 'Vector fiscal preluat: ' . $numar . ' obligații.';
                }

                return $rezultat['modificat']
                    ? 'ATENȚIE! VECTOR FISCAL MODIFICAT!'
                    : 'Nu sunt modificări în vectorul fiscal.';
            }

            if (strpos($tip, 'SITUATIE SINTETICA') !== false || strpos($tip, 'SITUAȚIE SINTETICĂ') !== false) {
                return $this->parser->areObligatiiRestante($calePdf)
                    ? 'ATENȚIE! SUNT OBLIGAȚII DE PLATĂ RESTANTE'
                    : 'Nu sunt obligații de plată restante.';
            }

            if (strpos($tip, 'DATE IDENTIFICARE') !== false) {
                $denumire = $this->parser->citesteDenumire($calePdf, $solicitare->cif);

                $this->actualizeazaSocietatea($solicitare->cif, [
                    'denumire' => $denumire,
                    'sursa' => 'date_identificare',
                    'campuri' => [
                        'date_identificare' => mb_substr($this->parser->textDocument($calePdf), 0, 5000),
                        'date_identificare_la' => now(),
                    ],
                ]);

                return $denumire
                    ? 'Date identificare preluate: ' . $denumire
                    : 'Date identificare preluate (denumirea nu a putut fi extrasă).';
            }
        } catch (\Exception $e) {
            return 'Documentul nu a putut fi interpretat: ' . $e->getMessage();
        }

        return null;
    }

    /**
     * Completeaza registrul de societati din documentul primit. Denumirea se
     * suprascrie doar dintr-o sursa cel putin la fel de sigura (vezi AnafSocietate).
     */
    protected function actualizeazaSocietatea(string $cif, array $date): void
    {
        $societate = AnafSocietate::firstOrNew(['cif' => $cif]);

        if (!$societate->exists) {
            $societate->fill(['tip' => AnafSocietate::tipDupaCif($cif), 'activ' => true]);
        }

        $societate->fill($date['campuri'] ?? [])->save();
        $societate->seteazaDenumire($date['denumire'] ?? null, $date['sursa']);

        // Denumirea e utila si in vectorul fiscal declarat manual.
        if ($societate->denumire) {
            VectorFiscal::updateOrCreate(['cui' => $cif], ['denumire' => $societate->denumire]);
        }
    }
}
