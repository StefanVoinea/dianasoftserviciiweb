<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarketingContact;
use App\Models\SiteVizita;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Vizitele paginii de prezentare spvcurier.ro: cine a intrat, de la ce adresă
 * și cât a stat.
 *
 * Pagina e un fișier static pe alt domeniu, fără jurnal la care să ajungem. Cât
 * e deschisă, ea bate aici din când în când și spune câte secunde a fost citită
 * și ce pagini s-au deschis. Ruta de înregistrare e deschisă oricui, ca și
 * cererea de demonstrație, deci e ținută scurt: câteva câmpuri, tăiate la
 * lungime, cu număr limitat de bătăi pe minut de la aceeași adresă.
 */
class ViziteSiteController extends Controller
{
    protected const PE_PAGINA = 100;

    /** O filă uitată deschisă nu e o vizită de o zi. */
    protected const DURATA_MAXIMA = 4 * 3600;

    /** Cât se țin vizitele; același termen e scris în politica de pe site. */
    protected const LUNI_PASTRATE = 12;

    /** Roboții care rulează JavaScript nu sunt vizitatori. */
    protected const ROBOTI = '/bot|crawl|spider|slurp|preview|headless|lighthouse|pingdom|uptime|monitor|curl|wget|python|scrapy/i';

    /**
     * O bătaie a paginii: prima deschide vizita, următoarele îi cresc durata.
     *
     * Vine ca text simplu, nu ca JSON declarat: așa o trimite browserul și la
     * închiderea paginii (sendBeacon), fără cererea de probă CORS dinainte.
     */
    public function inregistreaza(Request $request)
    {
        $date = json_decode((string) $request->getContent(), true);

        if (!is_array($date)) {
            $date = $request->all();
        }

        $sesiune = (string) ($date['id'] ?? '');

        if (!preg_match('/^[a-z0-9]{16,40}$/', $sesiune)) {
            return response()->json(['message' => 'Vizită fără număr.'], 422);
        }

        $agent = mb_substr((string) $request->userAgent(), 0, 500);

        if ($agent === '' || preg_match(self::ROBOTI, $agent)) {
            return response()->noContent();
        }

        $secunde = max(0, min(self::DURATA_MAXIMA, (int) ($date['secunde'] ?? 0)));
        $pagini = $this->paginile($date['pagini'] ?? []);
        $ip = (string) $request->ip();

        $vizita = SiteVizita::where('sesiune', $sesiune)->first();

        if (!$vizita) {
            $this->uitaCeleVechi();

            try {
                SiteVizita::create([
                    'sesiune' => $sesiune,
                    'ip' => $ip,
                    'user_agent' => $agent,
                    'dispozitiv' => $this->dispozitivul($agent),
                    'referrer' => $this->deUndeVine($date['referrer'] ?? null),
                    'pagina_intrare' => $pagini[0] ?? 'home',
                    'pagini' => $pagini,
                    'durata_secunde' => $secunde,
                    'contact_id' => $this->contactul($date['f'] ?? null),
                ]);

                return response()->noContent();
            } catch (QueryException $e) {
                // Doua batai deodata: cealalta a apucat sa scrie randul.
                $vizita = SiteVizita::where('sesiune', $sesiune)->first();

                if (!$vizita) {
                    throw $e;
                }
            }
        }

        // O vizita e a adresei de la care a pornit; altcineva nu-i poate umfla durata.
        if ($vizita->ip !== $ip) {
            return response()->noContent();
        }

        $vizita->pagini = array_values(array_unique(array_merge($vizita->pagini ?: [], $pagini)));
        $vizita->durata_secunde = max($vizita->durata_secunde, $secunde);
        $vizita->updated_at = now();
        $vizita->save();

        return response()->noContent();
    }

