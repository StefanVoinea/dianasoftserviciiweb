<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Models\AnafSocietate;
use App\Services\Anaf\Arhiva\ArhivaException;
use App\Services\Anaf\Arhiva\ArhivaService;
use App\Services\Anaf\Declaratii\ConcatenareService;
use App\Services\Anaf\Declaratii\CurataXml;
use App\Services\Anaf\Declaratii\D300\AntetD300;
use App\Services\Anaf\Declaratii\D300\DecontDinSaft;
use App\Services\Anaf\Declaratii\D300\DecontFormular;
use App\Services\Anaf\Declaratii\D300\PotrivireDecont;
use App\Services\Anaf\Declaratii\D300\DecontXml;
use App\Services\Anaf\Declaratii\D300\RanduriD300;
use App\Services\Anaf\Declaratii\DeclaratieException;
use App\Services\Anaf\Declaratii\DeclaratieXml;
use App\Services\Anaf\Declaratii\DepunereService;
use App\Services\Anaf\Declaratii\DukIntegrator;
use App\Services\Anaf\Declaratii\InterpretareErori;
use App\Services\Anaf\Declaratii\PdfDeclaratie;
use App\Services\Anaf\Declaratii\RecipisaService;
use App\Services\Anaf\Declaratii\SemnareService;
use App\Services\Anaf\Declaratii\TesteSaft;
use App\Services\Anaf\Declaratii\VerificareSaft;
use App\Services\Anaf\Format;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Spv\CertificatService;
use App\Support\ContextUtilizator;
use App\Support\Flux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DeclaratiiController extends Controller
{
    /** Cate caractere de eroare pleaca spre tabel; restul se vede la explicatii. */
    protected const EROARE_IN_TABEL = 4000;

    /** Entitatile inrolate deja cautate in cererea curenta, dupa cod fiscal. */
    protected $societati = [];

    /** Documentele aduse din arhiva clientului, de sters la sfarsitul cererii. */
    protected $temporare = [];

    public function __destruct()
    {
        foreach ($this->temporare as $cale) {
            @unlink($cale);
        }
    }

    public function index(Request $request)
    {
        $query = AnafDeclaratie::with('certificat')->orderByDesc('created_at');

        // Starea se alege dintr-o lista, deci se compara exact.
        if ($request->filled('pas')) {
            $query->where('pas', $request->query('pas'));
        }

        // Restul se scriu de mana: cautarea e pe bucata de text, ca sa nu fie
        // nevoie de valoarea intreaga si scrisa identic.
        foreach (['cui', 'tip', 'den_firma', 'index_recipisa'] as $filtru) {
            if ($request->filled($filtru)) {
                $query->where($filtru, 'like', '%' . $request->query($filtru) . '%');
            }
        }

        if ($request->filled('luna')) {
            $query->where('luna', (int) $request->query('luna'));
        }

        if ($request->filled('anul')) {
            $query->where('anul', (int) $request->query('anul'));
        }

        $declaratii = $query->get()->map(function ($declaratie) {
            return $this->prezinta($declaratie);
        });

        return response()->json(['success' => true, 'data' => $declaratii]);
    }

    /**
     * Incarca una sau mai multe declaratii (XML sau PDF) si le valideaza.
     *
     * Un PDF de declaratie ANAF poarta XML-ul original atasat, deci poate fi
     * verificat la fel ca un XML. Daca este deja semnat, ramane asa cum este si
     * trece direct la depunere.
     */
    public function store(Request $request, DeclaratieXml $analizor, DukIntegrator $duk, PdfDeclaratie $pdf, CurataXml $curatator)
    {
        $request->validate([
            'fisiere' => 'required_without:fisier|array|min:1',
            'fisiere.*' => 'file|max:51200',
            'fisier' => 'required_without:fisiere|file|max:51200',
        ]);

        $incarcate = $request->file('fisiere') ?: [$request->file('fisier')];

        $rezultate = [];
        $erori = [];

        foreach ($incarcate as $incarcat) {
            $nume = $incarcat->getClientOriginalName();

            try {
                $declaratie = strtolower($incarcat->getClientOriginalExtension()) === 'pdf'
                    ? $this->dinPdf($incarcat, $analizor, $duk, $pdf, $curatator, $request)
                    : $this->dinXml($incarcat, $analizor, $duk, $curatator, $request);

                $rezultate[] = $this->prezinta($declaratie);

                Jurnal::scrie(
                    'declaratie_incarcare',
                    sprintf(
                        'A încărcat declarația %s pentru %s (%s): %s',
                        $declaratie->tip,
                        $declaratie->cui ?: 'CUI necunoscut',
                        $nume,
                        $this->stareaLizibila($declaratie)
                    ),
                    // Erorile întregi stau pe declarație; aici intră doar cât s-a găsit.
                    ['tip' => $declaratie->tip, 'erori' => $this->cateErori($declaratie)],
                    $declaratie->cui,
                    !in_array($declaratie->pas, ['eroare_validare'], true)
                );
            } catch (DeclaratieException $e) {
                $erori[] = $nume . ': ' . $e->getMessage();

                Jurnal::esec('declaratie_incarcare', 'Încărcarea fișierului ' . $nume . ' a eșuat: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => $rezultate !== [],
            'data' => $rezultate,
            'erori' => $erori,
        ], $rezultate === [] ? 422 : 200);
    }

    /** Fișier XML: se validează și se generează PDF-ul oficial. */
    protected function dinXml($incarcat, DeclaratieXml $analizor, DukIntegrator $duk, CurataXml $curatator, Request $request): AnafDeclaratie
    {
        $director = config('anaf.declaratii.storage_path');
        $caleXml = $director . '/xml/' . uniqid('decl_', true) . '.xml';

        // Caracterele speciale neescapate se repara inainte de analiza si validare.
        Storage::put($caleXml, $curatator->curata($incarcat->get()));

        try {
            $meta = $analizor->analizeaza(Storage::path($caleXml));
        } catch (DeclaratieException $e) {
            Storage::delete($caleXml);

            throw $e;
        }

        // Ce n-a spus XML-ul (CUI, perioada) se poate citi din numele fisierului.
        $meta = $analizor->completeazaDinNume($meta, $incarcat->getClientOriginalName());

        $declaratie = AnafDeclaratie::create(array_merge($this->campuriDin($meta), [
            'nume_fisier' => $incarcat->getClientOriginalName(),
            'cale_xml' => $caleXml,
            'pas' => 'incarcat',
            'user_id' => optional($request->user())->id,
        ]));

        return $this->valideaza($declaratie, $duk);
    }

    /**
     * Fișier PDF: se extrage XML-ul atașat, se validează cu DUKIntegrator, iar
     * dacă PDF-ul este deja semnat trece direct la pasul de depunere.
     */
    protected function dinPdf($incarcat, DeclaratieXml $analizor, DukIntegrator $duk, PdfDeclaratie $pdf, CurataXml $curatator, Request $request): AnafDeclaratie
    {
        $director = config('anaf.declaratii.storage_path');
        $calePdf = $incarcat->storeAs($director . '/pdf', uniqid('decl_', true) . '.pdf');

        $info = $pdf->citeste(Storage::path($calePdf));

        if (empty($info['xml'])) {
            Storage::delete($calePdf);

            throw new DeclaratieException('PDF-ul nu conține declarația în format XML (nu pare o declarație ANAF).');
        }

        // XML-ul din PDF se păstrează separat, ca să poată fi validat.
        $caleXml = preg_replace('/\.pdf$/i', '', $calePdf) . '.xml';
        Storage::put($caleXml, $curatator->curata($info['xml']));

        try {
            $meta = $analizor->analizeaza(Storage::path($caleXml));
        } catch (DeclaratieException $e) {
            Storage::delete($calePdf);
            Storage::delete($caleXml);

            throw $e;
        }

        // Ce n-a spus XML-ul (CUI, perioada) se poate citi din numele fisierului.
        $meta = $analizor->completeazaDinNume($meta, $incarcat->getClientOriginalName());

        $declaratie = AnafDeclaratie::create(array_merge($this->campuriDin($meta), [
            'nume_fisier' => $incarcat->getClientOriginalName(),
            'cale_xml' => $caleXml,
            'pas' => 'incarcat',
            'semnat' => $info['semnat'],
            'user_id' => optional($request->user())->id,
        ]));

        // Verificarea se face pe XML-ul din PDF, la fel ca pentru un XML separat.
        $rezultat = $duk->valideazaSiGenereazaPdf(
            Storage::path($caleXml),
            $declaratie->tip,
            Storage::path(preg_replace('/\.pdf$/i', '', $calePdf) . '_duk.pdf'),
            null,
            $declaratie->anul,
            $declaratie->luna,
            $declaratie->perioada_tip
        );

        if (!$rezultat['valid']) {
            $declaratie->update(['pas' => 'eroare_validare', 'erori_validare' => $rezultat['erori']]);

            return $declaratie->fresh();
        }

        /*
         * PDF-ul primit poarta semnatura, cand are una: acela se pastreaza, si
         * nu se atinge cu nimic.
         *
         * Cel nesemnat se inlocuieste insa cu PDF-ul scos de DUKIntegrator din
         * acelasi XML. Programele de contabilitate dau un „PDF inteligent" —
         * formular XFA, a carui pagina e doar un paravan cu „Please wait...",
         * iar declaratia se deseneaza abia in Adobe. Semnat, un asemenea
         * document nu poate purta caseta de semnatura (ea cade pe paravan), si
         * nu se poate tipari decat prin Adobe.
         *
         * PDF-ul lui DUKIntegrator e o foaie obisnuita, cu XML-ul atasat, de
         * zece ori mai mica: se vede in orice program, se tipareste oriunde, si
         * poarta caseta. E tot documentul oficial — chiar cel pe care il face
         * aplicatia ANAF cand apesi „Validare + creare PDF".
         */
        $pastreazaPrimitul = $info['semnat'] || !$rezultat['cale_pdf'] || !is_file($rezultat['cale_pdf']);

        if ($pastreazaPrimitul) {
            if ($rezultat['cale_pdf'] && is_file($rezultat['cale_pdf'])) {
                @unlink($rezultat['cale_pdf']);
            }

            $declaratie->update($info['semnat']
                ? ['pas' => 'semnat', 'cale_pdf' => $calePdf, 'cale_pdf_semnat' => $calePdf, 'erori_validare' => null]
                : ['pas' => 'validat', 'cale_pdf' => $calePdf, 'erori_validare' => null]);

            return $declaratie->fresh();
        }

        $declaratie->update([
            'pas' => 'validat',
            'cale_pdf' => preg_replace('/\.pdf$/i', '', $calePdf) . '_duk.pdf',
            'erori_validare' => null,
        ]);

        return $declaratie->fresh();
    }

    protected function campuriDin(array $meta): array
    {
        $cui = $meta['cui'] ?? '';
        $societate = $this->societateInrolata($cui);

        return [
            'cui' => $cui,
            /*
             * Denumirea din entitatile inrolate are intaietate: acolo vine din
             * datele de identificare ANAF, pe cand cea din declaratie e scrisa
             * de cel care a intocmit-o si poate fi prescurtata sau gresita.
             */
            'den_firma' => $societate && $societate->denumire ? $societate->denumire : $meta['den_firma'],
            // Certificatul cu care a fost inrolata entitatea: cu el se semneaza.
            'certificat_id' => $societate ? $societate->certificat_id : null,
            'tip' => $meta['tip'],
            'luna' => $meta['luna'],
            'anul' => $meta['anul'],
            // L, T sau A — validatorul SAF-T il cere ca sa aleaga regulile perioadei
            'perioada_tip' => $meta['perioada_tip'] ?? null,
            'rectificativa' => $meta['rectificativa'],
        ];
    }

    /**
     * Duce declaratia semnata si XML-ul ei in arhiva de pe calculatorul clientului.
     *
     * Arhivarea nu are voie sa strice semnarea: daca programul local nu poate
     * scrie, declaratia ramane semnata, iar esecul se scrie doar in jurnal.
     */
    protected function arhiveaza(AnafDeclaratie $declaratie): void
    {
        $arhiva = app(ArhivaService::class);

        if (!$arhiva->activa()) {
            return;
        }

        $dosar = ArhivaService::dosarFirma($declaratie->den_firma, $declaratie->cui);
        $tip = ArhivaService::curata($declaratie->tip) ?: 'Diverse';
        $cai = [];

        // Documentele scrise cat timp denumirea firmei nu era stiuta stau intr-un
        // dosar purtand doar codul ei; se strang la un loc inainte de a scrie aici.
        $arhiva->uneste($declaratie->cui, $dosar);

        try {
            /*
             * Calea scrisa data trecuta se trimite ca sa poata fi inlocuita:
             * o resemnare inlocuieste propriul document, in timp ce o alta
             * declaratie pentru aceeasi luna primeste fisier separat.
             */
            if ($declaratie->cale_pdf_semnat && Storage::exists($declaratie->cale_pdf_semnat)) {
                $cai['arhiva_semnat'] = $arhiva->pune(
                    Storage::get($declaratie->cale_pdf_semnat),
                    $dosar,
                    $tip,
                    ArhivaService::numeDeclaratie($declaratie, 'semnata', 'pdf'),
                    $declaratie->arhiva_semnat
                );
            }

            /*
             * Ce s-a dat spre lucru sta deoparte, in „Initiale". In dosarul
             * tipului raman numai declaratia semnata si recipisa ei.
             */
            if ($declaratie->cale_xml && Storage::exists($declaratie->cale_xml)) {
                $cai['arhiva_xml'] = $arhiva->pune(
                    Storage::get($declaratie->cale_xml),
                    $dosar,
                    ArhivaService::dosarInitiale($declaratie->tip),
                    ArhivaService::numeDeclaratie($declaratie, '', 'xml'),
                    $declaratie->arhiva_xml
                );
            }

            /*
             * Si documentul primit, cand a fost altul decat cel semnat: PDF-ul
             * „inteligent" al programului de contabilitate se inlocuieste cu cel
             * scos de DUKIntegrator, dar nu se pierde.
             */
            /*
             * Coloana se cere abia dupa migrare. Pe un server care n-a rulat-o
             * inca, scrisul in ea ar opri toata arhivarea — si, pana adineauri,
             * chiar semnarea. Mai bine fara insemnarea caii decat fara nimic.
             */
            if ($declaratie->cale_pdf
                && $declaratie->cale_pdf !== $declaratie->cale_pdf_semnat
                && self::areColoanaInitial()
                && Storage::exists($declaratie->cale_pdf)) {
                $cai['arhiva_initial'] = $arhiva->pune(
                    Storage::get($declaratie->cale_pdf),
                    $dosar,
                    ArhivaService::dosarInitiale($declaratie->tip),
                    ArhivaService::numeDeclaratie($declaratie, '', 'pdf'),
                    $declaratie->arhiva_initial
                );
            }
        } catch (\Throwable $e) {
            /*
             * Nu doar esecurile arhivei: si o coloana lipsa, si orice
             * altceva. Ce s-a reusit pana aici nu se desface.
             */
            Jurnal::esec(
                'declaratie_arhivare',
                'Arhivarea locală a declarației ' . $declaratie->tip . ' pentru ' . $declaratie->cui
                    . ' a eșuat: ' . $e->getMessage(),
                [],
                $declaratie->cui
            );
        }

        if ($cai === []) {
            return;
        }

        $declaratie->update($cai);

        /*
         * Documentele stau la client, nu pe server. Copia de lucru se sterge
         * doar dupa ce arhivarea a reusit; PDF-ul nesemnat oricum nu mai
         * foloseste nimanui, iar cel semnat se aduce inapoi la nevoie.
         */
        if ($arhiva->stergeDePeServer()) {
            $sterse = [];

            if (isset($cai['arhiva_semnat'])) {
                Storage::delete($declaratie->cale_pdf_semnat);
                $sterse['cale_pdf_semnat'] = null;

                /*
                 * Documentul dat spre lucru se sterge de pe server numai dupa ce
                 * a ajuns in „Initiale". Altfel s-ar pierde tocmai ce trebuia
                 * pastrat: dovada a ceea ce s-a semnat.
                 */
                if (isset($cai['arhiva_initial']) || $declaratie->cale_pdf === $declaratie->cale_pdf_semnat) {
                    Storage::delete($declaratie->cale_pdf);
                    $sterse['cale_pdf'] = null;
                }
            }

            if (isset($cai['arhiva_xml'])) {
                Storage::delete($declaratie->cale_xml);
                $sterse['cale_xml'] = null;
            }

            $declaratie->update($sterse);
        }
    }

    /**
     * Exista coloana in care se tine calea documentului dat spre lucru?
     *
     * Se intreaba o singura data pe cerere: pe un server care n-a rulat inca
     * migrarea, raspunsul e nu, iar arhivarea merge mai departe fara ea.
     */
    protected static function areColoanaInitial(): bool
    {
        static $are = null;

        if ($are === null) {
            $are = Schema::hasColumn('anaf_declaratii', 'arhiva_initial');
        }

        return $are;
    }

    /** Dupa depunere, numele documentului arhivat poarta indicele de incarcare. */
    protected function redenumesteInArhiva(AnafDeclaratie $declaratie): void
    {
        $arhiva = app(ArhivaService::class);

        if (!$arhiva->activa() || !$declaratie->arhiva_semnat) {
            return;
        }

        try {
            $declaratie->update([
                'arhiva_semnat' => $arhiva->redenumeste(
                    $declaratie->arhiva_semnat,
                    ArhivaService::numeDeclaratie($declaratie, 'depusa', 'pdf')
                ),
            ]);
        } catch (ArhivaException $e) {
            Jurnal::esec(
                'declaratie_arhivare',
                'Documentul depus nu a putut fi redenumit în arhiva locală: ' . $e->getMessage(),
                [],
                $declaratie->cui
            );

            return;
        }

        // Declaratia depusa sta si in dosarul comun „Toate”, cu ale tuturor firmelor.
        $arhiva->copiazaInToate(
            $declaratie->arhiva_semnat,
            ArhivaService::numeDeclaratie($declaratie, 'depusa', 'pdf')
        );
    }

    /**
     * Calea de pe server a unui document, adus din arhiva clientului daca acolo
     * a ramas. Fisierele aduse asa sunt temporare si se sterg la sfarsitul cererii.
     */
    protected function calePeServer(AnafDeclaratie $declaratie, string $campLocal, string $campArhiva): ?string
    {
        if ($declaratie->$campLocal && Storage::exists($declaratie->$campLocal)) {
            return Storage::path($declaratie->$campLocal);
        }

        if (!$declaratie->$campArhiva) {
            return null;
        }

        $this->folosesteCertificatulEntitatii($declaratie);

        $continut = app(ArhivaService::class)->ia($declaratie->$campArhiva);

        $cale = tempnam(sys_get_temp_dir(), 'arh')
            . '.' . pathinfo($declaratie->$campArhiva, PATHINFO_EXTENSION);

        file_put_contents($cale, $continut);
        $this->temporare[] = $cale;

        return $cale;
    }

    /**
     * Entitatea inrolata pentru acest cod fiscal, daca exista.
     *
     * Raspunsul se tine minte pe durata cererii: tabelul are multe randuri ale
     * acelorasi firme si nu are rost sa fie intrebata baza de date pentru
     * fiecare in parte.
     */
    protected function societateInrolata(?string $cui): ?AnafSocietate
    {
        if (!$cui) {
            return null;
        }

        if (!array_key_exists($cui, $this->societati)) {
            $this->societati[$cui] = AnafSocietate::with('certificat')->where('cif', $cui)->first();
        }

        return $this->societati[$cui];
    }

    /**
     * Certificatul cu care s-a inrolat entitatea declaratiei.
     *
     * Semnarea si depunerea trebuie sa mearga pe calculatorul unde se afla acel
     * token: alt certificat nu are drept de semnatura pentru firma respectiva.
     */
    protected function folosesteCertificatulEntitatii(AnafDeclaratie $declaratie): void
    {
        $societate = $this->societateInrolata($declaratie->cui);

        if ($societate && $societate->certificat) {
            app(CertificatService::class)->foloseste($societate->certificat);
        }
    }

    protected function stareaLizibila(AnafDeclaratie $declaratie): string
    {
        switch ($declaratie->pas) {
            case 'validat':
                return 'validată';
            case 'semnat':
                return 'validată, deja semnată';
            case 'eroare_validare':
                return 'respinsă la validare';
            default:
                return $declaratie->pas;
        }
    }

    /** Re-valideaza o declaratie incarcata (utile dupa corectarea XML-ului). */
    public function valideazaDeclaratie(AnafDeclaratie $declaratie, DukIntegrator $duk)
    {
        $declaratie = $this->valideaza($declaratie, $duk);

        Jurnal::scrie(
            'declaratie_validare',
            'A revalidat declarația ' . $declaratie->tip . ' pentru ' . $declaratie->cui
                . ': ' . ($declaratie->pas === 'validat' ? 'validă' : 'cu erori'),
            ['erori' => $this->cateErori($declaratie)],
            $declaratie->cui,
            $declaratie->pas === 'validat'
        );

        return response()->json(['success' => true, 'data' => $this->prezinta($declaratie)]);
    }

    /**
     * Verifica din nou consistenta unui SAF-T, la cererea omului.
     *
     * Se cheama dupa ce declaratia a fost refacuta in programul de contabilitate
     * si incarcata din nou, sau cand verificarea de la incarcare n-a mers.
     */
    public function verificaConsistenta(AnafDeclaratie $declaratie, VerificareSaft $verificare)
    {
        if ($declaratie->tip !== 'D406') {
            return response()->json([
                'success' => false,
                'message' => 'Verificarea de consistență se face numai la D406 (SAF-T).',
            ], 422);
        }

        $declaratie = $this->verificaSaft($declaratie, $verificare);

        Jurnal::scrie(
            'declaratie_consistenta',
            'A verificat consistența declarației D406 pentru ' . $declaratie->cui . ': '
                . $this->cateNeconcordante($declaratie),
            [],
            $declaratie->cui,
            $declaratie->verificare_stare !== 'imposibil'
        );

        return response()->json([
            'success' => true,
            'data' => $this->prezinta($declaratie),
            'consistenta' => $this->consistentaDin($declaratie),
        ]);
    }

    /**
     * Liniile gasite la verificare, cu explicatia fiecarui test.
     *
     * Ele nu calatoresc odata cu tabelul: la un SAF-T incalcit sunt cu miile,
     * iar in tabel n-au ce cauta. Se cer cand se deschide fereastra.
     */
    public function consistenta(AnafDeclaratie $declaratie)
    {
        return response()->json([
            'success' => true,
            'data' => $this->consistentaDin($declaratie),
        ]);
    }

    /**
     * Decontul de TVA socotit din jurnalele unui SAF-T.
     *
     * Nu e inca o declaratie: sunt cifrele decontului, cu randurile asa cum le
     * numeste formularul ANAF, si cu o vorba despre cum au iesit. Din ele se va
     * scrie mai departe XML-ul D300.
     */
    public function decont(AnafDeclaratie $declaratie, DecontDinSaft $decont)
    {
        if ($declaratie->tip !== 'D406') {
            return response()->json([
                'success' => false,
                'message' => 'Decontul se scoate numai din D406 (SAF-T).',
            ], 422);
        }

        if (!$declaratie->cale_xml || !Storage::exists($declaratie->cale_xml)) {
            return response()->json([
                'success' => false,
                'message' => 'Declarația nu mai are fișierul XML pe server.',
            ], 422);
        }

        try {
            $rezultat = $decont->genereaza(Storage::path($declaratie->cale_xml));
        } catch (DeclaratieException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        Jurnal::scrie(
            'declaratie_decont',
            'A scos decontul de TVA din SAF-T pentru ' . $declaratie->cui . ': '
                . $rezultat['lamurire']['titlu'],
            ['linii' => $rezultat['linii']],
            $declaratie->cui
        );

        /*
         * Antetul declaratiei nu se afla in SAF-T: adresa, banca, contul, cine
         * semneaza. Ele stau pe fisa firmei, iar aici se spune doar daca sunt
         * complete — ca omul sa stie dinainte ce mai are de completat, nu dupa
         * ce declaratia a fost respinsa.
         */
        $antet = AntetD300::pentru($this->societateInrolata($declaratie->cui));

        return response()->json([
            'success' => true,
            'data' => [
                'cif' => $rezultat['cif'],
                'denumire' => $rezultat['denumire'],
                'luna' => $rezultat['luna'],
                'an' => $rezultat['an'],
                'linii' => $rezultat['linii'],
                'lamurire' => $rezultat['lamurire'],
                'randuri' => $this->randurileDecontului($rezultat['randuri']),
                'antet' => ['gata' => $antet['gata'], 'lipsesc' => $antet['lipsesc']],
            ],
        ]);
    }

    /**
     * Decontul, scris ca declaratie D300, gata de incarcat in formularul ANAF.
     *
     * Nu se depune de aici si nu se tine minte: fisierul pleaca la om, care il
     * deschide in formularul inteligent („soft A"), se uita peste cifre si il
     * depune de acolo. Cine vrea sa-l depuna prin aplicatie il incarca inapoi ca
     * pe orice declaratie — si atunci trece si prin DUKIntegrator.
     */
    public function decontXml(AnafDeclaratie $declaratie, DecontDinSaft $decont, DecontXml $scriitor)
    {
        if ($declaratie->tip !== 'D406') {
            return response()->json([
                'success' => false,
                'message' => 'Decontul se scoate numai din D406 (SAF-T).',
            ], 422);
        }

        if (!$declaratie->cale_xml || !Storage::exists($declaratie->cale_xml)) {
            return response()->json([
                'success' => false,
                'message' => 'Declarația nu mai are fișierul XML pe server.',
            ], 422);
        }

        try {
            $rezultat = $decont->genereaza(Storage::path($declaratie->cale_xml));
            $xml = $scriitor->scrie($rezultat, $this->societateInrolata($declaratie->cui));
        } catch (DeclaratieException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        Jurnal::scrie(
            'declaratie_decont_xml',
            'A scos declarația D300 din SAF-T pentru ' . $declaratie->cui
                . ' (' . $rezultat['luna'] . '/' . $rezultat['an'] . ')',
            ['linii' => $rezultat['linii']],
            $declaratie->cui
        );

        $nume = $scriitor->numeFisier($rezultat);

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $nume . '"',
            // Numele trebuie sa treaca si prin XHR, unde antetul nu se vede singur.
            'X-Nume-Fisier' => $nume,
            'Access-Control-Expose-Headers' => 'X-Nume-Fisier',
        ]);
    }

    /**
     * Acelasi decont, scris pentru formularul inteligent al ANAF.
     *
     * Deosebirea fata de declaratia de depus: formularul asteapta datele asezate
     * ca in el, nu ca in schema de depunere, si primeste si ce e neintreg — omul
     * il deschide, vede cifrele venite din SAF-T si completeaza restul.
     */
    public function decontFormular(AnafDeclaratie $declaratie, DecontDinSaft $decont, DecontFormular $scriitor)
    {
        if ($declaratie->tip !== 'D406') {
            return response()->json([
                'success' => false,
                'message' => 'Decontul se scoate numai din D406 (SAF-T).',
            ], 422);
        }

        if (!$declaratie->cale_xml || !Storage::exists($declaratie->cale_xml)) {
            return response()->json([
                'success' => false,
                'message' => 'Declarația nu mai are fișierul XML pe server.',
            ], 422);
        }

        try {
            $rezultat = $decont->genereaza(Storage::path($declaratie->cale_xml));
        } catch (DeclaratieException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $xml = $scriitor->scrie($rezultat, $this->societateInrolata($declaratie->cui));

        Jurnal::scrie(
            'declaratie_decont_formular',
            'A scos decontul pentru formularul ANAF, din SAF-T, pentru ' . $declaratie->cui
                . ' (' . $rezultat['luna'] . '/' . $rezultat['an'] . ')',
            ['linii' => $rezultat['linii']],
            $declaratie->cui
        );

        $nume = $scriitor->numeFisier($rezultat);

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $nume . '"',
            'X-Nume-Fisier' => $nume,
            'Access-Control-Expose-Headers' => 'X-Nume-Fisier',
        ]);
    }

    /**
     * Randurile cu cifre, in ordinea formularului.
     *
     * Cele ramase la zero nu se arata: decontul are peste o suta de randuri, iar
     * intr-o luna obisnuita se umplu cateva. Randurile „_P” nici nu sunt randuri
     * — sunt sume stranse deoparte in cuprinsul unei tranzactii.
     */
    protected function randurileDecontului(array $randuri): array
    {
        $lista = [];

        foreach ($randuri as $camp => $valoare) {
            if (substr($camp, -2) === '_P' || abs((float) $valoare) < 0.5) {
                continue;
            }

            $rand = RanduriD300::RANDURI[$camp] ?? null;

            $lista[] = [
                'camp' => $camp,
                'rand' => $rand ? $rand['rand'] : null,
                'denumire' => $rand ? $rand['denumire'] : null,
                // Sub ce nume intra valoarea in XML-ul D300
                'atribut' => $rand ? $rand['atribut'] : null,
                'fel' => substr($camp, -5) === '_BAZA' ? 'bază' : 'TVA',
                'valoare' => round((float) $valoare, 2),
            ];
        }

        return $lista;
    }

    /** Randurile care nu se potrivesc, cerute cand se deschide fereastra. */
    public function potrivire(AnafDeclaratie $declaratie)
    {
        $detalii = json_decode((string) $declaratie->potrivire_detalii, true) ?: [];

        return response()->json([
            'success' => true,
            'data' => $detalii + [
                'stare' => $declaratie->potrivire_stare,
                'numar' => (int) $declaratie->potrivire_numar,
                'potrivit_la' => Format::dataOra($declaratie->potrivire_la),
            ],
        ]);
    }

    /** Rezultatul scris pe declaratie, gata de aratat. */
    protected function consistentaDin(AnafDeclaratie $declaratie): array
    {
        $rezultat = json_decode((string) $declaratie->verificare_erori, true) ?: [];

        $erori = array_map(function ($linie) {
            return $linie + ['test' => TesteSaft::descrie($linie['stare'] ?? null)];
        }, $rezultat['erori'] ?? []);

        return [
            'stare' => $declaratie->verificare_stare,
            'numar' => $declaratie->verificare_numar,
            'verificat_la' => Format::dataOra($declaratie->verificare_la),
            'mesaj' => $rezultat['mesaj'] ?? null,
            'antet' => $rezultat['antet'] ?? [],
            'trunchiat' => (bool) ($rezultat['trunchiat'] ?? false),
            'erori' => $erori,
            // Cate linii a prins fiecare test, ca sa se vada de unde se apuca
            'pe_teste' => array_map(function ($numar, $cod) {
                return ['numar' => $numar] + TesteSaft::descrie($cod);
            }, $rezultat['pe_teste'] ?? [], array_keys($rezultat['pe_teste'] ?? [])),
        ];
    }

    /** Cat s-a gasit la verificare, spus in doua cuvinte pentru jurnal. */
    protected function cateNeconcordante(AnafDeclaratie $declaratie): string
    {
        if ($declaratie->verificare_stare === 'imposibil') {
            return 'nu s-a putut face';
        }

        $numar = (int) $declaratie->verificare_numar;

        if ($numar === 0) {
            return 'fără neconcordanțe';
        }

        return $numar . ($numar === 1 ? ' linie de îndreptat' : ' linii de îndreptat');
    }

    /** Raspunsul pentru omul caruia nu i s-a dat dreptul cerut. */
    protected function faraDreptul(string $fapta)
    {
        return response()->json([
            'success' => false,
            'message' => 'Nu aveți dreptul să ' . $fapta . '. Cereți-l administratorului firmei.',
        ], 403);
    }

    public function semneaza(AnafDeclaratie $declaratie, SemnareService $semnare)
    {
        if (!ContextUtilizator::poateSemna()) {
            return $this->faraDreptul('semnați declarații');
        }

        if (!$declaratie->cale_pdf) {
            return response()->json(['success' => false, 'message' => 'Declarația nu are PDF generat. Validați-o mai întâi.'], 422);
        }

        $caleSemnat = preg_replace('/\.pdf$/i', '', $declaratie->cale_pdf) . '_semnat.pdf';

        $this->folosesteCertificatulEntitatii($declaratie);

        try {
            $semnare->semneaza(Storage::path($declaratie->cale_pdf), Storage::path($caleSemnat));
        } catch (DeclaratieException $e) {
            // Esecul ramane scris pe declaratie, nu doar in raspunsul cererii:
            // altfel, in tabel, ea ar parea in continuare doar „validata".
            $declaratie->update([
                'pas' => 'eroare_semnare',
                'eroare_semnare' => $e->getMessage(),
            ]);

            Jurnal::esec(
                'declaratie_semnare',
                'Semnarea declarației ' . $declaratie->tip . ' pentru ' . $declaratie->cui . ' a eșuat: ' . $e->getMessage(),
                [],
                $declaratie->cui
            );

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        $declaratie->update([
            'cale_pdf_semnat' => $caleSemnat,
            'semnat' => true,
            'pas' => 'semnat',
            'eroare_semnare' => null,
            // Certificatul cu care s-a semnat
            'certificat_id' => app(CertificatService::class)->idCurent(),
        ]);

        /*
         * Arhivarea nu are voie sa strice semnarea.
         *
         * Ea e o inlesnire: documentul e semnat, iar daca nu ajunge acum la
         * client, ajunge mai tarziu, din fila de declaratii. Orice s-ar
         * intampla acolo — calculatorul inchis, arhiva plina, o coloana lipsa
         * fiindca pe server n-a rulat inca migrarea — nu trebuie sa se intoarca
         * omului ca „Server Error" peste o semnatura care a reusit.
         *
         * S-a intamplat: o coloana adaugata azi lipsea pe serverul aplicatiei,
         * iar semnarea cadea cu 500 desi documentul era semnat si arhivat.
         */
        try {
            $this->arhiveaza($declaratie);
        } catch (\Throwable $e) {
            Jurnal::esec(
                'declaratie_arhivare',
                'Arhivarea declarației ' . $declaratie->tip . ' pentru ' . $declaratie->cui
                    . ' a eșuat după semnare: ' . $e->getMessage(),
                [],
                $declaratie->cui
            );
        }

        Jurnal::scrie(
            'declaratie_semnare',
            'A semnat declarația ' . $declaratie->tip . ' pentru ' . $declaratie->cui
                . ($declaratie->luna ? ' (' . $declaratie->luna . '/' . $declaratie->anul . ')' : ''),
            ['tip' => $declaratie->tip],
            $declaratie->cui
        );

        return response()->json(['success' => true, 'data' => $this->prezinta($declaratie)]);
    }

    public function depune(AnafDeclaratie $declaratie, DepunereService $depunere)
    {
        if (!ContextUtilizator::poateDepune()) {
            return $this->faraDreptul('depuneți declarații');
        }

        // Documentul semnat poate sta pe server sau doar in arhiva clientului.
        if (!$declaratie->cale_pdf_semnat && !$declaratie->arhiva_semnat) {
            return response()->json(['success' => false, 'message' => 'Declarația nu este semnată.'], 422);
        }

        $this->folosesteCertificatulEntitatii($declaratie);

        try {
            $depunere->autentificare();
            $rezultat = $depunere->depune($this->calePeServer($declaratie, 'cale_pdf_semnat', 'arhiva_semnat'));
        } catch (DeclaratieException $e) {
            Jurnal::esec(
                'declaratie_depunere',
                'Depunerea declarației ' . $declaratie->tip . ' pentru ' . $declaratie->cui . ' a eșuat: ' . $e->getMessage(),
                [],
                $declaratie->cui
            );

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        if ($rezultat['index_recipisa'] === null) {
            $declaratie->update([
                'pas' => 'eroare_depunere',
                'stare_declaratie' => $rezultat['eroare'],
            ]);

            Jurnal::esec(
                'declaratie_depunere',
                'ANAF a respins declarația ' . $declaratie->tip . ' pentru ' . $declaratie->cui . ': ' . $rezultat['eroare'],
                [],
                $declaratie->cui
            );

            return response()->json(['success' => false, 'message' => $rezultat['eroare']], 422);
        }

        $declaratie->update([
            'index_recipisa' => $rezultat['index_recipisa'],
            'data_depunere' => now(),
            'pas' => 'depus',
            'stare_declaratie' => null,
            /*
             * PDF-urile venite gata semnate sar peste semnare, care scria
             * certificatul; fara el, recipisa s-ar cauta apoi cu alt token.
             * Se retine deci certificatul cu care s-a depus.
             */
            'certificat_id' => $declaratie->certificat_id ?: app(CertificatService::class)->idCurent(),
        ]);

        // In arhiva clientului, documentul primeste acum indicele de incarcare
        // in nume: se vede din dosar ca s-a depus, si cu ce index.
        $this->redenumesteInArhiva($declaratie);

        Jurnal::scrie(
            'declaratie_depunere',
            'A depus declarația ' . $declaratie->tip . ' pentru ' . $declaratie->cui
                . ', index de încărcare ' . $rezultat['index_recipisa'],
            ['index_recipisa' => $rezultat['index_recipisa']],
            $declaratie->cui
        );

        return response()->json(['success' => true, 'data' => $this->prezinta($declaratie)]);
    }

    /** Verifica recipisele pentru toate declaratiile depuse care asteapta raspuns. */
    public function verificaRecipise(Request $request, RecipisaService $recipise)
    {
        $rezultat = $recipise->verificaToate((int) $request->query('zile', 60));

        Jurnal::scrie(
            'declaratie_recipise',
            sprintf(
                'A verificat recipisele: %d declarații verificate, %d recipise noi',
                $rezultat['verificate'],
                $rezultat['descarcate']
            ),
            $rezultat,
            null,
            $rezultat['erori'] === []
        );

        return response()->json(['success' => true, 'data' => $rezultat]);
    }

    /**
     * Aceeasi verificare, cu numaratoarea la vedere.
     *
     * Raspunsul curge: dupa fiecare declaratie cercetata, fila afla a cata e
     * din cate. Fara asta, o sesiune cu zeci de declaratii arata la fel cu una
     * impotmolita — o rotita care se invarte.
     */
    public function verificaRecipiseFlux(Request $request, RecipisaService $recipise)
    {
        $zile = (int) $request->query('zile', 60);

        return Flux::raspunde(function () use ($recipise, $zile) {
            foreach ($recipise->pasCuPas($zile) as $pas) {
                if ($pas['tip'] === 'gata') {
                    Jurnal::scrie(
                        'declaratie_recipise',
                        sprintf(
                            'A verificat recipisele: %d declarații verificate, %d recipise noi',
                            $pas['verificate'],
                            $pas['descarcate']
                        ),
                        $pas,
                        null,
                        $pas['erori'] === []
                    );
                }

                yield $pas;
            }
        });
    }

    /**
     * Serveste PDF-ul generat, cel semnat sau recipisa. Fisierele stau pe discul
     * privat (storage/app), deci nu pot fi accesate printr-un link direct.
     */
    /**
     * Explica erorile de validare pe intelesul oricui: ce inseamna fiecare mesaj
     * al validatorului ANAF si ce anume trebuie schimbat in fisierul XML.
     */
    /**
     * Raspunsul curge pe masura ce se prelucreaza: fiecare eroare pleaca de
     * indata ce e gata, ca utilizatorul sa vada primele rezultate cat timp se
     * lucreaza la urmatoarele. Formatul este NDJSON — cate un obiect pe rand.
     */
    public function explicaErori(AnafDeclaratie $declaratie, InterpretareErori $interpretare)
    {
        $xml = $this->xmlPentruLocalizare($declaratie);
        $erori = $declaratie->erori_validare;

        return response()->stream(function () use ($interpretare, $erori, $declaratie, $xml) {
            // Fara golirea tampoanelor, totul ar ajunge la client abia la final.
            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            foreach ($interpretare->pasCuPas($erori, $declaratie, $xml) as $pas) {
                echo json_encode($pas, JSON_UNESCAPED_UNICODE) . "\n";
                flush();
            }
        }, 200, [
            'Content-Type' => 'application/x-ndjson; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store',
            // Opreste tamponarea din serverele care stau in fata aplicatiei
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Continutul XML-ului, ca explicatia sa poata arata linia si coloana de
     * corectat. Fisierele foarte mari (SAF-T) se sar: cautarea in ele ar tine
     * cererea in loc, fara folos real.
     */
    protected function xmlPentruLocalizare(AnafDeclaratie $declaratie): ?string
    {
        if (!$declaratie->cale_xml || !Storage::exists($declaratie->cale_xml)) {
            return null;
        }

        $limita = 20 * 1024 * 1024;

        if (Storage::size($declaratie->cale_xml) > $limita) {
            return null;
        }

        return Storage::get($declaratie->cale_xml);
    }

    /**
     * Un singur PDF cu declaratiile semnate cerute, pentru tiparire.
     *
     * Documentul rezultat nu mai poarta semnaturile digitale: ele se pierd la
     * unirea paginilor. Pentru ANAF raman valabile fisierele semnate separat.
     */
    public function concateneaza(Request $request, ConcatenareService $concatenare)
    {
        $date = $request->validate([
            'id' => 'required|array|min:1',
            'id.*' => 'integer',
            'tip' => 'nullable|in:semnat,recipisa',
            'filigran' => 'nullable|boolean',
            // Cu „tipareste", hartia iese pe imprimanta omului, langa el
            'tipareste' => 'nullable|boolean',
        ]);

        $recipise = ($date['tip'] ?? 'semnat') === 'recipisa';

        $camp = $recipise ? 'cale_recipisa' : 'cale_pdf_semnat';
        $campArhiva = $recipise ? 'arhiva_recipisa' : 'arhiva_semnat';

        // Domeniul clientului se aplica singur: declaratiile altui client nu ies.
        $declaratii = AnafDeclaratie::whereIn('id', $date['id'])
            ->where(function ($intrebare) use ($camp, $campArhiva) {
                // Documentul poate sta pe server sau doar in arhiva clientului.
                $intrebare->whereNotNull($camp)->orWhereNotNull($campArhiva);
            })
            ->orderBy('cui')
            ->orderBy('tip')
            ->get();

        if ($declaratii->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => $camp === 'cale_recipisa'
                    ? 'Niciuna dintre declarațiile cerute nu are recipisă descărcată.'
                    : 'Niciuna dintre declarațiile cerute nu are PDF semnat.',
            ], 422);
        }

        $cai = [];
        $filigrane = [];

        foreach ($declaratii as $declaratie) {
            try {
                $cale = $this->calePeServer($declaratie, $camp, $campArhiva);
            } catch (ArhivaException $e) {
                // Un calculator inchis nu opreste tiparirea celorlalte documente.
                Jurnal::esec('declaratie_deschidere', 'Documentul nu a putut fi adus din arhiva locală: ' . $e->getMessage());

                continue;
            }

            if (!$cale) {
                continue;
            }

            $cai[] = $cale;

            // Filigranul poarta denumirea firmei fiecarui document in parte:
            // intr-un fisier pot intra documente ale mai multor societati.
            $filigrane[] = empty($date['filigran']) ? '' : ($declaratie->den_firma ?: $declaratie->cui);
        }

        if ($cai === []) {
            return response()->json([
                'success' => false,
                'message' => 'Documentele cerute nu au putut fi aduse din arhiva locală.',
            ], 502);
        }

        $filigrane = array_filter($filigrane) === [] ? [] : $filigrane;
        $ceAnume = $camp === 'cale_recipisa' ? 'recipise' : 'declarații semnate';

        // Tipărirea se face pe calculatorul omului, deci documentul unit nu se
        // mai întoarce la aplicație — iese direct pe hârtie.
        if (!empty($date['tipareste'])) {
            return $this->tipareste($request, $concatenare, $declaratii, $cai, $filigrane, $ceAnume);
        }

        try {
            $continut = $concatenare->uneste($cai, $filigrane);
        } catch (DeclaratieException $e) {
            Jurnal::esec('declaratie_deschidere', 'Unirea declarațiilor pentru tipărire a eșuat: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        Jurnal::scrie(
            'declaratie_deschidere',
            'A descărcat pentru tipărire ' . $declaratii->count() . ' ' . $ceAnume,
            ['declaratii' => $declaratii->pluck('id')->all()]
        );

        $nume = ($camp === 'cale_recipisa' ? 'recipise_' : 'declaratii_semnate_')
            . now()->format('Ymd_His') . '.pdf';

        return response($continut, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $nume . '"',
        ]);
    }

    /**
     * Trimite documentele la imprimanta utilizatorului.
     *
     * Imprimanta e a lui, aleasa la crearea contului, iar ea spune si pe ce
     * calculator iese hartia: cererea pleaca spre bridge-ul certificatului cu
     * care a fost aleasa, nu spre cel folosit pentru semnare.
     */
    protected function tipareste(
        Request $request,
        ConcatenareService $concatenare,
        $declaratii,
        array $cai,
        array $filigrane,
        string $ceAnume
    ) {
        $utilizator = $request->user();

        if (!$utilizator || !$utilizator->imprimanta) {
            return response()->json([
                'success' => false,
                'message' => 'Nu aveți o imprimantă aleasă. Administratorul firmei o poate seta din fila Utilizatori.',
            ], 422);
        }

        // Bridge-ul imprimantei, nu cel al declaratiei.
        if ($utilizator->imprimanta_certificat_id) {
            $certificat = AnafCertificat::find($utilizator->imprimanta_certificat_id);

            if ($certificat) {
                app(CertificatService::class)->foloseste($certificat);
            }
        }

        try {
            $rezultat = $concatenare->tipareste($cai, $filigrane, $utilizator->imprimanta);
        } catch (DeclaratieException $e) {
            Jurnal::esec('declaratie_tiparire', 'Tipărirea a eșuat: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        Jurnal::scrie(
            'declaratie_tiparire',
            'A trimis la imprimanta „' . $rezultat['imprimanta'] . '” ' . $declaratii->count() . ' ' . $ceAnume,
            ['declaratii' => $declaratii->pluck('id')->all()]
        );

        return response()->json([
            'success' => true,
            'data' => [
                'tiparit' => true,
                'imprimanta' => $rezultat['imprimanta'],
                'documente' => $declaratii->count(),
            ],
        ]);
    }

    public function fisier(AnafDeclaratie $declaratie, string $tip)
    {
        // Al doilea nume e cel din arhiva clientului, de unde se aduce documentul
        // cand pe server nu mai exista.
        $campuri = [
            'pdf' => ['cale_pdf', 'arhiva_semnat'],
            'semnat' => ['cale_pdf_semnat', 'arhiva_semnat'],
            'recipisa' => ['cale_recipisa', 'arhiva_recipisa'],
        ];

        if (!isset($campuri[$tip])) {
            return response()->json(['success' => false, 'message' => 'Tip de fișier necunoscut.'], 404);
        }

        try {
            $cale = $this->calePeServer($declaratie, $campuri[$tip][0], $campuri[$tip][1]);
        } catch (ArhivaException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 502);
        }

        if (!$cale) {
            return response()->json(['success' => false, 'message' => 'Fișierul nu a fost găsit.'], 404);
        }

        Jurnal::scrie(
            'declaratie_deschidere',
            'A deschis ' . ($tip === 'recipisa' ? 'recipisa' : 'PDF-ul') . ' declarației '
                . $declaratie->tip . ' pentru ' . $declaratie->cui,
            ['tip_fisier' => $tip],
            $declaratie->cui
        );

        // Acelasi nume ca in arhiva clientului, ca sa fie usor de recunoscut.
        $nume = ArhivaService::numeDeclaratie(
            $declaratie,
            $tip === 'recipisa' ? 'recipisa' : ($declaratie->index_recipisa ? 'depusa' : 'semnata'),
            'pdf'
        );

        /*
         * Continutul se trimite direct, nu prin response()->file(): documentul
         * poate fi o copie temporara adusa din arhiva clientului, iar aceea se
         * sterge la sfarsitul cererii.
         */
        return response(file_get_contents($cale), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $nume . '"',
        ]);
    }

    public function destroy(AnafDeclaratie $declaratie)
    {
        /*
         * O declaratie ajunsa la ANAF nu se mai sterge: ea si recipisa ei sunt
         * dovada depunerii. Interfata nici nu arata butonul, dar oprirea trebuie
         * sa fie si aici — altfel ar fi de ajuns o cerere directa.
         */
        if ($declaratie->index_recipisa || in_array($declaratie->pas, ['depus', 'finalizat'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Declarația a fost depusă la ANAF și nu mai poate fi ștearsă.',
            ], 422);
        }

        Jurnal::scrie(
            'declaratie_stergere',
            'A șters declarația ' . $declaratie->tip . ' pentru ' . $declaratie->cui,
            ['declaratie_id' => $declaratie->id],
            $declaratie->cui
        );

        foreach (['cale_xml', 'cale_pdf', 'cale_pdf_semnat'] as $camp) {
            if ($declaratie->$camp) {
                Storage::delete($declaratie->$camp);
            }
        }

        $declaratie->delete();

        return response()->json(['success' => true]);
    }

    protected function valideaza(AnafDeclaratie $declaratie, DukIntegrator $duk): AnafDeclaratie
    {
        $calePdf = preg_replace('/\.xml$/i', '', $declaratie->cale_xml) . '.pdf';

        try {
            $rezultat = $duk->valideazaSiGenereazaPdf(
                Storage::path($declaratie->cale_xml),
                $declaratie->tip,
                Storage::path($calePdf),
                null,
                $declaratie->anul,
                $declaratie->luna,
                $declaratie->perioada_tip
            );
        } catch (DeclaratieException $e) {
            $declaratie->update(['pas' => 'eroare_validare', 'erori_validare' => $e->getMessage()]);

            return $declaratie->fresh();
        }

        $declaratie->update($rezultat['valid']
            ? ['pas' => 'validat', 'erori_validare' => null, 'cale_pdf' => $calePdf]
            : ['pas' => 'eroare_validare', 'erori_validare' => $rezultat['erori'], 'cale_pdf' => null]);

        $declaratie = $declaratie->fresh();

        /*
         * La SAF-T, validarea nu e tot. Ea spune ca declaratia e bine intocmita;
         * consistenta spune daca cifrele din ea se potrivesc intre ele. Se face
         * aici, unde trec si incarcarea, si revalidarea, ca sa nu ramana pe
         * seama cuiva sa-si aduca aminte de ea inainte de depunere.
         */
        $declaratie = $this->verificaSaft($declaratie);

        /*
         * Iar decontul si SAF-T-ul aceleiasi luni se pun fata in fata: daca nu
         * spun acelasi lucru, una din ele e gresita. Se face in amandoua
         * sensurile — la validarea D300 se cauta D406, si pe dos —, iar cand se
         * gaseste perechea, si ea se insemneaza, ca sa se vada din amandoua
         * partile.
         */
        return $this->potrivesteDecontul($declaratie);
    }

    /**
     * Decontul si SAF-T-ul aceleiasi luni, pusi fata in fata.
     *
     * Nu opreste nimic: o declaratie care nu se potriveste ramane valida si se
     * poate depune. Potrivirea e o parere, si se scrie ca atare — dar se scrie
     * pe amandoua declaratiile, fiindca cine se uita la una vrea sa stie si de
     * cealalta.
     */
    protected function potrivesteDecontul(AnafDeclaratie $declaratie): AnafDeclaratie
    {
        if (!in_array($declaratie->tip, ['D300', 'D406'], true)) {
            return $declaratie;
        }

        /*
         * O declaratie respinsa la validare n-are ce spune despre alta: cifrele
         * ei n-au trecut nici macar de ANAF. Se compara dupa ce trece.
         */
        if ($declaratie->pas !== 'validat') {
            return $declaratie;
        }

        if (!Schema::hasColumn('anaf_declaratii', 'potrivire_stare')) {
            return $declaratie;
        }

        $potrivire = app(PotrivireDecont::class)->pentru($declaratie);

        $this->scriePotrivirea($declaratie, $potrivire);

        /*
         * Perechea a fost validata mai demult, cand cealalta declaratie inca nu
         * era in aplicatie; acum se stie si despre ea, asa ca i se scrie si ei
         * raspunsul — altfel ar ramane cu „n-are cu ce fi comparata".
         */
        if (!empty($potrivire['perechea']['id'])) {
            $perechea = AnafDeclaratie::find($potrivire['perechea']['id']);

            if ($perechea) {
                $this->scriePotrivirea($perechea, app(PotrivireDecont::class)->pentru($perechea));
            }
        }

        return $declaratie->fresh();
    }

    /** Rezultatul potrivirii, scris pe declaratie. */
    protected function scriePotrivirea(AnafDeclaratie $declaratie, array $potrivire): void
    {
        $declaratie->update([
            'potrivire_stare' => $potrivire['stare'],
            'potrivire_numar' => $potrivire['numar'],
            'potrivire_detalii' => json_encode($potrivire, JSON_UNESCAPED_UNICODE),
            'potrivire_la' => now(),
        ]);
    }

    /**
     * Consistenta unui SAF-T, cu unealta ANAF.
     *
     * Nu opreste nimic: o declaratie cu neconcordante ramane valida si se poate
     * depune. Verificarea e o parere, si se scrie ca atare pe declaratie. De
     * aceea nici un esec al ei nu strica fluxul — se retine ca „imposibil", cu
     * motivul, si atat.
     */
    protected function verificaSaft(AnafDeclaratie $declaratie, ?VerificareSaft $verificare = null): AnafDeclaratie
    {
        if ($declaratie->tip !== 'D406' || !$declaratie->cale_xml || !Storage::exists($declaratie->cale_xml)) {
            return $declaratie;
        }

        if (!Schema::hasColumn('anaf_declaratii', 'verificare_stare')) {
            return $declaratie;
        }

        $verificare = $verificare ?: app(VerificareSaft::class);

        try {
            $rezultat = $verificare->verifica(Storage::path($declaratie->cale_xml));
        } catch (DeclaratieException $e) {
            $declaratie->update([
                'verificare_stare' => 'imposibil',
                'verificare_numar' => null,
                'verificare_erori' => json_encode(['mesaj' => $e->getMessage()], JSON_UNESCAPED_UNICODE),
                'verificare_la' => now(),
            ]);

            return $declaratie->fresh();
        }

        $declaratie->update([
            'verificare_stare' => $rezultat['stare'],
            'verificare_numar' => $rezultat['numar'],
            'verificare_erori' => json_encode($rezultat, JSON_UNESCAPED_UNICODE),
            'verificare_la' => now(),
        ]);

        return $declaratie->fresh();
    }

    /**
     * Eroarea, taiata la cat are rost sa calatoreasca spre tabel.
     *
     * Un SAF-T respins poate avea sute de mii de caractere de erori. Trimise
     * intregi, pentru fiecare rand din tabel, ar face raspunsul de zeci de MB
     * si ar bloca pagina. Textul intreg ramane in baza de date si se vede in
     * SPV Wizard.
     */
    /**
     * Cât s-a găsit la validare, spus în două cuvinte pentru jurnal.
     *
     * Erorile întregi rămân pe declarație și se citesc din tabel; în jurnal
     * intră doar mărimea problemei.
     */
    protected function cateErori(AnafDeclaratie $declaratie): ?string
    {
        if (!$declaratie->erori_validare) {
            return null;
        }

        // Fiecare eroare DUKIntegrator ocupă două rânduri: secțiunea și explicația.
        $sectiuni = preg_match_all('/^E: /m', $declaratie->erori_validare);

        return $sectiuni > 0
            ? $sectiuni . ($sectiuni === 1 ? ' eroare de validare' : ' erori de validare')
            : 'validare respinsă';
    }

    protected function eroareScurtata(?string $eroare): ?string
    {
        if ($eroare === null || mb_strlen($eroare) <= self::EROARE_IN_TABEL) {
            return $eroare;
        }

        $randuri = preg_split('/\r?\n/', $eroare) ?: [];
        $taiat = mb_substr($eroare, 0, self::EROARE_IN_TABEL);

        return rtrim($taiat) . "\n… lista continuă (" . count($randuri)
            . ' rânduri în total). Apăsați „SPV Wizard" pentru explicații.';
    }

    protected function prezinta(AnafDeclaratie $declaratie): array
    {
        $societate = $this->societateInrolata($declaratie->cui);

        return array_merge($declaratie->toArray(), [
            // Fara entitate inrolata declaratia se poate semna, dar nu se poate
            // depune: SPV primeste doar de la cine are drept pentru acel CUI.
            'inrolata' => (bool) $societate,
            'certificat_inrolare' => $societate ? optional($societate->certificat)->cn : null,
            // Coloana „Eroare" din tabel arata orice esec, indiferent de pasul
            // la care a aparut; doar cele de validare pot fi insa explicate,
            // pentru ca doar ele vin de la validatorul ANAF.
            'eroare' => $this->eroareScurtata($declaratie->erori_validare ?: $declaratie->eroare_semnare),
            'eroare_de_validare' => (bool) $declaratie->erori_validare,
            'clasificare' => RecipisaService::clasifica($declaratie->stare_declaratie),
            // Consistenta se verifica numai la SAF-T; la restul, butonul n-are ce cauta.
            'are_consistenta' => $declaratie->tip === 'D406',
            // Potrivirea are rost numai intre cele doua declaratii ale aceleiasi luni.
            'are_potrivire' => in_array($declaratie->tip, ['D300', 'D406'], true),
            'potrivire_la' => Format::dataOra($declaratie->potrivire_la),
            'verificare_la' => Format::dataOra($declaratie->verificare_la),
            'certificat_nume' => optional($declaratie->certificat)->cn,
            'data_depunere' => Format::dataOra($declaratie->data_depunere),
            'data_recipisa' => Format::dataOra($declaratie->data_recipisa),
            'created_at' => Format::dataOra($declaratie->created_at),
        ]);
    }
}
