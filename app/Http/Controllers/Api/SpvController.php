<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SpvMesaj;
use App\Services\Anaf\Spv\SpvClient;
use App\Services\Anaf\Format;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Spv\SocietatiService;
use App\Services\Anaf\Spv\SolicitareService;
use App\Services\Anaf\Spv\SpvException;
use App\Services\Anaf\Spv\SpvStorage;
use App\Support\Flux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpvController extends Controller
{
    public function index(Request $request, SpvClient $spvClient, SpvStorage $storage)
    {
        try {
            $cif = $request->query('cif');
            $zile = (int) $request->query('zile', 30);
            $mesaje = $spvClient->listaMesaje($zile, $cif ?: null);

            $salvate = [];

            $noi = 0;

            if (isset($mesaje['mesaje']) && is_array($mesaje['mesaje'])) {
                foreach ($mesaje['mesaje'] as $item) {
                    $mesaj = $storage->saveMessage($item, $cif ?: null);

                    /*
                     * „Nou" inseamna abia intrat, nu „intors iar de ANAF": lista
                     * vine cu toata fereastra de zile la fiecare citire.
                     *
                     * Se numara doar ce se si arata aici: o recipisa abia venita
                     * se vede in fila declaratiilor, iar un „un mesaj nou" fara
                     * niciun rand nou in tabel ar parea o scapare.
                     */
                    if ($mesaj->wasRecentlyCreated && $this->filaCareAduce($mesaj) === null) {
                        $noi++;
                    }

                    $salvate[] = $mesaj;
                }
            }

            /*
             * Raspunsurile gasite in SPV isi capata randul in fila de
             * solicitari, chiar daca cererea n-a plecat din aplicatie: de pe
             * site-ul ANAF, sau inainte de a fi folosita aplicatia. Cum ele nu
             * se mai arata aici, altfel n-ar fi de vazut nicaieri.
             */
            $solicitariGasite = app(SolicitareService::class)->inregistreazaCeleGasite(
                $mesaje['mesaje'] ?? [],
                optional($request->user())->id
            );

            $descarcare = $request->has('descarca')
                ? $request->boolean('descarca')
                : config('anaf.spv.descarcare_automata');

            $rezultatDescarcare = $descarcare
                ? $this->descarcaFisiereLipsa($salvate, $storage)
                : ['descarcate' => 0, 'ramase' => 0, 'erori' => []];

            Jurnal::scrie(
                'mesaje_citire',
                sprintf(
                    'A citit mesajele SPV pe %d zile%s: %d mesaje noi, %d fișiere descărcate%s',
                    $zile,
                    $cif ? ' pentru CIF ' . $cif : '',
                    $noi,
                    $rezultatDescarcare['descarcate'],
                    $solicitariGasite ? ', ' . $solicitariGasite . ' solicitări găsite' : ''
                ),
                $rezultatDescarcare + ['solicitari_gasite' => $solicitariGasite],
                $cif ?: null
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'titlu' => $mesaje['titlu'] ?? null,
                    // Tabelul arata tot istoricul, nu doar mesajele din interogarea
                    // curenta — "zile" limiteaza doar ce se cere de la ANAF.
                    'mesaje' => $this->istoric($request),
                    // Cate au fost intoarse de ANAF si cate dintre ele erau noi
                    'intoarse' => count($salvate),
                    'noi' => $noi,
                    // Solicitari carora li s-a gasit raspunsul, dar care nu erau
                    // in lista: se vad de acum in fila „Solicitări ANAF".
                    'solicitari_gasite' => $solicitariGasite,
                ],
                'descarcare' => $rezultatDescarcare,
            ]);
        } catch (SpvException $e) {
            Jurnal::esec('mesaje_citire', 'Citirea mesajelor SPV a eșuat: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mesajele deja stocate, fara sa se intrebe ANAF.
     *
     * Deschiderea filei si reincarcarea paginii nu au de ce sa consume din
     * limita de apeluri: mesajele sunt in baza de date, iar cele noi se aduc
     * cand omul apasa „Descarcă mesaje".
     */
    public function stocate(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'mesaje' => $this->istoric($request),
                'noi' => 0,
            ],
            'descarcare' => ['descarcate' => 0, 'ramase' => 0, 'erori' => []],
        ]);
    }

    /**
     * Urmatorul lot de documente lipsa, fara sa se mai intrebe ANAF de lista.
     *
     * O citire aduce cel mult atatea documente cate incap intr-o cerere de
     * pagina: fiecare are pauza ceruta de ANAF si drumul prin tunel, iar un lot
     * prea mare ar depasi rabdarea serverului de web si omul ar vedea o eroare
     * in locul lucrului deja facut. Restul se aduceau la urmatoarea apasare —
     * dar nimeni n-are de ce sa apese de cinci ori pentru o suta de mesaje.
     *
     * De aici incolo apasarea e una singura: fila cere lot dupa lot pana nu mai
     * ramane nimic. Lista nu se mai cere de la ANAF la fiecare lot — mesajele
     * sunt deja in baza de date —, deci nu se consuma apeluri degeaba.
     */
    public function descarcaLipsa(Request $request, SpvStorage $storage)
    {
        $mesaje = $this->neaduse($request);

        try {
            $rezultat = $this->descarcaFisiereLipsa($mesaje, $storage);
        } catch (SpvException $e) {
            Jurnal::esec('mesaje_descarcare', 'Aducerea documentelor lipsă a eșuat: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'data' => ['mesaje' => $this->istoric($request)],
            'descarcare' => $rezultat,
        ]);
    }

    /**
     * Aceleasi documente, aduse cu numaratoarea la vedere.
     *
     * Aducerea a zeci de documente tine minute — fiecare are pauza ceruta de
     * ANAF si drumul pana la tokenul clientului. Cu un raspuns obisnuit, omul
     * vede o rotita si atat: nu stie daca merge, unde s-a ajuns, sau daca s-a
     * impotmolit. Aici raspunsul curge, si dupa fiecare document se spune al
     * catelea e din cate.
     *
     * Cum se stie de la bun inceput cate sunt, nu mai e nevoie de loturi: se
     * aduc toate intr-o singura apasare, iar legatura nu tace niciodata destul
     * cat sa para cazuta.
     */
    public function descarcaLipsaFlux(Request $request, SpvStorage $storage)
    {
        $deDescarcat = $this->deDescarcat($this->neaduse($request));

        return Flux::raspunde(function () use ($deDescarcat, $storage, $request) {
            $total = count($deDescarcat);

            yield ['tip' => 'inceput', 'total' => $total];

            $descarcate = 0;
            $erori = [];

            foreach ($deDescarcat as $i => $mesaj) {
                // Fiecare document isi cere ragazul lui, socotit de la capat.
                if (function_exists('set_time_limit')) {
                    @set_time_limit(120);
                }

                $reusit = true;

                try {
                    // Documentul merge de la ANAF drept in dosarul firmei, la client.
                    $storage->aduce($mesaj);

                    $descarcate++;
                } catch (SpvException $e) {
                    $mesaj->update([
                        'incercari' => $mesaj->incercari + 1,
                        'ultima_eroare' => $e->getMessage(),
                    ]);

                    $erori[] = $mesaj->mesaj_id . ': ' . $e->getMessage();
                    $reusit = false;
                }

                yield [
                    'tip' => 'pas',
                    'facute' => $i + 1,
                    'total' => $total,
                    'reusit' => $reusit,
                    'ce' => trim(($mesaj->tip ?: 'Document') . ' ' . $mesaj->cif),
                ];
            }

            Jurnal::scrie(
                'mesaje_descarcare',
                sprintf('A adus documentele lipsă: %d din %d', $descarcate, $total),
                ['descarcate' => $descarcate, 'erori' => $erori],
                $request->query('cif') ?: null,
                $erori === []
            );

            yield [
                'tip' => 'gata',
                'descarcate' => $descarcate,
                'ramase' => max(0, $total - $descarcate - count($erori)),
                'erori' => $erori,
                'mesaje' => $this->istoric($request),
            ];
        });
    }

    /**
     * Mesajele al caror document n-a fost adus inca, dupa cat se poate sti din
     * baza de date. Se scot de aici cele aduse deja si cele care au esuat de
     * prea multe ori: altfel s-ar citi tot tabelul ca pe urma sa fie aruncat
     * aproape intreg.
     *
     * @return SpvMesaj[]
     */
    protected function neaduse(Request $request): array
    {
        return SpvMesaj::query()
            ->whereNull('arhiva_cale')
            ->where('incercari', '<', (int) config('anaf.spv.incercari_max'))
            ->when($request->filled('cif'), function ($intrebare) use ($request) {
                return $intrebare->where('cif', 'like', '%' . $request->query('cif') . '%');
            })
            ->orderByDesc('data_creare')
            ->orderByDesc('id')
            ->get()
            ->all();
    }

    /**
     * Toate mesajele salvate vreodata, cele mai noi primele. Filtrul de CIF se
     * aplica, dar nu si fereastra de zile — aceea priveste doar apelul la ANAF.
     */
    protected function istoric(Request $request): array
    {
        $query = SpvMesaj::query()
            ->orderByDesc('data_creare')
            ->orderByDesc('id');

        /*
         * Recipisele si raspunsurile la solicitari nu se mai arata aici.
         *
         * Locul lor e fila din care se aduc, unde stau langa documentul la care
         * raspund: recipisa langa declaratia depusa, raspunsul langa solicitarea
         * ceruta. Aici n-ar fi decat inca un rand din care omul n-are ce afla.
         *
         * Se scot din interogare, nu la sfarsit, ca limita de randuri sa numere
         * ce se vede cu adevarat. Mesajele fara tip raman: nu se stie ce sunt,
         * deci nu se ascund.
         */
        foreach (array_keys(config('anaf.spv.tipuri_din_alte_file', [])) as $bucata) {
            $query->where(function ($intrebare) use ($bucata) {
                $intrebare->whereNull('tip')
                    ->orWhere('tip', 'not like', '%' . $bucata . '%');
            });
        }

        if ($request->filled('cif')) {
            $query->where('cif', 'like', '%' . $request->query('cif') . '%');
        }

        $denumiri = SocietatiService::denumiri();

        return $query->with('certificat')
            ->limit((int) $request->query('limita', 500))
            ->get()
            ->map(function (SpvMesaj $mesaj) use ($denumiri) {
                return $this->prezinta($mesaj, $denumiri);
            })
            ->all();
    }

    /**
     * Descarca fisierele mesajelor care nu au fost inca preluate. Numarul e
     * limitat pe cerere (ANAF impune o pauza intre apeluri), iar mesajele care
     * esueaza repetat sunt sarite ca sa nu blocheze restul listei.
     *
     * @param  SpvMesaj[]  $mesaje
     * @return array{descarcate: int, ramase: int, erori: array}
     */
    protected function descarcaFisiereLipsa(array $mesaje, SpvStorage $storage): array
    {
        $limita = (int) config('anaf.spv.limita_descarcari');

        $deDescarcat = $this->deDescarcat($mesaje);

        $lot = array_slice($deDescarcat, 0, $limita);

        // Fiecare descarcare asteapta pauza impusa de ANAF, deci un lot intreg
        // poate depasi limita implicita de executie.
        if ($lot !== [] && function_exists('set_time_limit')) {
            @set_time_limit(60 + count($lot) * 15);
        }

        $descarcate = 0;
        $erori = [];

        foreach ($lot as $mesaj) {
            try {
                // Documentul merge de la ANAF drept in dosarul firmei, la client.
                $storage->aduce($mesaj);

                $descarcate++;
            } catch (SpvException $e) {
                $mesaj->update([
                    'incercari' => $mesaj->incercari + 1,
                    'ultima_eroare' => $e->getMessage(),
                ]);

                $erori[] = $mesaj->mesaj_id . ': ' . $e->getMessage();
            }
        }

        return [
            'descarcate' => $descarcate,
            'ramase' => max(0, count($deDescarcat) - count($lot)),
            'erori' => $erori,
        ];
    }

    /**
     * Care dintre mesaje mai au un document de adus.
     *
     * @param  SpvMesaj[]  $mesaje
     * @return SpvMesaj[]
     */
    protected function deDescarcat(array $mesaje): array
    {
        $incercariMax = (int) config('anaf.spv.incercari_max');

        return array_values(array_filter($mesaje, function (SpvMesaj $mesaj) use ($incercariMax) {
            // Recipisele si raspunsurile la solicitari se aduc din filele lor,
            // unde se si leaga de documentul la care raspund. Cerute si de aici,
            // ar consuma de doua ori din limita de apeluri catre ANAF.
            if ($this->filaCareAduce($mesaj) !== null) {
                return false;
            }

            return $this->lipsesteFisierul($mesaj) && $mesaj->incercari < $incercariMax;
        }));
    }

    /**
     * Documentul lipseste doar daca nu e nici pe server, nici in arhiva
     * clientului — altfel ar fi cerut de la ANAF a doua oara degeaba.
     */
    /**
     * Fila din care se aduce documentul acestui mesaj, daca nu de aici.
     *
     * Potrivirea e pe bucata de text si fara sa tina cont de litere mari sau
     * mici: ANAF scrie „RECIPISA", dar si „Recipisa depunere declaratie".
     */
    protected function filaCareAduce(SpvMesaj $mesaj): ?string
    {
        $tip = mb_strtolower(trim((string) $mesaj->tip));

        if ($tip === '') {
            return null;
        }

        foreach (config('anaf.spv.tipuri_din_alte_file', []) as $bucata => $fila) {
            if (mb_strpos($tip, mb_strtolower($bucata)) !== false) {
                return $fila;
            }
        }

        return null;
    }

    protected function lipsesteFisierul(SpvMesaj $mesaj): bool
    {
        if ($mesaj->arhiva_cale) {
            return false;
        }

        return !$mesaj->cale_fisier || !Storage::exists($mesaj->cale_fisier);
    }

    protected function prezinta(SpvMesaj $mesaj, array $denumiri = []): array
    {
        return [
            'id' => $mesaj->mesaj_id,
            'tip' => $mesaj->tip,
            'cif' => $mesaj->cif,
            'den_firma' => $denumiri[$mesaj->cif] ?? null,
            // Cand documentul se aduce din alta fila, aici scrie care
            'fila_care_aduce' => $this->filaCareAduce($mesaj),
            'data_creare' => Format::dataOra($mesaj->data_creare),
            'id_solicitare' => $mesaj->id_solicitare,
            'detalii' => $mesaj->detalii,
            'certificat' => optional($mesaj->certificat)->cn,
            'descarcat' => !$this->lipsesteFisierul($mesaj),
            'descarcat_la' => Format::dataOra($mesaj->descarcat_la),
            'ultima_eroare' => $mesaj->ultima_eroare,
        ];
    }
}
