<?php

namespace App\Services\Anaf\Spv;

use App\Models\AlertaMesajSpv;
use App\Models\AnafSocietate;
use App\Models\SpvSolicitare;
use App\Models\VectorFiscal;
use App\Services\Anaf\Arhiva\ArhivaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


/**
 * Solicitarea de documente din SPV si preluarea raspunsurilor: cererea se trimite
 * prin webserviciul ANAF, iar raspunsul apare ca mesaj in lista SPV, de unde e
 * descarcat si interpretat (vector fiscal, situatie sintetica, date identificare).
 */
class SolicitareService
{
    protected $client;
    protected $storage;
    protected $parser;
    protected $certificate;

    /** Arhiva de pe calculatorul clientului; lipseste doar in teste. */
    protected $arhiva;

    public function __construct(
        SpvClient $client,
        SpvStorage $storage,
        VectorFiscalParser $parser,
        CertificatService $certificate,
        ?ArhivaService $arhiva = null
    ) {
        $this->client = $client;
        $this->storage = $storage;
        $this->parser = $parser;
        $this->certificate = $certificate;
        $this->arhiva = $arhiva;
    }

    public function solicita(string $cui, string $tipDocument, array $optiuni = [], ?int $userId = null): SpvSolicitare
    {
        $raspuns = $this->client->cerere($tipDocument, $cui, $optiuni);

        return SpvSolicitare::create([
            'cif' => $cui,
            'den_firma' => optional(AnafSocietate::where('cif', $cui)->first())->denumire
                ?: optional(VectorFiscal::where('cui', $cui)->first())->denumire,
            'tip_document' => $tipDocument,
            'an' => $optiuni['an'] ?? null,
            'luna' => $optiuni['luna'] ?? null,
            'motiv' => $optiuni['motiv'] ?? null,
            'numar_inregistrare' => $optiuni['numar_inregistrare'] ?? null,
            'cui_pui' => $optiuni['cui_pui'] ?? null,
            'id_solicitare' => $raspuns['id_solicitare'] ?? ($raspuns['idSolicitare'] ?? null),
            'data_solicitarii' => now(),
            'detalii' => $raspuns['titlu'] ?? null,
            'stare' => 'trimisa',
            'certificat_id' => $this->certificate->idCurent(),
            'user_id' => $userId,
        ]);
    }

    /**
     * Inscrie in lista solicitarile ale caror raspunsuri se afla in SPV.
     *
     * Nu tot ce sta in SPV a fost cerut din aplicatie: cererile facute de pe
     * site-ul ANAF, sau inainte de a fi folosita aplicatia, isi au raspunsul
     * acolo fara ca aici sa existe randul lor. Iar cum raspunsurile nu se mai
     * arata in fila de mesaje, ele n-ar fi de vazut nicaieri.
     *
     * Solicitarea nou inscrisa ramane „in asteptare": documentul se aduce la
     * urmatoarea preluare, ca oricare alta, si atunci se si interpreteaza.
     *
     * @param array $mesaje mesajele intoarse de ANAF, asa cum vin
     * @param ?int  $userId cine a citit mesajele — el ajunge stapanul randului,
     *                      altfel un utilizator obisnuit nu l-ar vedea deloc
     *
     * @return int cate solicitari s-au adaugat
     */
    public function inregistreazaCeleGasite(array $mesaje, ?int $userId = null): int
    {
        $adaugate = 0;

        foreach ($mesaje as $mesaj) {
            $idSolicitare = trim((string) ($mesaj['id_solicitare'] ?? ''));

            // Numai raspunsurile la solicitari poarta numarul cererii.
            if ($idSolicitare === '' || mb_stripos((string) ($mesaj['tip'] ?? ''), 'RASPUNS SOLICITARE') === false) {
                continue;
            }

            $existenta = SpvSolicitare::query()->totiUtilizatorii()
                ->where('id_solicitare', $idSolicitare)
                ->exists();

            if ($existenta) {
                continue;
            }

            $cerut = $this->ceS_aCerut($mesaj['detalii'] ?? null);

            SpvSolicitare::create([
                'cif' => $cerut['cif'] ?: ($mesaj['cif'] ?? ''),
                'den_firma' => optional(AnafSocietate::where('cif', $cerut['cif'] ?: ($mesaj['cif'] ?? ''))->first())->denumire,
                'tip_document' => $cerut['tip'],
                'id_solicitare' => $idSolicitare,
                // Cand a fost ceruta nu se stie: cererea n-a plecat de aici.
                'data_solicitarii' => null,
                'detalii' => $mesaj['detalii'] ?? null,
                'stare' => 'trimisa',
                'certificat_id' => $this->certificate->idCurent(),
                'user_id' => $userId,
            ]);

            $adaugate++;
        }

        return $adaugate;
    }

