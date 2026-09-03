<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\FirmeContabilitateImport;
use App\Mail\ScrisoareMarketing;
use App\Models\MarketingContact;
use App\Models\MarketingTrimitere;
use App\Support\ContextUtilizator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Lista firmelor carora li se poate scrie despre aplicatiile noastre.
 *
 * E lista noastra, nu a vreunui client: o vede si o foloseste numai
 * administratorul aplicatiei. De aceea nu intra sub domeniile pe firma.
 *
 * Trei lucruri se tin cu strasnicie aici:
 *
 * Cine s-a dezabonat nu mai primeste nimic. Nu e o bifa pe care sa o poata
 * intoarce cineva dintr-un buton: dezabonarea o face omul, din scrisoare, si
 * numai el o poate desface, scriindu-ne.
 *
 * Fiecare scrisoare poarta legatura de dezabonare. Nu se poate trimite nimic
 * fara ea — vezi ScrisoareMarketing, unde se pune singura.
 *
 * Se scrie ce s-a trimis, cui si cand. Cand cineva intreaba de ce a primit o
 * scrisoare, raspunsul trebuie sa existe.
 */
class MarketingController extends Controller
{
    /** Cate contacte se dau deodata filei. */
    protected const PE_PAGINA = 100;

    public function index(Request $request)
    {
        $cautare = trim((string) $request->query('cauta'));
        $judet = trim((string) $request->query('judet'));
        $stare = trim((string) $request->query('stare'));

        $intrebare = MarketingContact::query()->orderBy('denumire');

        if ($cautare !== '') {
            $intrebare->where(function ($q) use ($cautare) {
                $q->where('denumire', 'like', '%' . $cautare . '%')
                    ->orWhere('email', 'like', '%' . $cautare . '%')
                    ->orWhere('cui', 'like', '%' . $cautare . '%');
            });
        }

        if ($judet !== '') {
            $intrebare->where('judet', $judet);
        }

        if ($stare === 'abonati') {
            $intrebare->where('abonat', true);
        } elseif ($stare === 'dezabonati') {
            $intrebare->where('abonat', false);
        } elseif ($stare === 'nescrisi') {
            $intrebare->where('abonat', true)->whereNull('ultima_trimitere_la');
        } elseif ($stare === 'demo') {
            $intrebare->whereNotNull('demo_cerut_la')->reorder('demo_cerut_la', 'desc');
        } elseif ($stare === 'fara_raspuns') {
            // Li s-a scris si n-au apasat: acolo e de insistat, sau de lasat.
            $intrebare->whereNotNull('ultima_trimitere_la')->whereNull('demo_cerut_la');
        }

        $pagina = $intrebare->paginate(self::PE_PAGINA);

        return response()->json([
            'success' => true,
            'data' => $pagina->items(),
            'total' => $pagina->total(),
            'pagina' => $pagina->currentPage(),
            'pagini' => $pagina->lastPage(),
            'judete' => MarketingContact::query()
                ->whereNotNull('judet')
                ->distinct()
                ->orderBy('judet')
                ->pluck('judet'),
            'sumar' => [
                'toate' => MarketingContact::count(),
                'abonati' => MarketingContact::where('abonat', true)->count(),
                'dezabonati' => MarketingContact::where('abonat', false)->count(),
                'nescrisi' => MarketingContact::where('abonat', true)->whereNull('ultima_trimitere_la')->count(),
                'demo' => MarketingContact::whereNotNull('demo_cerut_la')->count(),
                'fara_raspuns' => MarketingContact::whereNotNull('ultima_trimitere_la')
                    ->whereNull('demo_cerut_la')
                    ->count(),
            ],
        ]);
    }

