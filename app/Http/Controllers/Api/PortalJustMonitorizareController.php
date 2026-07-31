<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\PortalJustMonitorizariImport;
use App\Models\PortalJustModificare;
use App\Models\PortalJustMonitorizare;
use App\Services\Anaf\Format;
use App\Services\Just\ImportMonitorizari;
use App\Services\Just\MonitorizarePortalJust;
use App\Services\Just\PortalJustException;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Monitorizarea dosarelor din Portal Just: ce se urmărește, ce s-a schimbat și
 * unde ajung înștiințările.
 */
class PortalJustMonitorizareController extends Controller
{
    public function index()
    {
        $monitorizari = PortalJustMonitorizare::with('user')
            ->orderByDesc('activ')
            ->orderBy('valoare')
            ->get()
            ->map(function (PortalJustMonitorizare $m) {
                return [
                    'id' => $m->id,
                    'tip' => $m->tip,
                    'tip_eticheta' => $m->tip_etichete,
                    'valoare' => $m->valoare,
                    'institutie' => $m->institutie,
                    'email' => $m->email,
                    'activ' => $m->activ,
                    'dosare_urmarite' => $m->dosare_urmarite,
                    'ultima_verificare' => Format::dataOra($m->ultima_verificare),
                    'ultima_modificare' => Format::dataOra($m->ultima_modificare),
                    'ultima_eroare' => $m->ultima_eroare,
                    'adaugat_de' => optional($m->user)->name,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $monitorizari,
            'tipuri' => PortalJustMonitorizare::TIPURI,
        ]);
    }

    public function store(Request $request)
    {
        $date = $request->validate([
            'tip' => 'required|in:dosar,parte',
            'valoare' => 'required|string|max:200',
            'institutie' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
        ]);

        $monitorizare = $this->creeaza($request, $date);

        return response()->json([
            'success' => true,
            'data' => ['id' => $monitorizare->id],
            'message' => 'Monitorizarea a fost adăugată.',
        ], 201);
    }

    /**
     * Preia dintr-un fișier Excel o listă de numere de dosar sau nume de părți.
     */
    public function import(Request $request, ImportMonitorizari $parser)
    {
        $request->validate([
            'fisier' => 'required|file|max:5120|mimes:xls,xlsx,csv,txt',
            'email' => 'nullable|email|max:150',
        ]);

        $citire = new PortalJustMonitorizariImport;

        try {
            $foi = Excel::toArray($citire, $request->file('fisier'));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fișierul nu a putut fi citit: ' . $e->getMessage(),
            ], 422);
        }

        // Se ia prima foaie de calcul; restul sunt de regulă anexe.
        $randuri = $foi[0] ?? [];
        $rezultat = $parser->dinRanduri($randuri);

        if ($rezultat['intrari'] === []) {
            return response()->json([
                'success' => false,
                'message' => 'Fișierul nu conține numere de dosar sau nume de părți.',
            ], 422);
        }

        $adaugate = 0;
        $existente = 0;