    /**
     * Ce s-a cerut, citit din textul raspunsului.
     *
     * ANAF nu scrie textul acesta la fel de fiecare data:
     *
     *   „duplicat VECTOR FISCAL pentru CUI 15208744"
     *   „Obligatii de plata pentru CNP 1720913216197"
     *   „Document Fisa Rol pentru CIF=15208744 (cod arondare ...)"
     *
     * Se ia bucata dinaintea codului, iar felul se aduce — cand se poate — la
     * scrierea din nomenclatorul nostru, ca documentul sa se aseze in arhiva la
     * fel ca cele cerute din aplicatie. Se incearca intai textul intreg: pot
     * exista feluri care chiar incep cu „Duplicat".
     *
     * @return array{tip: string, cif: string}
     */
    protected function ceS_aCerut(?string $detalii): array
    {
        $tipar = '/^\s*(.+?)\s+pentru\s+(?:CUI|CIF|CNP)\s*[:=\s]*([0-9A-Za-z]+)/iu';

        if (!preg_match($tipar, (string) $detalii, $bucati)) {
            return ['tip' => 'Document SPV', 'cif' => ''];
        }

        $intreg = trim($bucati[1]);

        // Cuvintele de prisos puse de ANAF inaintea felului documentului.
        $curatat = trim(preg_replace('/^(?:duplicat|document)\s+/iu', '', $intreg));

        foreach ([$intreg, $curatat] as $candidat) {
            foreach (array_keys(config('anaf.spv.tipuri_documente', [])) as $cunoscut) {
                if (mb_strtolower($cunoscut) === mb_strtolower($candidat)) {
                    return ['tip' => $cunoscut, 'cif' => $bucati[2]];
                }
            }
        }

        return ['tip' => $curatat ?: $intreg, 'cif' => $bucati[2]];
    }

    /**
     * Cauta in mesajele SPV raspunsurile la solicitarile in asteptare, le descarca
     * si le interpreteaza.
     *
     * Cu o limita data se preiau cel mult atatea raspunsuri, iar restul raman
     * pentru chemarea urmatoare: fiecare descarcare are pauza ei impusa de ANAF,
     * si o suta de raspunsuri nu incap intr-o singura cerere web.
     *
     * @return array{verificate: int, preluate: int, ramase: int, erori: array}
     */
    public function preiaRaspunsuri(int $zile = 60, ?int $limita = null): array
    {
        $solicitari = SpvSolicitare::inAsteptare()->get();

        if ($solicitari->isEmpty()) {
            return ['verificate' => 0, 'preluate' => 0, 'ramase' => 0, 'erori' => []];
        }

        $erori = [];

        try {
            $lista = $this->client->listaMesaje($zile);
            $mesaje = isset($lista['mesaje']) && is_array($lista['mesaje']) ? $lista['mesaje'] : [];
        } catch (SpvException $e) {
            return [
                'verificate' => $solicitari->count(),
                'preluate' => 0,
                'ramase' => $solicitari->count(),
                'erori' => ['SPV: ' . $e->getMessage()],
            ];
        }

        $preluate = 0;
        $incercate = 0;

        foreach ($solicitari as $solicitare) {
            $mesaj = $this->potrivesteMesaj($solicitare, $mesaje);

            if ($mesaj === null) {
                continue;
            }

            $incercate++;

            try {
                $this->preia($solicitare, $mesaj);
                $preluate++;
            } catch (\Exception $e) {
                $erori[] = $solicitare->cif . ' / ' . $solicitare->tip_document . ': ' . $e->getMessage();
            }

            /*
             * Se numara incercarile, nu izbanzile: si o descarcare picata a
             * costat drumul pana la ANAF. Altfel, cand raspunsurile nu vin, lotul
             * s-ar intinde peste toata lista si am ajunge iar la o cerere web
             * care tine minute.
             */
            if ($limita !== null && $incercate >= $limita) {
                break;
            }
        }

        return [
            'verificate' => $solicitari->count(),
            'preluate' => $preluate,
            // Cate mai asteapta raspuns dupa lotul acesta
            'ramase' => SpvSolicitare::inAsteptare()->count(),
            'erori' => $erori,
        ];
    }

