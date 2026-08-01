<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbonamentClient;
use App\Models\Company;
use App\Models\User;
use App\Services\AccesIp;
use App\Services\Anaf\Format;
use App\Services\Anaf\Jurnal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Administrarea clientilor aplicatiei: firme, conturi, module si abonamente.
 *
 * Zona e rezervata unui singur cont (middleware „administrator.serviciu"), asa
 * ca aici nu se mai filtreaza pe client — dimpotriva, se lucreaza peste toti.
 */
class AdministrareController extends Controller
{
    /** Clientii, cu abonamentul si conturile fiecaruia. */
    public function index()
    {
        $abonamente = AbonamentClient::all()->keyBy('company_id');

        $clienti = Company::with(['users' => function ($intrebare) {
            $intrebare->orderBy('name');
        }])->orderBy('denumire')->get()->map(function (Company $client) use ($abonamente) {
            return $this->prezinta($client, $abonamente->get($client->id));
        });

        return response()->json(['success' => true, 'data' => $clienti]);
    }

    /** Client nou, cu primul lui cont de administrator. */
    public function creeazaClient(Request $request)
    {
        $date = $request->validate([
            'denumire' => 'required|string|max:191',
            'cui' => 'nullable|string|max:20',
            'email' => 'required|email|max:191|unique:users,email',
            'nume' => 'required|string|max:191',
            'parola' => 'required|string|min:8|max:191',
            'telefon' => 'nullable|string|max:45',
            'proba_zile' => 'nullable|integer|min:0|max:365',
        ]);

        $client = DB::transaction(function () use ($date) {
            $client = Company::create([
                'denumire' => $date['denumire'],
                'cui' => $date['cui'] ?? null,
            ]);

            $user = User::create([
                'name' => $date['nume'],
                'email' => $date['email'],
                'password' => Hash::make($date['parola']),
                'telefon' => $date['telefon'] ?? null,
                'user_type' => 'user',
                'blocat' => 'Nu',
                'status' => 'activ',
                // Fara ea, parola ar fi socotita expirata din prima zi.
                'data_expirare_parola' => dateFormatStocare(Carbon::today()->addMonths(3)),
            ]);

            // Primul cont al unui client este administratorul lui.
            $client->users()->attach($user->id, ['administrator' => true]);

            $zile = $date['proba_zile'] ?? 30;

            AbonamentClient::create([
                'company_id' => $client->id,
                'proba_zile' => $zile,
                'proba_pana_la' => $zile > 0 ? now()->addDays($zile)->toDateString() : null,
                'modul_spv' => true,
            ]);

            return $client;
        });

        Jurnal::scrie(
            'administrare_client',
            'A creat clientul „' . $client->denumire . '” și contul de administrator ' . $date['email'],
            ['company_id' => $client->id]
        );

        return response()->json([
            'success' => true,
            'data' => $this->prezinta($client->fresh('users'), AbonamentClient::alClientului($client->id)),
        ], 201);
    }

    /** Cont nou in firma unui client. */
    public function creeazaUtilizator(Request $request, Company $client)
    {
        $date = $request->validate([
            'nume' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'parola' => 'required|string|min:8|max:191',
            'telefon' => 'nullable|string|max:45',
            'administrator' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name' => $date['nume'],
            'email' => $date['email'],
            'password' => Hash::make($date['parola']),
            'telefon' => $date['telefon'] ?? null,
            'user_type' => 'user',
            'blocat' => 'Nu',
            'status' => 'activ',
            // Fara ea, parola ar fi socotita expirata din prima zi.
            'data_expirare_parola' => dateFormatStocare(Carbon::today()->addMonths(3)),
        ]);

        $client->users()->attach($user->id, ['administrator' => !empty($date['administrator'])]);

        Jurnal::scrie(
            'administrare_utilizator',
            'A creat contul ' . $user->email . ' pentru clientul „' . $client->denumire . '”',
            ['company_id' => $client->id, 'user_id' => $user->id]
        );

        return response()->json(['success' => true, 'data' => $this->prezintaUtilizator($user, $client)], 201);
    }

    /**
     * Blocare, deblocare, drept de administrator, parola noua.
     *
     * Blocarea nu sterge nimic: contul ramane, cu tot ce a lucrat, dar nu mai
     * poate intra. Tokenurile deschise se sting odata cu ea, altfel ar continua
     * sa lucreze pana la expirarea lor.
     */
    public function actualizeazaUtilizator(Request $request, User $utilizator)
    {
        $date = $request->validate([
            'nume' => 'nullable|string|max:191',
            'email' => ['nullable', 'email', 'max:191', Rule::unique('users', 'email')->ignore($utilizator->id)],
            'telefon' => 'nullable|string|max:45',
            'parola' => 'nullable|string|min:8|max:191',
            'blocat' => 'nullable|boolean',
            'administrator' => 'nullable|boolean',
            'company_id' => 'nullable|exists:companies,id',
            'ip_permise' => 'nullable|string|max:2000',
        ]);

        // Nici administratorul aplicatiei nu are voie sa se inchida singur afara.
        if (array_key_exists('ip_permise', $date)) {
            $motiv = AccesIp::motivRefuz(
                $date['ip_permise'],
                (int) $utilizator->id === (int) optional($request->user())->id,
                AccesIp::adresaCererii($request)
            );

            if ($motiv) {
                return response()->json(['success' => false, 'message' => $motiv], 422);
            }

            $utilizator->ip_permise = $date['ip_permise'] ?: null;
        }

        if (isset($date['nume'])) {
            $utilizator->name = $date['nume'];
        }

        if (isset($date['email'])) {
            $utilizator->email = $date['email'];
        }

        if (array_key_exists('telefon', $date)) {
            $utilizator->telefon = $date['telefon'];
        }

        if (trim((string) ($date['parola'] ?? '')) !== '') {
            $utilizator->password = Hash::make($date['parola']);
            $utilizator->data_expirare_parola = dateFormatStocare(Carbon::today()->addMonths(3));
        }

        if (array_key_exists('blocat', $date)) {
            $utilizator->blocat = $date['blocat'] ? 'Da' : 'Nu';
        }

        $utilizator->save();

        if (!empty($date['blocat'])) {
            $this->stingeTokenurile($utilizator);
        }

        // Dreptul de administrator e per client, deci are nevoie de client.
        if (array_key_exists('administrator', $date) && !empty($date['company_id'])) {
            DB::table('company_user')
                ->where('user_id', $utilizator->id)
                ->where('company_id', $date['company_id'])
                ->update(['administrator' => (bool) $date['administrator']]);
        }

        Jurnal::scrie(
            'administrare_utilizator',
            'A modificat contul ' . $utilizator->email
                . (array_key_exists('blocat', $date) ? ($date['blocat'] ? ' — blocat' : ' — deblocat') : ''),
            ['user_id' => $utilizator->id]
        );

        return response()->json(['success' => true, 'data' => $this->prezintaUtilizator($utilizator->fresh())]);
    }

    /** Il scoate din aplicatie acum: tokenurile lui nu mai sunt bune. */
    public function deconecteaza(User $utilizator)
    {
        $cate = $this->stingeTokenurile($utilizator);

        Jurnal::scrie(
            'administrare_utilizator',
            'A deconectat contul ' . $utilizator->email . ' (' . $cate . ' sesiuni închise)',
            ['user_id' => $utilizator->id]
        );

        return response()->json(['success' => true, 'data' => ['sesiuni' => $cate]]);
    }

    /** Tariful, perioada de proba, plata si modulele acordate. */
    public function salveazaAbonament(Request $request, Company $client)
    {
        $date = $request->validate([
            'tarif_lunar' => 'nullable|numeric|min:0|max:999999',
            'proba_zile' => 'nullable|integer|min:0|max:365',
            'proba_pana_la' => 'nullable|date',
            'platit_pana_la' => 'nullable|date',
            'blocat' => 'nullable|boolean',
            'motiv_blocare' => 'nullable|string|max:255',
            'modul_spv' => 'nullable|boolean',
            'modul_etransport' => 'nullable|boolean',
            'modul_portal_just' => 'nullable|boolean',
            'observatii' => 'nullable|string|max:2000',
        ]);

        $abonament = AbonamentClient::firstOrNew(['company_id' => $client->id]);

        /*
         * „proba_zile" e comanda, „proba_pana_la" e urmarea ei: cand se schimba
         * numarul de zile, data se recalculeaza de azi. Asa administratorul
         * scrie „mai da-i 15 zile" fara sa socoteasca el data.
         */
        if (array_key_exists('proba_zile', $date) && (int) $date['proba_zile'] !== (int) $abonament->proba_zile) {
            $abonament->proba_zile = (int) $date['proba_zile'];
            $abonament->proba_pana_la = $abonament->proba_zile > 0
                ? now()->addDays($abonament->proba_zile)->toDateString()
                : null;
        }

        // O data scrisa direct are ultimul cuvant.
        if (array_key_exists('proba_pana_la', $date)) {
            $abonament->proba_pana_la = $date['proba_pana_la'];
        }

        foreach (['tarif_lunar', 'platit_pana_la', 'motiv_blocare', 'observatii'] as $camp) {
            if (array_key_exists($camp, $date)) {
                $abonament->$camp = $date[$camp];
            }
        }

        foreach (['blocat', 'modul_spv', 'modul_etransport', 'modul_portal_just'] as $camp) {
            if (array_key_exists($camp, $date)) {
                $abonament->$camp = (bool) $date[$camp];
            }
        }

        $abonament->company_id = $client->id;
        $abonament->save();

        Jurnal::scrie(
            'administrare_abonament',
            'A actualizat abonamentul clientului „' . $client->denumire . '”',
            $date
        );

        return response()->json([
            'success' => true,
            'data' => $this->prezinta($client->fresh('users'), $abonament->fresh()),
        ]);
    }

    /** Scoate toate tokenurile utilizatorului si intoarce cate au fost. */
    protected function stingeTokenurile(User $utilizator): int
    {
        $cate = 0;

        foreach ($utilizator->tokens as $token) {
            $token->delete();
            $cate++;
        }

        return $cate;
    }

    protected function prezinta(Company $client, ?AbonamentClient $abonament): array
    {
        return [
            'id' => $client->id,
            'denumire' => $client->denumire,
            'cui' => $client->cui,
            'abonament' => $abonament ? [
                'tarif_lunar' => $abonament->tarif_lunar,
                'proba_zile' => $abonament->proba_zile,
                'proba_pana_la' => optional($abonament->proba_pana_la)->format('Y-m-d'),
                'platit_pana_la' => optional($abonament->platit_pana_la)->format('Y-m-d'),
                'blocat' => $abonament->blocat,
                'motiv_blocare' => $abonament->motiv_blocare,
                'modul_spv' => $abonament->modul_spv,
                'modul_etransport' => $abonament->modul_etransport,
                'modul_portal_just' => $abonament->modul_portal_just,
                'observatii' => $abonament->observatii,
                'activ' => $abonament->activ(),
                'in_proba' => $abonament->inProba(),
                'zile_ramase' => $abonament->zileRamase(),
                'motiv' => $abonament->motiv(),
            ] : null,
            'utilizatori' => $client->users->map(function (User $user) use ($client) {
                return $this->prezintaUtilizator($user, $client);
            })->all(),
        ];
    }

    protected function prezintaUtilizator(User $user, ?Company $client = null): array
    {
        $administrator = false;

        if ($client) {
            $administrator = $user->pivot
                ? (bool) $user->pivot->administrator
                : DB::table('company_user')
                    ->where('user_id', $user->id)
                    ->where('company_id', $client->id)
                    ->value('administrator') == true;
        }

        return [
            'id' => $user->id,
            'nume' => $user->name,
            'email' => $user->email,
            'telefon' => $user->telefon,
            'blocat' => $user->blocat === 'Da',
            'administrator' => $administrator,
            'ip_permise' => $user->ip_permise,
            'creat_la' => Format::dataOra($user->created_at),
        ];
    }
}
