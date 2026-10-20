<?php

namespace App\Services\Anaf\Declaratii\D300;

use App\Models\AnafDeclaratie;
use App\Services\Anaf\Declaratii\DeclaratieException;
use Illuminate\Support\Facades\Storage;

/**
 * Decontul depus, pus fata in fata cu cel care iese din SAF-T.
 *
 * Amandoua declaratiile vorbesc despre aceeasi luna a aceleiasi firme: D300
 * spune cat TVA a iesit, D406 spune din ce. Daca nu se potrivesc, una din ele e
 * gresita — si mai bine se afla acum decat peste doi ani, la control, cand ANAF
 * face chiar comparatia asta.
 *
 * Se face la validare, in amandoua sensurile: cand se valideaza D300 se cauta un
 * D406 al aceleiasi luni, iar cand se valideaza D406 se cauta D300. Nu opreste
 * nimic — o declaratie care nu se potriveste ramane valida si se poate depune;
 * potrivirea e o parere, si se scrie ca atare.
 */
class PotrivireDecont
{
    /**
     * De la ce pas incolo se socoteste ca o declaratie e buna de comparat.
     *
     * O declaratie cu erori de validare n-are ce spune despre alta: cifrele ei
     * n-au trecut nici macar de ANAF.
     */
    protected const PASI_BUNI = ['validat', 'semnat', 'depus', 'finalizat'];

    /** Peste cati lei se socoteste ca doua randuri chiar difera. */
    protected const PRAG_LEI = 1;

    /** @var DecontDinSaft */
    protected $saft;

    /** @var DecontXml */
    protected $scriitor;

    public function __construct(DecontDinSaft $saft, DecontXml $scriitor)
    {
        $this->saft = $saft;
        $this->scriitor = $scriitor;
    }

    /**
     * @return array{
     *     stare: string,
     *     titlu: string,
     *     explicatie: string,
     *     perechea: ?array,
     *     numar: int,
     *     diferente: array
     * }
     */
    public function pentru(AnafDeclaratie $declaratie): array
    {
        if (!in_array($declaratie->tip, ['D300', 'D406'], true)) {
            return $this->fara('Potrivirea se face numai între D300 și D406.');
        }

        $perechea = $this->perechea($declaratie);

        if ($perechea === null) {
            return $this->fara(
                $declaratie->tip === 'D300'
                    ? 'Nu există un D406 validat pentru aceeași firmă și lună, cu care să fie comparată.'
                    : 'Nu există un D300 validat pentru aceeași firmă și lună, cu care să fie comparat.'
            );
        }

        $saft = $declaratie->tip === 'D406' ? $declaratie : $perechea;
        $d300 = $declaratie->tip === 'D300' ? $declaratie : $perechea;

        try {
            $dinSaft = $this->randurileDinSaft($saft);
            $dinD300 = $this->randurileDinD300($d300);
        } catch (DeclaratieException $e) {
            return [
                'stare' => 'imposibil',
                'titlu' => 'Potrivirea nu s-a putut face',
                'explicatie' => $e->getMessage(),
                'perechea' => $this->prezintaPerechea($perechea),
                'numar' => 0,
                'diferente' => [],
            ];
        }

        $diferente = $this->diferentele($dinSaft, $dinD300);

        if ($diferente === []) {
            return [
                'stare' => 'potrivit',
                'titlu' => 'Decontul se potrivește cu SAF-T-ul',
                'explicatie' => 'Toate rândurile din D300 ies la fel din jurnalele fișierului SAF-T.',
                'perechea' => $this->prezintaPerechea($perechea),
                'numar' => 0,
                'diferente' => [],
            ];
        }

        return [
            'stare' => 'diferente',
            'titlu' => count($diferente) === 1
                ? 'Un rând nu se potrivește cu SAF-T-ul'
                : count($diferente) . ' rânduri nu se potrivesc cu SAF-T-ul',
            'explicatie' => 'Cifrele din D300 nu ies din jurnalele fișierului SAF-T al aceleiași luni.'
                . ' Una din cele două declarații spune altceva decât cealaltă — iar ANAF face chiar'
                . ' comparația aceasta.',
            'perechea' => $this->prezintaPerechea($perechea),
            'numar' => count($diferente),
            'diferente' => $diferente,
        ];
    }

    /**
     * Cealalta declaratie a aceleiasi luni.
     *
     * Se ia cea mai noua dintre cele bune: daca s-a depus o rectificativa, ea e
     * cea care conteaza.
     */
    protected function perechea(AnafDeclaratie $declaratie): ?AnafDeclaratie
    {
        if (!$declaratie->cui || !$declaratie->luna || !$declaratie->anul) {
            return null;
        }

        /*
         * [2026-10-15] Perioada se potrivește după luna ei de sfârșit.
         *
         * D300 poartă ultima lună a perioadei: decontul pe trimestrul 3 are
         * „luna 9". SAF-T-ul poartă prima: cel pe trimestrul 3 are „luna 7".
         * Comparate lună cu lună, cele două nu se găseau niciodată la firmele
         * cu decont trimestrial.
         */
        if ($declaratie->tip === 'D406') {
            return AnafDeclaratie::where('tip', 'D300')
                ->where('cui', $declaratie->cui)
                ->where('luna', $this->lunaDeSfarsit($declaratie))
                ->where('anul', $declaratie->anul)
                ->whereIn('pas', self::PASI_BUNI)
                ->orderByDesc('created_at')
                ->first();
        }

        $sfarsit = (int) $declaratie->luna;

        // SAF-T-urile care se pot incheia in luna decontului: lunar, trimestrial, anual.
        $inceputuri = array_values(array_unique(array_filter([$sfarsit, $sfarsit - 2, $sfarsit === 12 ? 1 : null])));

        return AnafDeclaratie::where('tip', 'D406')
            ->where('cui', $declaratie->cui)
            ->whereIn('luna', $inceputuri)
            ->where('anul', $declaratie->anul)
            ->whereIn('pas', self::PASI_BUNI)
            ->orderByDesc('created_at')
            ->get()
            ->first(function (AnafDeclaratie $saft) use ($sfarsit) {
                return $this->lunaDeSfarsit($saft) === $sfarsit;
            });
    }

