<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlertaMesajSpv;
use App\Models\AnafCertificat;
use App\Models\AnafSocietate;
use App\Models\SpvMesaj;
use App\Services\Anaf\Format;
use App\Services\Anaf\Jurnal;
use Illuminate\Http\Request;

/**
 * Instiintarile pe email cand intra in SPV un anumit fel de document.
 *
 * Interfata primeste odata cu lista si optiunile din care se alege:
 * certificatele clientului, firmele inrolate fiecaruia si tipurile de document
 * vazute pana acum in mesaje — asa nu trebuie ghicita denumirea exacta.
 */
class AlerteMesajeController extends Controller
{
    public function index()
    {
        $alerte = AlertaMesajSpv::with('certificat')->orderBy('email')->get()
            ->map(function (AlertaMesajSpv $alerta) {
                return $this->prezinta($alerta);
            });

        return response()->json([
            'success' => true,
            'data' => $alerte,
            'certificate' => AnafCertificat::orderBy('cn')->get()->map(function (AnafCertificat $certificat) {
                // Cele scoase din uz raman in lista, insemnate: pe ele pot sta
                // alerte facute mai demult, iar altfel ar aparea fara nume.
                return ['id' => $certificat->id, 'cn' => $certificat->cn, 'activ' => (bool) $certificat->activ];
            }),
            'societati' => AnafSocietate::orderBy('denumire')->get()->map(function (AnafSocietate $societate) {
                return [
                    'cif' => $societate->cif,
                    'denumire' => $societate->denumire,
                    'certificat_id' => $societate->certificat_id,
                ];
            }),
            'tipuri' => $this->tipuriDeDocument(),
        ]);
    }

    /**
     * Felurile de documente din care se poate alege.
     *
     * Intai cele intalnite chiar in mesajele clientului — acelea sunt sigure —
     * apoi cele cunoscute din configuratie. ANAF nu publica un nomenclator, deci
     * campul ramane liber: daca vine un fel nou, se scrie de mana.
     *
     * @return array<int, array{valoare: string, vazut: bool}>
     */
    protected function tipuriDeDocument(): array
    {
        $aleClientului = SpvMesaj::query()->toateCertificatele()
            ->whereNotNull('tip')
            ->where('tip', '!=', '')
            ->distinct()
            ->orderBy('tip')
            ->pluck('tip')
            ->all();

        $tipuri = [];
        $vazute = [];

        foreach ($aleClientului as $tip) {
            $cheie = mb_strtoupper(trim($tip));

            if ($cheie === '' || isset($vazute[$cheie])) {
                continue;
            }

            $vazute[$cheie] = true;
            $tipuri[] = ['valoare' => $tip, 'vazut' => true];
        }

        foreach (config('anaf.spv.tipuri_mesaje', []) as $tip) {
            $cheie = mb_strtoupper(trim($tip));

            if (isset($vazute[$cheie])) {
                continue;
            }

            $vazute[$cheie] = true;
            $tipuri[] = ['valoare' => $tip, 'vazut' => false];
        }

        return $tipuri;
    }

    public function store(Request $request)
    {
        $date = $this->valideaza($request);

        $alerta = AlertaMesajSpv::create(array_merge($date, [
            'user_id' => optional($request->user())->id,
        ]));

        Jurnal::scrie(
            'alerta_spv',
            'A creat o alertă pe email către ' . $alerta->email . ' pentru „' . ($alerta->tip_document ?: 'orice document') . '”',
            $date
        );

        return response()->json(['success' => true, 'data' => $this->prezinta($alerta->fresh())], 201);
    }

    public function update(Request $request, AlertaMesajSpv $alerta)
    {
        $alerta->update($this->valideaza($request));

        return response()->json(['success' => true, 'data' => $this->prezinta($alerta->fresh())]);
    }

    public function destroy(AlertaMesajSpv $alerta)
    {
        $email = $alerta->email;
        $alerta->delete();

        Jurnal::scrie('alerta_spv', 'A șters alerta pe email către ' . $email);

        return response()->json(['success' => true]);
    }

    protected function valideaza(Request $request): array
    {
        $date = $request->validate([
            'email' => 'required|email|max:191',
            'certificat_id' => 'nullable|exists:anaf_certificate,id',
            'tip_document' => 'nullable|string|max:100',
            'cif' => 'nullable|string|max:20',
            'activ' => 'nullable|boolean',
        ]);

        // Sirurile goale din formular inseamna „oricare", nu text gol.
        foreach (['certificat_id', 'tip_document', 'cif'] as $camp) {
            if (array_key_exists($camp, $date) && $date[$camp] === '') {
                $date[$camp] = null;
            }
        }

        $date['activ'] = !array_key_exists('activ', $date) || (bool) $date['activ'];

        return $date;
    }

    protected function prezinta(AlertaMesajSpv $alerta): array
    {
        return [
            'id' => $alerta->id,
            'email' => $alerta->email,
            'certificat_id' => $alerta->certificat_id,
            'certificat_nume' => optional($alerta->certificat)->cn,
            'tip_document' => $alerta->tip_document,
            'cif' => $alerta->cif,
            'activ' => $alerta->activ,
            'trimise' => $alerta->trimise,
            'ultima_alerta_la' => Format::dataOra($alerta->ultima_alerta_la),
        ];
    }
}
