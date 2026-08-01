<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnafCertificat;
use App\Models\CertificatUtilizator;
use App\Models\Company;
use App\Models\User;
use App\Services\AccesIp;
use App\Services\Anaf\Format;
use App\Services\Anaf\Jurnal;
use App\Support\ContextCompanie;
use App\Support\ContextUtilizator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Conturile din firma clientului, gestionate de administratorul firmei.
 *
 * Fiecare cont primeste certificatele digitale la care are drept: de ele tine
 * ce vede in modul — mesajele din SPV ale acelor certificate. Declaratiile si
 * solicitarile le vede doar pe ale lui, indiferent de certificat.
 */
class UtilizatoriClientController extends Controller
{
    public function index()
    {
        $client = $this->clientul();

        $legaturi = CertificatUtilizator::with('certificat')->get();

        $utilizatori = $client->users()->orderBy('name')->get()->map(function (User $user) use ($client, $legaturi) {
            return $this->prezinta($user, $client, $legaturi);
        });

        return response()->json([
            'success' => true,
            'data' => $utilizatori,
            'certificate' => AnafCertificat::orderBy('cn')->get()->map(function (AnafCertificat $certificat) {
                return ['id' => $certificat->id, 'cn' => $certificat->cn, 'activ' => (bool) $certificat->activ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $client = $this->clientul();

        $date = $request->validate([
            'nume' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'parola' => 'required|string|min:8|max:191',
            'telefon' => 'nullable|string|max:45',
            'administrator' => 'nullable|boolean',
            // Semnatura e a omului cu tokenul, depunerea nu se mai poate lua inapoi
            'poate_semna' => 'nullable|boolean',
            'poate_depune' => 'nullable|boolean',
            'certificate' => 'nullable|array',
            'certificate.*' => 'integer|exists:anaf_certificate,id',
            'imprimanta' => 'nullable|string|max:191',
            'imprimanta_certificat_id' => 'nullable|exists:anaf_certificate,id',
            'ip_permise' => 'nullable|string|max:2000',
        ]);

        $user = User::create([
            'name' => $date['nume'],
            'email' => $date['email'],
            'password' => Hash::make($date['parola']),
            'telefon' => $date['telefon'] ?? null,
            'imprimanta' => $date['imprimanta'] ?? null,
            'imprimanta_certificat_id' => $date['imprimanta_certificat_id'] ?? null,
            'ip_permise' => $date['ip_permise'] ?? null,
            'user_type' => 'user',
            'blocat' => 'Nu',
            'status' => 'activ',
        ]);

        $client->users()->attach($user->id, [
            'administrator' => !empty($date['administrator']),
            'poate_semna' => !empty($date['poate_semna']),
            'poate_depune' => !empty($date['poate_depune']),
        ]);

        $this->potrivesteCertificatele($user, $date['certificate'] ?? []);

        Jurnal::scrie(
            'utilizatori_client',
            'A creat contul ' . $user->email . ' în firma „' . $client->denumire . '”',
            ['user_id' => $user->id]
        );

        return response()->json(['success' => true, 'data' => $this->prezinta($user, $client)], 201);
    }

    public function update(Request $request, User $utilizator)
    {
        $client = $this->clientul();
        $this->verificaApartenenta($utilizator, $client);

        $date = $request->validate([
            'nume' => 'nullable|string|max:191',
            'email' => ['nullable', 'email', 'max:191', Rule::unique('users', 'email')->ignore($utilizator->id)],
            'telefon' => 'nullable|string|max:45',
            'parola' => 'nullable|string|min:8|max:191',
            'blocat' => 'nullable|boolean',
            'administrator' => 'nullable|boolean',
            // Semnatura e a omului cu tokenul, depunerea nu se mai poate lua inapoi
            'poate_semna' => 'nullable|boolean',
            'poate_depune' => 'nullable|boolean',
            'certificate' => 'nullable|array',
            'certificate.*' => 'integer|exists:anaf_certificate,id',
            'imprimanta' => 'nullable|string|max:191',
            'imprimanta_certificat_id' => 'nullable|exists:anaf_certificate,id',
            'ip_permise' => 'nullable|string|max:2000',
        ]);

        // O lista gresita, sau una care nu cuprinde adresa de acum, ar inchide
        // omul afara — la propriul cont, oprirea e obligatorie.
        if (array_key_exists('ip_permise', $date)) {
            $motiv = AccesIp::motivRefuz(
                $date['ip_permise'],
                (int) $utilizator->id === (int) optional($request->user())->id,
                AccesIp::adresaCererii($request)
            );

            if ($motiv) {
                return response()->json(['success' => false, 'message' => $motiv], 422);
            }
        }

        if (isset($date['nume'])) {
            $utilizator->name = $date['nume'];
        }

        foreach (['imprimanta', 'imprimanta_certificat_id', 'ip_permise'] as $camp) {
            if (array_key_exists($camp, $date)) {
                $utilizator->$camp = $date[$camp] ?: null;
            }
        }

        if (isset($date['email'])) {
            $utilizator->email = $date['email'];
        }

        if (array_key_exists('telefon', $date)) {
            $utilizator->telefon = $date['telefon'];
        }

        if (!empty($date['parola'])) {
            $utilizator->password = Hash::make($date['parola']);
        }

        if (array_key_exists('blocat', $date)) {
            // Administratorul firmei nu se poate bloca pe el insusi: firma ar
            // ramane fara nimeni care sa poata debloca pe cineva.
            if ($date['blocat'] && (int) $utilizator->id === (int) optional($request->user())->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nu vă puteți bloca propriul cont.',
                ], 422);
            }

            $utilizator->blocat = $date['blocat'] ? 'Da' : 'Nu';
        }

        $utilizator->save();

        if (!empty($date['blocat'])) {
            foreach ($utilizator->tokens as $token) {
                $token->delete();
            }
        }

        $drepturi = [];

        foreach (['administrator', 'poate_semna', 'poate_depune'] as $drept) {
            if (array_key_exists($drept, $date)) {
                $drepturi[$drept] = (bool) $date[$drept];
            }
        }

        if ($drepturi !== []) {
            DB::table('company_user')
                ->where('user_id', $utilizator->id)
                ->where('company_id', $client->id)
                ->update($drepturi);
        }

        if (array_key_exists('certificate', $date)) {
            $this->potrivesteCertificatele($utilizator, $date['certificate'] ?? []);
        }

        Jurnal::scrie(
            'utilizatori_client',
            'A modificat contul ' . $utilizator->email
                . (array_key_exists('blocat', $date) ? ($date['blocat'] ? ' — blocat' : ' — deblocat') : ''),
            ['user_id' => $utilizator->id]
        );

        return response()->json(['success' => true, 'data' => $this->prezinta($utilizator->fresh(), $client)]);
    }

    /** Il scoate acum din aplicatie, fara sa-i blocheze contul. */
    public function deconecteaza(Request $request, User $utilizator)
    {
        $this->verificaApartenenta($utilizator, $this->clientul());

        $cate = 0;

        foreach ($utilizator->tokens as $token) {
            $token->delete();
            $cate++;
        }

        Jurnal::scrie(
            'utilizatori_client',
            'A deconectat contul ' . $utilizator->email . ' (' . $cate . ' sesiuni închise)',
            ['user_id' => $utilizator->id]
        );

        return response()->json(['success' => true, 'data' => ['sesiuni' => $cate]]);
    }

    /**
     * Certificatele la care are drept contul.
     *
     * Legatura se tine dupa email, ca sa functioneze si pentru persoane fara
     * cont inca; aici avem contul, deci se scriu amandoua.
     */
    protected function potrivesteCertificatele(User $user, array $certificate): void
    {
        $cerute = array_map('intval', $certificate);

        CertificatUtilizator::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->get()
            ->each(function (CertificatUtilizator $legatura) use ($cerute) {
                if (!in_array((int) $legatura->certificat_id, $cerute, true)) {
                    $legatura->delete();
                }
            });

        foreach ($cerute as $certificatId) {
            CertificatUtilizator::firstOrCreate(
                ['certificat_id' => $certificatId, 'email' => $user->email],
                ['user_id' => $user->id, 'nume' => $user->name, 'activ' => true]
            )->update(['user_id' => $user->id, 'activ' => true]);
        }
    }

    protected function clientul(): Company
    {
        $companie = ContextCompanie::curenta();

        abort_unless($companie, 400, 'Nu este selectată nicio societate.');

        return Company::findOrFail($companie);
    }

    protected function verificaApartenenta(User $utilizator, Company $client): void
    {
        $apartine = DB::table('company_user')
            ->where('user_id', $utilizator->id)
            ->where('company_id', $client->id)
            ->exists();

        abort_unless($apartine, 404, 'Utilizatorul nu face parte din această firmă.');
    }

    protected function prezinta(User $user, Company $client, $legaturi = null): array
    {
        $legaturi = $legaturi ?: CertificatUtilizator::with('certificat')->get();

        $aleLui = $legaturi->filter(function (CertificatUtilizator $legatura) use ($user) {
            return (int) $legatura->user_id === (int) $user->id
                || strcasecmp((string) $legatura->email, (string) $user->email) === 0;
        });

        $legatura = DB::table('company_user')
            ->where('user_id', $user->id)
            ->where('company_id', $client->id)
            ->first();

        return [
            'id' => $user->id,
            'nume' => $user->name,
            'email' => $user->email,
            'telefon' => $user->telefon,
            'blocat' => $user->blocat === 'Da',
            'administrator' => (bool) optional($legatura)->administrator,
            'poate_semna' => (bool) optional($legatura)->poate_semna,
            'poate_depune' => (bool) optional($legatura)->poate_depune,
            'imprimanta' => $user->imprimanta,
            'imprimanta_certificat_id' => $user->imprimanta_certificat_id,
            'ip_permise' => $user->ip_permise,
            'eu' => (int) $user->id === (int) optional(ContextUtilizator::curent())->id,
            'certificate' => $aleLui->map(function (CertificatUtilizator $legatura) {
                return [
                    'id' => (int) $legatura->certificat_id,
                    'cn' => optional($legatura->certificat)->cn,
                ];
            })->values()->all(),
            'creat_la' => Format::dataOra($user->created_at),
        ];
    }
}