    /** Fila din Administrare: cifrele, vizitele una câte una și adunate pe adresă. */
    public function index(Request $request)
    {
        $zile = max(1, min(365, (int) $request->query('zile', 30)));
        $deLa = now()->subDays($zile - 1)->startOfDay();
        $ip = trim((string) $request->query('ip', ''));

        $intrebare = SiteVizita::with('contact:id,denumire,email')
            ->where('created_at', '>=', $deLa)
            ->orderByDesc('id');

        if ($ip !== '') {
            $intrebare->where('ip', $ip);
        }

        $pagina = $intrebare->paginate(self::PE_PAGINA);

        $peAdresa = SiteVizita::query()
            ->where('created_at', '>=', $deLa)
            ->groupBy('ip')
            ->orderByDesc('ultima')
            ->limit(200)
            ->get([
                'ip',
                DB::raw('count(*) as vizite'),
                DB::raw('sum(durata_secunde) as durata_secunde'),
                DB::raw('min(created_at) as prima'),
                DB::raw('max(created_at) as ultima'),
                DB::raw('max(contact_id) as contact_id'),
            ]);

        $firme = MarketingContact::whereIn('id', $peAdresa->pluck('contact_id')->filter()->unique())
            ->pluck('denumire', 'id');

        return response()->json([
            'success' => true,
            'zile' => $zile,
            'sumar' => [
                'azi' => $this->cifre(now()->startOfDay()),
                'sapte_zile' => $this->cifre(now()->subDays(6)->startOfDay()),
                'treizeci_zile' => $this->cifre(now()->subDays(29)->startOfDay()),
            ],
            'data' => collect($pagina->items())->map(function (SiteVizita $vizita) {
                return [
                    'id' => $vizita->id,
                    'cand' => $vizita->created_at->format('d.m.Y H:i'),
                    'ip' => $vizita->ip,
                    'firma' => optional($vizita->contact)->denumire,
                    'email' => optional($vizita->contact)->email,
                    'durata_secunde' => $vizita->durata_secunde,
                    'pagini' => $vizita->pagini ?: [],
                    'dispozitiv' => $vizita->dispozitiv,
                    'referrer' => $vizita->referrer,
                    'user_agent' => $vizita->user_agent,
                ];
            })->all(),
            'total' => $pagina->total(),
            'pagina' => $pagina->currentPage(),
            'pe_adresa' => $peAdresa->map(function ($rand) use ($firme) {
                return [
                    'ip' => $rand->ip,
                    'vizite' => (int) $rand->vizite,
                    'durata_secunde' => (int) $rand->durata_secunde,
                    'prima' => date('d.m.Y H:i', strtotime($rand->prima)),
                    'ultima' => date('d.m.Y H:i', strtotime($rand->ultima)),
                    'firma' => $rand->contact_id ? ($firme[$rand->contact_id] ?? null) : null,
                ];
            })->all(),
        ]);
    }

    /**
     * Vizitele mai vechi de un an se șterg: atât spune politica de pe site că
     * le ținem. Se face din când în când, la deschiderea unei vizite noi, ca să
     * nu atârne de un program pornit pe server.
     */
    protected function uitaCeleVechi(): void
    {
        if (random_int(1, 50) !== 1) {
            return;
        }

        SiteVizita::where('created_at', '<', now()->subMonths(self::LUNI_PASTRATE))->delete();
    }

    /**
     * Câte vizite, de la câte adrese și cât au stat în medie, de la o zi încoace.
     *
     * @return array{vizite: int, adrese: int, durata_medie: int}
     */
    protected function cifre($deLa): array
    {
        $rand = SiteVizita::where('created_at', '>=', $deLa)
            ->selectRaw('count(*) as vizite, count(distinct ip) as adrese, coalesce(avg(durata_secunde), 0) as medie')
            ->first();

        return [
            'vizite' => (int) $rand->vizite,
            'adrese' => (int) $rand->adrese,
            'durata_medie' => (int) round($rand->medie),
        ];
    }

    /**
     * Numele paginilor deschise, curățate: numai litere, cifre și liniuțe.
     *
     * @param mixed $pagini
     * @return array<int, string>
     */
    protected function paginile($pagini): array
    {
        $curate = [];

        foreach ((array) $pagini as $pagina) {
            if (!is_string($pagina)) {
                continue;
            }

            $pagina = mb_substr(preg_replace('/[^a-z0-9_-]/i', '', $pagina), 0, 60);

            if ($pagina !== '' && !in_array($pagina, $curate, true)) {
                $curate[] = $pagina;
            }
        }

        return array_slice($curate, 0, 30);
    }

    /** De unde a venit, fără coada de parametri; site-ul însuși nu se numără. */
    protected function deUndeVine($referrer): ?string
    {
        $referrer = trim((string) $referrer);

        if ($referrer === '' || !preg_match('#^https?://#i', $referrer)) {
            return null;
        }

        $gazda = (string) parse_url($referrer, PHP_URL_HOST);
        $site = (string) parse_url((string) config('prezentare.site'), PHP_URL_HOST);

        if ($gazda === '' || ($site !== '' && strcasecmp($gazda, $site) === 0)) {
            return null;
        }

        return mb_substr($gazda . (string) parse_url($referrer, PHP_URL_PATH), 0, 300);
    }

    /** Firma căreia i-am scris, după codul purtat de legătura din scrisoare. */
    protected function contactul($cod): ?int
    {
        $cod = (string) $cod;

        if (!preg_match('/^[A-Za-z0-9]{20,64}$/', $cod)) {
            return null;
        }

        return MarketingContact::where('jeton', $cod)->value('id');
    }

    protected function dispozitivul(string $agent): string
    {
        if (preg_match('/ipad|tablet/i', $agent)) {
            return 'tabletă';
        }

        if (preg_match('/mobi|iphone|android/i', $agent)) {
            return 'telefon';
        }

        return 'calculator';
    }
}