    /**
     * Ultima lună a perioadei unei declarații.
     *
     * La D300 e chiar luna ei. La SAF-T se citește din antet, din data de
     * sfârșit a selecției; fără ea, din felul perioadei — trimestrul se încheie
     * după două luni, anul în decembrie.
     */
    public function lunaDeSfarsit(AnafDeclaratie $declaratie): int
    {
        $luna = (int) $declaratie->luna;

        if ($declaratie->tip !== 'D406') {
            return $luna;
        }

        $dinFisier = $this->lunaDeSfarsitDinFisier($declaratie);

        if ($dinFisier !== null) {
            return $dinFisier;
        }

        switch (strtoupper((string) $declaratie->perioada_tip)) {
            case 'T':
                return min(12, $luna + 2);
            case 'A':
                return 12;
            default:
                return $luna;
        }
    }

    /** „<SelectionEndDate>2026-09-30</SelectionEndDate>" din antetul SAF-T-ului. */
    protected function lunaDeSfarsitDinFisier(AnafDeclaratie $saft): ?int
    {
        if (!$saft->cale_xml || !Storage::exists($saft->cale_xml)) {
            return null;
        }

        // Antetul sta la inceput; fisierul intreg are uneori sute de megaocteti.
        $inceput = @file_get_contents(Storage::path($saft->cale_xml), false, null, 0, 20000);

        if ($inceput !== false && preg_match('/<(?:\w+:)?SelectionEndDate>\s*\d{4}-(\d{2})-\d{2}/', $inceput, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    /** Randurile care ies din jurnalele SAF-T, in atributele declaratiei. */
    protected function randurileDinSaft(AnafDeclaratie $saft): array
    {
        if (!$saft->cale_xml || !Storage::exists($saft->cale_xml)) {
            throw new DeclaratieException('Fișierul SAF-T nu mai este pe server.');
        }

        $decont = $this->saft->genereaza(Storage::path($saft->cale_xml));

        return $this->scriitor->randurileDeclaratiei($decont['randuri']);
    }

    /**
     * Randurile scrise in D300, asa cum le-a depus omul.
     *
     * Ele stau in atributele elementului de la radacina — „R5_1", „R17_2" — si
     * se citesc de acolo, oricare ar fi versiunea schemei.
     */
    protected function randurileDinD300(AnafDeclaratie $d300): array
    {
        if (!$d300->cale_xml || !Storage::exists($d300->cale_xml)) {
            throw new DeclaratieException('Fișierul D300 nu mai este pe server.');
        }

        $anterior = libxml_use_internal_errors(true);
        $declaratie = simplexml_load_string(Storage::get($d300->cale_xml));
        libxml_use_internal_errors($anterior);

        if ($declaratie === false) {
            throw new DeclaratieException('Fișierul D300 nu a putut fi citit.');
        }

        $randuri = [];

        foreach ($declaratie->attributes() as $nume => $valoare) {
            if (preg_match('/^R\d[\d_]*$/', (string) $nume) === 1) {
                $randuri[(string) $nume] = (int) round((float) (string) $valoare);
            }
        }

        return $randuri;
    }

    /**
     * Randurile care nu se potrivesc.
     *
     * Se trece prin toate randurile decontului, nu numai prin cele scrise:
     * lipsa unui rand din D300 inseamna zero, iar un zero acolo unde SAF-T-ul
     * are cifra e tocmai genul de nepotrivire care se cauta.
     */
    protected function diferentele(array $dinSaft, array $dinD300): array
    {
        $diferente = [];

        foreach (RanduriD300::RANDURI as $camp => $rand) {
            $atribut = $rand['atribut'];

            $saft = $dinSaft[$atribut] ?? 0;
            $d300 = $dinD300[$atribut] ?? 0;

            if (abs($saft - $d300) < self::PRAG_LEI) {
                continue;
            }

            $diferente[$atribut] = [
                'atribut' => $atribut,
                'rand' => $rand['rand'],
                'denumire' => $rand['denumire'],
                'fel' => substr($camp, -5) === '_BAZA' ? 'bază' : 'TVA',
                'din_saft' => $saft,
                'din_d300' => $d300,
                'diferenta' => $saft - $d300,
            ];
        }

        return array_values($diferente);
    }

    /** Perechea, asa cum se arata in tabel. */
    protected function prezintaPerechea(AnafDeclaratie $perechea): array
    {
        return [
            'id' => $perechea->id,
            'tip' => $perechea->tip,
            'nume_fisier' => $perechea->nume_fisier,
            'pas' => $perechea->pas,
        ];
    }

    /** Nu e cu ce compara — si asta nu e o greseala. */
    protected function fara(string $explicatie): array
    {
        return [
            'stare' => 'fara_pereche',
            'titlu' => 'Nu are cu ce fi comparată',
            'explicatie' => $explicatie,
            'perechea' => null,
            'numar' => 0,
            'diferente' => [],
        ];
    }
}
