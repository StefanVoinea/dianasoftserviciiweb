<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SpvMesaj;
use App\Services\Anaf\Spv\SpvClient;
use App\Services\Anaf\Format;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Spv\SocietatiService;
use App\Services\Anaf\Spv\SpvException;
use App\Services\Anaf\Spv\SpvStorage;
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

                    // „Nou" inseamna abia intrat, nu „intors iar de ANAF": lista
                    // vine cu toata fereastra de zile la fiecare citire.
                    if ($mesaj->wasRecentlyCreated) {
                        $noi++;
                    }

                    $salvate[] = $mesaj;
                }
            }

            $descarcare = $request->has('descarca')
                ? $request->boolean('descarca')
                : config('anaf.spv.descarcare_automata');

            $rezultatDescarcare = $descarcare
                ? $this->descarcaFisiereLipsa($salvate, $spvClient, $storage)
                : ['descarcate' => 0, 'ramase' => 0, 'erori' => []];

            Jurnal::scrie(
                'mesaje_citire',
                sprintf(
                    'A citit mesajele SPV pe %d zile%s: %d mesaje noi, %d fișiere descărcate',
                    $zile,
                    $cif ? ' pentru CIF ' . $cif : '',
                    $noi,
                    $rezultatDescarcare['descarcate']
                ),
                $rezultatDescarcare,
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
     * Toate mesajele salvate vreodata, cele mai noi primele. Filtrul de CIF se
     * aplica, dar nu si fereastra de zile — aceea priveste doar apelul la ANAF.
     */
    protected function istoric(Request $request): array
    {
        $query = SpvMesaj::query()
            ->orderByDesc('data_creare')
            ->orderByDesc('id');

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
    protected function descarcaFisiereLipsa(array $mesaje, SpvClient $spvClient, SpvStorage $storage): array
    {
        $limita = (int) config('anaf.spv.limita_descarcari');
        $incercariMax = (int) config('anaf.spv.incercari_max');

        $deDescarcat = array_values(array_filter($mesaje, function (SpvMesaj $mesaj) use ($incercariMax) {
            // Recipisele si raspunsurile la solicitari se aduc din filele lor,
            // unde se si leaga de documentul la care raspund. Cerute si de aici,
            // ar consuma de doua ori din limita de apeluri catre ANAF.
            if ($this->filaCareAduce($mesaj) !== null) {
                return false;
            }

            return $this->lipsesteFisierul($mesaj) && $mesaj->incercari < $incercariMax;
        }));

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
                $fisier = $spvClient->descarcare($mesaj->mesaj_id);
                $salvat = $storage->saveFile($fisier, 'downloads');

                $mesaj->update([
                    'cale_fisier' => $salvat['path'],
                    'hash_fisier' => $salvat['hash'],
                    'descarcat_la' => now(),
                    'ultima_eroare' => null,
                ]);

                // Documentul ramane la client, in dosarul firmei lui.
                $storage->arhiveazaMesaj($mesaj, $fisier);

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
