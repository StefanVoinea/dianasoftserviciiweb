<?php

namespace App\Mail;

use App\Models\MarketingContact;
use Illuminate\Mail\Mailable;

/**
 * Vestea către noi că o firmă din lista de marketing a cerut o demonstrație,
 * din formularul deschis cu butonul din scrisoare.
 *
 * Fără ea, cererea stătea doar în fila Marketing, iar acolo nu se uită nimeni în
 * fiecare zi; omul aștepta un telefon care nu venea.
 */
class CerereDemoDinScrisoare extends Mailable
{
    /** @var MarketingContact */
    public $contact;

    public function __construct(MarketingContact $contact)
    {
        $this->contact = $contact;
    }

    public function build()
    {
        $contact = $this->contact;

        $randuri = [
            'Cerere de demonstrație SPV Curier, din scrisoarea de marketing',
            '',
            'Firma: ' . $contact->denumire,
            'E-mail: ' . $contact->email,
            'Persoana de contact: ' . (trim((string) $contact->demo_persoana) ?: '—'),
            'Telefon: ' . (trim((string) $contact->demo_telefon) ?: trim((string) $contact->telefon) ?: '—'),
            'Ce o interesează: ' . (trim((string) $contact->demo_mesaj) ?: '—'),
            'Campania: ' . (trim((string) $contact->demo_campanie) ?: '—'),
            '',
            'Cerută la ' . optional($contact->demo_cerut_la)->format('d.m.Y H:i') . '.',
        ];

        $scrisoare = $this->subject('Cerere demo SPV Curier — ' . $contact->denumire)
            ->html('<pre style="font: 14px/1.6 sans-serif; white-space: pre-wrap;">'
                . e(implode("\n", $randuri))
                . '</pre>');

        // Raspunsul merge direct la firma care a cerut demonstratia.
        if (filter_var($contact->email, FILTER_VALIDATE_EMAIL)) {
            $scrisoare->replyTo($contact->email, $contact->denumire);
        }

        return $scrisoare;
    }
}
