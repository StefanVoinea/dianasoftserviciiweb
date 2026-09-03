<?php

namespace App\Http\Controllers;

use App\Models\MarketingContact;
use Illuminate\Http\Request;

/**
 * Cine a apasat „Solicita demo" in scrisoare.
 *
 * Apasarea se insemneaza pe loc, la deschiderea paginii: ea e semnul de interes,
 * si el nu trebuie sa depinda de faptul ca omul mai completeaza si un formular.
 * Multi apasa, se uita si inchid — dar au apasat, si asta se vede.
 *
 * Cine vrea sa lase si numele si telefonul, le lasa in pagina; atunci se stie si
 * pe cine sa ceri cand suni, nu doar „cineva de la firma asta".
 *
 * Pagina e publica, ca si dezabonarea: cine primeste o scrisoare nu are cont la
 * noi si nici n-are de ce sa-si faca unul ca sa ceara o demonstratie.
 */
class DemoController extends Controller
{
    public function arata(Request $request, string $jeton)
    {
        $contact = MarketingContact::where('jeton', $jeton)->first();

        if (!$contact) {
            return response()->view('demo', [
                'gasit' => false,
                'contact' => null,
                'trimis' => false,
            ], 404);
        }

        /*
         * Clipa se scrie o singura data: la a doua apasare, ce se tine minte e
         * tot cea dintai — atunci s-a aprins interesul.
         */
        if ($contact->demo_cerut_la === null) {
            $contact->update([
                'demo_cerut_la' => now(),
                'demo_campanie' => trim((string) $request->query('c')) ?: null,
            ]);
        }

        return response()->view('demo', [
            'gasit' => true,
            'contact' => $contact->fresh(),
            'trimis' => false,
        ]);
    }

    /** Datele lasate de om: pe cine sa ceri si la ce numar. */
    public function primeste(Request $request, string $jeton)
    {
        $contact = MarketingContact::where('jeton', $jeton)->first();

        if (!$contact) {
            return response()->view('demo', [
                'gasit' => false,
                'contact' => null,
                'trimis' => false,
            ], 404);
        }

        $date = $request->validate([
            'persoana' => 'nullable|string|max:190',
            'telefon' => 'nullable|string|max:60',
            'mesaj' => 'nullable|string|max:1000',
        ]);

        $contact->update([
            'demo_cerut_la' => $contact->demo_cerut_la ?: now(),
            'demo_persoana' => $date['persoana'] ?? null,
            'demo_telefon' => $date['telefon'] ?? null,
            'demo_mesaj' => $date['mesaj'] ?? null,
        ]);

        return response()->view('demo', [
            'gasit' => true,
            'contact' => $contact->fresh(),
            'trimis' => true,
        ]);
    }
}
