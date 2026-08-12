<?php

namespace App\Mail;

use App\Models\AlertaMesajSpv;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Instiintarea pentru ce s-a citit in document, nu pentru sosirea lui.
 *
 * Pleaca numai la firmele la care s-a constatat ceva: vectorul fiscal modificat
 * fata de ultima citire, sau restante in situatia sintetica. Subiectul spune
 * despre ce e vorba si la cine, ca sa se vada din lista de emailuri fara sa fie
 * deschis.
 */
class AlertaConstatareSpvEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $constatare;
    public $vorba;
    public $cif;
    public $denumire;

    public function __construct(string $constatare, string $vorba, ?string $cif, ?string $denumire)
    {
        $this->constatare = $constatare;
        $this->vorba = $vorba;
        $this->cif = $cif;
        $this->denumire = $denumire;
    }

    public function build()
    {
        $firma = $this->denumire ?: ($this->cif ?: 'o firmă înrolată');

        return $this->replyTo('office@dianasoft.ro')
            ->subject($this->titlu() . ': ' . $firma)
            ->markdown('emails.alertaconstatarespv', [
                'constatare' => $this->constatare,
                'titlu' => $this->titlu(),
                'vorba' => $this->vorba,
                'cif' => $this->cif,
                'denumire' => $this->denumire,
                'indemn' => $this->indemn(),
            ]);
    }

    /** Ce sa scrie in subiect, pe scurt si limpede. */
    protected function titlu(): string
    {
        return $this->constatare === AlertaMesajSpv::CAND_VECTOR_MODIFICAT
            ? 'SPV: vector fiscal modificat'
            : 'SPV: obligații de plată restante';
    }

    /** Ce are omul de facut mai departe. */
    protected function indemn(): string
    {
        return $this->constatare === AlertaMesajSpv::CAND_VECTOR_MODIFICAT
            ? 'Verificați ce obligații fiscale s-au schimbat: o periodicitate nouă '
                . 'sau o obligație adăugată schimbă și declarațiile de depus.'
            : 'Verificați situația sintetică pentru sumele datorate și termenele lor.';
    }
}
