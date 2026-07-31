<?php

namespace App\Services\Just;

use App\Models\PortalJustDosar;
use App\Models\PortalJustModificare;
use App\Models\PortalJustMonitorizare;

/**
 * Compară starea dosarelor urmărite cu ce întoarce acum Portal Just și
 * înregistrează diferențele.
 *
 * Prima verificare a unei monitorizări doar fixează punctul de referință: fără
 * asta, utilizatorul ar primi pe email tot istoricul dosarului ca „noutate”.
 */
class MonitorizarePortalJust
{
    protected $client;

    public function __construct(PortalJustClient $client)
    {
        $this->client = $client;
    }

    /**
     * Verifică o monitorizare și întoarce modificările nou apărute.
     *
     * @return PortalJustModificare[]
     */
    public function verifica(PortalJustMonitorizare $monitorizare): array
    {
        $primaVerificare = $monitorizare->ultima_verificare === null;

        try {
            $dosare = $this->client->cautaDosare($monitorizare->criterii());
        } catch (PortalJustException $e) {
            $monitorizare->update([
                'ultima_verificare' => now(),
                'ultima_eroare' => mb_substr($e->getMessage(), 0, 255),
            ]);

            throw $e;
        }

        $cunoscute = $monitorizare->dosare()->get()->keyBy('numar');
        $modificari = [];

        foreach ($dosare as $dosar) {
            $numar = $dosar['numar'] ?: '(fără număr)';
            $stare = $this->stare($dosar);
            $amprenta = $this->amprenta($stare);

            $cunoscut = $cunoscute->get($numar);

            if ($cunoscut === null) {
                if (!$primaVerificare) {
                    $modificari[] = $this->inregistreaza($monitorizare, $dosar, 'dosar_nou', sprintf(
                        'Dosar nou: %s la %s%s',
                        $numar,
                        $dosar['institutie_eticheta'],
                        $dosar['obiect'] ? ', obiect: ' . $dosar['obiect'] : ''
                    ), $stare);
                }

                PortalJustDosar::create([
                    'monitorizare_id' => $monitorizare->id,
                    'numar' => $numar,
                    'institutie' => $dosar['institutie'],
                    'amprenta' => $amprenta,
                    'stare' => $stare,
                    'vazut_la' => now(),
                ]);

                continue;
            }

            if ($cunoscut->amprenta === $amprenta) {
                $cunoscut->update(['vazut_la' => now()]);

                continue;
            }

            foreach ($this->diferente((array) $cunoscut->stare, $stare) as $diferenta) {
                $modificari[] = $this->inregistreaza(
                    $monitorizare,
                    $dosar,
                    $diferenta['tip'],
                    $diferenta['descriere'],
                    $diferenta['detalii'] ?? null
                );
            }

            $cunoscut->update([
                'amprenta' => $amprenta,
                'stare' => $stare,
                'institutie' => $dosar['institutie'],
                'vazut_la' => now(),
            ]);
        }

        $monitorizare->update([
            'ultima_verificare' => now(),
            'ultima_eroare' => null,
            'dosare_urmarite' => count($dosare),
            'ultima_modificare' => $modificari ? now() : $monitorizare->ultima_modificare,
        ]);

        return $modificari;
    }

    /** Câmpurile care contează la comparare, în forma în care se păstrează. */
    public function stare(array $dosar): array
    {
        return [
            'numar' => $dosar['numar'],
            'institutie' => $dosar['institutie_eticheta'],
            'departament' => $dosar['departament'],
            'obiect' => $dosar['obiect'],
            'categorie' => $dosar['categorie'],
            'stadiu' => $dosar['stadiu'],
            'data_modificare' => $dosar['data_modificare'],
            'parti' => $dosar['parti'],
            'termene' => $dosar['sedinte'],
            'cai_atac' => $dosar['cai_atac'],
        ];
    }

    public function amprenta(array $stare): string
    {
        // Data ultimei modificări se schimbă și fără efect vizibil în dosar;
        // amprenta se face pe conținut, ca să nu anunțăm modificări goale.
        unset($stare['data_modificare']);

        return sha1(json_encode($stare));
    }

    /**
     * Diferențele dintre două stări, în ordinea în care interesează cititorul.
     *
     * @return array<int, array{tip:string, descriere:string, detalii?:array}>
     */
    public function diferente(array $vechi, array $nou): array
    {
        $diferente = [];

        foreach ($this->termeneNoi($vechi, $nou) as $diferenta) {
            $diferente[] = $diferenta;
        }

        if (($vechi['stadiu'] ?? null) !== ($nou['stadiu'] ?? null)) {
            $diferente[] = [
                'tip' => 'stadiu',
                'descriere' => sprintf(
                    'Stadiul procesual s-a schimbat din „%s” în „%s”',
                    $vechi['stadiu'] ?: 'nedefinit',
                    $nou['stadiu'] ?: 'nedefinit'
                ),
            ];
        }

        foreach ($this->caiAtacNoi($vechi, $nou) as $diferenta) {
            $diferente[] = $diferenta;
        }

        foreach ($this->partiNoi($vechi, $nou) as $diferenta) {
            $diferente[] = $diferenta;
        }

        if (($vechi['obiect'] ?? null) !== ($nou['obiect'] ?? null)) {
            $diferente[] = [
                'tip' => 'obiect',
                'descriere' => sprintf(
                    'Obiectul dosarului s-a schimbat din „%s” în „%s”',
                    $vechi['obiect'] ?: 'nedefinit',
                    $nou['obiect'] ?: 'nedefinit'
                ),
            ];
        }

        // Ceva s-a schimbat, dar nu într-un câmp pe care îl explicităm.
        if ($diferente === []) {
            $diferente[] = [
                'tip' => 'actualizare',
                'descriere' => 'Dosarul a fost actualizat la instanță.',
            ];
        }

        return $diferente;
    }

