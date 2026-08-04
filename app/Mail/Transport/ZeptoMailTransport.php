<?php

namespace App\Mail\Transport;

use Illuminate\Mail\Transport\Transport;
use Illuminate\Support\Facades\Http;
use Swift_Attachment;
use Swift_Mime_SimpleMessage;
use Swift_MimePart;
use Swift_TransportException;

/**
 * Trimite emailurile prin API-ul HTTP ZeptoMail (Zoho), nu prin SMTP.
 *
 * De ce: serverul din cloud nu poate deschide porturile SMTP — gazduirea le
 * blocheaza ca masura anti-spam — asa ca legatura cu smtppro.zoho.eu murea cu
 * "Connection timed out" si nicio instiintare nu pleca. API-ul merge pe 443,
 * ca orice pagina de internet, deci trece de oriunde.
 *
 * Configurare (config/services.php -> zeptomail):
 *   key — jetonul "Send Mail Token" al agentului ZeptoMail (Zoho-enczapikey)
 *   url — punctul de intrare; implicit centrul de date european
 */
class ZeptoMailTransport extends Transport
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $raspuns = Http::withHeaders([
            'Authorization' => 'Zoho-enczapikey ' . ($this->config['key'] ?? ''),
        ])
            ->timeout($this->config['timeout'] ?? 30)
            ->post(
                $this->config['url'] ?? 'https://api.zeptomail.eu/v1.1/email',
                $this->sarcina($message)
            );

        if ($raspuns->failed()) {
            $detalii = $raspuns->json('message')
                ?: ($raspuns->json('error.details.0.message') ?: $raspuns->body());

            throw new Swift_TransportException(
                'ZeptoMail a refuzat emailul (HTTP ' . $raspuns->status() . '): ' . mb_substr((string) $detalii, 0, 500)
            );
        }

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    /** @return array<string, mixed> corpul cererii, in forma ceruta de ZeptoMail */
    protected function sarcina(Swift_Mime_SimpleMessage $message): array
    {
        $sarcina = array_filter([
            'from' => $this->adresa($message->getFrom()),
            'to' => $this->destinatari($message->getTo()),
            'cc' => $this->destinatari($message->getCc()),
            'bcc' => $this->destinatari($message->getBcc()),
            'reply_to' => $this->adrese($message->getReplyTo()),
            'subject' => $message->getSubject() ?: '(fără subiect)',
        ]);

        // Corpul principal: markdown-urile Laravel pun HTML-ul aici.
        if (stripos((string) $message->getContentType(), 'text/plain') === 0) {
            $sarcina['textbody'] = (string) $message->getBody();
        } else {
            $sarcina['htmlbody'] = (string) $message->getBody();
        }

        foreach ($message->getChildren() as $parte) {
            if ($parte instanceof Swift_Attachment) {
                $sarcina['attachments'][] = [
                    'content' => base64_encode((string) $parte->getBody()),
                    'mime_type' => $parte->getContentType() ?: 'application/octet-stream',
                    'name' => $parte->getFilename() ?: 'document',
                ];
            } elseif ($parte instanceof Swift_MimePart && $parte->getContentType() === 'text/plain') {
                // Varianta text a emailului HTML, pentru clientii fara HTML.
                $sarcina['textbody'] = (string) $parte->getBody();
            }
        }

        return $sarcina;
    }

    /**
     * Prima adresa dintr-o lista SwiftMailer (expeditorul).
     *
     * @param array|string|null $adrese
     */
    protected function adresa($adrese): ?array
    {
        $toate = $this->adrese($adrese);

        return $toate[0] ?? null;
    }

    /**
     * Adresele SwiftMailer (adresa => nume) in forma ZeptoMail.
     *
     * SwiftMailer poate da inapoi si un singur sir (reply-to setat simplu),
     * nu doar lista — se aduce totul la aceeasi forma.
     *
     * @param array|string|null $adrese
     * @return array<int, array{address: string, name?: string}>
     */
    protected function adrese($adrese): array
    {
        if (is_string($adrese)) {
            $adrese = [$adrese => null];
        }

        $rezultat = [];

        foreach ($adrese ?: [] as $adresa => $nume) {
            $rezultat[] = array_filter([
                'address' => $adresa,
                'name' => $nume,
            ]);
        }

        return $rezultat;
    }

    /**
     * Destinatarii, care la ZeptoMail stau cu un nivel in plus (email_address).
     *
     * @param array|string|null $adrese
     * @return array<int, array{email_address: array}>
     */
    protected function destinatari($adrese): array
    {
        return array_map(function (array $adresa) {
            return ['email_address' => $adresa];
        }, $this->adrese($adrese));
    }
}
