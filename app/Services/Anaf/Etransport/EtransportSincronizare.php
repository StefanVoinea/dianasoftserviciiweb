<?php

namespace App\Services\Anaf\Etransport;

use App\Models\EtransportNotificare;
use App\Services\Anaf\Spv\CertificatService;
use Carbon\Carbon;

/**
 * Preluarea și păstrarea notificărilor e-Transport.
 *
 * ANAF întoarce doar stările finale ale notificărilor valide, plus toate
 * notificările care au avut erori, deci lista se poate reinteroga oricând fără
 * să se dubleze înregistrările.
 */
class EtransportSincronizare
{
    protected $client;
    protected $certificate;

    public function __construct(EtransportClient $client, CertificatService $certificate)
    {
        $this->client = $client;
        $this->certificate = $certificate;
    }

    /**
     * @return array{preluate: int, noi: int, cu_erori: int}
     */
    public function preia(int $zile, string $cif, ?int $userId = null): array
    {
        $raspuns = $this->client->lista($zile, $cif);

        $mesaje = $raspuns['mesaje'] ?? ($raspuns['notificari'] ?? []);

        if (!is_array($mesaje)) {
            $mesaje = [];
        }

        // ANAF raportează lipsa notificărilor tot prin „Errors”; asta nu e o
        // eroare, ci un rezultat gol. Restul erorilor trebuie semnalate.
        $erori = $raspuns['Errors'] ?? [];

        if ($mesaje === [] && $erori !== []) {
            $text = implode(' | ', array_filter(array_map(function ($eroare) {
                return is_array($eroare) ? ($eroare['errorMessage'] ?? null) : (string) $eroare;
            }, $erori)));

            if (stripos($text, 'nu exista mesaje') === false) {
                throw new EtransportException($text ?: 'Răspuns fără notificări de la e-Transport.');
            }
        }

        $noi = 0;
        $cuErori = 0;

        foreach ($mesaje as $mesaj) {
            $notificare = EtransportNotificare::firstOrNew([
                'uit' => $mesaj['uit'] ?? null,
                'tip' => $mesaj['tip'] ?? null,
                'id_incarcare' => $mesaj['id_incarcare'] ?? null,
            ]);

            $noi += $notificare->exists ? 0 : 1;

            $notificare->fill($this->campuri($mesaj, $userId))->save();

            if (($mesaj['stare'] ?? null) === 'ERR') {
                $cuErori++;
            }
        }

        return ['preluate' => count($mesaje), 'noi' => $noi, 'cu_erori' => $cuErori];
    }

    protected function campuri(array $mesaj, ?int $userId): array
    {
        return [
            'stare' => $mesaj['stare'] ?? null,
            'cod_decl' => $mesaj['cod_decl'] ?? null,
            'ref_decl' => $mesaj['ref_decl'] ?? null,
            'post_avarie' => $mesaj['post_avarie'] ?? null,
            'sursa' => $mesaj['sursa'] ?? null,
            'tip_op' => $mesaj['tip_op'] ?? null,
            'data_transp' => $this->data($mesaj['data_transp'] ?? null),
            'data_creare' => $this->data($mesaj['data_creare'] ?? null),
            'data_modif' => $this->data($mesaj['data_modif'] ?? null),
            'pc_tara' => $mesaj['pc_tara'] ?? null,
            'pc_cod' => $mesaj['pc_cod'] ?? null,
            'pc_den' => $mesaj['pc_den'] ?? null,
            'tr_tara' => $mesaj['tr_tara'] ?? null,
            'tr_cod' => $mesaj['tr_cod'] ?? null,
            'tr_den' => $mesaj['tr_den'] ?? null,
            'nr_veh' => $mesaj['nr_veh'] ?? null,
            'nr_rem1' => $mesaj['nr_rem1'] ?? null,
            'nr_rem2' => $mesaj['nr_rem2'] ?? null,
            'nr_linii' => $mesaj['nr_linii'] ?? null,
            'gr_tot_neta' => $mesaj['gr_tot_neta'] ?? null,
            'gr_tot_bruta' => $mesaj['gr_tot_bruta'] ?? null,
            'val_tot' => $mesaj['val_tot'] ?? null,
            'modif_veh' => $mesaj['modif_veh'] ?? null,
            'confirmare' => $mesaj['confirmare'] ?? null,
            'mesaje' => $mesaj['mesaje'] ?? null,
            'certificat_id' => $this->certificate->idCurent(),
            'user_id' => $userId,
        ];
    }

    /** Datele vin în formate diferite; ce nu poate fi interpretat rămâne gol. */
    protected function data($valoare): ?string
    {
        if (empty($valoare)) {
            return null;
        }

        try {
            return Carbon::parse($valoare)->toDateTimeString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
