<?php

namespace App\Services\Anaf\Declaratii;

use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Models\AnafSocietate;
use App\Services\Anaf\Arhiva\ArhivaService;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\SpvClient;
use App\Services\Anaf\Spv\SpvException;
use App\Services\Anaf\Spv\SpvStorage;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Preluarea recipiselor pentru declaratiile depuse. Sursa principala este SPV
 * (mesaje de tip RECIPISA, potrivite dupa indicele de incarcare); cand SPV nu
 * are inca mesajul, starea se interogheaza public pe StareD112.
 */
class RecipisaService
{
    protected $config;
    protected $spvClient;
    protected $spvStorage;

    /** Arhiva de pe calculatorul clientului; lipseste doar in teste. */
    protected $arhiva;

    /** Alegerea certificatului cu care se intreaba SPV; lipseste doar in teste. */
    protected $certificate;

    /** Listele de mesaje SPV deja aduse, una pe certificat. */
    protected $liste = [];

    /**
     * Paginile StareD112 aduse deodată, pe numărul declarației.
     *
     * Se țin doar cât ține cererea de acum: starea unei declarații se schimbă
     * la ANAF de la un ceas la altul, iar o pagină păstrată mai mult ar spune
     * ce era, nu ce este.
     */
    protected $paginiStare = [];

    public function __construct(
        array $config,
        SpvClient $spvClient,
        SpvStorage $spvStorage,
        ?ArhivaService $arhiva = null,
        ?CertificatService $certificate = null
    ) {
        $this->config = $config;
        $this->spvClient = $spvClient;
        $this->spvStorage = $spvStorage;
        $this->arhiva = $arhiva;
        $this->certificate = $certificate;
    }

    /**
     * Verifica toate declaratiile care asteapta recipisa.
     *
     * @return array{verificate: int, descarcate: int, erori: array}
     */
    public function verificaToate(int $zile = 60): array
    {
        $declaratii = AnafDeclaratie::asteaptaRecipisa()->get();

        if ($declaratii->isEmpty()) {
            return ['verificate' => 0, 'descarcate' => 0, 'descarcate_id' => [], 'erori' => []];
        }

        $erori = [];

        // Se retin si declaratiile a caror recipisa tocmai a venit: din ele se
        // poate face pe loc un singur fisier de tiparit.
        $descarcate = [];

        $this->pregatesteStarilePublice($declaratii);

        foreach ($declaratii as $declaratie) {
            try {
                if ($this->verificaDeclaratie($declaratie, $this->mesajele($declaratie, $zile, $erori))) {
                    $descarcate[] = $declaratie->id;
                }
            } catch (\Exception $e) {
                $erori[] = $declaratie->index_recipisa . ': ' . $e->getMessage();
            }
        }

        return [
            'verificate' => $declaratii->count(),
            'descarcate' => count($descarcate),
            'descarcate_id' => $descarcate,
            'erori' => $erori,
        ];
    }

    /**
     * Aceeasi verificare, dar spusa pe masura ce se face.
     *
     * O sesiune cu cateva zeci de declaratii tine minute: pentru fiecare se
     * intreaba ANAF de starea ei, iar recipisa gasita se aduce. Cu un raspuns
     * obisnuit, omul vede o rotita si atat. Aici afla dupa fiecare declaratie a
     * cata e din cate si a cui e.
     *
     * @return \Generator randurile trimise filei, in ordinea lucrului
     */
    public function pasCuPas(int $zile = 60): \Generator
    {
        $declaratii = AnafDeclaratie::asteaptaRecipisa()->get();

        yield ['tip' => 'inceput', 'total' => $declaratii->count()];

        if ($declaratii->isEmpty()) {
            yield ['tip' => 'gata', 'verificate' => 0, 'descarcate' => 0, 'descarcate_id' => [], 'erori' => []];

            return;
        }

        $erori = [];
        $descarcate = [];
        $facute = 0;

        // Starile publice se intreaba toate deodata, inainte de lucrul propriu-zis:
        // ele nu tin nici de certificat, nici de pauza ceruta de ANAF pentru SPV.
        $this->pregatesteStarilePublice($declaratii);

        foreach ($declaratii as $declaratie) {
            // Fiecare declaratie isi cere ragazul ei, socotit de la capat.
            ragaz(120);

            $adusa = false;

            try {
                $adusa = $this->verificaDeclaratie($declaratie, $this->mesajele($declaratie, $zile, $erori));

                if ($adusa) {
                    $descarcate[] = $declaratie->id;
                }
            } catch (\Exception $e) {
                $erori[] = $declaratie->index_recipisa . ': ' . $e->getMessage();
            }

            $facute++;

            yield [
                'tip' => 'pas',
                'facute' => $facute,
                'total' => $declaratii->count(),
                'adus' => $adusa,
                'ce' => trim($declaratie->tip . ' ' . $declaratie->cui),
            ];
        }

        yield [
            'tip' => 'gata',
            'verificate' => $declaratii->count(),
            'descarcate' => count($descarcate),
            'descarcate_id' => $descarcate,
            'erori' => $erori,
        ];
    }

