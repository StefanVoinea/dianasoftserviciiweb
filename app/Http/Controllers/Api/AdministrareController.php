<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbonamentClient;
use App\Models\Company;
use App\Models\DianaSoftMenuOption;
use App\Models\User;
use App\Support\Modul;
use App\Services\AccesIp;
use App\Services\Anaf\Format;
use App\Services\Anaf\ImportVectorMf;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\VectorMde;
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

        return response()->json([
            'success' => true,
            'data' => $clienti,
            'module' => Modul::lista(),
        ]);
    }

    /**
     * Statistici despre toti clientii: cat au depus cu aplicatia si cand au
     * folosit-o ultima oara.
     *
     * Se numara doar depunerile facute cu aplicatia (cu data depunerii si
     * certificatul cu care s-a semnat), nu si istoricul importat din programul
     * vechi — acela ar umfla cifrele fara sa spuna nimic despre folosire.
     */
    public function statistici()
    {
        $inceputCurenta = now()->startOfMonth();
        $inceputAnterioara = now()->subMonthNoOverflow()->startOfMonth();

        $depuneri = DB::table('anaf_declaratii')
            ->selectRaw(
                'company_id,'
                . ' COUNT(*) as total,'
                . ' COUNT(DISTINCT cui) as cuiuri,'
                . ' SUM(CASE WHEN data_depunere >= ? THEN 1 ELSE 0 END) as luna_curenta,'
                . ' COUNT(DISTINCT CASE WHEN data_depunere >= ? THEN cui END) as cuiuri_luna_curenta,'
                . ' SUM(CASE WHEN data_depunere >= ? AND data_depunere < ? THEN 1 ELSE 0 END) as luna_anterioara,'
                . ' COUNT(DISTINCT CASE WHEN data_depunere >= ? AND data_depunere < ? THEN cui END) as cuiuri_luna_anterioara,'
                . ' MAX(data_depunere) as ultima_depunere',
                [
                    $inceputCurenta, $inceputCurenta,
                    $inceputAnterioara, $inceputCurenta,
                    $inceputAnterioara, $inceputCurenta,
                ]
            )
            ->whereNotNull('data_depunere')
            ->whereNotNull('certificat_id')
            ->whereNotNull('company_id')
            ->groupBy('company_id')
            ->get()
            ->keyBy('company_id');

        // Ultima logare: cel mai nou token de acces al vreunui cont al clientului.
        $logari = DB::table('oauth_access_tokens')
            ->join('company_user', 'company_user.user_id', '=', 'oauth_access_tokens.user_id')
            ->selectRaw('company_user.company_id, MAX(oauth_access_tokens.created_at) as ultima')
            ->groupBy('company_user.company_id')
            ->get()
            ->keyBy('company_id');

        // Ultima treaba facuta, din jurnalul de activitate.
        $jurnal = DB::table('anaf_jurnal')
            ->selectRaw('company_id, MAX(created_at) as ultima')
            ->whereNotNull('company_id')
            ->groupBy('company_id')
            ->get()
            ->keyBy('company_id');

        $clienti = Company::orderBy('denumire')->get()->map(function (Company $client) use ($depuneri, $logari, $jurnal) {
            $d = $depuneri->get($client->id);

            // Accesarea e ori o logare, ori o treaba facuta: cea mai noua dintre ele.
            $accesari = array_filter([
                optional($logari->get($client->id))->ultima,
                optional($jurnal->get($client->id))->ultima,
            ]);

            return [
                'id' => $client->id,
                'denumire' => $client->denumire,
                'cui' => $client->cui,
                'declaratii' => $d ? (int) $d->total : 0,
                'cuiuri' => $d ? (int) $d->cuiuri : 0,
                'declaratii_luna_curenta' => $d ? (int) $d->luna_curenta : 0,
                'cuiuri_luna_curenta' => $d ? (int) $d->cuiuri_luna_curenta : 0,
                'declaratii_luna_anterioara' => $d ? (int) $d->luna_anterioara : 0,
                'cuiuri_luna_anterioara' => $d ? (int) $d->cuiuri_luna_anterioara : 0,
                'ultima_depunere' => $d ? Format::dataOra($d->ultima_depunere) : null,
                'ultima_accesare' => $accesari !== [] ? Format::dataOra(max($accesari)) : null,
            ];
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
            'module' => 'nullable|array',
            'module.*' => 'string|in:' . implode(',', Modul::chei()),
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

            /*
             * Primul cont al unui client este administratorul lui.
             *
             * Drepturile se scriu toate, nu se lasa pe seama valorii implicite a
             * coloanei: pe alt server ea poate lipsi, iar legatura n-ar mai putea
             * fi scrisa deloc.
             */
            $client->users()->attach($user->id, [
                'administrator' => true,
                'poate_semna' => true,
                'poate_depune' => true,
            ]);

            /*
             * Fara module, contul intra intr-o aplicatie fara nimic si n-are ce
             * face acolo. Cand nu s-a ales nimic anume, administratorul primeste
             * SPV Curier — acelasi modul cu care porneste si abonamentul de mai
             * jos.
             */
            $this->potriveasteModulele($user, $client, $date['module'] ?? ['spv']);

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
    /**
     * Importa periodicitatile declaratiilor din programul vechi al clientului.
     *
     * Se primeste chiar fisierul vector.mde (sau CSV-ul tabelului vectormf,
     * pentru serverele care nu pot citi Access). Randurile intra in
     * vector_declaratii ca "manuala" — cuvantul omului, care bate deductia —
     * si tin de clientul de pe randul apasat, nu de vreun context.
     */
    public function importaVector(Request $request, Company $client, VectorMde $mde, ImportVectorMf $import)
    {
        $request->validate([
            'fisier' => 'required|file|max:51200',
        ]);

        $fisier = $request->file('fisier');
        $extensie = strtolower($fisier->getClientOriginalExtension() ?: 'mde');

        if (!in_array($extensie, ['mde', 'mdb', 'accdb', 'csv'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Se acceptă fișiere Access (.mde, .mdb, .accdb) sau CSV-ul tabelului vectormf.',
            ], 422);
        }

        // Fisierul urcat are nume trecator, fara extensie; ea decide drumul.
        $cale = $fisier->getRealPath() . '.' . $extensie;
        copy($fisier->getRealPath(), $cale);

        try {
            $rezultat = $import->importaCsv($mde->inCsv($cale), $client->id);
        } catch (\Exception $e) {
            Jurnal::esec(
                'vector_import',
                'Importul vectorului pentru „' . $client->denumire . '” a eșuat: ' . $e->getMessage()
            );

            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } finally {
            @unlink($cale);
        }

        Jurnal::scrie(
            'vector_import',
            sprintf(
                'A importat vectorul din %s pentru „%s”: %d firme, %d periodicități',
                $fisier->getClientOriginalName(),
                $client->denumire,
                $rezultat['firme'],
                $rezultat['scrise']
            )
        );

        return response()->json(['success' => true, 'data' => $rezultat]);
    }

    /**
     * Importa istoricul depunerilor din programul vechi al clientului.
     *
     * Se primeste fisierul declmf.mde (sau CSV-ul tabelului „depuneri").
     * Firmele inrolate fara denumire si-o primesc din tabel, depunerile intra
     * in fila Declaratii fiscale ca istoric incheiat, iar declaratiile si
     * recipisele — aflate pe calculatorul clientului — se copiaza in arhiva
     * printr-o lucrare in fundal, prin programul local.
     */
    /**
     * Anii din fisierul declmf.mde, cu numarul depunerilor bune din fiecare:
     * fereastra de import ii arata, iar omul alege ce ani sa aduca.
     */
    public function aniiDeclaratiilor(Request $request, Company $client, VectorMde $mde, \App\Services\Anaf\ImportDepuneri $import)
    {
        $request->validate(['fisier' => 'required|file|max:102400']);

        $fisier = $request->file('fisier');
        $extensie = strtolower($fisier->getClientOriginalExtension() ?: 'mde');

        if (!in_array($extensie, ['mde', 'mdb', 'accdb', 'csv'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Se acceptă fișiere Access (.mde, .mdb, .accdb) sau CSV-ul tabelului depuneri.',
            ], 422);
        }

        $cale = $fisier->getRealPath() . '.' . $extensie;
        copy($fisier->getRealPath(), $cale);

        try {
            $ani = $import->anii($mde->inCsv($cale, 'depuneri'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } finally {
            @unlink($cale);
        }

        return response()->json(['success' => true, 'data' => $ani]);
    }

    public function importaDeclaratii(Request $request, Company $client, VectorMde $mde, \App\Services\Anaf\ImportDepuneri $import)
    {
        $request->validate([
            'fisier' => 'required|file|max:102400',
            'ani' => 'nullable|array',
            'ani.*' => 'integer',
        ]);

        $fisier = $request->file('fisier');
        $extensie = strtolower($fisier->getClientOriginalExtension() ?: 'mde');

        if (!in_array($extensie, ['mde', 'mdb', 'accdb', 'csv'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Se acceptă fișiere Access (.mde, .mdb, .accdb) sau CSV-ul tabelului depuneri.',
            ], 422);
        }

        $cale = $fisier->getRealPath() . '.' . $extensie;
        copy($fisier->getRealPath(), $cale);

        try {
            $rezultat = $import->importaCsv(
                $mde->inCsv($cale, 'depuneri'),
                $client->id,
                array_map('intval', $request->input('ani', []))
            );
        } catch (\Exception $e) {
            Jurnal::esec(
                'import_depuneri',
                'Importul depunerilor pentru „' . $client->denumire . '” a eșuat: ' . $e->getMessage()
            );

            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } finally {
            @unlink($cale);
        }

        /*
         * Arhivarea merge in loturi mici, cate o lucrare de coada fiecare: una
         * singura cu mii de copieri ar depasi timpul ingaduit unei lucrari si
         * ar fi declarata picata la mijlocul treburilor.
         */
        foreach (array_chunk($rezultat['de_arhivat'], 50) as $lot) {
            \App\Jobs\ArhiveazaDepunerileImportate::dispatch($client->id, $lot);
        }

        Jurnal::scrie(
            'import_depuneri',
            sprintf(
                'A importat depunerile din %s pentru „%s”: %d rânduri, %d declarații noi, %d denumiri completate,'
                    . ' %d depuneri de arhivat',
                $fisier->getClientOriginalName(),
                $client->denumire,
                $rezultat['randuri'],
                $rezultat['scrise'],
                $rezultat['denumiri'],
                count($rezultat['de_arhivat'])
            )
        );

        return response()->json([
            'success' => true,
            'data' => [
                'randuri' => $rezultat['randuri'],
                'scrise' => $rezultat['scrise'],
                'existente' => $rezultat['existente'],
                'respinse' => $rezultat['respinse'],
                'sterse' => $rezultat['sterse'],
                'in_alti_ani' => $rezultat['in_alti_ani'],
                'denumiri' => $rezultat['denumiri'],
                'de_arhivat' => count($rezultat['de_arhivat']),
                'sarite' => $rezultat['sarite'],
            ],
        ]);
    }

    public function creeazaUtilizator(Request $request, Company $client)
    {
        $date = $request->validate([
            'nume' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'parola' => 'required|string|min:8|max:191',
            'telefon' => 'nullable|string|max:45',
            'administrator' => 'nullable|boolean',
            // Modulele la care are acces: intrările din meniu pe care le va vedea
            'module' => 'nullable|array',
            'module.*' => 'string|in:' . implode(',', Modul::chei()),
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

        $administrator = !empty($date['administrator']);

        $client->users()->attach($user->id, [
            'administrator' => $administrator,
            // Semnarea si depunerea nu se dau din greseala: doar administratorul
            // le are din start, ceilalti le primesc anume, din „Utilizatori".
            'poate_semna' => $administrator,
            'poate_depune' => $administrator,
        ]);

        $module = $this->potriveasteModulele($user, $client, $date['module'] ?? []);

        Jurnal::scrie(
            'administrare_utilizator',
            'A creat contul ' . $user->email . ' pentru clientul „' . $client->denumire . '”'
                . ($module === [] ? ', fără niciun modul' : ', cu acces la: ' . implode(', ', $module)),
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
            'module' => 'nullable|array',
            'module.*' => 'string|in:' . implode(',', Modul::chei()),
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

        /*
         * Modulele sunt si ele per client — acelasi om poate lucra la doua firme,
         * cu meniuri deosebite — deci se schimba doar cand se stie in care.
         */
        $client = array_key_exists('module', $date) && !empty($date['company_id'])
            ? Company::find($date['company_id'])
            : null;

        $module = $client ? $this->potriveasteModulele($utilizator, $client, $date['module'] ?: []) : null;

        Jurnal::scrie(
            'administrare_utilizator',
            'A modificat contul ' . $utilizator->email
                . (array_key_exists('blocat', $date) ? ($date['blocat'] ? ' — blocat' : ' — deblocat') : '')
                . ($module === null ? '' : ' — module: ' . ($module === [] ? 'niciunul' : implode(', ', $module))),
            ['user_id' => $utilizator->id]
        );

        return response()->json([
            'success' => true,
            'data' => $this->prezintaUtilizator($utilizator->fresh(), $client),
        ]);
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

    /**
     * Scrie modulele date contului si intoarce numele lor.
     *
     * Sunt doua scrieri, pentru ca sunt doua feluri de a le arata:
     *
     *   - darea propriu-zisa, in „company_user.module": dupa ea se face antetul
     *     cu siglele si tot dupa ea opreste middleware-ul cererile;
     *   - intrarile din meniul din stanga care tin de modulele acelea, in
     *     legatura om–optiune. Fara ele, omul ar avea modulul, dar meniul gol.
     *
     * @param  array<int, string>  $chei  cheile modulelor bifate
     * @return array<int, string> numele modulelor date acum
     */
    protected function potriveasteModulele(User $user, Company $client, array $chei): array
    {
        $chei = array_values(array_intersect($chei, Modul::chei()));

        Modul::scrie($user->id, $client->id, $chei);

        $toate = DianaSoftMenuOption::all();
        $sluguri = Modul::slugurileMeniului($chei);

        $deschise = $this->cuTotCeEDedesubt(
            $toate,
            $toate->whereIn('slug', $sluguri)->pluck('id')->map(function ($id) {
                return (int) $id;
            })->all()
        );

        DB::table('dianasoftmenuoption_user')
            ->where('user_id', $user->id)
            ->where('company_id', $client->id)
            ->delete();

        $randuri = [];

        foreach ($toate as $optiune) {
            $randuri[] = [
                'user_id' => $user->id,
                'dianasoftmenuoption_id' => $optiune->id,
                'company_id' => $client->id,
                'isactive' => in_array((int) $optiune->id, $deschise, true),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($randuri !== []) {
            DB::table('dianasoftmenuoption_user')->insert($randuri);
        }

        return array_map(function ($cheie) {
            return Modul::CATALOG[$cheie]['nume'];
        }, $chei);
    }

    /**
     * Modulele alese, cu tot ce se afla sub ele.
     *
     * Legatura de parinte se tine pe nume, nu pe id („Loguri" sta sub „Util"),
     * asa ca se coboara din nume in nume. Fara asta, cine primeste „Util" ar
     * vedea o intrare de meniu goala.
     *
     * @param  array<int, int>  $alese
     * @return array<int, int>
     */
    protected function cuTotCeEDedesubt($toate, array $alese): array
    {
        $copiii = [];

        foreach ($toate as $optiune) {
            $copiii[(string) $optiune->parent][] = $optiune;
        }

        $active = [];
        $deCercetat = $toate->whereIn('id', $alese)->values()->all();

        while ($deCercetat !== []) {
            $optiune = array_pop($deCercetat);

            if (in_array((int) $optiune->id, $active, true)) {
                continue;
            }

            $active[] = (int) $optiune->id;

            foreach (isset($copiii[(string) $optiune->name]) ? $copiii[(string) $optiune->name] : [] as $copil) {
                $deCercetat[] = $copil;
            }
        }

        return $active;
    }

    /**
     * Modulele pe care contul le are acum in firma clientului.
     *
     * Contul caruia nu i s-a ales nimic anume — cele facute inainte de bifele
     * acestea — are tot ce cuprinde abonamentul; asa se si arata in fereastra.
     *
     * @return array<int, string>
     */
    protected function moduleleContului(User $user, Company $client): array
    {
        $aleLui = Modul::aleContului($user->id, $client->id);

        return $aleLui === null
            ? Modul::vazuteDe($user->id, $client->id)
            : $aleLui;
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
            'module' => $client ? $this->moduleleContului($user, $client) : [],
            'creat_la' => Format::dataOra($user->created_at),
        ];
    }
}
