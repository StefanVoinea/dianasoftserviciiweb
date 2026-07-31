<?php

namespace App\Services\Just;

use App\Services\Anaf\Format;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

/**
 * Clientul serviciului web Portal Just (portalquery.just.ro).
 *
 * Serviciul este SOAP 1.1 și se apelează fără autentificare. Nu folosim
 * SoapClient din PHP, pentru că elementele opționale sunt declarate
 * `nillable` cu `minOccurs=1`: ele trebuie trimise explicit ca `xsi:nil`,
 * iar SoapClient le-ar omite. Plicul se compune aici, direct.
 */
class PortalJustClient
{
    /** Spațiul de nume al serviciului — fără „http://”, așa cum e în WSDL. */
    protected function spatiuNume(): string
    {
        return (string) config('portaljust.namespace');
    }

    /**
     * Caută dosare. Cel puțin unul dintre numărul dosarului, obiect sau numele
     * părții este obligatoriu — serviciul refuză căutările nefiltrate.
     *
     * @param array{numar_dosar?:string,obiect?:string,nume_parte?:string,institutie?:string,data_start?:string,data_stop?:string,modificat_de?:string,modificat_pana?:string} $criterii
     */
    public function cautaDosare(array $criterii): array
    {
        $numar = $this->curata($criterii['numar_dosar'] ?? null);
        $obiect = $this->curata($criterii['obiect'] ?? null);
        $parte = $this->curata($criterii['nume_parte'] ?? null);

        if ($numar === null && $obiect === null && $parte === null) {
            throw new PortalJustException(
                'Completați cel puțin numărul dosarului, obiectul sau numele părții.'
            );
        }

        // Filtrele pe data ultimei modificări există doar în varianta a doua a metodei.
        $dupaModificare = !empty($criterii['modificat_de']) || !empty($criterii['modificat_pana']);
        $metoda = $dupaModificare ? 'CautareDosare2' : 'CautareDosare';

        $campuri = [
            $this->elementText('numarDosar', $numar),
            $this->elementText('obiectDosar', $obiect),
            $this->elementText('numeParte', $parte),
            $this->elementOptional('institutie', $this->curata($criterii['institutie'] ?? null)),
            $this->elementOptional('dataStart', $this->dataXml($criterii['data_start'] ?? null)),
            $this->elementOptional('dataStop', $this->dataXml($criterii['data_stop'] ?? null)),
        ];

        if ($dupaModificare) {
            $campuri[] = $this->elementOptional('dataUltimaModificareStart', $this->dataXml($criterii['modificat_de'] ?? null));
            $campuri[] = $this->elementOptional('dataUltimaModificareStop', $this->dataXml($criterii['modificat_pana'] ?? null));
        }

        $rezultat = $this->apel($metoda, implode('', $campuri));

        $dosare = [];

        foreach ($rezultat->Dosar ?? [] as $dosar) {
            $dosare[] = $this->citesteDosar($dosar);
        }

        return $dosare;
    }

    /** Ședințele unei instanțe într-o anumită zi. Ambele criterii sunt obligatorii. */
    public function cautaSedinte(string $data, string $institutie): array
    {
        $corp = $this->elementText('dataSedinta', $this->dataXml($data))
            . $this->elementText('institutie', $institutie);

        $rezultat = $this->apel('CautareSedinte', $corp);

        $sedinte = [];

        foreach ($rezultat->Sedinta ?? [] as $sedinta) {
            $sedinte[] = [
                'departament' => $this->text($sedinta, 'departament'),
                'complet' => $this->text($sedinta, 'complet'),
                'data' => Format::data($this->text($sedinta, 'data')),
                'ora' => $this->text($sedinta, 'ora'),
                'dosare' => $this->citesteDosareSedinta($sedinta),
            ];
        }

        return $sedinte;
    }

    /**
     * Lista instanțelor, citită din WSDL. Valorile sunt cele acceptate de
     * serviciu; eticheta e forma lizibilă, pentru afișare.
     *
     * @return array<int, array{valoare:string, eticheta:string}>
     */
    public function institutii(): array
    {
        $minute = (int) config('portaljust.cache_institutii_minute');

        return Cache::remember('portaljust.institutii', now()->addMinutes($minute), function () {
            $raspuns = Http::timeout((int) config('portaljust.timeout'))
                ->get(config('portaljust.url') . '?WSDL');

            if ($raspuns->failed()) {
                throw new PortalJustException('Lista instanțelor nu a putut fi citită de la Portal Just.');
            }

            return $this->institutiiDinWsdl($raspuns->body());
        });
    }

    /** @return array<int, array{valoare:string, eticheta:string}> */
    public function institutiiDinWsdl(string $wsdl): array
    {
        $anterior = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($wsdl);
        libxml_use_internal_errors($anterior);

        if ($xml === false) {
            throw new PortalJustException('Descrierea serviciului Portal Just nu a putut fi interpretată.');
        }

        $xml->registerXPathNamespace('s', 'http://www.w3.org/2001/XMLSchema');
        $noduri = $xml->xpath("//s:simpleType[@name='Institutie']/s:restriction/s:enumeration") ?: [];

        $institutii = [];

        foreach ($noduri as $nod) {
            $valoare = trim((string) $nod['value']);

            if ($valoare === '') {
                continue;
            }

            $institutii[] = ['valoare' => $valoare, 'eticheta' => $this->etichetaInstitutie($valoare)];
        }

        usort($institutii, function ($a, $b) {
            return strcmp($a['eticheta'], $b['eticheta']);
        });

        return $institutii;
    }

