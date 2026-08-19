<?php

namespace App\Services\Anaf\Etransport;

use App\Models\EtransportDeclaratie;
use DOMDocument;
use DOMElement;

/**
 * Declarația Intrastat, întocmită din declarațiile e-Transport cu UIT.
 *
 * Cine declară transporturile în e-Transport a spus deja tot ce cere și
 * Intrastat-ul lunar: codul NC8, greutatea netă, valoarea în lei, țara
 * partenerului. De aici iese fișierul XML pe schema INS
 * (http://www.intrastat.ro/xml/InsSchema), care se încarcă în aplicația
 * Intrastat (online sau offline) — aceasta îl validează și îl criptează la
 * depunere, conform ghidului INS de implementare XML.
 *
 * Sosirile ies din achizițiile intracomunitare (AIC), expedierile din
 * livrările intracomunitare (LIC), cu liniile adunate pe cod NC8 și țară.
 */
class IntrastatXml
{
    public const NAMESPACE = 'http://www.intrastat.ro/xml/InsSchema';

    /** Fluxurile declarației și tipul de operațiune e-Transport din care ies. */
    public const FLUXURI = [
        'sosiri' => 10,
        'expedieri' => 20,
    ];

    /**
     * Versiunile nomenclatoarelor, cerute în antet. Aplicația Intrastat
     * revalidează oricum totul la import; anul NC8 se pune la generare.
     */
    protected const VERSIUNI = [
        'CountryVer' => '2007',
        'EuCountryVer' => '2007',
        'CnVer' => '',
        'ModeOfTransportVer' => '2005',
        'DeliveryTermsVer' => '2011',
        'NatureOfTransactionAVer' => '2010',
        'NatureOfTransactionBVer' => '2010',
        'CountyVer' => '2005',
        'LocalityVer' => '2005',
        'UnitVer' => '2005',
    ];

    /**
     * @param array{cif: string, firma: string, nume: string, prenume: string,
     *     telefon: string, email: ?string, incoterm: string} $antet
     * @return array{nume: string, xml: string, linii: int, declaratii: int, valoare: int}
     */
    public function genereaza(int $luna, int $anul, string $flux, array $antet): array
    {
        if (!isset(self::FLUXURI[$flux])) {
            throw new EtransportException('Fluxul cerut nu există: se alege între sosiri și expedieri.');
        }

        $declaratii = EtransportDeclaratie::whereNotNull('uit')
            ->where('tip_operatiune', self::FLUXURI[$flux])
            ->whereYear('data_transport', $anul)
            ->whereMonth('data_transport', $luna)
            ->get();

        if ($declaratii->isEmpty()) {
            throw new EtransportException(sprintf(
                'Nicio declarație e-Transport cu UIT pentru %s pe %02d/%d.',
                $flux === 'sosiri' ? 'achiziții intracomunitare (sosiri)' : 'livrări intracomunitare (expedieri)',
                $luna,
                $anul
            ));
        }

        $linii = $this->aduna($declaratii, $flux);

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $radacina = $doc->createElementNS(
            self::NAMESPACE,
            $flux === 'sosiri' ? 'InsNewArrival' : 'InsNewDispatch'
        );
        $doc->appendChild($radacina);
        $radacina->setAttribute('SchemaVersion', '1.0');

        $radacina->appendChild($this->versiunile($doc, $anul));
        $radacina->appendChild($this->antetul($doc, $luna, $anul, $antet));

        $numarLinie = 0;
        $valoareTotala = 0;

        foreach ($linii as $linie) {
            $element = $doc->createElementNS(
                self::NAMESPACE,
                $flux === 'sosiri' ? 'InsArrivalItem' : 'InsDispatchItem'
            );
            $radacina->appendChild($element);
            $element->setAttribute('OrderNr', (string) ++$numarLinie);

            $this->text($doc, $element, 'Cn8Code', $linie['cn8']);
            $this->text($doc, $element, 'InvoiceValue', (string) $linie['valoare']);
            $this->text($doc, $element, 'StatisticalValue', (string) $linie['valoare']);
            $this->text($doc, $element, 'NetMass', (string) $linie['masa']);
            // Natura tranzacției 1/1: cumpărare/vânzare definitivă.
            $this->text($doc, $element, 'NatureOfTransactionACode', '1');
            $this->text($doc, $element, 'NatureOfTransactionBCode', '1');
            $this->text($doc, $element, 'DeliveryTermsCode', $antet['incoterm']);
            // Transport rutier: doar el trece prin e-Transport.
            $this->text($doc, $element, 'ModeOfTransportCode', '3');
            $this->text($doc, $element, 'CountryOfOrigin', $linie['origine']);

            if ($flux === 'sosiri') {
                $this->text($doc, $element, 'CountryOfConsignment', $linie['tara']);
            } else {
                $this->text($doc, $element, 'CountryOfDestination', $linie['tara']);
                $this->text($doc, $element, 'PartnerCountryCode', $linie['tara']);
                $this->text($doc, $element, 'PartnerVatNr', $linie['partener_cod'] ?: '-');
            }

            $valoareTotala += $linie['valoare'];
        }

        return [
            'nume' => sprintf('intrastat_%s_%d_%02d_%s.xml', $flux, $anul, $luna, preg_replace('/\D/', '', $antet['cif'])),
            'xml' => $doc->saveXML(),
            'linii' => count($linii),
            'declaratii' => $declaratii->count(),
            'valoare' => $valoareTotala,
        ];
    }