    /**
     * Mesajele SPV printre care se cauta recipisa acestei declaratii.
     *
     * Lista se cere cu certificatul cu care a fost depusa declaratia: pe un
     * calculator cu doua tokene, recipisa vine pe drepturile certificatului
     * care a depus, iar lista celuilalt n-o cuprinde. Cat timp se intreba o
     * singura data, cu certificatul activ intamplator, jumatate din recipise
     * nu se gaseau niciodata.
     *
     * Se aduce o lista pe certificat, nu una pe declaratie: acelasi token
     * raspunde de obicei de multe firme, iar SPV cere pauza intre intrebari.
     */
    protected function mesajele(AnafDeclaratie $declaratie, int $zile, array &$erori): array
    {
        $cheie = $declaratie->certificat_id ?: 0;

        if (array_key_exists($cheie, $this->liste)) {
            return $this->liste[$cheie];
        }

        if ($cheie && $this->certificate) {
            $alLui = AnafCertificat::where('activ', true)->find($cheie);

            if ($alLui) {
                $this->certificate->foloseste($alLui);
            }
        }

        try {
            $lista = $this->spvClient->listaMesaje($zile);

            return $this->liste[$cheie] = isset($lista['mesaje']) && is_array($lista['mesaje'])
                ? $lista['mesaje']
                : [];
        } catch (SpvException $e) {
            // SPV indisponibil sau fara mesaje — se continua doar cu StareD112.
            $erori[] = 'SPV: ' . $e->getMessage();

            return $this->liste[$cheie] = [];
        }
    }

    public function verificaDeclaratie(AnafDeclaratie $declaratie, array $mesaje = []): bool
    {
        $this->intregeste($declaratie);

        $mesaj = $this->potrivesteMesaj($declaratie, $mesaje);

        if ($mesaj !== null) {
            return $this->preiaDinSpv($declaratie, $mesaj);
        }

        $this->preiaStareaPublica($declaratie);

        return false;
    }

    /**
     * Intregeste declaratiile ramase fara CUI sau fara certificat.
     *
     * PDF-urile din alte programe (mai ales D406/SAF-T) au intrat o vreme fara
     * CUI — analiza XML nu-l gasea — si fara certificat, ca veneau gata
     * semnate. Fara ele, recipisa nu se potrivea si firma aparea cu liniute.
     * Numele fisierului le poarta insa pe amandoua; aici se pun la locul lor,
     * inclusiv pentru randurile intrate inainte de indreptarea incarcarii.
     */
    protected function intregeste(AnafDeclaratie $declaratie): void
    {
        $deScris = [];

        if (!trim((string) $declaratie->cui) && $declaratie->nume_fisier) {
            $meta = app(DeclaratieXml::class)->completeazaDinNume([
                'tip' => $declaratie->tip,
                'cui' => null,
                'luna' => $declaratie->luna,
                'anul' => $declaratie->anul,
            ], $declaratie->nume_fisier);

            if (!empty($meta['cui'])) {
                $deScris['cui'] = $meta['cui'];
                $deScris['luna'] = $declaratie->luna ?: $meta['luna'];
                $deScris['anul'] = $declaratie->anul ?: $meta['anul'];
            }
        }

        $cui = $deScris['cui'] ?? $declaratie->cui;

        if ($cui && (!$declaratie->den_firma || !$declaratie->certificat_id)) {
            $societate = AnafSocietate::where('cif', $cui)->first();

            if ($societate) {
                $deScris['den_firma'] = $declaratie->den_firma ?: $societate->denumire;
                $deScris['certificat_id'] = $declaratie->certificat_id ?: $societate->certificat_id;
            }
        }

        if ($deScris !== []) {
            $declaratie->update(array_filter($deScris));
        }
    }

    /** Mesajul SPV de tip RECIPISA al carui text contine indicele de incarcare. */
    protected function potrivesteMesaj(AnafDeclaratie $declaratie, array $mesaje): ?array
    {
        if (!$declaratie->index_recipisa) {
            return null;
        }

        foreach ($mesaje as $mesaj) {
            $tip = strtoupper($mesaj['tip'] ?? '');
            $detalii = $mesaj['detalii'] ?? '';

            if ($tip === 'RECIPISA' && strpos($detalii, $declaratie->index_recipisa) !== false) {
                return $mesaj;
            }
        }

        return null;
    }

