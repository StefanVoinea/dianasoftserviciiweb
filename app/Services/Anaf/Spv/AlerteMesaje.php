<?php

namespace App\Services\Anaf\Spv;

use App\Mail\AlertaConstatareSpvEmail;
use App\Mail\AlertaMesajSpvEmail;
use App\Models\AlertaMesajSpv;
use App\Models\AnafSocietate;
use App\Models\SpvMesaj;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Trimite instiintarile configurate cand intra un mesaj nou in SPV.
 *
 * Se apeleaza doar pentru mesajele abia inregistrate: la fiecare citire a
 * listei ANAF intoarce si mesajele stiute, iar pe acelea nu are rost sa le mai
 * anunte nimeni a doua oara.
 */
class AlerteMesaje
{
    /** Firmele inrolate fiecarui certificat, tinute minte pe durata cererii. */
    protected $inrolate = [];

    /**
     * @return int cate instiintari au plecat
     */
    public function pentruMesajNou(SpvMesaj $mesaj): int
    {
        // Doar cele care asteapta o hartie; cele legate de o constatare pleaca
        // mai tarziu, dupa ce documentul a fost talcuit.
        $alerte = AlertaMesajSpv::active()->laSosire()->get();

        if ($alerte->isEmpty()) {
            return 0;
        }

        $trimise = 0;

        foreach ($alerte as $alerta) {
            if (!$alerta->seAplica($mesaj, $this->cifuriInrolate($alerta->certificat_id))) {
                continue;
            }

            if ($this->trimite($alerta, $mesaj)) {
                $trimise++;
            }
        }

        return $trimise;
    }

    /**
     * Instiintarile pentru ce s-a citit in document, nu pentru sosirea lui.
     *
     * Aici e toata deosebirea fata de alertele obisnuite: la o descarcare de
     * doua sute cincizeci de firme, o alerta pe „vector fiscal” ar trimite doua
     * sute cincizeci de emailuri, desi numai la cateva s-a schimbat ceva. Una
     * legata de constatare pleaca doar la acelea.
     *
     * @param string $ce una dintre constatarile din AlertaMesajSpv::CONSTATARI
     *
     * @return int cate instiintari au plecat
     */
    public function pentruConstatare(string $ce, ?string $cif, ?int $certificatId, string $vorba): int
    {
        $alerte = AlertaMesajSpv::active()->laConstatare($ce)->get();

        if ($alerte->isEmpty()) {
            return 0;
        }

        $societate = $cif ? AnafSocietate::where('cif', $cif)->first() : null;
        $trimise = 0;

        foreach ($alerte as $alerta) {
            if (!$alerta->seAplicaLaConstatare($cif, $certificatId, $this->cifuriInrolate($alerta->certificat_id))) {
                continue;
            }

            try {
                Mail::to($alerta->email)->send(new AlertaConstatareSpvEmail(
                    $ce,
                    $vorba,
                    $cif,
                    optional($societate)->denumire
                ));
            } catch (\Exception $e) {
                // Ca si la celelalte: o adresa gresita nu opreste preluarea.
                Log::error('Alertă SPV eșuată către ' . $alerta->email . ': ' . $e->getMessage());

                continue;
            }

            $alerta->update([
                'ultima_alerta_la' => now(),
                'trimise' => $alerta->trimise + 1,
            ]);

            $trimise++;
        }

        return $trimise;
    }

    protected function trimite(AlertaMesajSpv $alerta, SpvMesaj $mesaj): bool
    {
        $societate = $mesaj->cif ? AnafSocietate::where('cif', $mesaj->cif)->first() : null;

        try {
            Mail::to($alerta->email)->send(new AlertaMesajSpvEmail(
                $mesaj->tip ?: 'Document SPV',
                $mesaj->cif,
                optional($societate)->denumire,
                $mesaj->detalii,
                $mesaj->data_creare
            ));
        } catch (\Exception $e) {
            /*
             * O adresa gresita nu are voie sa opreasca descarcarea mesajelor:
             * mesajul e deja inregistrat, iar esecul ramane in log.
             */
            Log::error('Alertă SPV eșuată către ' . $alerta->email . ': ' . $e->getMessage());

            return false;
        }

        $alerta->update([
            'ultima_alerta_la' => now(),
            'trimise' => $alerta->trimise + 1,
        ]);

        return true;
    }

    /**
     * Codurile fiscale inrolate unui certificat.
     *
     * @return array<int, string>
     */
    protected function cifuriInrolate($certificatId): array
    {
        if (!$certificatId) {
            return [];
        }

        if (!array_key_exists($certificatId, $this->inrolate)) {
            $this->inrolate[$certificatId] = AnafSocietate::inLucru()
                ->where('certificat_id', $certificatId)
                ->pluck('cif')
                ->map(function ($cif) {
                    return trim((string) $cif);
                })
                ->all();
        }

        return $this->inrolate[$certificatId];
    }
}
