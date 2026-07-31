<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DispozitivNotificare;
use App\Services\Notificari\Fcm;
use Illuminate\Http\Request;

/**
 * Telefoanele pe care utilizatorul primeste alerte instantanee.
 *
 * Tokenul Firebase se schimba singur (la reinstalare, la stergerea datelor,
 * periodic), de aceea aplicatia mobila il retrimite ori de cate ori se
 * modifica, iar aici se actualizeaza inregistrarea existenta.
 */
class DispozitiveController extends Controller
{
    public function store(Request $request, Fcm $fcm)
    {
        $date = $request->validate([
            'token' => 'required|string|max:255',
            'platforma' => 'nullable|string|max:20',
            'model' => 'nullable|string|max:100',
        ]);

        $dispozitiv = DispozitivNotificare::updateOrCreate(
            ['token' => $date['token']],
            [
                'user_id' => $request->user()->id,
                'company_id' => $request->header('AuthorizationHeader') ?: null,
                'platforma' => $date['platforma'] ?? 'android',
                'model' => $date['model'] ?? null,
                'ultima_folosire' => now(),
                'esecuri' => 0,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => ['id' => $dispozitiv->id],
            // Aplicatia afla astfel daca serverul chiar poate trimite alerte
            // instantanee sau daca ramane pe verificarea periodica.
            'push_activ' => $fcm->activ(),
        ]);
    }

    /** La deconectare, telefonul nu mai trebuie sa primeasca alerte. */
    public function destroy(Request $request)
    {
        $date = $request->validate(['token' => 'required|string|max:255']);

        DispozitivNotificare::where('token', $date['token'])
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['success' => true]);
    }
}