    protected function preiaDinSpv(AnafDeclaratie $declaratie, array $mesaj): bool
    {
        $inregistrat = $this->spvStorage->saveMessage($mesaj, $mesaj['cif'] ?? $declaratie->cui);

        /*
         * Recipisa merge de la ANAF drept in arhiva clientului — in dosarul SPV
         * al firmei si, pe deasupra, cu o copie langa declaratia la care
         * raspunde. Incoace vine doar textul din ea, atat cat sa se stie
         * verdictul ANAF.
         */
        $adus = $this->spvStorage->aduce($inregistrat, true, 'recipise');

        $stare = $adus['text'] !== null
            ? $this->verdictul($adus['text'], $declaratie->cui)
            : 'In prelucrare';

        $declaratie->update([
            'cale_recipisa' => $adus['pe_server'],
            'stare_declaratie' => mb_substr($stare, 0, 1000),
            'data_recipisa' => now(),
            'pas' => 'finalizat',
        ]);

        return true;
    }

    /**
     * Starea publica de pe StareD112 (fara certificat). Se cauta randul cu
     * indicele de incarcare in tabelul HTML returnat.
     */
    /**
     * Intreaba deodata starea publica a mai multor declaratii.
     *
     * StareD112 e o pagina publica, ceruta de pe serverul nostru: n-are nici
     * certificat, nici bridge, nici pauza ceruta de ANAF pentru SPV. Nimic nu
     * cere deci ca intrebarile sa stea la rand — iar pe rand ele adaugau o
     * secunda de fiecare declaratie fara recipisa.
     *
     * Ce se afla se tine minte pentru cererea de acum; declaratiile ale caror
     * pagini n-au venit trec mai departe pe drumul dinainte, una cate una.
     *
     * @param iterable<AnafDeclaratie> $declaratii
     */
    public function pregatesteStarilePublice(iterable $declaratii): void
    {
        $deIntrebat = [];

        foreach ($declaratii as $declaratie) {
            if ($declaratie->index_recipisa && !isset($this->paginiStare[$declaratie->id])) {
                $deIntrebat[] = $declaratie;
            }
        }

        if ($deIntrebat === []) {
            return;
        }

        $deodata = max(1, (int) ($this->config['stari_deodata'] ?? 8));

        foreach (array_chunk($deIntrebat, $deodata) as $lot) {
            $raspunsuri = Http::pool(function (Pool $bazin) use ($lot) {
                $cereri = [];

                foreach ($lot as $declaratie) {
                    $cereri[] = $bazin->as((string) $declaratie->id)
                        ->asForm()
                        ->timeout($this->config['timeout'])
                        ->post($this->config['url_stare'], [
                            'ghis' => 'N',
                            'id' => $declaratie->index_recipisa,
                            'cui' => $declaratie->cui,
                        ]);
                }

                return $cereri;
            });

            foreach ($lot as $declaratie) {
                $raspuns = $raspunsuri[(string) $declaratie->id] ?? null;

                /*
                 * Ce n-a venit nu se tine minte: declaratia va merge pe drumul
                 * dinainte, cu o intrebare a ei, si acolo pricina ajunge in
                 * jurnal ca pana acum.
                 */
                if ($raspuns instanceof Response && $raspuns->successful()) {
                    $this->paginiStare[$declaratie->id] = $raspuns->body();
                }
            }
        }
    }

