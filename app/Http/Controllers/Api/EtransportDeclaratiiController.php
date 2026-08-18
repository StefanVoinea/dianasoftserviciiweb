<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EtransportCodVamal;
use App\Models\EtransportDeclaratie;
use App\Services\Anaf\Etransport\DeclaratieXml;
use App\Services\Anaf\Etransport\EtransportClient;
use App\Services\Anaf\Etransport\EtransportException;
use App\Services\Anaf\Etransport\Import\ImportFisiere;
use App\Services\Anaf\Etransport\Nomenclatoare;
use App\Services\Anaf\Format;
use App\Services\Anaf\Jurnal;
use Illuminate\Http\Request;

/**
 * Declarațiile e-Transport lucrate în aplicație, de la fișierele furnizorului
 * până la UIT: import linii, completare, generare XML și depunere la ANAF.
 */
class EtransportDeclaratiiController extends Controller
{
    public function index(Request $request)
    {
        $query = EtransportDeclaratie::orderByDesc('id');

        if ($request->filled('stare')) {
            $query->where('stare', $request->query('stare'));
        }

        $declaratii = $query->limit((int) $request->query('limita', 200))->get()
            ->map(function (EtransportDeclaratie $d) {
                return [
                    'id' => $d->id,
                    'stare' => $d->stare,
                    'stare_eticheta' => EtransportDeclaratie::STARI[$d->stare] ?? $d->stare,
                    'poate_fi_modificata' => $d->poate_fi_modificata,
                    'cif_declarant' => $d->cif_declarant,
                    'tip_operatiune' => $d->tip_operatiune,
                    'operatiune' => Nomenclatoare::TIPURI_OPERATIUNE[$d->tip_operatiune] ?? null,
                    'partener' => $d->partener_denumire,
                    'vehicul' => trim(implode(' + ', array_filter([$d->nr_vehicul, $d->nr_remorca1, $d->nr_remorca2]))),
                    'data_transport' => Format::data($d->data_transport),
                    'nr_linii' => count($d->linii ?: []),
                    'valoare_lei' => round(array_sum(array_column($d->linii ?: [], 'valoare_lei')), 2),
                    'uit' => $d->uit,
                    'index_incarcare' => $d->index_incarcare,
                    'creata_la' => Format::dataOra($d->created_at),
                ];
            });

        return response()->json(['success' => true, 'data' => $declaratii]);
    }

    /** Nomenclatoarele fixe, pentru listele din formular. */
    public function nomenclatoare(Request $request)
    {
        return response()->json([
            'success' => true,
            'import_permis' => $this->importPermis($request),
            // Declarantul e de obicei chiar clientul: CIF-ul lui se pune din prima.
            'cif_implicit' => $this->cifClientului(),
            'tipuri_operatiune' => Nomenclatoare::TIPURI_OPERATIUNE,
            'scopuri' => Nomenclatoare::SCOPURI,
            'scopuri_pe_operatiune' => Nomenclatoare::SCOPURI_PE_OPERATIUNE,
            'traseu_pe_operatiune' => Nomenclatoare::TRASEU_PE_OPERATIUNE,
            'judete' => Nomenclatoare::JUDETE,
            'ptf' => Nomenclatoare::PTF,
            'birouri_vamale' => Nomenclatoare::BIROURI_VAMALE,
            'tipuri_document' => Nomenclatoare::TIPURI_DOCUMENT,
            'unitati_masura' => Nomenclatoare::UNITATI_MASURA,
            'tari' => Nomenclatoare::tari(),
        ]);
    }

