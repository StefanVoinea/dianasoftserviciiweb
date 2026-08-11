<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Arhivarea nu are voie sa strice semnarea.
 *
 * Semnarea e lucrul care conteaza: dupa ea, declaratia poarta semnatura si poate
 * fi depusa. Arhivarea la client e o inlesnire — daca nu se face acum, se face
 * mai tarziu, din fila de declaratii.
 *
 * Si totusi orice poticnire de acolo se intorcea omului drept „Server Error"
 * peste o semnatura care reusise. S-a intamplat cu o coloana adaugata intr-o zi
 * si nemigrata inca pe serverul aplicatiei: documentul era semnat si scris in
 * arhiva, iar in fila scria ca semnarea a esuat.
 */
class ArhivareaNuStricaSemnareaTest extends TestCase
{
    protected const COMPANIE = 999;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);
    }

    protected function tearDown(): void
    {
        AnafDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /** Semnarea prinde orice cade la arhivare, nu doar esecurile arhivei. */
    public function test_semnarea_prinde_orice_cade_la_arhivare(): void
    {
        $sursa = file_get_contents(app_path('Http/Controllers/Api/DeclaratiiController.php'));

        $inceput = strpos($sursa, '$this->arhiveaza($declaratie);');
        $bucata = substr($sursa, max(0, $inceput - 900), 1200);

        $this->assertStringContainsString(
            'try {',
            $bucata,
            'arhivarea de după semnare trebuie ținută în frâu'
        );
        $this->assertStringContainsString('catch (\\Throwable $e)', $bucata);
    }

    /** Si arhivarea insasi prinde orice, nu doar ArhivaException. */
    public function test_arhivarea_prinde_orice(): void
    {
        foreach ([
            'Http/Controllers/Api/DeclaratiiController.php',
            'Services/Anaf/Declaratii/MonitorizareFolder.php',
        ] as $fisier) {
            $sursa = file_get_contents(app_path($fisier));

            $inceput = strpos($sursa, 'protected function arhiveaza');
            // Bucata trebuie sa cuprinda si prinderea, care sta dupa scrieri.
            $bucata = substr($sursa, $inceput, 4200);

            $this->assertStringContainsString(
                'catch (\\Throwable $e)',
                $bucata,
                $fisier . ': o coloană lipsă n-are voie să oprească tot'
            );
        }
    }

    /**
     * Coloana in care se tine calea documentului dat spre lucru se scrie numai
     * daca exista: pe un server care n-a rulat inca migrarea, arhivarea merge
     * mai departe fara ea.
     */
    public function test_coloana_se_scrie_doar_daca_exista(): void
    {
        foreach ([
            'Http/Controllers/Api/DeclaratiiController.php' => 'self::areColoanaInitial()',
            'Services/Anaf/Declaratii/MonitorizareFolder.php' => "Schema::hasColumn('anaf_declaratii', 'arhiva_initial')",
        ] as $fisier => $paza) {
            $this->assertStringContainsString(
                $paza,
                file_get_contents(app_path($fisier)),
                $fisier . ': se scrie într-o coloană care poate lipsi'
            );
        }
    }

    /**
     * Proba pe viu: cu coloana lipsa, scrisul in ea cade — deci paza chiar are
     * ce pazi. Fara randul acesta, probele de mai sus ar cantari doar cuvinte.
     */
    public function test_scrisul_intr_o_coloana_lipsa_chiar_cade(): void
    {
        $certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'POPESCU ION',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
        ]);

        $declaratie = AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'D100_15208744_2026-07.xml',
            'tip' => 'D100',
            'cui' => '15208744',
            'certificat_id' => $certificat->id,
            'pas' => 'semnat',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        $declaratie->update(['coloana_care_nu_exista' => 'x']);
    }

    /** Iar coloana chiar a fost adaugata: migrarea exista in depozit. */
    public function test_coloana_exista_dupa_migrare(): void
    {
        $this->assertTrue(
            Schema::hasColumn('anaf_declaratii', 'arhiva_initial'),
            'rulați php artisan migrate'
        );
    }
}
