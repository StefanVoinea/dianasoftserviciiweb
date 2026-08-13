<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\AdministrareController;
use App\Models\Company;
use App\Models\VectorDeclaratie;
use App\Services\Anaf\ImportVectorMf;
use App\Services\Anaf\VectorMde;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * Importul periodicitatilor din programul vechi (tabelul vectormf).
 *
 * CSV-ul are cate un rand pe firma si cate o coloana pe declaratie, cu
 * periodicitatea in ea. Randurile intra ca "manuala" — cuvantul omului, care
 * bate deductia si nu e rescris de ea — iar rularea repetata nu dubleaza nimic.
 */
class ImportaVectorMfTest extends TestCase
{
    protected const COMPANIE = 996;

    protected function tearDown(): void
    {
        VectorDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        parent::tearDown();
    }

    protected function csv(string $continut): string
    {
        $cale = tempnam(sys_get_temp_dir(), 'vmf') . '.csv';
        file_put_contents($cale, $continut);

        return $cale;
    }

    public function test_periodicitatile_intra_ca_randuri_manuale_fara_dubluri(): void
    {
        // Cu BOM in fata, ca fisierul scos de Export-Csv.
        $cale = $this->csv("\xEF\xBB\xBF" . implode("\n", [
            '"id","cui","denumire","D112","D300","D100","D1"',
            '"1","15208744","ALFA","Lunar","Trimestrial","Trimestrial","Lunar"',
            '"2","22489650","BETA","","lunar","",""',
            '"3","","fara cui","Lunar","","",""',
        ]));

        $this->artisan('anaf:import-vectormf', ['fisier' => $cale, '--companie' => self::COMPANIE])
            ->assertExitCode(0);

        $randuri = VectorDeclaratie::query()->toateCompaniile()
            ->where('company_id', self::COMPANIE)
            ->orderBy('cui')
            ->orderBy('tip')
            ->get();

        $this->assertSame(
            ['15208744 D100 Trimestrial', '15208744 D112 Lunar', '15208744 D300 Trimestrial', '22489650 D300 Lunar'],
            $randuri->map(function (VectorDeclaratie $rand) {
                return $rand->cui . ' ' . $rand->tip . ' ' . $rand->perfisc;
            })->all()
        );

        $this->assertSame(['manuala'], $randuri->pluck('sursa')->unique()->all());

        // A doua rulare nu dubleaza nimic.
        $this->artisan('anaf:import-vectormf', ['fisier' => $cale, '--companie' => self::COMPANIE])
            ->assertExitCode(0);

        $this->assertSame(4, VectorDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->count());

        unlink($cale);
    }

    /**
     * Importul din Administrare clienti scrie pe clientul de pe randul apasat.
     *
     * In administrare nu exista un client curent — se lucreaza peste toti —
     * deci compania vine din ruta, nu din context, si intra si in cheia de
     * cautare: altfel randul unui client l-ar calca pe al altuia.
     */
    public function test_importul_din_administrare_scrie_pe_clientul_ales(): void
    {
        $client = Company::create(['denumire' => 'CLIENT VECTOR SRL', 'cui' => '99000112']);

        $cale = $this->csv("cui,denumire,D112\n15208744,ALFA,Lunar\n");
        $urcat = new UploadedFile($cale, 'vectormf.csv', 'text/csv', null, true);

        $cerere = Request::create('/', 'POST', [], [], ['fisier' => $urcat]);

        $raspuns = app(AdministrareController::class)->importaVector(
            $cerere,
            $client,
            app(VectorMde::class),
            app(ImportVectorMf::class)
        );

        $this->assertTrue($raspuns->getData(true)['success']);
        $this->assertSame(1, $raspuns->getData(true)['data']['scrise']);

        $rand = VectorDeclaratie::query()->toateCompaniile()
            ->where('company_id', $client->id)
            ->first();

        $this->assertNotNull($rand);
        $this->assertSame('D112', $rand->tip);
        $this->assertSame('Lunar', $rand->perfisc);

        VectorDeclaratie::query()->toateCompaniile()->where('company_id', $client->id)->delete();
        $client->delete();
    }

    public function test_fara_companie_sau_fara_fisier_se_opreste(): void
    {
        $this->artisan('anaf:import-vectormf', ['fisier' => 'nu-exista.csv', '--companie' => self::COMPANIE])
            ->assertExitCode(1);

        $cale = $this->csv("cui\n15208744\n");

        $this->artisan('anaf:import-vectormf', ['fisier' => $cale])
            ->assertExitCode(1);

        unlink($cale);
    }
}
