<?php

namespace App\Http\Controllers;

use App\Mail\CerereDemoDinScrisoare;
use App\Models\MarketingContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Cine a apasat „Solicita demo" in scrisoare.
 *
 * [2026-10-06] Deschiderea paginii nu mai e cerere. Se insemna pe loc, ca semn
 * de interes — numai ca legaturile din e-mail le deschid si filtrele de
 * securitate ale cutiilor postale, inainte ca omul sa fi vazut scrisoarea. Asa
 * au aparut „cereri" la un minut dupa trimitere, fara nume si fara telefon, iar
 * la telefon omul a spus ca n-a cerut nimic. Deschiderea se tine deoparte, ca
 * semn slab; cererea e formularul trimis, si ea ne vine si pe e-mail.
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

        // Clipa dintai: la a doua deschidere, ce se tine minte e tot cea dintai.
        if ($contact->demo_deschis_la === null) {
            $contact->update([
                'demo_deschis_la' => now(),
                'demo_campanie' => $contact->demo_campanie ?: (trim((string) $request->query('c')) ?: null),
            ]);
        }

        return response()->view('demo', [
            'gasit' => true,
            'contact' => $contact->fresh(),
            'trimis' => false,
        ]);
    }

    /** Formularul trimis: abia asta e cererea. Datele lasate spun pe cine sa ceri si la ce numar. */
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
            'demo_deschis_la' => $contact->demo_deschis_la ?: now(),
            'demo_persoana' => $date['persoana'] ?? null,
            'demo_telefon' => $date['telefon'] ?? null,
            'demo_mesaj' => $date['mesaj'] ?? null,
        ]);

        $this->daDeVeste($contact->fresh());

        return response()->view('demo', [
            'gasit' => true,
            'contact' => $contact->fresh(),
            'trimis' => true,
        ]);
    }

    /**
     * Cererea ne vine pe e-mail, la aceeasi adresa ca cele de pe site.
     *
     * Daca scrisoarea nu pleaca, cererea tot ramane: in fila Marketing si in
     * jurnalul cererilor, ca sa nu se piarda cat tine o defectiune a serverului
     * de e-mail. Omului i se spune oricum „am notat": a notat, chiar daca
     * vestea intarzie.
     */
    protected function daDeVeste(MarketingContact $contact): void
    {
        $destinatari = array_filter(array_map('trim', explode(',', (string) config('prezentare.email_demo'))));

        if ($destinatari === []) {
            Log::error('Cerere demo din scrisoare: nu e configurată nicio adresă (CERERE_DEMO_EMAIL).');

            return;
        }

        try {
            Mail::to($destinatari)->send(new CerereDemoDinScrisoare($contact));
        } catch (\Throwable $e) {
            Log::error('Cerere demo din scrisoare netrimisă: ' . $e->getMessage(), ['exception' => get_class($e)]);
            Log::channel('cereri_demo')->error(sprintf(
                "Cerere din scrisoare netrimisă, de recuperat cu mâna:\nFirma: %s\nE-mail: %s\nPersoana: %s\nTelefon: %s\nMesaj: %s",
                $contact->denumire,
                $contact->email,
                $contact->demo_persoana ?: '—',
                $contact->demo_telefon ?: '—',
                $contact->demo_mesaj ?: '—'
            ));
        }
    }
}
