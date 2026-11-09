<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Formularul „Scrie-ne pentru o demonstrație" de pe dsgarage.ro (din 09.11.2026) bate în aceeași rută ca spvcurier.ro,
 * cu produs = dsgarage: alte câmpuri (firma, persoana, telefon, email, mesaj), adresa lui (CERERE_DEMO_DSGARAGE_EMAIL,
 * altfel cea comună) și subiectul „Cerere demo DS Garage — firma". Cererea SPV Curier merge mai departe neschimbată.
 */
class CerereDemoDsGarageTest extends TestCase
{
    /** Scrisoarea prinsă din Mail::raw: textul și ce i s-a pus pe plic (to / subject / replyTo). */
    protected function prindeScrisoarea(): object
    {
        $prinsa = new class {
            public $text = '';
            public $to = [];
            public $subject = '';
            public $replyTo = null;

            public function to($adrese) { $this->to = (array) $adrese; return $this; }
            public function subject($s) { $this->subject = $s; return $this; }
            public function replyTo($adresa, $nume = null) { $this->replyTo = [$adresa, $nume]; return $this; }
        };
        Mail::shouldReceive('raw')->once()->andReturnUsing(function ($text, $callback) use ($prinsa) {
            $prinsa->text = $text;
            $callback($prinsa);
        });

        return $prinsa;
    }

    public function test_cererea_ds_garage_pleaca_la_adresa_ei_cu_toate_datele(): void
    {
        config(['prezentare.email_demo_dsgarage' => 'stefan@proba-dsgarage.ro, office@proba-dsgarage.ro']);
        $scrisoare = $this->prindeScrisoarea();

        $this->postJson('/api/cerere-demo', [
            'produs' => 'dsgarage', 'firma' => 'AUTO PROBA SERVICE SRL', 'persoana' => 'Ion Popescu', 'telefon' => '0722 000 000',
            'email' => 'ion@auto-proba.ro', 'mesaj' => "15 masini pe zi\nlucram in Excel",
        ])->assertOk()->assertJson(['message' => 'Cererea a fost trimisă.']);

        $this->assertSame(['stefan@proba-dsgarage.ro', 'office@proba-dsgarage.ro'], $scrisoare->to);
        $this->assertSame('Cerere demo DS Garage — AUTO PROBA SERVICE SRL', $scrisoare->subject);
        $this->assertSame(['ion@auto-proba.ro', 'AUTO PROBA SERVICE SRL'], $scrisoare->replyTo);
        foreach (['DS Garage (dsgarage.ro)', 'Service / firma: AUTO PROBA SERVICE SRL', 'Persoana de contact: Ion Popescu', 'Telefon: 0722 000 000', 'Email: ion@auto-proba.ro', "Mesaj: 15 masini pe zi\nlucram in Excel"] as $rand) {
            $this->assertStringContainsString($rand, $scrisoare->text);
        }
    }

    public function test_fara_firma_sau_fara_telefon_si_email_cererea_e_refuzata(): void
    {
        Mail::shouldReceive('raw')->never();

        $this->postJson('/api/cerere-demo', ['produs' => 'dsgarage', 'telefon' => '0722 000 000'])->assertStatus(422);
        $this->postJson('/api/cerere-demo', ['produs' => 'dsgarage', 'firma' => 'AUTO PROBA SERVICE SRL'])
            ->assertStatus(422)->assertJson(['message' => 'Completați un email sau un număr de telefon.']);
    }

    public function test_cererea_spv_curier_ramane_la_fel(): void
    {
        config(['prezentare.email_demo' => 'spv@proba-dsgarage.ro', 'prezentare.email_demo_dsgarage' => 'stefan@proba-dsgarage.ro']);
        $scrisoare = $this->prindeScrisoarea();

        $this->postJson('/api/cerere-demo', ['firma' => 'PROBA DEMO SRL', 'email' => 'contabil@proba-demo.ro', 'cui_estimare' => '40', 'interval' => 'dimineața'])->assertOk();

        $this->assertSame(['spv@proba-dsgarage.ro'], $scrisoare->to);
        $this->assertSame('Cerere demo SPV Curier — PROBA DEMO SRL', $scrisoare->subject);
        $this->assertStringContainsString('Număr estimat de CUI-uri: 40', $scrisoare->text);
    }
}
