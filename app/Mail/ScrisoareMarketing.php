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
 * se pun cele doua legaturi — cererea de demonstratie si pagina de prezentare —
 * plus dezabonarea, fara de care scrisoarea nu pleaca deloc.
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

    /** Antetul prin care scrisoarea isi spune numarul din evidenta. */
    public const ANTETUL = 'X-Trimitere';

    public $contact;
    public $subiectul;
    public $textul;
    public $legaturaDezabonare;
    public $legaturaDemo;

    /** Pagina de prezentare, pentru cine vrea sa se uite intai singur. */
    public $legaturaPrezentare;

    /** Cine scrie: numele si adresa casei, aceleasi si in antet, si in subsol. */
    public $expeditorNume;
    public $expeditorAdresa;

    /**
     * Numarul randului din evidenta, purtat pana la plecare.
     *
     * Scrisoarea pleaca mai tarziu, din coada, si de-acolo nu se mai stie carui
     * rand ii apartine. Numarul merge cu ea, intr-un antet al ei, iar cand
     * serverul de email o primeste cu adevarat, randul se insemneaza.
     *
     * @var int|null
     */
    public $trimitereaId;

    public function __construct(
        MarketingContact $contact,
        string $subiect,
        string $text,
        string $campanie = '',
        ?int $trimitereaId = null
    ) {
        $this->trimitereaId = $trimitereaId;
        $this->contact = $contact;
        $this->subiectul = $subiect;

        $this->legaturaDezabonare = URL::to('/dezabonare/' . $contact->jeton);

        /*
         * Butonul „Solicita demo". Poarta si numele campaniei, ca sa se vada pe
         * urma care scrisoare a prins si care nu.
         */
        $this->legaturaDemo = URL::to('/demo/' . $contact->jeton)
            . ($campanie !== '' ? '?c=' . urlencode($campanie) : '');

        $this->legaturaPrezentare = (string) config('prezentare.site');

        $expeditor = config('marketing.expeditor', []);
        $this->expeditorAdresa = trim((string) ($expeditor['adresa'] ?? '')) ?: config('mail.from.address');
        $this->expeditorNume = trim((string) ($expeditor['nume'] ?? '')) ?: config('mail.from.name');

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

        /*
         * Cine scrie se spune aici, nu se lasa pe seama expeditorului obisnuit
         * al aplicatiei.
         *
         * MAIL_FROM_NAME e bun pentru o instiintare tehnica si poate fi orice
         * pe serverul unde se intampla sa ruleze aplicatia — numele omului care
         * a pus-o acolo, de pilda. Intr-o scrisoare catre cineva care nu ne
         * cunoaste, in cutia lui trebuie sa scrie numele casei.
         */
        if ($this->expeditorAdresa) {
            $this->from($this->expeditorAdresa, $this->expeditorNume ?: null);
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

                // Numarul randului din evidenta, ca sa se stie ce a plecat cu adevarat.
                if ($this->trimitereaId) {
                    $mesaj->getHeaders()->addTextHeader(self::ANTETUL, (string) $this->trimitereaId);
                }
            })
            ->view('emails.marketing');
    }
}
