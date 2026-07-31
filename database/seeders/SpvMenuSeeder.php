<?php

namespace Database\Seeders;

use App\Models\DianaSoftMenuOption;
use App\Models\User;
use Illuminate\Database\Seeder;

class SpvMenuSeeder extends Seeder
{
    public function run(): void
    {
        $menu = DianaSoftMenuOption::firstOrCreate(
            ['slug' => 'spv'],
            [
                'name' => 'SPV',
                'url' => '/spv',
                'slug' => 'spv',
                'icon' => 'FileTextIcon',
                'tag' => null,
                'tagcolor' => null,
                'i18n' => 'SPV',
                'dropdown' => 0,
                'parent' => '\\',
                'position1' => 999,
                'position2' => 0,
                'isdisabled' => false,
            ]
        );

        $users = User::all();
        foreach ($users as $user) {
            $user->dianasoftmenuoptions()->syncWithoutDetaching([$menu->id => ['isactive' => true]]);
        }
    }
}
