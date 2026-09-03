<?php

namespace App\Http\Controllers;

use App\Models\MarketingContact;
use Illuminate\Http\Request;

/**
 * Dezabonarea de la scrisorile noastre.
 *
 * E o pagina publica, fara cont si fara nicio piedica: cine primeste o
 * scrisoare trebuie sa poata iesi dintr-o apasare, nu dupa ce isi face cont
 * undeva ori scrie cuiva. De aceea nu e sub „api" si nu cere autentificare.
 *
 * Jetonul e lung si intamplator, deci nimeni nu poate dezabona pe altcineva
 * ghicindu-l; iar mai mult de atat nu se cere, fiindca orice pas in plus
 * inseamna oameni care raman abonati fara sa vrea.
 *
 * Se raspunde si la POST, nu numai la GET: asa cere „List-Unsubscribe-Post",
 * antetul prin care programele de posta isi arata butonul lor de dezabonare.
 */
class DezabonareController extends Controller
{
    public function arata(Request $request, string $jeton)
    {
        $contact = MarketingContact::where('jeton', $jeton)->first();

        if (!$contact) {
            return response()->view('dezabonare', [
                'gasit' => false,
                'contact' => null,
                'deAcum' => false,
            ], 404);
        }

        // Era deja dezabonat: i se spune, si nu se scrie nimic din nou.
        $deAcum = $contact->abonat;

        if ($contact->abonat) {
            $contact->update([
                'abonat' => false,
                'dezabonat_la' => now(),
                'motiv_dezabonare' => trim((string) $request->input('motiv')) ?: null,
            ]);
        }

        return response()->view('dezabonare', [
            'gasit' => true,
            'contact' => $contact,
            'deAcum' => $deAcum,
        ]);
    }
}
