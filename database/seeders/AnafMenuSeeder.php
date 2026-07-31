<?php

namespace Database\Seeders;

use App\Models\DianaSoftMenuOption;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnafMenuSeeder extends Seeder
{
    /**
     * Societatile, declaratiile si solicitarile au devenit taburi in pagina SPV,
     * asa ca in meniu raman doar SPV (creat de SpvMenuSeeder) si Vector fiscal.
     */
    protected const OBSOLETE = ['declaratii', 'spv-solicitari', 'anaf-societati'];

    public function run(): void
    {
        $optiuni = [
            [
                'name' => 'E-transport',
                'url' => '/etransport',
                'slug' => 'etransport-anaf',
                'icon' => 'TruckIcon',
                'i18n' => 'Etransport',
                'position1' => 1001,
            ],
            [
                'name' => 'Vector fiscal',
                'url' => '/vector-fiscal',
                'slug' => 'vector-fiscal',
                'icon' => 'GridIcon',
                'i18n' => 'VectorFiscal',
                'position1' => 1002,
            ],
        ];

        $users = User::all();

        foreach ($optiuni as $optiune) {
            $meniu = DianaSoftMenuOption::firstOrCreate(
                ['slug' => $optiune['slug']],
                array_merge($optiune, [
                    'tag' => null,
                    'tagcolor' => null,
                    'dropdown' => 0,
                    'parent' => '\\',
                    'position2' => 0,
                    'isdisabled' => false,
                ])
            );

            foreach ($users as $user) {
                $user->dianasoftmenuoptions()->syncWithoutDetaching([$meniu->id => ['isactive' => true]]);
            }
        }

        foreach (DianaSoftMenuOption::whereIn('slug', self::OBSOLETE)->get() as $vechi) {
            $vechi->users()->detach();
            $vechi->delete();
        }
    }
}