    protected function preiaStareaPublica(AnafDeclaratie $declaratie): void
    {
        /*
         * Cand intrebarea nu ajunge la ANAF, omului i se spune „In prelucrare”:
         * e adevarul, fiindca nu se stie nimic altceva. Dar in jurnal trebuie sa
         * ramana pricina — altfel o declaratie care asteapta un raspuns arata
         * intocmai ca una despre care nu s-a putut afla nimic, si o adresa
         * mutata poate trece luni de zile neobservata.
         */
        $nuSAPutut = function (string $pricina) use ($declaratie) {
            Log::warning('Starea publica D112 nu s-a putut afla: ' . $pricina, [
                'declaratie' => $declaratie->id,
                'adresa' => $this->config['url_stare'],
            ]);

            $declaratie->update(['stare_declaratie' => 'In prelucrare']);
        };

        // Pagina adusa in lotul dinainte nu se mai cere inca o data.
        $html = $this->paginiStare[$declaratie->id] ?? null;

        if ($html === null) {
            try {
                $raspuns = Http::asForm()
                    ->timeout($this->config['timeout'])
                    ->post($this->config['url_stare'], [
                        'ghis' => 'N',
                        'id' => $declaratie->index_recipisa,
                        'cui' => $declaratie->cui,
                    ]);

                $html = $raspuns->body();
            } catch (\Exception $e) {
                $nuSAPutut($e->getMessage());

                return;
            }

            /*
             * O adresa mutata nu arunca exceptie: intoarce o pagina de eroare,
             * care trece de aici mai departe si sfarseste tot in „In
             * prelucrare”. Asa a si trecut neobservata mutarea StareD112 pe
             * alta gazda.
             */
            if ($raspuns->failed()) {
                $nuSAPutut('ANAF a raspuns ' . $raspuns->status());

                return;
            }
        }

        if (strpos($html, 'Fisierul depus nu este un document valid') !== false) {
            $declaratie->update(['stare_declaratie' => 'Fisierul depus nu este un document valid']);

            return;
        }

        if (strpos($html, '<td>' . $declaratie->index_recipisa . '</td>') !== false) {
            $declaratie->update([
                'stare_declaratie' => mb_substr($this->stareaDinPagina($html, $declaratie->index_recipisa), 0, 1000),
            ]);

            return;
        }

        $declaratie->update(['stare_declaratie' => 'In prelucrare']);
    }

    /**
     * Starea declaratiei, scoasa din randul ei din pagina StareD112.
     *
     * Pagina insira toate depunerile din ultimele luni; inainte se pastra tot
     * textul ei, cu antetul ministerului si cu entitatile HTML nedecodate —
     * "Ministerul Finan&#355;elor... IndexTip documentStare..." — iar starea
     * adevarata, "Documentul este valid", se ineca in el. Se ia doar randul
     * indicelui cautat: tipul, starea si numarul de inregistrare.
     */
    protected function stareaDinPagina(string $html, string $index): string
    {
        preg_match_all('#<tr[^>]*>(.*?)</tr>#si', $html, $randuri);

        foreach ($randuri[1] as $rand) {
            if (strpos($rand, '>' . $index . '<') === false) {
                continue;
            }

            preg_match_all('#<td[^>]*>(.*?)</td>#si', $rand, $celule);

            $curate = [];

            foreach ($celule[1] as $celula) {
                $text = trim(preg_replace(
                    '/\s+/u',
                    ' ',
                    html_entity_decode(strip_tags($celula), ENT_QUOTES | ENT_HTML401, 'UTF-8')
                ));

                // Fara indicele cautat (e deja pe declaratie) si fara textul
                // legaturii "recipisa" — raman tipul, starea si inregistrarea.
                if ($text === '' || $text === $index || strcasecmp($text, 'recipisa') === 0) {
                    continue;
                }

                $curate[] = $text;
            }

            if ($curate !== []) {
                return implode(' ', $curate);
            }
        }

        // Randul nu s-a lasat citit: macar tot textul, dar decodat si curat.
        return trim(preg_replace(
            '/\s+/u',
            ' ',
            html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML401, 'UTF-8')
        )) ?: 'In prelucrare';
    }

    /**
     * Verdictul ANAF din recipisa: textul de dupa prima aparitie a CUI-ului.
     *
     * Lucreaza pe textul documentului, nu pe fisierul lui: recipisa e citita pe
     * calculatorul clientului, acolo unde ramane, iar incoace vine doar ce scrie
     * in ea.
     */
    public function verdictul(string $text, string $cui): string
    {
        $pozitie = strpos($text, $cui);

        if ($pozitie === false) {
            return trim(preg_replace('/\s+/', ' ', $text)) ?: 'In prelucrare';
        }

        $verdict = substr($text, $pozitie + strlen($cui));

        return trim(preg_replace('/\s+/', ' ', $verdict)) ?: 'In prelucrare';
    }

    /** Clasificarea verdictului, ca in aplicatia desktop. */
    public static function clasifica(?string $stare): string
    {
        if ($stare === null || trim($stare) === '') {
            return 'in_asteptare';
        }

        if (stripos($stare, 'In prelucrare') !== false) {
            return 'in_prelucrare';
        }

        if (stripos($stare, 'are erori de validare') !== false) {
            return 'invalid';
        }

        if (mb_stripos($stare, 'ATENȚIONĂRI') !== false || stripos($stare, 'ATENTIONARI') !== false) {
            return 'valid_cu_atentionari';
        }

        if (stripos($stare, 'Documentul este valid') !== false
            || (stripos($stare, 'Nu exist') !== false && stripos($stare, 'erori de validare') !== false)) {
            return 'valid';
        }

        return 'necunoscut';
    }
}
