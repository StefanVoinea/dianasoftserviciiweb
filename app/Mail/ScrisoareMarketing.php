<?php

namespace App\Mail;

use App\Models\MarketingContact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

/**
 * O scrisoare catre o firma din lista de marketing.
 *
 * Textul vine de la om, din fila; aici se potriveste pe fiecare destinatar si i
 * se pune legatura de dezabonare — fara ea, scrisoarea nu pleaca deloc.
 *
 * Legatura merge si in antetul „List-Unsubscribe": asa o arata si programele de
 * posta, cu un buton al lor, iar omul care nu vrea sa mai primeasca nimic nu e
 * nevoit sa caute prin text. Tot de acolo isi iau si serverele mari semnul ca
 * scrisoarea e trimisa cum trebuie.
 */
class ScrisoareMarketing extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $contact;
    public $subiectul;
    public $textul;
    public $legaturaDezabonare;
    public $legaturaDemo;

    public function __construct(MarketingContact $contact, string $subiect, string $text, string $campanie = '')
    {
        $this->contact = $contact;
        $this->subiectul = $subiect;

        $this->legaturaDezabonare = URL::to('/dezabonare/' . $contact->jeton);

        /*
         * Butonul „Solicita demo". Poarta si numele campaniei, ca sa se vada pe
         * urma care scrisoare a prins si care nu.
         */
        $this->legaturaDemo = URL::to('/demo/' . $contact->jeton)
            . ($campanie !== '' ? '?c=' . urlencode($campanie) : '');

        $this->textul = self::potriveste($text, $contact);
    }

    /**
     * Textul, cu locurile goale umplute.
     *
     * Se scriu intre acolade: {nume}, {firma}, {cui}, {judet}. Ce nu se
     * cunoaste se sterge, nu se lasa scris — o scrisoare care spune „Buna ziua,
     * {nume}" e mai rea decat una care nu spune niciun nume.
     */
    public static function potriveste(string $text, MarketingContact $contact): string
    {
        return strtr($text, [
            '{nume}' => $contact->nume_de_potrivit,
            '{firma}' => (string) $contact->denumire,
            '{cui}' => (string) $contact->cui,
            '{judet}' => (string) $contact->judet,
        ]);
    }

    public function build()
    {
        /*
         * Copia catre casa.
         *
         * Ascunsa, ca destinatarul sa nu vada adresa noastra langa a lui — el a
         * primit o scrisoare, nu o lista de trimitere. Din ea se vede in cutia
         * noastra ce a plecat si cum arata la celalalt capat, fara sa deschida
         * cineva evidenta.
         */
        $copia = trim((string) config('marketing.copie_ascunsa'));

        if ($copia !== '') {
            $this->bcc($copia);
        }

        return $this->subject($this->subiectul)
            ->withSwiftMessage(function ($mesaj) {
                /*
                 * Antetul prin care programele de posta isi arata butonul lor de
                 * dezabonare. „One-Click" spune ca e de ajuns o apasare, fara
                 * nicio pagina de confirmare.
                 */
                $mesaj->getHeaders()->addTextHeader('List-Unsubscribe', '<' . $this->legaturaDezabonare . '>');
                $mesaj->getHeaders()->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
            })
            ->view('emails.marketing');
    }
}
