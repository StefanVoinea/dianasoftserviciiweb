<?php

namespace App\Services\Anaf\Declaratii;

use App\Services\Anaf\Spv\CertificatService;
use Illuminate\Support\Facades\Http;

/**
 * Uneste mai multe PDF-uri de declaratii intr-unul singur, pentru tiparire.
 *
 * Lucrul se face de programul local, care are deja biblioteca de PDF folosita
 * si la semnare; aplicatia nu are nevoie de o biblioteca in plus.
 *
 * Documentul rezultat NU mai poarta semnaturile digitale: ele sunt legate de
 * octetii fiecarui fisier in parte si se pierd la copierea paginilor. Este un
 * exemplar de tiparit, nu unul de depus — pentru ANAF raman valabile fisierele
 * semnate, luate unul cate unul.
 */
class ConcatenareService
{
    protected $config;
    protected $certificate;

    public function __construct(array $config, CertificatService $certificate)
    {
        $this->config = $config;
        $this->certificate = $certificate;
    }

    /**
     * Uneste documentele si le trimite la imprimanta de langa om.
     *
     * Hartia iese pe calculatorul unde e imprimanta, nu pe server, deci
     * documentul unit nu se mai intoarce la aplicatie.
     *
     * @param array<int, string> $caiPdf    caile complete ale fisierelor de unit
     * @param array<int, string> $filigrane textul din filigran, in aceeasi ordine
     *
     * @return array{imprimanta: string, detalii: ?string}
     */
    public function tipareste(array $caiPdf, array $filigrane, ?string $imprimanta): array
    {
        $raspuns = $this->trimite($caiPdf, $filigrane, ['imprimanta' => (string) $imprimanta, 'tipareste' => '1']);

        $payload = json_decode($raspuns->body(), true);

        if (!is_array($payload) || empty($payload['tiparit'])) {
            throw new DeclaratieException(
                'Tipărirea a eșuat: ' . ($payload['detalii'] ?? $payload['eroare'] ?? 'răspuns neașteptat de la programul local.')
            );
        }

        return [
            'imprimanta' => $payload['imprimanta'] ?? 'imprimanta implicită',
            'detalii' => $payload['detalii'] ?? null,
        ];
    }

    /**
     * @param array<int, string> $caiPdf     caile complete ale fisierelor de unit
     * @param array<int, string> $filigrane  textul scris in filigran pe paginile
     *                                       fiecarui fisier, in aceeasi ordine
     *
     * @return string continutul PDF-ului unit
     */
    public function uneste(array $caiPdf, array $filigrane = []): string
    {
        return $this->trimite($caiPdf, $filigrane)->body();
    }

    /** Partea comuna: trimite fisierele programului local si verifica raspunsul. */
    protected function trimite(array $caiPdf, array $filigrane, array $formularSuplimentar = [])
    {
        $existente = [];
        $texte = [];

        foreach (array_values($caiPdf) as $pozitie => $cale) {
            if (!is_file($cale)) {
                continue;
            }

            $existente[] = $cale;
            $texte[] = $filigrane[$pozitie] ?? '';
        }

        if ($existente === []) {
            throw new DeclaratieException('Niciunul dintre fișierele de unit nu a fost găsit pe disc.');
        }

        $bridge = $this->certificate->bridge();

        $cerere = Http::withToken($bridge['token'])->timeout($this->config['timeout']);

        foreach ($existente as $index => $cale) {
            $cerere = $cerere->attach('fisiere[' . $index . ']', file_get_contents($cale), basename($cale));
        }

        $formular = [];

        foreach ($texte as $index => $text) {
            $formular['watermark[' . $index . ']'] = (string) $text;
        }

        $raspuns = $cerere->post(
            rtrim($bridge['url'], '/') . '/concateneaza',
            array_merge($formular, $formularSuplimentar)
        );

        if ($raspuns->failed()) {
            $payload = json_decode($raspuns->body(), true);

            throw new DeclaratieException(
                'Unirea documentelor a eșuat: '
                . ($payload['detalii'] ?? $payload['eroare'] ?? 'HTTP ' . $raspuns->status())
            );
        }

        return $raspuns;
    }
}