    /**
     * Aceeasi preluare, dar spusa pe masura ce se face.
     *
     * Fiecare raspuns are pauza ceruta de ANAF si drumul pana la tokenul
     * clientului, deci o preluare cu zeci de raspunsuri tine minute. Cu un
     * raspuns obisnuit, omul vede o rotita si atat.
     *
     * Se numara cele care chiar au ce aduce, nu toate solicitarile in asteptare:
     * „3 din 3" spune adevarul, „3 din 120" ar parea ca s-a oprit la inceput.
     *
     * @return \Generator randurile trimise filei, in ordinea lucrului
     */
    public function pasCuPasRaspunsuri(int $zile = 60): \Generator
    {
        $solicitari = SpvSolicitare::inAsteptare()->get();

        if ($solicitari->isEmpty()) {
            yield ['tip' => 'inceput', 'total' => 0];
            yield ['tip' => 'gata', 'verificate' => 0, 'preluate' => 0, 'ramase' => 0, 'erori' => []];

            return;
        }

        try {
            $lista = $this->client->listaMesaje($zile);
            $mesaje = isset($lista['mesaje']) && is_array($lista['mesaje']) ? $lista['mesaje'] : [];
        } catch (SpvException $e) {
            yield ['tip' => 'inceput', 'total' => 0];
            yield [
                'tip' => 'gata',
                'verificate' => $solicitari->count(),
                'preluate' => 0,
                'ramase' => $solicitari->count(),
                'erori' => ['SPV: ' . $e->getMessage()],
            ];

            return;
        }

        // Intai se vede cine are raspuns, ca numaratoarea sa porneasca de la un
        // total adevarat; abia pe urma se aduc, unul cate unul.
        $deAdus = [];

        foreach ($solicitari as $solicitare) {
            $mesaj = $this->potrivesteMesaj($solicitare, $mesaje);

            if ($mesaj !== null) {
                $deAdus[] = [$solicitare, $mesaj];
            }
        }

        $total = count($deAdus);

        yield ['tip' => 'inceput', 'total' => $total];

        $preluate = 0;
        $erori = [];

        foreach ($deAdus as $i => [$solicitare, $mesaj]) {
            // Fiecare raspuns isi cere ragazul lui, socotit de la capat.
            ragaz(120);

            $reusit = true;

            try {
                $this->preia($solicitare, $mesaj);
                $preluate++;
            } catch (\Exception $e) {
                $erori[] = $solicitare->cif . ' / ' . $solicitare->tip_document . ': ' . $e->getMessage();
                $reusit = false;
            }

            yield [
                'tip' => 'pas',
                'facute' => $i + 1,
                'total' => $total,
                'reusit' => $reusit,
                'ce' => trim($solicitare->tip_document . ' ' . $solicitare->cif),
            ];
        }

        yield [
            'tip' => 'gata',
            'verificate' => $solicitari->count(),
            'preluate' => $preluate,
            'ramase' => SpvSolicitare::inAsteptare()->count(),
            'erori' => $erori,
        ];
    }