        foreach ($rezultat['intrari'] as $intrare) {
            $deja = PortalJustMonitorizare::where('tip', $intrare['tip'])
                ->where('valoare', $intrare['valoare'])
                ->where('institutie', $intrare['institutie'])
                ->exists();

            if ($deja) {
                $existente++;

                continue;
            }

            $this->creeaza($request, [
                'tip' => $intrare['tip'],
                'valoare' => $intrare['valoare'],
                'institutie' => $intrare['institutie'],
                'email' => $intrare['email'] ?: $request->input('email'),
            ]);

            $adaugate++;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'adaugate' => $adaugate,
                'existente' => $existente,
                'ignorate' => $rezultat['ignorate'],
            ],
            'message' => sprintf(
                '%d monitorizări adăugate, %d existau deja, %d linii ignorate.',
                $adaugate,
                $existente,
                $rezultat['ignorate']
            ),
        ]);
    }

    public function update(Request $request, PortalJustMonitorizare $monitorizare)
    {
        $date = $request->validate([
            'email' => 'nullable|email|max:150',
            'activ' => 'nullable|boolean',
            'institutie' => 'nullable|string|max:100',
        ]);

        $monitorizare->update(array_filter($date, function ($valoare) {
            return $valoare !== null;
        }));

        return response()->json(['success' => true, 'message' => 'Monitorizarea a fost actualizată.']);
    }

    public function destroy(PortalJustMonitorizare $monitorizare)
    {
        // Istoricul modificărilor pleacă odată cu monitorizarea: fără ea nu mai
        // are context și nu mai poate fi citit corect.
        $monitorizare->modificari()->delete();
        $monitorizare->dosare()->delete();
        $monitorizare->delete();

        return response()->json(['success' => true, 'message' => 'Monitorizarea a fost ștearsă.']);
    }

    /**
     * Verifică acum, fără să aștepte rularea programată. Emailul pleacă tot la
     * rularea programată, ca înștiințările să nu se dubleze.
     */
    public function verifica(Request $request, MonitorizarePortalJust $serviciu)
    {
        $date = $request->validate(['id' => 'nullable|integer']);

        $query = PortalJustMonitorizare::active();

        if (!empty($date['id'])) {
            $query->where('id', $date['id']);
        }

        $monitorizari = $query->get();

        if ($monitorizari->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Nu există monitorizări active.'], 422);
        }

        $gasite = 0;
        $esecuri = [];

        foreach ($monitorizari as $monitorizare) {
            try {
                $gasite += count($serviciu->verifica($monitorizare));
            } catch (PortalJustException $e) {
                $esecuri[] = $monitorizare->valoare . ': ' . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'data' => ['verificate' => $monitorizari->count(), 'modificari' => $gasite, 'esecuri' => $esecuri],
            'message' => sprintf('%d monitorizări verificate, %d modificări găsite.', $monitorizari->count(), $gasite),
        ]);
    }

    /** Istoricul modificărilor sesizate, cel mai recent întâi. */
    public function modificari(Request $request)
    {
        $query = PortalJustModificare::with('monitorizare')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($request->filled('monitorizare_id')) {
            $query->where('monitorizare_id', (int) $request->query('monitorizare_id'));
        }

        if ($request->filled('dosar')) {
            $query->where('dosar_numar', 'like', '%' . $request->query('dosar') . '%');
        }

        // Sincronizare incrementală: aplicația mobilă cere doar ce a apărut de
        // la ultima verificare, ca să nu descarce tot istoricul la fiecare tură.
        if ($request->filled('dupa_id')) {
            $query->where('id', '>', (int) $request->query('dupa_id'));
        }

        $modificari = $query->limit((int) $request->query('limita', 200))->get()
            ->map(function (PortalJustModificare $m) {
                return [
                    'id' => $m->id,
                    'dosar_numar' => $m->dosar_numar,
                    'institutie' => $m->institutie,
                    'tip' => $m->tip,
                    'tip_eticheta' => $m->tip_etichete,
                    'descriere' => $m->descriere,
                    'urmarit_pentru' => $m->monitorizare ? $m->monitorizare->valoare : null,
                    'sesizat_la' => Format::dataOra($m->created_at),
                    'notificat_la' => Format::dataOra($m->notificat_la),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $modificari,
            'tipuri' => PortalJustModificare::TIPURI,
        ]);
    }

    protected function creeaza(Request $request, array $date): PortalJustMonitorizare
    {
        return PortalJustMonitorizare::create([
            'tip' => $date['tip'],
            'valoare' => trim($date['valoare']),
            'institutie' => $date['institutie'] ?? null,
            // Fără o adresă anume, înștiințările merg la utilizatorul care a cerut monitorizarea.
            'email' => $date['email'] ?: optional($request->user())->email,
            'user_id' => optional($request->user())->id,
            'activ' => true,
        ]);
    }
}
