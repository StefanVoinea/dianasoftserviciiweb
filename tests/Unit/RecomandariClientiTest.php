<?php

namespace Tests\Unit;

use App\Models\AbonamentClient;
use App\Models\Company;
use App\Models\RecomandareClient;
use Tests\TestCase;

/**
 * Luna gratuită pentru recomandări: cine aduce un client primește o lună, și
 * tot o lună primește și cel adus.
 *
 * Partea care se poate greși ușor e de unde se socotește luna. Ea se pune la
 * coada dreptului de lucru — după plata sau proba care ține mai mult — și
 * niciodată în trecut, altfel o lună dată unui client cu abonamentul expirat
 * de un an ar fi o lună deja consumată.
 */
class RecomandariClientiTest extends TestCase
{
    protected $clienti = [];

    protected function tearDown(): void
    {
        foreach ($this->clienti as $client) {
            RecomandareClient::where('company_id', $client->id)
                ->orWhere('recomandat_de_id', $client->id)
                ->delete();
            AbonamentClient::where('company_id', $client->id)->delete();
            $client->delete();
        }

        parent::tearDown();
    }

    protected function client(string $denumire, array $abonament = []): Company
    {
        $client = Company::create(['denumire' => $denumire, 'cui' => (string) random_int(90000000, 99999999)]);
        $this->clienti[] = $client;

        AbonamentClient::create(['company_id' => $client->id, 'modul_spv' => true] + $abonament);

        return $client;
    }

    public function test_luna_se_pune_dupa_abonamentul_platit()
    {
        $client = $this->client('PROBA Platit', ['platit_pana_la' => now()->addMonths(2)->toDateString()]);
        $abonament = AbonamentClient::alClientului($client->id);

        $panaLa = $abonament->adaugaLuniGratuite(1);

        $this->assertSame(now()->addMonths(2)->addMonthNoOverflow()->toDateString(), $panaLa);
    }

    /** Clientul aflat în probă: luna începe după ce se încheie proba, nu peste ea. */
    public function test_luna_se_pune_dupa_proba_cand_proba_tine_mai_mult()
    {
        $client = $this->client('PROBA In proba', [
            'proba_zile' => 90,
            'proba_pana_la' => now()->addDays(90)->toDateString(),
        ]);
        $abonament = AbonamentClient::alClientului($client->id);

        $panaLa = $abonament->adaugaLuniGratuite(1);

        $this->assertSame(now()->addDays(90)->addMonthNoOverflow()->toDateString(), $panaLa);
    }

    /** Abonament expirat demult: luna e de acum, nu una consumată anul trecut. */
    public function test_luna_nu_se_pune_niciodata_in_trecut()
    {
        $client = $this->client('PROBA Expirat', ['platit_pana_la' => now()->subYear()->toDateString()]);
        $abonament = AbonamentClient::alClientului($client->id);

        $panaLa = $abonament->adaugaLuniGratuite(1);

        $this->assertSame(now()->startOfDay()->addMonthNoOverflow()->toDateString(), $panaLa);
    }

    public function test_recomandarea_stie_cine_mai_are_de_primit()
    {
        $recomandant = $this->client('PROBA Recomandant');
        $adus = $this->client('PROBA Adus');

        $recomandare = RecomandareClient::create([
            'company_id' => $adus->id,
            'recomandat_de_id' => $recomandant->id,
        ]);

        $this->assertTrue($recomandare->maiEDeDat());
        $this->assertFalse($recomandare->recomandantulEPlatit());

        $recomandare->acordata_recomandantului_la = now()->toDateString();
        $recomandare->acordata_recomandatului_la = now()->toDateString();
        $recomandare->save();

        $proaspata = $recomandare->fresh();

        $this->assertTrue($proaspata->recomandantulEPlatit());
        $this->assertTrue($proaspata->recomandatulEPlatit());
        $this->assertFalse($proaspata->maiEDeDat());
    }

    /** Un client e recomandat o singură dată: legătura se schimbă, nu se dublează. */
    public function test_un_client_are_o_singura_recomandare()
    {
        $unul = $this->client('PROBA Unul');
        $altul = $this->client('PROBA Altul');
        $adus = $this->client('PROBA Adusul');

        RecomandareClient::create(['company_id' => $adus->id, 'recomandat_de_id' => $unul->id]);

        $recomandare = RecomandareClient::where('company_id', $adus->id)->first();
        $recomandare->recomandat_de_id = $altul->id;
        $recomandare->save();

        $this->assertSame(1, RecomandareClient::where('company_id', $adus->id)->count());
        $this->assertSame($altul->id, RecomandareClient::where('company_id', $adus->id)->first()->recomandat_de_id);
    }
}