    /**
     * Adună liniile declarațiilor pe cod NC8, țară parteneră și țară de
     * origine: Intrastat cere totaluri pe fel de marfă, nu fiecare transport.
     */
    protected function aduna($declaratii, string $flux): array
    {
        $linii = [];

        foreach ($declaratii as $declaratie) {
            $tara = strtoupper(trim((string) $declaratie->partener_tara)) ?: 'XX';

            foreach ($declaratie->linii ?: [] as $rand) {
                $cn8 = preg_replace('/\D/', '', (string) ($rand['cod_tarifar'] ?? ''));

                if (strlen($cn8) !== 8) {
                    // Fara cod NC8 intreg, linia nu are loc in Intrastat.
                    continue;
                }

                /*
                 * La sosiri, tara de origine e a marfii (din fisierul
                 * furnizorului); necunoscuta, ramane tara partenerului. La
                 * expedieri, marfa pleaca de aici.
                 */
                $origine = $flux === 'sosiri'
                    ? (strtoupper(trim((string) ($rand['tara_origine'] ?? ''))) ?: $tara)
                    : 'RO';

                $cheie = $cn8 . '|' . $tara . '|' . $origine;

                if (!isset($linii[$cheie])) {
                    $linii[$cheie] = [
                        'cn8' => $cn8,
                        'tara' => $tara,
                        'origine' => $origine,
                        'valoare' => 0,
                        'masa' => 0.0,
                        'partener_cod' => trim((string) $declaratie->partener_cod),
                    ];
                }

                $linii[$cheie]['valoare'] += (int) round((float) ($rand['valoare_lei'] ?? 0));
                $linii[$cheie]['masa'] += (float) ($rand['greutate_neta'] ?? 0);
            }
        }

        foreach ($linii as &$linie) {
            // INS cere numere intregi, iar sub un kilogram se scrie 1.
            $linie['masa'] = max(1, (int) round($linie['masa']));
            $linie['valoare'] = max(1, $linie['valoare']);
        }

        return array_values($linii);
    }

    protected function versiunile(DOMDocument $doc, int $anul): DOMElement
    {
        $element = $doc->createElementNS(self::NAMESPACE, 'InsCodeVersions');

        foreach (self::VERSIUNI as $nume => $valoare) {
            $this->text($doc, $element, $nume, $nume === 'CnVer' ? (string) $anul : $valoare);
        }

        return $element;
    }

    protected function antetul(DOMDocument $doc, int $luna, int $anul, array $antet): DOMElement
    {
        $element = $doc->createElementNS(self::NAMESPACE, 'InsDeclarationHeader');

        // Schema cere fix 10 cifre; CIF-ul se completeaza cu zerouri in fata.
        $this->text($doc, $element, 'VatNr', str_pad(preg_replace('/\D/', '', $antet['cif']), 10, '0', STR_PAD_LEFT));
        $this->text($doc, $element, 'FirmName', $antet['firma']);
        $this->text($doc, $element, 'RefPeriod', sprintf('%d-%02d', $anul, $luna));
        $this->text($doc, $element, 'CreateDt', now()->format('Y-m-d\TH:i:s'));

        $contact = $doc->createElementNS(self::NAMESPACE, 'ContactPerson');
        $element->appendChild($contact);

        $this->text($doc, $contact, 'LastName', $antet['nume']);
        $this->text($doc, $contact, 'FirstName', $antet['prenume']);

        if (!empty($antet['email'])) {
            $this->text($doc, $contact, 'Email', $antet['email']);
        }

        $this->text($doc, $contact, 'Phone', $antet['telefon']);

        return $element;
    }

    protected function text(DOMDocument $doc, DOMElement $parinte, string $nume, string $valoare): void
    {
        $element = $doc->createElementNS(self::NAMESPACE, $nume);
        $element->appendChild($doc->createTextNode($valoare));
        $parinte->appendChild($element);
    }
}
