<?php

namespace Tests\Unit;

use App\Http\Middleware\CompanieAnaf;
use App\Models\User;
use App\Support\ContextCompanie;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Poarta de intrare a modulului: cine poate lucra și pentru care client.
 */
class CompanieAnafMiddlewareTest extends TestCase
{
    protected function trece(Request $request)
    {
        return (new CompanieAnaf())->handle($request, function () {
            return response()->json(['ok' => true, 'companie' => ContextCompanie::curenta()]);
        });
    }

    protected function tearDown(): void
    {
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    public function test_cererea_fara_utilizator_este_respinsa(): void
    {
        $raspuns = $this->trece(Request::create('/api/spv', 'GET'));

        $this->assertSame(401, $raspuns->getStatusCode());
    }

    public function test_utilizatorul_fara_societate_selectata_este_respins(): void
    {
        $request = Request::create('/api/spv', 'GET');
        $request->setUserResolver(function () {
            $user = new User();
            $user->id = 555001;
            $user->user_type = 'user';

            return $user;
        });

        $raspuns = $this->trece($request);

        $this->assertSame(400, $raspuns->getStatusCode());
        $this->assertStringContainsString('societate', $raspuns->getContent());
    }

    /** Nu poți lucra pentru un client la care nu ești arondat. */
    public function test_accesul_la_o_societate_straina_este_refuzat(): void
    {
        $request = Request::create('/api/spv', 'GET');
        $request->headers->set('AuthorizationHeader', '424242');
        $request->setUserResolver(function () {
            $user = new User();
            $user->id = 555002;
            $user->user_type = 'user';

            return $user;
        });

        $raspuns = $this->trece($request);

        $this->assertSame(403, $raspuns->getStatusCode());
    }

    /** Administratorul serviciului poate lucra fără client selectat: vede tot. */
    public function test_administratorul_trece_fara_societate_selectata(): void
    {
        $request = Request::create('/api/spv', 'GET');
        $request->setUserResolver(function () {
            $user = new User();
            $user->id = 1;
            $user->user_type = 'owner';

            return $user;
        });

        $this->app['auth']->guard('api')->setUser($request->user());

        $raspuns = $this->trece($request);

        $this->assertSame(200, $raspuns->getStatusCode());
    }
}
