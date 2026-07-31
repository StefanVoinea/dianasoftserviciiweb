<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Instiintarea pe email despre modificarile sesizate la dosarele urmarite.
 */
class ModificariPortalJustEmail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var array modificarile grupate pe dosar */
    public $dosare;

    /** @var int numarul total de modificari */
    public $total;

    public function __construct(array $dosare, int $total)
    {
        $this->dosare = $dosare;
        $this->total = $total;
    }

    public function build()
    {
        $subiect = $this->total === 1
            ? 'Portal Just: o modificare la dosarele urmărite'
            : 'Portal Just: ' . $this->total . ' modificări la dosarele urmărite';

        return $this->replyTo('office@dianasoft.ro')
            ->subject($subiect)
            ->markdown('emails.portaljustmodificari', [
                'dosare' => $this->dosare,
                'total' => $this->total,
                'user' => null,
            ]);
    }
}
