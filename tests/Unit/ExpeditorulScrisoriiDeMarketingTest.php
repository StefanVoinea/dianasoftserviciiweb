<?php

namespace Tests\Unit;

use App\Mail\ScrisoareMarketing;
use App\Models\MarketingContact;
use Tests\TestCase;

/**
 * Cine scrie, și unde duc butoanele scrisorii.
 *
 * În cutia cuiva care nu ne cunoaște, expeditorul e prima și uneori singura
 * vorbă citită. El nu poate fi lăsat pe seama lui MAIL_FROM_NAME: acela e bun
 * pentru o înștiințare tehnică și poate fi orice pe serverul unde se întâmplă
 * să ruleze aplicația — numele omului care a pus-o acolo, de pildă. Într-o
 * scrisoare de marketing trebuie să scrie numele casei.
 */
class ExpeditorulScrisoriiDeMarketingTest extends TestCase
{
    protected function contact(): MarketingContact
    {
        return new MarketingContact([
            'denumire' => 'Contabil Priceput SRL',
            'email' => 'cineva@proba-expeditor.ro',
            'jeton' => str_repeat('a', 48),
        ]);
    }

    protected function scrisoare(): ScrisoareMarketing
    {
        $scrisoare = new ScrisoareMarketing($this->contact(), 'Proba', 'Bună ziua, {nume}.', 'proba');
        $scrisoare->build();

        return $scrisoare;
    }

    /** Expeditorul scris în antet, adus la o formă pe care o putem compara. */
    protected function expeditorul(ScrisoareMarketing $scrisoare): array
    {
        $de = $scrisoare->from[0] ?? [];

        return [$de['address'] ?? null, $de['name'] ?? null];
    }

    /** @test */
    public function expeditorul_e_numele_casei_nu_al_serverului()
    {
        config([
            'mail.from' => ['address' => 'office@dianasoft.ro', 'name' => 'Stefan Voinea'],
            'marketing.expeditor' => ['adresa' => '', 'nume' => 'Diana Soft'],
        ]);

        $scrisoare = $this->scrisoare();

        $this->assertSame(['office@dianasoft.ro', 'Diana Soft'], $this->expeditorul($scrisoare));
    }

    /** Subsolul spune același lucru ca antetul; altfel s-ar contrazice singure. */
    /** @test */
    public function subsolul_spune_acelasi_expeditor_ca_antetul()
    {
        config([
            'mail.from' => ['address' => 'office@dianasoft.ro', 'name' => 'Stefan Voinea'],
            'marketing.expeditor' => ['adresa' => '', 'nume' => 'Diana Soft'],
        ]);

        $html = $this->scrisoare()->render();

        $this->assertStringContainsString('Diana Soft', $html);
        $this->assertStringNotContainsString('Stefan Voinea', $html);
    }

    /** Când nu e scris nimic anume, se folosește expeditorul aplicației. */
    /** @test */
    public function fara_expeditor_scris_anume_ramane_cel_al_aplicatiei()
    {
        config([
            'mail.from' => ['address' => 'office@dianasoft.ro', 'name' => 'Diana Soft'],
            'marketing.expeditor' => ['adresa' => '', 'nume' => ''],
        ]);

        $this->assertSame(['office@dianasoft.ro', 'Diana Soft'], $this->expeditorul($this->scrisoare()));
    }

    /** @test */
    public function se_poate_scrie_si_alta_adresa_decat_a_aplicatiei()
    {
        config([
            'mail.from' => ['address' => 'server@dianasoft.ro', 'name' => 'Server'],
            'marketing.expeditor' => ['adresa' => 'office@dianasoft.ro', 'nume' => 'Diana Soft'],
        ]);

        $this->assertSame(['office@dianasoft.ro', 'Diana Soft'], $this->expeditorul($this->scrisoare()));
    }

    /**
     * Butonul spre pagina de prezentare: pentru cine vrea să se uite întâi
     * singur, fără să ceară nimănui nimic.
     *
     * @test
     */
    public function scrisoarea_duce_si_la_pagina_de_prezentare()
    {
        config(['prezentare.site' => 'https://spvcurier.ro']);

        $html = $this->scrisoare()->render();

        $this->assertStringContainsString('https://spvcurier.ro', $html);
        $this->assertStringContainsString('Prezentarea aplicației', $html);

        // Adresa se vede și scrisă, nu doar ascunsă sub buton.
        $this->assertStringContainsString('spvcurier.ro</a>', $html);
    }

    /** Legătura de dezabonare rămâne în fiecare scrisoare, oricâte butoane s-ar adăuga. */
    /** @test */
    public function dezabonarea_ramane_la_locul_ei()
    {
        $html = $this->scrisoare()->render();

        $this->assertStringContainsString('/dezabonare/' . str_repeat('a', 48), $html);
    }
}
