<?php

namespace App\Mail;

use App\Models\EtransportDeclaratie;
use App\Services\Anaf\Etransport\Nomenclatoare;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Codul UIT al unui transport, trimis pe email.
 *
 * Șoferul sau partenerul trebuie să aibă codul la el pe drum; de aici pleacă
 * direct din aplicație, fără copiat prin alte canale. Pleacă prin coadă, ca
 * un server de email leneș să nu țină pagina în loc.
 */
class EtransportUitEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $declaratie;

    public function __construct(EtransportDeclaratie $declaratie)
    {
        $this->declaratie = $declaratie;
    }

    public function build()
    {
        $d = $this->declaratie;

        return $this->replyTo('office@dianasoft.ro')
            ->subject('Cod UIT ' . $d->uit
                . ($d->nr_vehicul ? ' — vehicul ' . $d->nr_vehicul : ''))
            ->markdown('emails.etransportuit', [
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
}