    /** Autocomplete pe nomenclatorul codurilor vamale. */
    public function coduriVamale(Request $request)
    {
        $termen = trim((string) $request->query('q', ''));

        if (mb_strlen($termen) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        return response()->json([
            'success' => true,
            'data' => EtransportCodVamal::cauta($termen)->limit(30)
                ->get(['cod', 'denumire', 'denumire_scurta']),
        ]);
    }

    /** Cursul BNR pentru valuta si ziua ceruta (cel mai recent pana la acea zi). */
    public function curs(Request $request)
    {
        $date = $request->validate([
            'valuta' => 'required|string|size:3',
            'data' => 'required|date',
        ]);

        $curs = cursBNR(dateFormatStocare($date['data']), strtoupper($date['valuta']));

        return response()->json([
            'success' => (bool) $curs,
            'curs' => $curs ?: null,
            'message' => $curs ? null : 'Nu există curs BNR pentru ' . strtoupper($date['valuta']) . ' până la data cerută.',
        ]);
    }

    /**
     * Citește fișierele furnizorului și întoarce liniile și antetul găsite,
     * fără a atinge vreo declarație: formularul decide ce păstrează.
     */
    public function importa(Request $request, ImportFisiere $import)
    {
        if (!$this->importPermis($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Importul de fișiere nu e deschis pentru acest utilizator; liniile se completează în formular.',
            ], 403);
        }

        $request->validate([
            'fisiere' => 'required|array|min:1',
            'fisiere.*' => 'file|max:10240',
        ]);

        $fisiere = array_map(function ($fisier) {
            return ['nume' => $fisier->getClientOriginalName(), 'cale' => $fisier->getRealPath()];
        }, $request->file('fisiere'));

        try {
            $rezultat = $import->importa($fisiere, $request->boolean('grupate', true));
        } catch (EtransportException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'linii' => $rezultat['linii'],
            'antet' => $rezultat['antet'],
            'avertismente' => $rezultat['avertismente'],
            'fisiere' => array_column($fisiere, 'nume'),
        ]);
    }

    public function store(Request $request)
    {
        $declaratie = EtransportDeclaratie::create(
            $this->date($request) + ['user_id' => optional($request->user())->id]
        );

        return response()->json(['success' => true, 'data' => $this->detalii($declaratie)]);
    }

    public function show(EtransportDeclaratie $declaratie)
    {
        return response()->json(['success' => true, 'data' => $this->detalii($declaratie)]);
    }

    public function update(Request $request, EtransportDeclaratie $declaratie)
    {
        if (!$declaratie->poate_fi_modificata) {
            return response()->json([
                'success' => false,
                'message' => 'Declarația a fost depusă și nu se mai poate modifica.',
            ], 422);
        }

        $declaratie->update($this->date($request));

        return response()->json(['success' => true, 'data' => $this->detalii($declaratie->fresh())]);
    }

    public function destroy(EtransportDeclaratie $declaratie)
    {
        if (!$declaratie->poate_fi_modificata) {
            return response()->json([
                'success' => false,
                'message' => 'Declarația a fost depusă și nu se mai poate șterge.',
            ], 422);
        }

        $declaratie->delete();

        return response()->json(['success' => true]);
    }

    /** Construiește XML-ul, îl depune la ANAF și păstrează răspunsul (index, UIT). */
    public function depune(EtransportDeclaratie $declaratie, DeclaratieXml $xml, EtransportClient $client)
    {
        if (!$declaratie->poate_fi_modificata) {
            return response()->json([
                'success' => false,
                'message' => 'Declarația e deja depusă. Verificați starea pentru UIT.',
            ], 422);
        }

        try {
            $continut = $xml->construieste($declaratie);
            $raspuns = $client->upload($continut, $declaratie->cif_declarant);
        } catch (EtransportException $e) {
            Jurnal::esec(
                'etransport_declaratie',
                'Depunerea declarației e-Transport #' . $declaratie->id . ' a eșuat: ' . $e->getMessage(),
                [],
                $declaratie->cif_declarant
            );

            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $index = $raspuns['index_incarcare'] ?? null;
        $uit = $raspuns['UIT'] ?? ($raspuns['uit'] ?? null);
        $erori = $raspuns['Errors'] ?? ($raspuns['errors'] ?? []);

        $declaratie->update([
            'stare' => $index ? ($uit ? 'validata' : 'depusa') : 'respinsa',
            'index_incarcare' => $index,
            'uit' => $uit,
            'raspuns_anaf' => $raspuns,
            'depusa_la' => now(),
        ]);

        Jurnal::scrie(
            'etransport_declaratie',
            $index
                ? 'A depus declarația e-Transport #' . $declaratie->id . ' pentru ' . $declaratie->cif_declarant
                    . ($uit ? ', UIT ' . $uit : ', index de încărcare ' . $index)
                : 'ANAF a respins declarația e-Transport #' . $declaratie->id . ' pentru ' . $declaratie->cif_declarant,
            $raspuns,
            $declaratie->cif_declarant,
            (bool) $index
        );

        return response()->json([
            'success' => (bool) $index,
            'data' => $this->detalii($declaratie->fresh()),
            'erori' => $erori,
        ], $index ? 200 : 422);
    }

    /** Întreabă ANAF de soarta declarației depuse și reține UIT-ul când apare. */
    public function verifica(EtransportDeclaratie $declaratie, EtransportClient $client)
    {
        if (!$declaratie->index_incarcare) {
            return response()->json([
                'success' => false,
                'message' => 'Declarația nu a fost depusă încă.',
            ], 422);
        }

        try {
            $raspuns = $client->stareMesaj($declaratie->index_incarcare, $declaratie->cif_declarant);
        } catch (EtransportException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        $stare = mb_strtolower((string) ($raspuns['stare'] ?? ''));
        $uit = $raspuns['UIT'] ?? ($raspuns['uit'] ?? null);

        if ($uit || $stare === 'ok') {
            $declaratie->update([
                'stare' => 'validata',
                'uit' => $uit ?: $declaratie->uit,
                'raspuns_anaf' => $raspuns,
            ]);
        } elseif ($stare !== '' && strpos($stare, 'erori') !== false) {
            $declaratie->update(['stare' => 'respinsa', 'raspuns_anaf' => $raspuns]);
        }

        return response()->json([
            'success' => true,
            'data' => $this->detalii($declaratie->fresh()),
            'raspuns' => $raspuns,
        ]);
    }

    /** Trimite codul UIT pe email, șoferului sau partenerului. */
    public function trimiteEmail(Request $request, EtransportDeclaratie $declaratie)
    {
        if (!$declaratie->uit) {
            return response()->json([
                'success' => false,
                'message' => 'Declarația nu are încă un cod UIT de trimis.',
            ], 422);
        }

        $date = $request->validate(['adrese' => 'required|string|max:500']);

        $adrese = array_filter(array_map('trim', preg_split('/[,;\s]+/', $date['adrese'])));

        $gresite = array_filter($adrese, function ($adresa) {
            return !filter_var($adresa, FILTER_VALIDATE_EMAIL);
        });

        if ($adrese === [] || $gresite !== []) {
            return response()->json([
                'success' => false,
                'message' => $gresite !== []
                    ? 'Adresa „' . reset($gresite) . '" nu arată a email.'
                    : 'Scrieți cel puțin o adresă de email.',
            ], 422);
        }

        foreach ($adrese as $adresa) {
            \Illuminate\Support\Facades\Mail::to($adresa)->send(new \App\Mail\EtransportUitEmail($declaratie));
        }

        Jurnal::scrie(
            'etransport_declaratie',
            'A trimis codul UIT ' . $declaratie->uit . ' către ' . implode(', ', $adrese),
            ['adrese' => $adrese],
            $declaratie->cif_declarant
        );

        return response()->json([
            'success' => true,
            'message' => 'Codul UIT a plecat către ' . implode(', ', $adrese) . '.',
        ]);
    }

    /** Codul fiscal al clientului curent, doar cifrele (fără „RO"). */
    protected function cifClientului(): ?string
    {
        $companie = \App\Support\ContextCompanie::curenta();

        if (!$companie) {
            return null;
        }

        $cui = optional(\App\Models\Company::find($companie))->cui;

        return $cui ? preg_replace('/\D/', '', $cui) : null;
    }

    /**
     * Importul de fișiere e deschis doar utilizatorilor din configurație:
     * parserele sunt scrise pe formatele furnizorilor lor.
     */
    protected function importPermis(Request $request): bool
    {
        $email = mb_strtolower((string) optional($request->user())->email);

        $permise = array_filter(array_map('trim', explode(
            ',',
            mb_strtolower((string) config('anaf.etransport.import_emails'))
        )));

        return $email !== '' && in_array($email, $permise, true);
    }

    /** Câmpurile primite din formular, cu validarea ușoară a unei ciorne. */
    protected function date(Request $request): array
    {
        return $request->validate([
            'cif_declarant' => 'nullable|string|max:20',
            'referinta_interna' => 'nullable|string|max:50',
            'tip_operatiune' => 'nullable|integer',
            'partener_tara' => 'nullable|string|size:2',
            'partener_cod' => 'nullable|string|max:30',
            'partener_denumire' => 'nullable|string|max:200',
            'nr_vehicul' => 'nullable|string|max:20',
            'nr_remorca1' => 'nullable|string|max:20',
            'nr_remorca2' => 'nullable|string|max:20',
            'transportator_tara' => 'nullable|string|size:2',
            'transportator_cod' => 'nullable|string|max:30',
            'transportator_denumire' => 'nullable|string|max:200',
            'data_transport' => 'nullable|date',
            'loc_start' => 'nullable|array',
            'loc_final' => 'nullable|array',
            'documente' => 'nullable|array',
            'linii' => 'nullable|array',
            'valuta' => 'nullable|string|size:3',
            'curs' => 'nullable|numeric',
            'fisiere_importate' => 'nullable|array',
        ]);
    }

    protected function detalii(EtransportDeclaratie $d): array
    {
        return [
            'id' => $d->id,
            'stare' => $d->stare,
            'stare_eticheta' => EtransportDeclaratie::STARI[$d->stare] ?? $d->stare,
            'poate_fi_modificata' => $d->poate_fi_modificata,
            'cif_declarant' => $d->cif_declarant,
            'referinta_interna' => $d->referinta_interna,
            'tip_operatiune' => $d->tip_operatiune,
            'partener_tara' => $d->partener_tara,
            'partener_cod' => $d->partener_cod,
            'partener_denumire' => $d->partener_denumire,
            'nr_vehicul' => $d->nr_vehicul,
            'nr_remorca1' => $d->nr_remorca1,
            'nr_remorca2' => $d->nr_remorca2,
            'transportator_tara' => $d->transportator_tara,
            'transportator_cod' => $d->transportator_cod,
            'transportator_denumire' => $d->transportator_denumire,
            'data_transport' => optional($d->data_transport)->format('Y-m-d'),
            'loc_start' => $d->loc_start,
            'loc_final' => $d->loc_final,
            'documente' => $d->documente ?: [],
            'linii' => $d->linii ?: [],
            'valuta' => $d->valuta,
            'curs' => $d->curs,
            'fisiere_importate' => $d->fisiere_importate ?: [],
            'index_incarcare' => $d->index_incarcare,
            'uit' => $d->uit,
            'raspuns_anaf' => $d->raspuns_anaf,
            'depusa_la' => Format::dataOra($d->depusa_la),
        ];
    }
}
