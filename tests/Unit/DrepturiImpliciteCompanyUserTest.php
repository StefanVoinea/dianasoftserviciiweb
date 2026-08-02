<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Ce primeste un om legat de o firma fara sa i se scrie drepturile.
 *
 * Legaturile se fac din mai multe locuri, unele vechi, care nu stiu de
 * drepturile de semnare si de depunere. Coloanele trebuie deci sa aiba o
 * valoare implicita — altfel MySQL refuza scrierea („Field 'poate_semna'
 * doesn't have a default value") — iar valoarea aceea trebuie sa fie „nu":
 * un drept nu se capata din tacere.
 */
class DrepturiImpliciteCompanyUserTest extends TestCase
{
    protected const DREPTURI = ['administrator', 'poate_semna', 'poate_depune'];

    /** @dataProvider drepturi */
    public function test_coloana_are_implicit_nu(string $drept): void
    {
        $coloana = collect(DB::select('SHOW COLUMNS FROM company_user'))
            ->firstWhere('Field', $drept);

        $indreptare = ' Baza de date nu e normalizată — se pune la loc cu:'
            . ' php artisan migrate:rollback --path=database/migrations/2026_08_02_090000_normalizeaza_drepturile_din_company_user.php'
            . ' && php artisan migrate.';

        $this->assertNotNull($coloana, 'Coloana „' . $drept . '” lipsește din company_user.' . $indreptare);
        $this->assertSame(
            '0',
            (string) $coloana->Default,
            'Dreptul „' . $drept . '” nu are implicit „nu”.' . $indreptare
        );
    }

    public function drepturi(): array
    {
        return array_map(function (string $drept) {
            return [$drept];
        }, self::DREPTURI);
    }

    /** Legatura scrisa fara drepturi trebuie sa treaca, si sa nu dea nimic. */
    public function test_legatura_fara_drepturi_se_scrie_si_nu_da_nimic(): void
    {
        $client = Company::create(['denumire' => 'FIRMA IMPLICIT SRL', 'cui' => '99000777']);

        $utilizator = User::create([
            'name' => 'Om Fara Drepturi Scrise',
            'email' => 'om.implicit@example.com',
            'password' => Hash::make('ParolaDeProba1'),
            'user_type' => 'user',
            'blocat' => 'Nu',
            'status' => 'activ',
        ]);

        // Asa leaga codul vechi: doar cei doi id, fara niciun drept.
        $client->users()->attach($utilizator->id);

        $legatura = DB::table('company_user')
            ->where('company_id', $client->id)
            ->where('user_id', $utilizator->id)
            ->first();

        DB::table('company_user')->where('company_id', $client->id)->delete();
        $utilizator->forceDelete();
        $client->delete();

        $this->assertNotNull($legatura, 'Legătura nu a putut fi scrisă fără drepturi.');
        $this->assertSame(0, (int) $legatura->administrator);
        $this->assertSame(0, (int) $legatura->poate_semna);
        $this->assertSame(0, (int) $legatura->poate_depune);
    }
}
