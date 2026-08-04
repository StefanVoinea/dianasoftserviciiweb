<?php

namespace App\Services\Anaf\Arhiva;

use App\Models\AnafDeclaratie;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Spv\CertificatService;
use Illuminate\Support\Facades\Http;

/**
 * Arhiva de documente de pe calculatorul clientului.
 *
 * Aplicatia sta in cloud, dar declaratiile, recipisele si documentele aduse din
 * SPV raman la client, scrise prin programul local in structura:
 *
 *     <radacina>\<Denumire firma (CUI)>\<TIP>\<TIP>_<CUI>_<perioada>_<stare>.pdf
 *
 * Serverul retine doar calea relativa, ca sa stie de la ce bridge sa ceara
 * fisierul cand omul apasa pe el. Bridge-ul se alege ca la semnare: cel al
 * certificatului cu care e inrolata firma, deci documentele unei firme ajung
 * pe calculatorul care raspunde de ea.
 */
class ArhivaService
{
    /** Cat se asteapta strangerea dosarelor la un loc — e doar o asezare. */
    protected const UNIRE_SECUNDE = 15;

    protected $config;
    protected $certificate;

    /** Firmele ale caror dosare au fost deja stranse la un loc. */
    protected $unite = [];

    public function __construct(array $config, CertificatService $certificate)
    {
        $this->config = $config;
        $this->certificate = $certificate;
    }

    public function activa(): bool
    {
        return (bool) ($this->config['activa'] ?? false);
    }

    /** Copia de lucru de pe server se sterge dupa ce documentul e la client. */
    public function stergeDePeServer(): bool
    {
        return $this->activa() && (bool) ($this->config['sterge_de_pe_server'] ?? false);
    }

    /**
     * Scrie un document in arhiva clientului si intoarce calea relativa.
     *
     * Numele cerut poate fi deja luat: pentru aceeasi luna se pot depune mai
     * multe D394, a doua fiind rectificativa fara sa fie bifata asa. In acel caz
     * programul local alege un nume liber si il intoarce, deci calea primita
     * inapoi poate sa nu fie chiar cea ceruta.
     *
     * @param string  $continut    octetii documentului
     * @param string  $firma       dosarul firmei, deja alcatuit cu dosarFirma()
     * @param string  $dosar       subdosarul (tipul declaratiei sau "SPV")
     * @param string  $nume        numele fisierului, cu extensie
     * @param ?string $inlocuieste calea documentului scris data trecuta pentru
     *                             aceeasi declaratie; doar el poate fi suprascris
     */
    public function pune(
        string $continut,
        string $firma,
        string $dosar,
        string $nume,
        ?string $inlocuieste = null
    ): string {
        $raspuns = $this->cerere()
            ->attach('fisier', $continut, $nume)
            ->post($this->url('/arhiva'), array_filter([
                'firma' => $firma,
                'dosar' => $dosar,
                'nume' => $nume,
                'inlocuieste' => $inlocuieste,
            ]));

        return $this->cale($raspuns, 'Scrierea în arhiva locală a eșuat');
    }

    /**
     * Inca un exemplar al unui document deja arhivat, in alt dosar al arhivei.
     *
     * Copia se face intre doua dosare de pe calculatorul clientului: documentul
     * e deja acolo, deci nu are de ce sa mai faca drumul pana la server si
     * inapoi doar ca sa fie scris a doua oara.
     */
    public function copiaza(string $caleRelativa, string $firma, string $dosar, string $nume): string
    {
        $raspuns = $this->cerere()->asForm()->post($this->url('/arhiva/copiaza'), [
            'cale' => $caleRelativa,
            'firma' => $firma,
            'dosar' => $dosar,
            'nume' => $nume,
        ]);

        return $this->cale($raspuns, 'Copia în arhiva locală a eșuat');
    }

    /**
     * Strange la un loc cele doua dosare ale unei firme.
     *
     * Dosarul poarta denumirea firmei, cu CUI-ul in paranteza; pana cand
     * denumirea e stiuta, el poarta doar codul. Asa o firma ajunge cu
     * documentele impartite in doua — „15208744" si „ALFA SRL (15208744)" —
     * care arata a dezordine, desi nu s-a pierdut nimic.
     *
     * Se cere o singura data pe firma intr-o cerere web: dupa prima unire nu mai
     * e nimic de mutat, iar raspunsul ar fi mereu acelasi.
     *
     * Un esec nu are voie sa strice arhivarea care urmeaza: documentul e mai
     * important decat asezarea dosarelor, asa ca se scrie doar in jurnal.
     */
    public function uneste(?string $cui, string $firma): void
    {
        $cui = trim((string) $cui);

        // Fara denumire cunoscuta, dosarul e chiar codul: n-are cu ce fi unit.
        if (!$this->activa() || $cui === '' || $firma === $cui) {
            return;
        }

        if (isset($this->unite[$cui])) {
            return;
        }

        $this->unite[$cui] = true;

        try {
            /*
             * Cu rabdare putina, si la conectare, si la raspuns: asezarea
             * dosarelor e o inlesnire, nu o treaba de care atarna documentul.
             * Daca programul local nu raspunde, se merge mai departe fara ea —
             * scrierea care urmeaza va spune oricum ce s-a intamplat.
             */
            $this->cerere()
                ->timeout(self::UNIRE_SECUNDE)
                ->withOptions(['connect_timeout' => self::UNIRE_SECUNDE])
                ->asForm()
                ->post($this->url('/arhiva/uneste-dosarul'), [
                    'din' => $cui,
                    'in' => $firma,
                ]);
        } catch (\Exception $e) {
            Jurnal::esec(
                'arhiva_unire',
                'Dosarele firmei ' . $cui . ' nu au putut fi strânse la un loc: ' . $e->getMessage(),
                [],
                $cui
            );
        }
    }

