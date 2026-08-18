<?php

namespace App\Mail;

use App\Models\EtransportDeclaratie;
use App\Models\EtransportNotificare;
use App\Services\Anaf\Etransport\Nomenclatoare;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Codul UIT al unui transport, trimis pe email.
 *
 * Șoferul sau partenerul trebuie să aibă codul la el pe drum; de aici pleacă
 * direct din aplicație, fără copiat prin alte canale. Codul poate veni dintr-o
 * declarație lucrată în aplicație sau dintr-o notificare preluată de la ANAF —
 * emailul arată la fel. Pleacă prin coadă, ca un server de email leneș să nu
 * țină pagina în loc.
 */
class EtransportUitEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $date;

    /**
     * @param array{uit: string, operatiune: ?string, cif: ?string, partener: ?string,
     *     transportator: ?string, vehicul: ?string, dataTransport: ?string, linii: int} $date
     */
    public function __construct(array $date)
    {
        $this->date = $date;
    }

    public static function dinDeclaratie(EtransportDeclaratie $d): self
    {
        return new self([
            'uit' => $d->uit,
            'operatiune' => Nomenclatoare::TIPURI_OPERATIUNE[$d->tip_operatiune] ?? null,
            'cif' => $d->cif_declarant,
            'partener' => $d->partener_denumire,
            'transportator' => $d->transportator_denumire,
            'vehicul' => trim(implode(' + ', array_filter([$d->nr_vehicul, $d->nr_remorca1, $d->nr_remorca2]))),
            'dataTransport' => optional($d->data_transport)->format('d.m.Y'),
            'linii' => count($d->linii ?: []),
        ]);
    }

    public static function dinNotificare(EtransportNotificare $n): self
    {
        return new self([
            'uit' => $n->uit,
            'operatiune' => $n->operatiune,
            'cif' => $n->cod_decl,
            'partener' => $n->pc_den,
            'transportator' => $n->tr_den,
            'vehicul' => trim(implode(' + ', array_filter([$n->nr_veh, $n->nr_rem1, $n->nr_rem2]))),
            'dataTransport' => optional($n->data_transp)->format('d.m.Y'),
            'linii' => (int) $n->nr_linii,
        ]);
    }

    public function build()
    {
        return $this->replyTo('office@dianasoft.ro')
            ->subject('Cod UIT ' . $this->date['uit']
                . (!empty($this->date['vehicul']) ? ' — vehicul ' . $this->date['vehicul'] : ''))
            ->markdown('emails.etransportuit', $this->date);
    }
}