    /** Aduce lista dintr-un fisier Excel. */
    public function importa(Request $request)
    {
        $request->validate([
            'fisier' => 'required|file|mimes:xlsx,xls,csv,txt|max:20480',
        ]);

        $fisier = $request->file('fisier');
        $import = new FirmeContabilitateImport($fisier->getClientOriginalName());

        try {
            Excel::import($import, $fisier);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fișierul nu a putut fi citit: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => sprintf(
                '%d firme adăugate, %d actualizate. Lăsate deoparte: %d fără adresă de e-mail, %d cu adresa repetată.',
                $import->adaugate,
                $import->innoite,
                $import->fara_email,
                $import->repetate
            ),
            'adaugate' => $import->adaugate,
            'innoite' => $import->innoite,
        ]);
    }

    /**
     * Trimite scrisoarea catre firmele alese.
     *
     * Se trimite prin coada, nu pe loc: la cateva sute de destinatari, cererea
     * din browser ar expira demult inainte de a se incheia, iar omul n-ar sti
     * nici cate au plecat, nici cate nu. Puse in coada, ele pleaca in ritmul lor,
     * iar fila arata pe urma ce s-a intamplat cu fiecare.
     */
    public function trimite(Request $request)
    {
        $date = $request->validate([
            'contacte' => 'required|array|min:1',
            'contacte.*' => 'integer',
            'subiect' => 'required|string|max:200',
            'text' => 'required|string|max:20000',
            'campanie' => 'nullable|string|max:100',
        ]);

        /*
         * Cine s-a dezabonat se scoate aici, nu se lasa in seama celui care a
         * ales: o lista aleasa acum cinci minute poate cuprinde pe cineva care
         * intre timp s-a dezabonat.
         */
        $contacte = MarketingContact::whereIn('id', $date['contacte'])
            ->caroraLiSePoateScrie()
            ->get();

        if ($contacte->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Niciunul dintre contactele alese nu mai poate primi mesaje.',
            ], 422);
        }

        $omul = ContextUtilizator::curent();
        $trimise = 0;
        $cazute = 0;

        foreach ($contacte as $contact) {
            try {
                Mail::to($contact->email)->queue(
                    new ScrisoareMarketing($contact, $date['subiect'], $date['text'], $date['campanie'] ?? '')
                );

                $contact->update([
                    'ultima_trimitere_la' => now(),
                    'cate_trimiteri' => $contact->cate_trimiteri + 1,
                ]);

                MarketingTrimitere::create([
                    'contact_id' => $contact->id,
                    'campanie' => $date['campanie'] ?? null,
                    'subiect' => $date['subiect'],
                    'reusit' => true,
                    'user_id' => optional($omul)->id,
                    'user_nume' => optional($omul)->name,
                ]);

                $trimise++;
            } catch (\Exception $e) {
                MarketingTrimitere::create([
                    'contact_id' => $contact->id,
                    'campanie' => $date['campanie'] ?? null,
                    'subiect' => $date['subiect'],
                    'reusit' => false,
                    'eroare' => mb_substr($e->getMessage(), 0, 500),
                    'user_id' => optional($omul)->id,
                    'user_nume' => optional($omul)->name,
                ]);

                $cazute++;
            }
        }

        $sarite = count($date['contacte']) - $contacte->count();

        return response()->json([
            'success' => true,
            'message' => sprintf(
                '%d mesaje puse la trimitere.%s%s',
                $trimise,
                $cazute ? ' ' . $cazute . ' nu au putut fi puse.' : '',
                $sarite ? ' ' . $sarite . ' sărite (dezabonate).' : ''
            ),
            'trimise' => $trimise,
            'cazute' => $cazute,
            'sarite' => $sarite,
        ]);
    }

    /** Cum arata scrisoarea pentru un anume contact, inainte de a pleca. */
    public function previzualizare(Request $request)
    {
        $date = $request->validate([
            'contact_id' => 'required|integer',
            'text' => 'required|string|max:20000',
        ]);

        $contact = MarketingContact::find($date['contact_id']);

        if (!$contact) {
            return response()->json(['success' => false, 'message' => 'Contactul nu există.'], 404);
        }

        return response()->json([
            'success' => true,
            'catre' => $contact->denumire . ' <' . $contact->email . '>',
            'text' => ScrisoareMarketing::potriveste($date['text'], $contact),
        ]);
    }

    /** Scoate din evidenta contactele alese. */
    public function sterge(Request $request)
    {
        $date = $request->validate([
            'contacte' => 'required|array|min:1',
            'contacte.*' => 'integer',
        ]);

        /*
         * Cei dezabonati nu se sterg: sterse, adresele lor s-ar putea intoarce
         * in evidenta la urmatorul import, iar omul ar primi din nou scrisori
         * dupa ce a spus limpede ca nu vrea.
         */
        $cate = MarketingContact::whereIn('id', $date['contacte'])
            ->where('abonat', true)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $cate . ' contacte șterse. Cele dezabonate rămân, ca să nu revină la un import viitor.',
        ]);
    }
}
