<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\ContractClient;
use Tests\TestCase;

/**
 * Contractul de abonament scos din datele stocate.
 *
 * Documentul se poate scoate și neîntreg — uneori tocmai ca să se vadă ce mai
 * trebuie cerut de la client —, așa că partea care contează e ca omul să afle
 * dinainte ce rămâne necompletat, și ca fișierul să poarte un nume după care
 * poate fi găsit între altele.
 */
class ContractClientTest extends TestCase
{
    protected $clienti = [];

    protected function tearDown(): void
    {
        foreach ($this->clienti as $client) {
            ContractClient::where('company_id', $client->id)->delete();
            $client->delete();
        }

        parent::tearDown();
    }

    protected function client(string $denumire): Company
    {
        $client = Company::create(['denumire' => $denumire, 'cui' => (string) random_int(90000000, 99999999)]);
        $this->clienti[] = $client;

        return $client;
    }

    protected function contractIntreg(Company $client, array $peste = []): ContractClient
    {
        return ContractClient::create(array_merge([
            'company_id' => $client->id,
            'numar' => '117',
            'data' => now()->toDateString(),
            'beneficiar_denumire' => $client->denumire,
            'beneficiar_adresa' => 'Str. Mihai Viteazu nr. 4, Constanța',
            'beneficiar_reg_com' => 'J13/1234/2015',
            'beneficiar_cui' => 'RO34567890',
            'beneficiar_reprezentant' => 'Ionescu Maria',
            'beneficiar_functie' => 'administrator',
            'plan' => 'CABINET',
            'periodicitate' => 'anual',
        ], $peste));
    }

    /** @test */
    public function un_contract_intreg_nu_are_ce_sa_ii_lipseasca()
    {
        $contract = $this->contractIntreg($this->client('CONTRACT INTREG SRL'));

        $this->assertSame([], $contract->ceLipseste());
    }

    /** @test */
    public function spune_pe_nume_ce_lipseste()
    {
        $contract = $this->contractIntreg($this->client('CONTRACT CIUNTIT SRL'), [
            'beneficiar_adresa' => '',
            'plan' => null,
        ]);

        $lipsa = $contract->ceLipseste();

        $this->assertContains('adresa beneficiarului', $lipsa);
        $this->assertContains('planul ales', $lipsa);
        $this->assertCount(2, $lipsa);
    }

    /** @test */
    public function un_contract_nescris_cere_tot()
    {
        $gol = new ContractClient();

        $this->assertCount(10, $gol->ceLipseste());
    }

    /** @test */
    public function numele_fisierului_poarta_numarul_si_beneficiarul()
    {
        $contract = $this->contractIntreg($this->client('NUMELE FISIERULUI SRL'));

        $this->assertSame('Contract SPV Curier - nr 117 - NUMELE FISIERULUI SRL.pdf', $contract->numeFisier());
    }

    /** @test */
    public function numele_fisierului_scapa_de_semnele_care_supara_sistemul_de_fisiere()
    {
        $contract = $this->contractIntreg($this->client('SEMNE SRL'), [
            'numar' => '117/2026',
            'beneficiar_denumire' => 'ALFA & OMEGA "TRANS" SRL',
        ]);

        $nume = $contract->numeFisier();

        $this->assertStringNotContainsString('/', $nume);
        $this->assertStringNotContainsString('"', $nume);
        $this->assertStringEndsWith('.pdf', $nume);
    }

    /** @test */
    public function fara_numar_numele_ramane_al_beneficiarului()
    {
        $contract = $this->contractIntreg($this->client('FARA NUMAR SRL'), ['numar' => null]);

        $this->assertSame('Contract SPV Curier - FARA NUMAR SRL.pdf', $contract->numeFisier());
    }

    /** @test */
    public function contractul_se_gaseste_dupa_client_si_numai_unul()
    {
        $client = $this->client('UN SINGUR CONTRACT SRL');

        $this->assertNull(ContractClient::alClientului($client->id));

        $contract = $this->contractIntreg($client);

        $this->assertSame($contract->id, ContractClient::alClientului($client->id)->id);
        $this->assertNull(ContractClient::alClientului(null));
    }
}
