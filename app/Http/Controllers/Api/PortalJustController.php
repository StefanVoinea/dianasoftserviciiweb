<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Just\PortalJustClient;
use App\Services\Just\PortalJustException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Portal Just: interogarea dosarelor, părților și ședințelor din ECRIS.
 *
 * Datele sunt publice și identice pentru toți clienții aplicației, așa că nu se
 * salvează nimic local; răspunsurile se păstrează doar câteva minute în cache,
 * ca reluarea unei căutări să nu reinterogheze serviciul.
 */
class PortalJustController extends Controller
{
    /** Lista instanțelor acceptate de serviciu, pentru filtrele din interfață. */
    public function institutii(PortalJustClient $client)
    {
        try {
            $institutii = $client->institutii();
        } catch (PortalJustException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 502);
        }

        return response()->json(['success' => true, 'data' => $institutii]);
    }

    public function dosare(Request $request, PortalJustClient $client)
    {
        $criterii = $request->validate([
            'numar_dosar' => 'nullable|string|max:100',
            'obiect' => 'nullable|string|max:200',
            'nume_parte' => 'nullable|string|max:200',
            'institutie' => 'nullable|string|max:100',
            'data_start' => 'nullable|date',
            'data_stop' => 'nullable|date',
            'modificat_de' => 'nullable|date',
            'modificat_pana' => 'nullable|date',
        ]);

        try {
            $dosare = $this->cuCache('dosare', $criterii, function () use ($client, $criterii) {
                return $client->cautaDosare($criterii);
            });
        } catch (PortalJustException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $maxim = (int) config('portaljust.maxim_dosare');

        return response()->json([
            'success' => true,
            'data' => $dosare,
            'total' => count($dosare),
            // Serviciul taie lista la 1000; utilizatorul trebuie să știe că mai pot exista dosare.
            'incomplet' => count($dosare) >= $maxim,
        ]);
    }

    public function sedinte(Request $request, PortalJustClient $client)
    {
        $date = $request->validate([
            'data' => 'required|date',
            'institutie' => 'required|string|max:100',
        ]);

        try {
            $sedinte = $this->cuCache('sedinte', $date, function () use ($client, $date) {
                return $client->cautaSedinte($date['data'], $date['institutie']);
            });
        } catch (PortalJustException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $sedinte,
            'total' => count($sedinte),
        ]);
    }

    /** Cheia de cache se construiește din criteriile efective ale căutării. */
    protected function cuCache(string $prefix, array $criterii, callable $interogare)
    {
        $minute = (int) config('portaljust.cache_rezultate_minute');

        if ($minute <= 0) {
            return $interogare();
        }

        ksort($criterii);
        $cheie = 'portaljust.' . $prefix . '.' . md5(json_encode($criterii));

        return Cache::remember($cheie, now()->addMinutes($minute), $interogare);
    }
}
