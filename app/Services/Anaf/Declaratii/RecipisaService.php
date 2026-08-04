<?php

namespace App\Services\Anaf\Declaratii;

use App\Models\AnafDeclaratie;
use App\Services\Anaf\Arhiva\ArhivaService;
use App\Services\Anaf\Spv\SpvClient;
use App\Services\Anaf\Spv\SpvException;
use App\Services\Anaf\Spv\SpvStorage;
use Illuminate\Support\Facades\Http;

/**
 * Preluarea recipiselor pentru declaratiile depuse. Sursa principala este SPV
 * (mesaje de tip RECIPISA, potrivite dupa indicele de incarcare); cand SPV nu
 * are inca mesajul, starea se interogheaza public pe StareD112.
 */
class RecipisaService
{
    protected $config;
    protected $spvClient;
    protected $spvStorage;

    /** Arhiva de pe calculatorul clientului; lipseste doar in teste. */
    protected $arhiva;

    public function __construct(
        array $config,
        SpvClient $spvClient,
        SpvStorage $spvStorage,
        ?ArhivaService $arhiva = null
    ) {
        $this->config = $config;
        $this->spvClient = $spvClient;
        $this->spvStorage = $spvStorage;
        $this->arhiva = $arhiva;
    }

    /**
     * Verifica toate declaratiile care asteapta recipisa.
     *
     * @return array{verificate: int, descarcate: int, erori: array}
     */
    public function verificaToate(int $zile = 60): array
    {
        $declaratii = AnafDeclaratie::asteaptaRecipisa()->get();

        if ($declaratii->isEmpty()) {
            return ['verificate' => 0, 'descarcate' => 0, 'descarcate_id' => [], 'erori' => []];
        }

        $mesaje = [];
        $erori = [];

        try {
            $lista = $this->spvClient->listaMesaje($zile);
            $mesaje = isset($lista['mesaje']) && is_array($lista['mesaje']) ? $lista['mesaje'] : [];
        } catch (SpvException $e) {
            // SPV indisponibil sau fara mesaje — continuam doar cu StareD112.
            $erori[] = 'SPV: ' . $e->getMessage();
        }

        // Se retin si declaratiile a caror recipisa tocmai a venit: din ele se
        // poate face pe loc un singur fisier de tiparit.
        $descarcate = [];

        foreach ($declaratii as $declaratie) {
            try {
                if ($this->verificaDeclaratie($declaratie, $mesaje)) {
                    $descarcate[] = $declaratie->id;
                }
            } catch (\Exception $e) {
                $erori[] = $declaratie->index_recipisa . ': ' . $e->getMessage();
            }
        }

        return [
            'verificate' => $declaratii->count(),
            'descarcate' => count($descarcate),
            'descarcate_id' => $descarcate,
            'erori' => $erori,
        ];
    }

    public function verificaDeclaratie(AnafDeclaratie $declaratie, array $mesaje = []): bool
    {
        $mesaj = $this->potrivesteMesaj($declaratie, $mesaje);

        if ($mesaj !== null) {
            return $this->preiaDinSpv($declaratie, $mesaj);
        }

        $this->preiaStareaPublica($declaratie);

        return false;
    }

    /** Mesajul SPV de tip RECIPISA al carui text contine indicele de incarcare. */
    protected function potrivesteMesaj(AnafDeclaratie $declaratie, array $mesaje): ?array
    {
        if (!$declaratie->index_recipisa) {
            return null;
        }

        foreach ($mesaje as $mesaj) {
            $tip = strtoupper($mesaj['tip'] ?? '');
            $detalii = $mesaj['detalii'] ?? '';

            if ($tip === 'RECIPISA' && strpos($detalii, $declaratie->index_recipisa) !== false) {
                return $mesaj;
            }
        }

        return null;
    }

    protected function preiaDinSpv(AnafDeclaratie $declaratie, array $mesaj): bool
    {
        $inregistrat = $this->spvStorage->saveMessage($mesaj, $mesaj['cif'] ?? $declaratie->cui);

        /*
         * Recipisa merge de la ANAF drept in arhiva clientului — in dosarul SPV
         * al firmei si, pe deasupra, cu o copie langa declaratia la care
         * raspunde. Incoace vine doar textul din ea, atat cat sa se stie
         * verdictul ANAF.
         */
        $adus = $this->spvStorage->aduce($inregistrat, true, 'recipise');

        $stare = $adus['text'] !== null
            ? $this->verdictul($adus['text'], $declaratie->cui)
            : 'In prelucrare';

        $declaratie->update([
            'cale_recipisa' => $adus['pe_server'],
            'stare_declaratie' => mb_substr($stare, 0, 1000),
            'data_recipisa' => now(),
            'pas' => 'finalizat',
        ]);

        return true;
    }

    /**
     * Starea publica de pe StareD112 (fara certificat). Se cauta randul cu
     * indicele de incarcare in tabelul HTML returnat.
     */
    protected function preiaStareaPublica(AnafDeclaratie $declaratie): void
    {
        try {
            $raspuns = Http::asForm()
                ->timeout($this->config['timeout'])
                ->post($this->config['url_stare'], [
                    'ghis' => 'N',
                    'id' => $declaratie->index_recipisa,
                    'cui' => $declaratie->cui,
                ]);

            $html = $raspuns->body();
        } catch (\Exception $e) {
            $declaratie->update(['stare_declaratie' => 'In prelucrare']);

            return;
        }

        if (strpos($html, 'Fisierul depus nu este un document valid') !== false) {
            $declaratie->update(['stare_declaratie' => 'Fisierul depus nu este un document valid']);

            return;
        }

        if (strpos($html, '<td>' . $declaratie->index_recipisa . '</td>') !== false) {
            $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
            $declaratie->update(['stare_declaratie' => mb_substr($text, 0, 1000)]);

            return;
        }

        $declaratie->update(['stare_declaratie' => 'In prelucrare']);
    }

    /**
     * Verdictul ANAF din recipisa: textul de dupa prima aparitie a CUI-ului.
     *
     * Lucreaza pe textul documentului, nu pe fisierul lui: recipisa e citita pe
     * calculatorul clientului, acolo unde ramane, iar incoace vine doar ce scrie
     * in ea.
     */
    public function verdictul(string $text, string $cui): string
    {
        $pozitie = strpos($text, $cui);

        if ($pozitie === false) {
            return trim(preg_replace('/\s+/', ' ', $text)) ?: 'In prelucrare';
        }

        $verdict = substr($text, $pozitie + strlen($cui));

        return trim(preg_replace('/\s+/', ' ', $verdict)) ?: 'In prelucrare';
    }

    /** Clasificarea verdictului, ca in aplicatia desktop. */
    public static function clasifica(?string $stare): string
    {
        if ($stare === null || trim($stare) === '') {
            return 'in_asteptare';
        }

        if (stripos($stare, 'In prelucrare') !== false) {
            return 'in_prelucrare';
        }

        if (stripos($stare, 'are erori de validare') !== false) {
            return 'invalid';
        }

        if (mb_stripos($stare, 'ATENȚIONĂRI') !== false || stripos($stare, 'ATENTIONARI') !== false) {
            return 'valid_cu_atentionari';
        }

        if (stripos($stare, 'Documentul este valid') !== false
            || (stripos($stare, 'Nu exist') !== false && stripos($stare, 'erori de validare') !== false)) {
            return 'valid';
        }

        return 'necunoscut';
    }
}
