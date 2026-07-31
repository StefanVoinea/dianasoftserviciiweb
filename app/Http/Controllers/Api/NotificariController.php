<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NotificareEmail;
use App\Models\Company;
use App\Models\NotificareAplicatie;
use App\Models\User;
use App\Services\Anaf\Format;
use App\Services\Anaf\Jurnal;
use App\Support\ContextUtilizator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Instiintarile trimise utilizatorilor din zona de administrare.
 *
 * Ajung in aplicatie — pe pagina principala, pana sunt citite — si, daca se
 * cere, si pe email. Emailul poate esua (adresa gresita, server picat) fara ca
 * notificarea din aplicatie sa se piarda: ea e deja scrisa, iar esecul ramane
 * inregistrat langa ea.
 */
class NotificariController extends Controller
{
    /** Notificarile mele, cele necitite primele. */
    public function aleMele(Request $request)
    {
        $notificari = NotificareAplicatie::ale($request->user()->id)
            ->orderByRaw('citita_la IS NOT NULL')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function (NotificareAplicatie $notificare) {
                return $this->prezinta($notificare);
            });

        return response()->json([
            'success' => true,
            'data' => $notificari,
            'necitite' => NotificareAplicatie::ale($request->user()->id)->necitite()->count(),
        ]);
    }

    public function marcheazaCitita(Request $request, NotificareAplicatie $notificare)
    {
        // Se poate citi doar propria notificare.
        abort_unless((int) $notificare->user_id === (int) $request->user()->id, 404);

        if (!$notificare->citita_la) {
            $notificare->update(['citita_la' => now()]);
            $this->confirmaExpeditorului($notificare, $request->user());
        }

        return response()->json(['success' => true, 'data' => $this->prezinta($notificare->fresh())]);
    }

    public function marcheazaToate(Request $request)
    {
        $necitite = NotificareAplicatie::ale($request->user()->id)->necitite()->get();

        foreach ($necitite as $notificare) {
            $notificare->update(['citita_la' => now()]);
            $this->confirmaExpeditorului($notificare, $request->user());
        }

        return response()->json(['success' => true, 'data' => ['citite' => $necitite->count()]]);
    }

    /**
     * Instiinteaza expeditorul ca notificarea lui a fost citita.
     *
     * Confirmarea e ea insasi o notificare, deci trebuie oprita bucla: citirea
     * unei confirmari nu mai naste alta. Nici expeditorul care se citeste pe
     * sine nu se instiinteaza singur.
     */
    protected function confirmaExpeditorului(NotificareAplicatie $notificare, User $cititor): void
    {
        if (!$notificare->confirma_citirea || $notificare->este_confirmare || !$notificare->trimis_de) {
            return;
        }

        if ((int) $notificare->trimis_de === (int) $cititor->id) {
            return;
        }

        NotificareAplicatie::create([
            'user_id' => $notificare->trimis_de,
            'company_id' => $notificare->company_id,
            'titlu' => 'Notificare citită: ' . $notificare->titlu,
            'mesaj' => ($cititor->name ?: $cititor->email) . ' (' . $cititor->email . ') a citit înștiințarea „'
                . $notificare->titlu . '” la ' . now()->format('d.m.Y H:i') . '.',
            'importanta' => 'informare',
            'este_confirmare' => true,
            'trimis_de' => $cititor->id,
            'trimis_de_nume' => $cititor->name,
        ]);
    }

    /**
     * Trimite o notificare. Destinatarii se aleg in trei feluri: anumiti
     * utilizatori, toti utilizatorii unui client, sau toata lumea.
     */
    public function trimite(Request $request)
    {
        $date = $request->validate([
            'titlu' => 'required|string|max:191',
            'mesaj' => 'required|string|max:5000',
            'importanta' => ['nullable', Rule::in(NotificareAplicatie::IMPORTANTE)],
            'destinatari' => ['required', Rule::in(['utilizatori', 'client', 'toti'])],
            'utilizatori' => 'required_if:destinatari,utilizatori|array',
            'utilizatori.*' => 'integer|exists:users,id',
            'company_id' => 'required_if:destinatari,client|nullable|exists:companies,id',
            'in_aplicatie' => 'nullable|boolean',
            'pe_email' => 'nullable|boolean',
            'confirma_citirea' => 'nullable|boolean',
        ]);

        $inAplicatie = !array_key_exists('in_aplicatie', $date) || $date['in_aplicatie'];
        $peEmail = !empty($date['pe_email']);

        if (!$inAplicatie && !$peEmail) {
            return response()->json([
                'success' => false,
                'message' => 'Alegeți cel puțin o cale: în aplicație sau pe email.',
            ], 422);
        }

        $destinatari = $this->destinatarii($date);

        if ($destinatari->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Nu s-a găsit niciun destinatar.'], 422);
        }

        $expeditor = ContextUtilizator::curent();
        $importanta = $date['importanta'] ?? 'informare';
        $companie = $date['company_id'] ?? null;

        $trimise = 0;
        $emailuri = 0;
        $esecuri = [];

        // Randurile plecate deodata poarta acelasi lot: asa se vede mai tarziu
        // cati dintre destinatari au citit.
        $lot = (string) Str::uuid();

        foreach ($destinatari as $user) {
            $notificare = null;

            if ($inAplicatie) {
                $notificare = NotificareAplicatie::create([
                    'lot' => $lot,
                    'user_id' => $user->id,
                    'company_id' => $companie,
                    'titlu' => $date['titlu'],
                    'mesaj' => $date['mesaj'],
                    'importanta' => $importanta,
                    'pe_email' => $peEmail,
                    'confirma_citirea' => !empty($date['confirma_citirea']),
                    'trimis_de' => optional($expeditor)->id,
                    'trimis_de_nume' => optional($expeditor)->name,
                ]);

                $trimise++;
            }

            if (!$peEmail || !$user->email) {
                continue;
            }

            try {
                Mail::to($user->email)->send(
                    new NotificareEmail($date['titlu'], $date['mesaj'], $importanta, $user->name)
                );

                $emailuri++;
                optional($notificare)->update(['trimis_email_la' => now()]);
            } catch (\Exception $e) {
                /*
                 * Un email picat nu opreste restul lotului si nu sterge
                 * notificarea din aplicatie: destinatarul o vede acolo, iar
                 * motivul ramane scris langa ea.
                 */
                Log::error('Notificare pe email eșuată pentru ' . $user->email . ': ' . $e->getMessage());

                $esecuri[] = $user->email;
                optional($notificare)->update(['eroare_email' => mb_substr($e->getMessage(), 0, 500)]);
            }
        }

        Jurnal::scrie(
            'notificare_trimisa',
            'A trimis notificarea „' . $date['titlu'] . '” către ' . $destinatari->count() . ' utilizatori'
                . ($peEmail ? ' (' . $emailuri . ' pe email)' : ''),
            ['destinatari' => $destinatari->pluck('email')->all()],
            null,
            $esecuri === []
        );

        return response()->json([
            'success' => true,
            'data' => [
                'destinatari' => $destinatari->count(),
                'in_aplicatie' => $trimise,
                'emailuri' => $emailuri,
                'esecuri' => $esecuri,
            ],
        ]);
    }

    /**
     * Ce s-a trimis pana acum, grupat pe loturi.
     *
     * Un lot e o trimitere: acelasi text, mai multi destinatari. Asa se vede
     * dintr-o privire cati au citit, si cine anume — nu un sir de randuri
     * identice, cate unul de fiecare om.
     */
    public function istoric()
    {
        $loturi = NotificareAplicatie::with('user')
            ->where('este_confirmare', false)
            ->orderByDesc('created_at')
            ->limit(500)
            ->get()
            ->groupBy(function (NotificareAplicatie $notificare) {
                // Randurile scrise inainte de loturi raman fiecare pe cont propriu.
                return $notificare->lot ?: 'singur-' . $notificare->id;
            })
            ->map(function ($randuri) {
                $primul = $randuri->first();
                $citite = $randuri->filter(function (NotificareAplicatie $notificare) {
                    return $notificare->citita_la !== null;
                });

                return [
                    'lot' => $primul->lot,
                    'titlu' => $primul->titlu,
                    'mesaj' => $primul->mesaj,
                    'importanta' => $primul->importanta,
                    'pe_email' => $primul->pe_email,
                    'confirma_citirea' => $primul->confirma_citirea,
                    'trimis_de_nume' => $primul->trimis_de_nume,
                    'trimisa_la' => Format::dataOra($primul->created_at),
                    'destinatari' => $randuri->count(),
                    'citite' => $citite->count(),
                    'lista' => $randuri->map(function (NotificareAplicatie $notificare) {
                        return [
                            'id' => $notificare->id,
                            'email' => optional($notificare->user)->email,
                            'nume' => optional($notificare->user)->name,
                            'citita_la' => Format::dataOra($notificare->citita_la),
                            'trimis_email_la' => Format::dataOra($notificare->trimis_email_la),
                            'eroare_email' => $notificare->eroare_email,
                        ];
                    })->values()->all(),
                ];
            })
            ->values();

        return response()->json(['success' => true, 'data' => $loturi]);
    }

    /** @return \Illuminate\Support\Collection<int, User> */
    protected function destinatarii(array $date)
    {
        if ($date['destinatari'] === 'utilizatori') {
            return User::whereIn('id', $date['utilizatori'])->get();
        }

        if ($date['destinatari'] === 'client') {
            $client = Company::find($date['company_id']);

            return $client ? $client->users()->get() : collect();
        }

        // Toata lumea inseamna conturile care pot intra: cele blocate n-au ce face cu ele.
        return User::where(function ($intrebare) {
            $intrebare->whereNull('blocat')->orWhere('blocat', '!=', 'Da');
        })->get();
    }

    protected function prezinta(NotificareAplicatie $notificare): array
    {
        return [
            'id' => $notificare->id,
            'titlu' => $notificare->titlu,
            'mesaj' => $notificare->mesaj,
            'importanta' => $notificare->importanta,
            'citita' => $notificare->citita_la !== null,
            'pe_email' => $notificare->pe_email,
            'primita_la' => Format::dataOra($notificare->created_at),
        ];
    }
}