    /** Continutul unui document din arhiva clientului. */
    public function ia(string $caleRelativa): string
    {
        $raspuns = $this->cerere()->get($this->url('/arhiva'), ['cale' => $caleRelativa]);

        if ($raspuns->failed()) {
            throw new ArhivaException($this->motivul($raspuns, 'Documentul nu a putut fi citit din arhiva locală'));
        }

        return $raspuns->body();
    }

    /**
     * Schimba numele unui document deja arhivat.
     *
     * Dupa depunere, declaratia semnata isi schimba numele ca sa poarte indicele
     * de incarcare: raman un singur fisier si numele spune ca s-a depus.
     */
    public function redenumeste(string $caleRelativa, string $nume): string
    {
        $raspuns = $this->cerere()->asForm()->post($this->url('/arhiva/redenumeste'), [
            'cale' => $caleRelativa,
            'nume' => $nume,
        ]);

        return $this->cale($raspuns, 'Redenumirea în arhiva locală a eșuat');
    }

    public function exista(?string $caleRelativa): bool
    {
        if (!$caleRelativa) {
            return false;
        }

        return $this->cerere()->get($this->url('/arhiva/exista'), ['cale' => $caleRelativa])
            ->json('exista') === true;
    }

    /**
     * Dosarul firmei: denumirea din Entitati inrolate, cu CUI-ul in paranteza.
     *
     * CUI-ul nu e de prisos — doua firme pot avea aceeasi denumire, iar fara el
     * documentele lor ar ajunge in acelasi dosar.
     */
    public static function dosarFirma(?string $denumire, ?string $cui): string
    {
        $cui = trim((string) $cui);
        $denumire = self::curata($denumire);

        if ($denumire === '') {
            return $cui !== '' ? $cui : 'Fara CUI';
        }

        return $cui !== '' ? $denumire . ' (' . $cui . ')' : $denumire;
    }

    /**
     * Numele documentului: tipul, CUI-ul, perioada si starea.
     *
     * @param string $stare "semnata", "depusa", "recipisa" sau "" pentru XML
     */
    public static function numeDeclaratie(AnafDeclaratie $declaratie, string $stare, string $extensie): string
    {
        $bucati = [
            $declaratie->tip ?: 'declaratie',
            $declaratie->cui ?: 'fara-cui',
            self::perioada($declaratie),
        ];

        if ($declaratie->rectificativa) {
            $bucati[] = 'rectificativa';
        }

        if ($stare !== '') {
            $bucati[] = $stare;
        }

        // Indicele de incarcare deosebeste depunerile repetate ale aceleiasi luni.
        if (in_array($stare, ['depusa', 'recipisa'], true) && $declaratie->index_recipisa) {
            $bucati[] = $declaratie->index_recipisa;
        }

        return self::curata(implode('_', array_filter($bucati))) . '.' . ltrim($extensie, '.');
    }

    /** "2026-06" pentru declaratiile lunare, "2026" pentru cele anuale. */
    protected static function perioada(AnafDeclaratie $declaratie): string
    {
        if (!$declaratie->anul) {
            return '';
        }

        return $declaratie->luna
            ? $declaratie->anul . '-' . str_pad($declaratie->luna, 2, '0', STR_PAD_LEFT)
            : (string) $declaratie->anul;
    }

    /**
     * Curata o bucata de nume de tot ce Windows nu accepta in cai.
     *
     * Punctele si spatiile de la sfarsit sunt taiate dinadins: Windows le
     * inlatura singur, iar un dosar numit "S.C. ALFA S.R.L." ar fi altul decat
     * cel scris data viitoare.
     */
    public static function curata(?string $text): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', (string) $text));
        $text = str_replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|'], '-', $text);
        $text = trim($text, " .-\t\n\r\0\x0B");

        return mb_substr($text, 0, 120);
    }

    /**
     * Cererea catre programul local, cu radacina arhivei atasata.
     *
     * Calea se tine in aplicatie, la certificatul care raspunde de acel
     * calculator, ca sa nu fie nevoie de umblat prin bridge.env pe fiecare
     * statie; lipsa ei inseamna „foloseste ce scrie in bridge.env".
     */
    protected function cerere()
    {
        $bridge = $this->certificate->bridge();

        $cerere = Http::withToken($bridge['token'])->timeout($this->config['timeout'] ?? 120);

        return $bridge['arhiva']
            ? $cerere->withHeaders(['X-Arhiva-Cale' => $bridge['arhiva']])
            : $cerere;
    }

    protected function url(string $cale): string
    {
        return rtrim($this->certificate->bridge()['url'], '/') . $cale;
    }

    protected function cale($raspuns, string $mesaj): string
    {
        if ($raspuns->failed()) {
            throw new ArhivaException($this->motivul($raspuns, $mesaj));
        }

        $cale = $raspuns->json('cale');

        if (!is_string($cale) || $cale === '') {
            throw new ArhivaException($mesaj . ': programul local nu a întors calea documentului.');
        }

        return $cale;
    }

    protected function motivul($raspuns, string $mesaj): string
    {
        $payload = json_decode($raspuns->body(), true);

        return $mesaj . ': ' . ($payload['detalii'] ?? $payload['eroare'] ?? 'HTTP ' . $raspuns->status());
    }
}
