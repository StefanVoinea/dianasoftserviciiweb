<?php

namespace App\Http\Middleware;

use App\Models\Ipautorizat;
use App\Models\User;
use App\Notifications\AlerteazaAdministrator;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Lista globala de adrese de la care se poate intra in aplicatie.
 *
 * E o poarta veche, dinaintea listelor pe fiecare cont, si nu mai sta pe nicio
 * ruta: autentificarea foloseste acum App\Services\AccesIp, care tine adresele
 * permise pe cont si lasa sa treaca pe oricine nu are lista scrisa.
 *
 * A ramas inregistrata pentru rutele care ar avea nevoie de o limitare la
 * nivelul intregii aplicatii. Ce era de indreptat: un email picat sau un cont
 * de administrator sters nu mai transforma refuzul in eroare de server, iar
 * cererile venite de la aplicatie primesc un raspuns pe care il pot citi, nu o
 * trimitere catre un site din afara.
 */
class IpMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Ipautorizat::where('ip', $request->ip())->exists()) {
            return $next($request);
        }

        $this->anunta($request);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Accesul de la această adresă nu este permis. Adresa folosită: '
                    . ($request->ip() ?: 'necunoscută') . '.',
            ], 403);
        }

        return redirect()->away('https://www.whatismyip.com/');
    }

    /** Instiintarea administratorului. Un esec al ei nu opreste raspunsul. */
    protected function anunta(Request $request): void
    {
        $mesaj = 'ACCES NEAUTORIZAT DE LA IP ' . $request->ip() . ' ' . $request->path();

        try {
            $administrator = User::find(1);

            if (!$administrator) {
                Log::warning($mesaj . ' — nu există contul administrator, înștiințarea nu a plecat.');

                return;
            }

            $administrator->notify(new AlerteazaAdministrator($mesaj, $this->detalii($request)));
        } catch (\Throwable $e) {
            /*
             * Se prinde si Error, nu doar Exception: pana acum, o eroare la
             * trimiterea emailului ajungea la om ca „server error", in loc de
             * refuzul care trebuia sa se vada.
             */
            Log::error('Înștiințarea de acces neautorizat nu a plecat: ' . $e->getMessage());
        }
    }

    /** Ce s-a trimis in cerere, pe scurt si fara parole. */
    protected function detalii(Request $request): string
    {
        return collect($request->except(['password', 'parola', 'password_confirmation']))
            ->map(function ($valoare, $cheie) {
                return $cheie . '=' . (is_scalar($valoare) ? $valoare : json_encode($valoare));
            })
            ->implode(' ');
    }
}