    protected function termeneNoi(array $vechi, array $nou): array
    {
        $vechiDupaCheie = [];

        foreach ($vechi['termene'] ?? [] as $termen) {
            $vechiDupaCheie[$this->cheieTermen($termen)] = $termen;
        }

        $diferente = [];

        foreach ($nou['termene'] ?? [] as $termen) {
            $cheie = $this->cheieTermen($termen);
            $anterior = $vechiDupaCheie[$cheie] ?? null;

            if ($anterior === null) {
                $diferente[] = [
                    'tip' => $termen['solutie'] ? 'solutie' : 'termen_nou',
                    'descriere' => $this->descriereTermen($termen),
                    'detalii' => $termen,
                ];

                continue;
            }

            // Soluția se completează de regulă după ce termenul a fost deja anunțat.
            if (($anterior['solutie'] ?? null) !== ($termen['solutie'] ?? null)
                || ($anterior['solutie_sumar'] ?? null) !== ($termen['solutie_sumar'] ?? null)) {
                $diferente[] = [
                    'tip' => 'solutie',
                    'descriere' => sprintf(
                        'Soluție la termenul din %s: %s%s',
                        $termen['data'],
                        $termen['solutie'] ?: 'fără soluție',
                        $termen['numar_document'] ? ' (' . $termen['numar_document'] . ')' : ''
                    ),
                    'detalii' => $termen,
                ];
            }
        }

        return $diferente;
    }

    protected function descriereTermen(array $termen): string
    {
        $descriere = 'Termen nou: ' . $termen['data'];

        if (!empty($termen['ora'])) {
            $descriere .= ', ora ' . $termen['ora'];
        }

        if (!empty($termen['complet'])) {
            $descriere .= ', complet ' . $termen['complet'];
        }

        if (!empty($termen['solutie'])) {
            $descriere .= ' — soluție: ' . $termen['solutie'];
        }

        return $descriere;
    }

    protected function cheieTermen(array $termen): string
    {
        return implode('|', [
            $termen['data'] ?? '',
            $termen['ora'] ?? '',
            $termen['complet'] ?? '',
        ]);
    }

    protected function caiAtacNoi(array $vechi, array $nou): array
    {
        $cunoscute = [];

        foreach ($vechi['cai_atac'] ?? [] as $cale) {
            $cunoscute[] = ($cale['tip'] ?? '') . '|' . ($cale['data_declarare'] ?? '') . '|' . ($cale['parte_declaratoare'] ?? '');
        }

        $diferente = [];

        foreach ($nou['cai_atac'] ?? [] as $cale) {
            $cheie = ($cale['tip'] ?? '') . '|' . ($cale['data_declarare'] ?? '') . '|' . ($cale['parte_declaratoare'] ?? '');

            if (in_array($cheie, $cunoscute, true)) {
                continue;
            }

            $diferente[] = [
                'tip' => 'cale_atac',
                'descriere' => sprintf(
                    'Cale de atac nouă: %s, declarată de %s la %s',
                    $cale['tip'] ?: 'nespecificată',
                    $cale['parte_declaratoare'] ?: 'parte nespecificată',
                    $cale['data_declarare'] ?: 'dată nespecificată'
                ),
                'detalii' => $cale,
            ];
        }

        return $diferente;
    }

    protected function partiNoi(array $vechi, array $nou): array
    {
        $cunoscute = [];

        foreach ($vechi['parti'] ?? [] as $parte) {
            $cunoscute[] = ($parte['nume'] ?? '') . '|' . ($parte['calitate'] ?? '');
        }

        $diferente = [];

        foreach ($nou['parti'] ?? [] as $parte) {
            $cheie = ($parte['nume'] ?? '') . '|' . ($parte['calitate'] ?? '');

            if (in_array($cheie, $cunoscute, true)) {
                continue;
            }

            $diferente[] = [
                'tip' => 'parte',
                'descriere' => sprintf(
                    'Parte nouă în dosar: %s (%s)',
                    $parte['nume'] ?: 'nume nespecificat',
                    $parte['calitate'] ?: 'calitate nespecificată'
                ),
                'detalii' => $parte,
            ];
        }

        return $diferente;
    }

    protected function inregistreaza(
        PortalJustMonitorizare $monitorizare,
        array $dosar,
        string $tip,
        string $descriere,
        ?array $detalii = null
    ): PortalJustModificare {
        return PortalJustModificare::create([
            'company_id' => $monitorizare->company_id,
            'monitorizare_id' => $monitorizare->id,
            'dosar_numar' => $dosar['numar'],
            'institutie' => $dosar['institutie_eticheta'],
            'tip' => $tip,
            'descriere' => $descriere,
            'detalii' => $detalii,
        ]);
    }
}