    /**
     * Ia denumirea firmelor din documentele de identificare deja descarcate.
     *
     * Pentru fiecare cod fiscal se cauta ULTIMUL document de tipul „DATE
     * IDENTIFICARE" care are fisier — pe server sau in arhiva clientului —, se
     * citeste, si de acolo se ia denumirea. Apoi dosarele firmei se strang la un
     * loc, dupa cod: pana se afla numele, documentele au apucat sa intre in
     * dosare purtand ce nume era atunci.
     *
     * Nu se cere nimic de la ANAF. Se umbla numai prin ce e deja adus, si numai
     * prin documentele de acest fel: recitirea tuturor solicitarilor, de orice
     * tip, tinea minute intregi si nu aducea nimic in plus pentru denumire.
     *
     * @param array<int, string> $cifuri numai aceste firme; gol = toate
     *
     * @return array{citite: int, denumiri: int, cu_document: array<int, string>}
     */
    public function citesteDenumirileDinIdentificare(array $cifuri = []): array
    {
        $solicitari = SpvSolicitare::where('tip_document', 'like', '%DATE IDENTIFICARE%')
            ->where(function ($intrebare) {
                $intrebare->whereNotNull('cale_fisier')->orWhereNotNull('arhiva_cale');
            })
            ->when($cifuri !== [], function ($intrebare) use ($cifuri) {
                return $intrebare->whereIn('cif', $cifuri);
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $citite = 0;
        $denumiri = 0;
        $cuDocument = [];

        foreach ($solicitari as $solicitare) {
            $cif = trim((string) $solicitare->cif);

            // Numai cel mai nou al fiecarei firme: restul sunt istoric.
            if ($cif === '' || isset($cuDocument[$cif])) {
                continue;
            }

            $text = $this->textulRaspunsului($solicitare);

            if ($text === null) {
                continue;
            }

            $cuDocument[$cif] = true;
            $citite++;

            $denumire = $this->parser->citesteDenumire($text, $cif);

            $this->actualizeazaSocietatea($cif, [
                'denumire' => $denumire,
                'sursa' => 'date_identificare',
                'campuri' => [
                    'date_identificare' => mb_substr($this->parser->textDocument($text), 0, 5000),
                    'date_identificare_la' => now(),
                ],
            ]);

            $societate = AnafSocietate::where('cif', $cif)->first();

            if ($denumire !== null && $societate && $societate->denumire) {
                $denumiri++;

                // Dosarele purtand numele de dinainte se strang la cel de acum.
                if ($this->arhiva) {
                    $this->arhiva->uneste($cif, ArhivaService::dosarFirma($societate->denumire, $cif));
                }
            }
        }

        return [
            'citite' => $citite,
            'denumiri' => $denumiri,
            /*
             * Codurile raman siruri: PHP preface in numere cheile care arata a
             * numar, iar un cod fiscal ramas numar s-ar potrivi altfel decat cel
             * din tabel, unde e text.
             */
            'cu_document' => array_map('strval', array_keys($cuDocument)),
        ];
    }

    /**
     * Reinterpreteaza documentele deja descarcate — utila cand registrul de
     * societati e populat dupa ce raspunsurile au fost preluate.
     *
     * @return int numarul de documente reinterpretate
     */
    public function reinterpreteaza(): int
    {
        $solicitari = SpvSolicitare::where(function ($query) {
            $query->whereNotNull('cale_fisier')->orWhereNotNull('arhiva_cale');
        })->get();

        $procesate = 0;

        foreach ($solicitari as $solicitare) {
            $text = $this->textulRaspunsului($solicitare);

            if ($text === null) {
                continue;
            }

            $obs = $this->interpreteaza($solicitare, $text);

            if ($obs !== null) {
                $solicitare->update(['obs' => $obs]);
            }

            $procesate++;
        }

        return $procesate;
    }

    /**
     * Textul raspunsului deja preluat, luat de unde se afla documentul: din
     * arhiva de pe calculatorul clientului sau, pentru cele aduse inainte, de pe
     * discul serverului. Nici intr-un caz, nici in celalalt nu se scrie nimic.
     */
    protected function textulRaspunsului(SpvSolicitare $solicitare): ?string
    {
        try {
            if ($solicitare->cale_fisier && Storage::exists($solicitare->cale_fisier)) {
                return TextPdf::dinCale(Storage::path($solicitare->cale_fisier));
            }

            if ($solicitare->arhiva_cale && $this->arhiva) {
                return TextPdf::dinContinut($this->arhiva->ia($solicitare->arhiva_cale));
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    protected function potrivesteMesaj(SpvSolicitare $solicitare, array $mesaje): ?array
    {
        foreach ($mesaje as $mesaj) {
            $idSolicitare = (string) ($mesaj['id_solicitare'] ?? '');

            if ($solicitare->id_solicitare && $idSolicitare === (string) $solicitare->id_solicitare) {
                return $mesaj;
            }
        }

        return null;
    }

    protected function preia(SpvSolicitare $solicitare, array $mesaj): void
    {
        $inregistrat = $this->storage->saveMessage($mesaj, $solicitare->cif);

        /*
         * Raspunsul merge de la ANAF drept in arhiva clientului; incoace vine
         * doar textul din el, si numai cand avem ce citi in el.
         *
         * Pana acum textul se cerea pentru orice raspuns. Dar el se scoate din
         * PDF pe calculatorul clientului si face drumul inapoi peste retea, iar
         * pentru tipurile pe care nu le talcuim serverul il arunca — asa ca
         * fiecare astfel de raspuns platea o citire si un drum degeaba. Iar cand
         * programul local nu izbutea sa citeasca, documentul era cerut inapoi
         * din arhiva: inca un drum, tot pentru un text de aruncat.
         */
        $vreaText = $this->areNevoieDeText($solicitare->tip_document);

        $adus = $this->storage->aduce($inregistrat, $vreaText, 'solicitari');

        $obs = null;

        if ($vreaText) {
            $obs = $adus['text'] !== null
                ? $this->interpreteaza($solicitare, $adus['text'])
                : 'Documentul nu a putut fi citit pe calculatorul clientului.';
        }

        $solicitare->update([
            'mesaj_id' => $mesaj['id'],
            'cale_fisier' => $adus['pe_server'],
            'arhiva_cale' => $adus['cale'],
            'data_afisare' => now(),
            'detalii' => $mesaj['detalii'] ?? $solicitare->detalii,
            'obs' => $obs,
            'stare' => 'preluata',
        ]);
    }

    /**
     * Cele doua constatari care merita strigate.
     *
     * Stau in constante fiindca acelasi text ajunge in doua locuri: in coloana
     * de observatii din fila si in emailul de instiintare. Scrise de mana in
     * amandoua, s-ar fi departat una de alta la prima indreptare de virgula.
     */
    public const VECTOR_MODIFICAT = 'ATENȚIE! VECTOR FISCAL MODIFICAT!';
    public const RESTANTE = 'ATENȚIE! SUNT OBLIGAȚII DE PLATĂ RESTANTE';

    /**
     * Trimite instiintarile legate de o constatare.
     *
     * Nu darama preluarea daca nu izbuteste: documentul e adus si talcuit, iar
     * observatia se vede oricum in fila. Un server de email obosit n-are voie sa
     * strice lucrarea pentru care omul a asteptat.
     */
    protected function instiinteaza(SpvSolicitare $solicitare, string $ce, string $vorba): void
    {
        try {
            app(AlerteMesaje::class)->pentruConstatare(
                $ce,
                $solicitare->cif,
                $this->certificate->idCurent(),
                $vorba
            );
        } catch (\Throwable $e) {
            Log::warning('Înștiințarea „' . $ce . '" nu a plecat: ' . $e->getMessage(), [
                'cif' => $solicitare->cif,
            ]);
        }
    }

    /**
     * Tipurile de raspuns din care chiar citim ceva.
     *
     * Sunt aceleasi pe care le talcuieste „interpreteaza” mai jos; daca acolo se
     * adauga unul nou, aici trebuie trecut, altfel documentul soseste fara text
     * si nu mai are ce fi citit. Cele doua liste sunt tinute impreuna de o proba.
     */
    protected const CU_TEXT = [
        'VECTOR FISCAL',
        'SITUATIE SINTETICA',
        'SITUAȚIE SINTETICĂ',
        'DATE IDENTIFICARE',
    ];

    /** Are rost sa cerem textul acestui raspuns? */
    protected function areNevoieDeText(?string $tip): bool
    {
        $tip = mb_strtoupper(trim((string) $tip));

        foreach (self::CU_TEXT as $care) {
            if (strpos($tip, $care) !== false) {
                return true;
            }
        }

        return false;
    }

    /** Interpretarea documentului descarcat, in functie de tip. */
    protected function interpreteaza(SpvSolicitare $solicitare, string $text): ?string
    {
        $tip = mb_strtoupper($solicitare->tip_document);

        try {
            if (strpos($tip, 'VECTOR FISCAL') !== false) {
                $rezultat = $this->parser->citesteVectorFiscal($text, $solicitare->cif);
                $numar = count($rezultat['randuri']);

                // Antetul vectorului contine denumirea oficiala a contribuabilului.
                $this->actualizeazaSocietatea($solicitare->cif, [
                    'denumire' => $this->parser->citesteDenumire($text, $solicitare->cif),
                    'sursa' => 'vector',
                    'campuri' => ['vector_la' => now()],
                ]);

                if ($numar === 0) {
                    return 'Vectorul fiscal nu a putut fi interpretat (0 obligații citite).';
                }

                if ($rezultat['prima_preluare']) {
                    return 'Vector fiscal preluat: ' . $numar . ' obligații.';
                }

                if (!$rezultat['modificat']) {
                    return 'Nu sunt modificări în vectorul fiscal.';
                }

                $this->instiinteaza($solicitare, AlertaMesajSpv::CAND_VECTOR_MODIFICAT, self::VECTOR_MODIFICAT);

                return self::VECTOR_MODIFICAT;
            }

            if (strpos($tip, 'SITUATIE SINTETICA') !== false || strpos($tip, 'SITUAȚIE SINTETICĂ') !== false) {
                /*
                 * Textul, nu o cale: aici scria „$calePdf", o variabila care nu
                 * exista. Situatia sintetica raspundea deci mereu „nu sunt
                 * obligatii restante", iar datele de identificare nu dadeau
                 * niciodata denumirea — de unde si firmele ramase cu numele
                 * citit din vectorul fiscal, uneori doar „SRL".
                 */
                if (!$this->parser->areObligatiiRestante($text)) {
                    return 'Nu sunt obligații de plată restante.';
                }

                $this->instiinteaza($solicitare, AlertaMesajSpv::CAND_RESTANTE, self::RESTANTE);

                return self::RESTANTE;
            }

            if (strpos($tip, 'DATE IDENTIFICARE') !== false) {
                $denumire = $this->parser->citesteDenumire($text, $solicitare->cif);

                $this->actualizeazaSocietatea($solicitare->cif, [
                    'denumire' => $denumire,
                    'sursa' => 'date_identificare',
                    'campuri' => [
                        'date_identificare' => mb_substr($this->parser->textDocument($text), 0, 5000),
                        'date_identificare_la' => now(),
                    ],
                ]);

                return $denumire
                    ? 'Date identificare preluate: ' . $denumire
                    : 'Date identificare preluate (denumirea nu a putut fi extrasă).';
            }
        } catch (\Exception $e) {
            return 'Documentul nu a putut fi interpretat: ' . $e->getMessage();
        }

        return null;
    }

    /**
     * Completeaza registrul de societati din documentul primit. Denumirea se
     * suprascrie doar dintr-o sursa cel putin la fel de sigura (vezi AnafSocietate).
     */
    protected function actualizeazaSocietatea(string $cif, array $date): void
    {
        $societate = AnafSocietate::firstOrNew(['cif' => $cif]);

        if (!$societate->exists) {
            $societate->fill(['tip' => AnafSocietate::tipDupaCif($cif), 'activ' => true]);
        }

        $societate->fill($date['campuri'] ?? [])->save();
        $societate->seteazaDenumire($date['denumire'] ?? null, $date['sursa']);

        // Denumirea e utila si in vectorul fiscal declarat manual.
        if ($societate->denumire) {
            VectorFiscal::updateOrCreate(['cui' => $cif], ['denumire' => $societate->denumire]);
        }
    }
}
