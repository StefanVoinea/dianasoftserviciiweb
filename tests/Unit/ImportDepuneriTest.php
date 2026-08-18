<?php

namespace Tests\Unit;

use App\Models\AnafDeclaratie;
use App\Models\AnafSocietate;
use App\Models\Company;
use App\Services\Anaf\ImportDepuneri;
use Tests\TestCase;

/**
 * Istoricul depunerilor din programul vechi (declmf.mde, tabelul „depuneri").
 *
 * Depunerile intra ca istoric incheiat, firmele inrolate fara denumire si-o
 * primesc din tabel, iar caile fisierelor de pe calculatorul clientului se dau
 * mai departe arhivarii. Reimportul nu dubleaza nimic.
 */
class ImportDepuneriTest extends TestCase
{
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Company::create(['denumire' => 'PROBA DEPUNERI SRL', 'cui' => '99000888']);
    }

    protected function tearDown(): void
    {
        AnafDeclaratie::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
        AnafSocietate::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
        $this->client->delete();

        parent::tearDown();
    }

    public function test_depunerile_intra_ca_istoric_cu_datele_pe_amandoua_formatele()
    {
        $rezultat = (new ImportDepuneri())->importaCsv($this->csv(), $this->client->id);

        $this->assertSame(2, $rezultat['randuri']);
        $this->assertSame(2, $rezultat['scrise']);

        $depusa = AnafDeclaratie::query()->toateCompaniile()
            ->where('company_id', $this->client->id)
            ->where('index_recipisa', '620298755')
            ->first();

        $this->assertSame('D394', $depusa->tip);
        $this->assertSame('finalizat', $depusa->pas);
        $this->assertTrue($depusa->semnat);
        $this->assertSame(8, $depusa->luna);
        $this->assertSame(2023, $depusa->anul);
        // Formatul american al driverului Access se citeste ca ora romaneasca.
        $this->assertSame('04.08.2023 10:54', $depusa->data_depunere->format('d.m.Y H:i'));

        // Recipisa „Eroare" nu e o cale: nu se da la arhivat.
        $lucrari = collect($rezultat['de_arhivat']);
        $this->assertCount(2, $lucrari);
        $this->assertNull($lucrari->firstWhere('id', '!=', $depusa->id)['recipisa']);
        $this->assertStringContainsString('Recipisa_D394', $lucrari->firstWhere('id', $depusa->id)['recipisa']);
    }

    public function test_firmele_fara_denumire_o_primesc_iar_reimportul_nu_dubleaza()
    {
        AnafSocietate::query()->toateCompaniile()->create([
            'company_id' => $this->client->id,
            'cif' => '21452670',
            'denumire' => null,
        ]);
        AnafSocietate::query()->toateCompaniile()->create([
            'company_id' => $this->client->id,
            'cif' => '99999999',
            'denumire' => 'NUME PUS DE OM',
        ]);

        $rezultat = (new ImportDepuneri())->importaCsv($this->csv(), $this->client->id);

        $this->assertSame(1, $rezultat['denumiri']);
        $this->assertSame(
            'SC LEONIM VEST SRL',
            AnafSocietate::query()->toateCompaniile()->where('cif', '21452670')->value('denumire')
        );
        // Denumirea deja completata nu se rescrie.
        $this->assertSame(
            'NUME PUS DE OM',
            AnafSocietate::query()->toateCompaniile()
                ->where('company_id', $this->client->id)->where('cif', '99999999')->value('denumire')
        );

        $dinNou = (new ImportDepuneri())->importaCsv($this->csv(), $this->client->id);

        $this->assertSame(0, $dinNou['scrise']);
        $this->assertSame(2, $dinNou['existente']);
    }

    /** Un CSV mic, in forma exportului din declmf.mde. */
    protected function csv(): string
    {
        $cale = tempnam(sys_get_temp_dir(), 'dep') . '.csv';

        file_put_contents($cale, implode("\n", [
            '"id_depuneri","Tip_declaratie","CUI","Fisier","Index_recipisa","Stare_declaratie","Date_inregistrare",'
                . '"Recipisa","Den_firma","Tip_soft","Luna","Anul","Rectificativa","Total_plata","Data_depunere"',
            '"257","D100","99999999","C:\Program Files\AutomaticIT\Depuse\DEMOSRL_D100_062012_99999999.pdf",'
                . '"999997283","Fisierul depus nu este un document valid","23.07.2012","Eroare","DEMO SRL","SoftA",'
                . '"06","2012","Nu","","23.07.2012 12:15:12"',
            '"260","D394","21452670","C:\Program Files (x86)\AutomaticIT\Depuse\SCLEONIMVESTSRL_D394_082023_21452670.pdf",'
                . '"620298755","Documentul este valid","04.08.2023",'
                . '"C:\Program Files (x86)\AutomaticIT\Recipise\SCLEONIMVESTSRL_Recipisa_D394_082023_21452670.pdf",'
                . '"SC LEONIM VEST SRL","SoftA","08","2023","Nu","","8/4/2023 10:54:30 AM"',
        ]));

        return $cale;
    }
}
