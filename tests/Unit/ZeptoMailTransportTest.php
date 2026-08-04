<?php

namespace Tests\Unit;

use App\Mail\Transport\ZeptoMailTransport;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Trimiterea emailurilor prin API-ul HTTP ZeptoMail.
 *
 * Serverul din cloud are porturile SMTP blocate de gazduire, deci legatura cu
 * smtppro.zoho.eu murea cu "Connection timed out". API-ul merge pe 443.
 */
class ZeptoMailTransportTest extends TestCase
{
    protected function transport(): ZeptoMailTransport
    {
        return new ZeptoMailTransport([
            'key' => 'cheie-de-proba',
            'url' => 'https://api.zeptomail.eu/v1.1/email',
        ]);
    }

    protected function mesaj(): \Swift_Message
    {
        $mesaj = new \Swift_Message('Declarația nu a putut fi prelucrată');
        $mesaj->setFrom(['office@dianasoft.ro' => 'Diana Soft']);
        $mesaj->setTo(['contabil@firma.ro' => 'Contabil']);
        $mesaj->setReplyTo('office@dianasoft.ro');
        $mesaj->setBody('<h1>Motivul</h1>', 'text/html');

        return $mesaj;
    }

    public function test_emailul_pleaca_in_forma_ceruta_de_zeptomail(): void
    {
        Http::fake(['api.zeptomail.eu/*' => Http::response(['data' => [['code' => 'EM_104']]], 201)]);

        $mesaj = $this->mesaj();
        $mesaj->attach(new \Swift_Attachment('%PDF-1.4 proba', 'declaratie_semnata.pdf', 'application/pdf'));

        $trimisi = $this->transport()->send($mesaj);

        $this->assertSame(1, $trimisi);

        Http::assertSent(function (Request $cerere) {
            $date = $cerere->data();

            return $cerere->url() === 'https://api.zeptomail.eu/v1.1/email'
                && $cerere->hasHeader('Authorization', 'Zoho-enczapikey cheie-de-proba')
                && $date['from'] === ['address' => 'office@dianasoft.ro', 'name' => 'Diana Soft']
                && $date['to'][0]['email_address']['address'] === 'contabil@firma.ro'
                && $date['reply_to'][0]['address'] === 'office@dianasoft.ro'
                && $date['subject'] === 'Declarația nu a putut fi prelucrată'
                && $date['htmlbody'] === '<h1>Motivul</h1>'
                && $date['attachments'][0]['name'] === 'declaratie_semnata.pdf'
                && base64_decode($date['attachments'][0]['content']) === '%PDF-1.4 proba';
        });
    }

    /** Refuzul ZeptoMail nu trece tacut: exceptia poarta motivul lor. */
    public function test_refuzul_zeptomail_arunca_exceptie_cu_motivul(): void
    {
        Http::fake([
            'api.zeptomail.eu/*' => Http::response(['message' => 'Invalid API Token found'], 401),
        ]);

        $this->expectException(\Swift_TransportException::class);
        $this->expectExceptionMessage('Invalid API Token found');

        $this->transport()->send($this->mesaj());
    }

    /** Mailerul "zeptomail" e legat in aplicatie si foloseste transportul acesta. */
    public function test_mailerul_zeptomail_este_inregistrat(): void
    {
        config(['services.zeptomail.key' => 'cheie-de-proba']);

        $transport = $this->app->make('mail.manager')->mailer('zeptomail')->getSwiftMailer()->getTransport();

        $this->assertInstanceOf(ZeptoMailTransport::class, $transport);
    }
}
