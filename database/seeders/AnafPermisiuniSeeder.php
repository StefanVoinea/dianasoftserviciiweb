<?php

namespace Database\Seeders;

use App\Models\DianaSoftMenuOption;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Drepturile pe operațiuni în modulul ANAF/SPV.
 *
 * Se acordă per client (company), ca în restul aplicației: un utilizator poate
 * avea dreptul de a verifica mesajele pentru un client, fără a putea depune
 * declarații pentru el.
 */
class AnafPermisiuniSeeder extends Seeder
{
    public const PERMISIUNI = [
        'incarcareDeclaratiiAnaf' => 'Încărcare și depunere declarații ANAF',
        'verificareMesajeSpv' => 'Verificare mesaje SPV',
        'creareSolicitariSpv' => 'Creare solicitări SPV',
        'vizualizareJurnalAnaf' => 'Vizualizare jurnal activități ANAF',
    ];

    public function run(): void
    {
        $meniu = DianaSoftMenuOption::where('slug', 'spv')->first();

        $permisiuni = [];

        foreach (self::PERMISIUNI as $nume => $denumire) {
            $permisiuni[] = Permission::updateOrCreate(
                ['name' => $nume],
                [
                    'display_name' => $denumire,
                    'dianasoftmenuoption_id' => optional($meniu)->id,
                ]
            );
        }

        // Utilizatorii existenți primesc drepturile pentru societățile lor, ca
        // trecerea la drepturi granulare să nu blocheze pe nimeni.
        foreach (User::with('companies')->get() as $user) {
            foreach ($user->companies as $company) {
                foreach ($permisiuni as $permisiune) {
                    $existent = DB::table('permission_user')
                        ->where('permission_id', $permisiune->id)
                        ->where('user_id', $user->id)
                        ->where('company_id', $company->id)
                        ->exists();

                    if ($existent) {
                        continue;
                    }

                    DB::table('permission_user')->insert([
                        'permission_id' => $permisiune->id,
                        'user_id' => $user->id,
                        'company_id' => $company->id,
                        'isactive' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
