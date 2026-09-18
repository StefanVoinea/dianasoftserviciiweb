<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Cererile de demonstrație de pe pagina de prezentare (spvcurier.ro).
 *
 * Pagina e un fișier static, așa că n-are cum trimite singură un email.
 * Formularul ei bate aici, iar de aici pleacă scrisoarea cu datele contului de
 * email al casei. Ruta e deschisă oricui — pagina nu are pe cine autentifica —
 * de aceea e ținută scurt: numai câmpurile formularului, cu număr limitat de
 * cereri pe minut de la aceeași adresă.
 */
class CerereDemoController extends Controller
{
    public function trimite(Request $request)
    {
        $date = $request->validate([
            'email' => ['nullable', 'email', 'max:190'],
            'telefon' => ['nullable', 'string', 'max:40'],
            'firma' => ['required', 'string', 'max:190'],
            'cui_estimare' => ['required', 'string', 'max:80'],
            'interval' => ['required', 'string', 'max:80'],
        ]);

        // Una din doua trebuie sa fie: altfel n-avem pe unde raspunde.
        if (trim((string) ($date['email'] ?? '')) === '' && trim((string) ($date['telefon'] ?? '')) === '') {
            return response()->json([
                'message' => 'Completați un email sau un număr de telefon.',
            ], 422);
        }

        $destinatari = array_filter(array_map('trim', explode(',', (string) config('prezentare.email_demo'))));

        if ($destinatari === []) {
            Log::error('Cerere demo: nu e configurată nicio adresă (CERERE_DEMO_EMAIL).');

            return response()->json([
                'message' => 'Cererea nu a putut fi trimisă. Vă rugăm să ne scrieți direct.',
            ], 500);
        }

        try {
            Mail::raw($this->mesaj($date, $request), function ($mail) use ($date, $destinatari) {
                $mail->to($destinatari)
                    ->subject('Cerere demo SPV Curier — ' . $date['firma']);

                // Raspunsul merge direct la cel care a cerut demonstratia.
                if (!empty($date['email'])) {
                    $mail->replyTo($date['email'], $date['firma']);
                }
            });
        } catch (\Throwable $e) {
            Log::error('Cerere demo netrimisă: ' . $e->getMessage(), ['exception' => get_class($e)]);

            /*
             * Scrisoarea n-a plecat, dar omul a scris-o: se pastreaza intreaga,
             * intr-un jurnal al ei. Altfel, cat tine defectiunea serverului de
             * email, fiecare cerere s-ar pierde fara urma, iar cel care a
             * cerut-o ar ramane cu „incercati din nou in cateva minute".
             */
            Log::channel('cereri_demo')->error(
                "Cerere netrimisă, de recuperat cu mâna:\n" . $this->mesaj($date, $request)
            );

            return response()->json([
                'message' => 'Cererea nu a putut fi trimisă acum. Încercați din nou în câteva minute.',
            ], 502);
        }

        return response()->json(['message' => 'Cererea a fost trimisă.']);
    }

    /** Scrisoarea, așa cum o citește cine o primește. */
    protected function mesaj(array $date, Request $request): string
    {
        $randuri = [
            'Cerere de demonstrație SPV Curier',
            '',
            'Companie: ' . $date['firma'],
            'Email: ' . (trim((string) ($date['email'] ?? '')) ?: '—'),
            'Telefon: ' . (trim((string) ($date['telefon'] ?? '')) ?: '—'),
            'Număr estimat de CUI-uri: ' . $date['cui_estimare'],
            'Interval orar preferat: ' . $date['interval'],
            '',
            'Trimisă la ' . now()->format('d.m.Y H:i') . ' de la ' . ($request->ip() ?: 'adresă necunoscută') . '.',
        ];

        return implode("\n", $randuri);
    }
}