    /**
     * Valorile din WSDL sunt lipite („CurteadeApelALBAIULIA”). Le despărțim
     * după denumirea instanței și localitate, ca să fie citibile în listă.
     */
    public function etichetaInstitutie(string $valoare): string
    {
        // Denumirile compuse se recunosc întâi, altfel prefixul scurt le-ar ciunti.
        $denumiri = [
            'TribunalulMilitarTeritorial' => 'Tribunalul Militar Teritorial',
            'TribunalulpentruminoriSifamilie' => 'Tribunalul pentru Minori și Familie',
            'CurteaMilitaradeApel' => 'Curtea Militară de Apel',
            'TribunalulComercial' => 'Tribunalul Comercial',
            'TribunalulMilitar' => 'Tribunalul Militar',
            'CurteadeApel' => 'Curtea de Apel',
            'Judecatoria' => 'Judecătoria',
            'Tribunalul' => 'Tribunalul',
        ];

        foreach ($denumiri as $prefix => $eticheta) {
            if (strpos($valoare, $prefix) === 0) {
                $localitate = substr($valoare, strlen($prefix));

                return trim($eticheta . ' ' . $this->localitate($localitate));
            }
        }

        return $valoare;
    }

    /** „SECTORUL4BUCURESTI” → „SECTORUL 4 BUCURESTI”. */
    protected function localitate(string $text): string
    {
        $text = preg_replace('/(\d+)/', ' $1 ', $text);

        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * Trimite plicul SOAP și întoarce nodul cu rezultatul.
     */
    protected function apel(string $metoda, string $corp): SimpleXMLElement
    {
        $spatiu = $this->spatiuNume();

        $plic = '<?xml version="1.0" encoding="utf-8"?>'
            . '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
            . ' xmlns:xsd="http://www.w3.org/2001/XMLSchema"'
            . ' xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">'
            . '<soap:Body><' . $metoda . ' xmlns="' . $spatiu . '">'
            . $corp
            . '</' . $metoda . '></soap:Body></soap:Envelope>';

        $raspuns = Http::timeout((int) config('portaljust.timeout'))
            ->withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => '"' . $spatiu . '/' . $metoda . '"',
            ])
            ->withBody($plic, 'text/xml; charset=utf-8')
            ->post(config('portaljust.url'));

        if ($raspuns->serverError() || $raspuns->clientError()) {
            throw new PortalJustException($this->eroareDin($raspuns->body(), $raspuns->status()));
        }

