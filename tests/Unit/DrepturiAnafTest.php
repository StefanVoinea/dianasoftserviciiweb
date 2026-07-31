<?php

namespace Tests\Unit;

use App\Http\Middleware\CompanieAnaf;
use App\Models\Permission;
use App\Models\User;
use App\Support\ContextCompanie;
use Database\Seeders\AnafPermisiuniSeeder;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Drepturile pe operațiuni, în interiorul aceluiași client.
 */
class DrepturiAnafTest extends TestCase
{
    protected function tearDown(): void
    {
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    public function test_cele_patru_drepturi_exista_in_aplicatie(): void
    {
        foreach (array_keys(AnafPermisiuniSeeder::PERMISIUNI) as $nume) {
            $this->assertNotNull(
                Permission::where('name', $nume)->first(),
                'Lipsește dreptul ' . $nume
            );
        }
    }

    public function test_fiecare_drept_are_denumire_lizibila(): void
    {
        foreach (AnafPermisiuniSeeder::PERMISIUNI as $nume => $denumire) {
            $permisiune = Permission::where('name', $nume)->first();

            $this->assertSame($denumire, $permisiune->display_name);
        }
    }

    protected function cerere($user, ?string $permisiune)
    {
        $request = Request::create('/api/spv', 'GET');
        $request->headers->set('AuthorizationHeader', '777001');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return (new CompanieAnaf())->handle($request, function () {
            return response()->json(['ok' => true]);
        }, $permisiune);
    }

    /** Utilizator arondat clientului, dar fără dreptul cerut de rută. */
    public function test_lipsa_dreptului_blocheaza_operatiunea(): void
    {
        $user = new class extends User {
            public function hasPermissionToCompany($id)
            {
                return true;
            }

            public function hasPermission($name, $company_id)
            {
                return $name === 'verificareMesajeSpv';
            }

            public function isOwner()
            {
                return false;
            }
        };
        $user->id = 777100;
        $user->user_type = 'user';

        $this->assertSame(200, $this->cerere($user, 'verificareMesajeSpv')->getStatusCode());

        $refuzat = $this->cerere($user, 'incarcareDeclaratiiAnaf');
        $this->assertSame(403, $refuzat->getStatusCode());
        $this->assertStringContainsString('incarcareDeclaratiiAnaf', $refuzat->getContent());
    }

    /** Proprietarul societății nu are nevoie de drepturi explicite. */
    public function test_proprietarul_are_toate_drepturile(): void
    {
        $user = new class extends User {
            public function hasPermissionToCompany($id)
            {
                return true;
            }

            public function hasPermission($name, $company_id)
            {
                return false;
            }

            public function isOwner()
            {
                return true;
            }
        };
        $user->id = 777101;
        $user->user_type = 'owner';

        $this->assertSame(200, $this->cerere($user, 'incarcareDeclaratiiAnaf')->getStatusCode());
    }
}