        return $this->rezultatDin($raspuns->body(), $metoda);
    }

    protected function rezultatDin(string $corp, string $metoda): SimpleXMLElement
    {
        $anterior = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($corp);
        libxml_use_internal_errors($anterior);

        if ($xml === false) {
            throw new PortalJustException('Răspunsul Portal Just nu a putut fi interpretat.');
        }

        $body = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body;

        if ($body === null) {
            throw new PortalJustException('Răspunsul Portal Just nu conține un plic SOAP valid.');
        }

        $eroare = $body->children('http://schemas.xmlsoap.org/soap/envelope/')->Fault;

        if ($eroare !== null && $eroare->count() > 0) {
            throw new PortalJustException($this->explicatieFault($eroare) ?: 'Serviciul Portal Just a respins cererea.');
        }

        $continut = $body->children($this->spatiuNume());
        $raspuns = $continut->{$metoda . 'Response'};

        if ($raspuns === null || $raspuns->count() === 0) {
            throw new PortalJustException('Răspunsul Portal Just nu conține rezultatul așteptat.');
        }

        return $raspuns->{$metoda . 'Result'};
    }

    protected function citesteDosar(SimpleXMLElement $dosar): array
    {
        return [
            'numar' => $this->text($dosar, 'numar'),
            'numar_vechi' => $this->text($dosar, 'numarVechi'),
            'data' => Format::data($this->text($dosar, 'data')),
            'institutie' => $this->text($dosar, 'institutie'),
            'institutie_eticheta' => $this->etichetaInstitutie((string) $this->text($dosar, 'institutie')),
            'departament' => $this->text($dosar, 'departament'),
            'obiect' => $this->text($dosar, 'obiect'),
            'categorie' => $this->text($dosar, 'categorieCazNume'),
            'stadiu' => $this->text($dosar, 'stadiuProcesualNume'),
            'data_modificare' => Format::dataOra($this->text($dosar, 'dataModificare')),
            'parti' => $this->citesteParti($dosar),
            'sedinte' => $this->citesteSedinteDosar($dosar),
            'cai_atac' => $this->citesteCaiAtac($dosar),
        ];
    }

    protected function citesteParti(SimpleXMLElement $dosar): array
    {
        $parti = [];

        foreach ($dosar->parti->DosarParte ?? [] as $parte) {
            $parti[] = [
                'nume' => $this->text($parte, 'nume'),
                'calitate' => $this->text($parte, 'calitateParte'),
            ];
        }

        return $parti;
    }

    protected function citesteSedinteDosar(SimpleXMLElement $dosar): array
    {
        $sedinte = [];

        foreach ($dosar->sedinte->DosarSedinta ?? [] as $sedinta) {
            $sedinte[] = [
                'complet' => $this->text($sedinta, 'complet'),
                'data' => Format::data($this->text($sedinta, 'data')),
                'ora' => $this->text($sedinta, 'ora'),
                'solutie' => $this->text($sedinta, 'solutie'),
                'solutie_sumar' => $this->text($sedinta, 'solutieSumar'),
                'data_pronuntare' => Format::data($this->text($sedinta, 'dataPronuntare')),
                'document' => $this->text($sedinta, 'documentSedinta'),
                'numar_document' => $this->text($sedinta, 'numarDocument'),
                'data_document' => Format::data($this->text($sedinta, 'dataDocument')),
            ];
        }

        return $sedinte;
    }

    protected function citesteCaiAtac(SimpleXMLElement $dosar): array
    {
        $cai = [];

        foreach ($dosar->caiAtac->DosarCaleAtac ?? [] as $cale) {
            $cai[] = [
                'data_declarare' => Format::data($this->text($cale, 'dataDeclarare')),
                'parte_declaratoare' => $this->text($cale, 'parteDeclaratoare'),
                'tip' => $this->text($cale, 'tipCaleAtac'),
            ];
        }

        return $cai;
    }

    protected function citesteDosareSedinta(SimpleXMLElement $sedinta): array
    {
        $dosare = [];

        foreach ($sedinta->dosare->SedintaDosar ?? [] as $dosar) {
            $dosare[] = [
                'numar' => $this->text($dosar, 'numar'),
                'numar_vechi' => $this->text($dosar, 'numar_vechi'),
                'data' => Format::data($this->text($dosar, 'data')),
                'ora' => $this->text($dosar, 'ora'),
                'categorie' => $this->text($dosar, 'categorieCazNume'),
                'stadiu' => $this->text($dosar, 'stadiuProcesualNume'),
            ];
        }

        return $dosare;
    }

    /** Elementele goale sau marcate `xsi:nil` se întorc ca null, nu ca șir vid. */
    protected function text(SimpleXMLElement $nod, string $camp): ?string
    {
        if (!isset($nod->{$camp})) {
            return null;
        }

        $valoare = trim((string) $nod->{$camp});

        return $valoare === '' ? null : $valoare;
    }

    protected function elementText(string $nume, ?string $valoare): string
    {
        if ($valoare === null) {
            return '';
        }

        return '<' . $nume . '>' . htmlspecialchars($valoare, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</' . $nume . '>';
    }

    /** Câmpurile neprecizate se trimit explicit ca `nil`, altfel serviciul le refuză. */
    protected function elementOptional(string $nume, ?string $valoare): string
    {
        if ($valoare === null) {
            return '<' . $nume . ' xsi:nil="true" />';
        }

        return $this->elementText($nume, $valoare);
    }

    protected function curata($valoare): ?string
    {
        if ($valoare === null) {
            return null;
        }

        $valoare = trim((string) $valoare);

        return $valoare === '' ? null : $valoare;
    }

    /** Datele se trimit în formatul xsd:dateTime. */
    protected function dataXml($valoare): ?string
    {
        $valoare = $this->curata($valoare);

        if ($valoare === null) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($valoare)->format('Y-m-d\TH:i:s');
        } catch (\Exception $e) {
            throw new PortalJustException('Data „' . $valoare . '” nu poate fi interpretată.');
        }
    }

    protected function eroareDin(string $corp, int $status): string
    {
        // Erorile SOAP vin cu HTTP 500 și explicația în plic.
        $anterior = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($corp);
        libxml_use_internal_errors($anterior);

        if ($xml !== false) {
            $fault = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body
                ->children('http://schemas.xmlsoap.org/soap/envelope/')->Fault;

            if ($fault !== null && $fault->count() > 0) {
                $mesaj = $this->explicatieFault($fault);

                if ($mesaj !== '') {
                    return 'Portal Just: ' . $mesaj;
                }
            }
        }

        return 'Portal Just a răspuns cu eroarea HTTP ' . $status . '.';
    }

    /**
     * În SOAP 1.1 câmpurile erorii (`faultstring`) nu poartă prefix de spațiu de
     * nume, deși `Fault` îl are — de aceea se citesc din spațiul implicit.
     */
    protected function explicatieFault(SimpleXMLElement $fault): string
    {
        $mesaj = trim((string) $fault->children()->faultstring);

        if ($mesaj === '') {
            $mesaj = trim((string) $fault->children('http://www.w3.org/2003/05/soap-envelope')->Reason->Text);
        }

        return $mesaj;
    }
}
