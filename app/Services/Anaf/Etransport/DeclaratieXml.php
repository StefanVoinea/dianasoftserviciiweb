<?php

namespace App\Services\Anaf\Etransport;

use App\Models\EtransportDeclaratie;
use DOMDocument;

/**
 * XML-ul declarației e-Transport, construit din ce s-a completat în formular.
 *
 * Înainte de a pleca la ANAF, documentul se verifică pe schema oficială
 * (resources/anaf/eTransport_v2.xsd): o declarație cu lipsuri se oprește aici,
 * cu mesaje pe românește, nu la ANAF cu un cod de eroare.
 */
class DeclaratieXml
{
    public const NAMESPACE = 'mfp:anaf:dgti:eTransport:declaratie:v2';

    public function construieste(EtransportDeclaratie $declaratie): string
    {
        $this->verifica($declaratie);

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $radacina = $doc->createElementNS(self::NAMESPACE, 'eTransport');
        $doc->appendChild($radacina);
        $radacina->setAttribute('codDeclarant', preg_replace('/\D/', '', $declaratie->cif_declarant));

        if ($declaratie->referinta_interna) {
            $radacina->setAttribute('refDeclarant', $declaratie->referinta_interna);
        }

        $notificare = $doc->createElementNS(self::NAMESPACE, 'notificare');
        $radacina->appendChild($notificare);
        $notificare->setAttribute('codTipOperatiune', (string) $declaratie->tip_operatiune);

        foreach ($declaratie->linii as $linie) {
            $bunuri = $doc->createElementNS(self::NAMESPACE, 'bunuriTransportate');
            $notificare->appendChild($bunuri);

            $bunuri->setAttribute('codScopOperatiune', (string) $linie['scop_operatiune']);

            if (!empty($linie['cod_tarifar'])) {
                $bunuri->setAttribute('codTarifar', $linie['cod_tarifar']);
            }

            $bunuri->setAttribute('denumireMarfa', mb_substr(trim($linie['denumire']), 0, 200));
            $bunuri->setAttribute('cantitate', $this->numar($linie['cantitate']));
            $bunuri->setAttribute('codUnitateMasura', $linie['um'] ?: 'H87');

            if (!empty($linie['greutate_neta'])) {
                $bunuri->setAttribute('greutateNeta', $this->numar($linie['greutate_neta']));
            }

            $bunuri->setAttribute('greutateBruta', $this->numar($linie['greutate_bruta']));

            if (isset($linie['valoare_lei']) && $linie['valoare_lei'] !== null && $linie['valoare_lei'] !== '') {
                $bunuri->setAttribute('valoareLeiFaraTva', $this->numar($linie['valoare_lei']));
            }
        }

        $partener = $doc->createElementNS(self::NAMESPACE, 'partenerComercial');
        $notificare->appendChild($partener);
        $partener->setAttribute('codTara', $declaratie->partener_tara);

        if ($declaratie->partener_cod) {
            $partener->setAttribute('cod', $declaratie->partener_cod);
        }

        $partener->setAttribute('denumire', $declaratie->partener_denumire);

        $transport = $doc->createElementNS(self::NAMESPACE, 'dateTransport');
        $notificare->appendChild($transport);
        $transport->setAttribute('nrVehicul', $this->inmatriculare($declaratie->nr_vehicul));

        if ($declaratie->nr_remorca1) {
            $transport->setAttribute('nrRemorca1', $this->inmatriculare($declaratie->nr_remorca1));
        }

        if ($declaratie->nr_remorca2) {
            $transport->setAttribute('nrRemorca2', $this->inmatriculare($declaratie->nr_remorca2));
        }

        $transport->setAttribute('codTaraOrgTransport', $declaratie->transportator_tara);

        if ($declaratie->transportator_cod) {
            $transport->setAttribute('codOrgTransport', $declaratie->transportator_cod);
        }

        $transport->setAttribute('denumireOrgTransport', $declaratie->transportator_denumire);
        $transport->setAttribute('dataTransport', $declaratie->data_transport->format('Y-m-d'));

        $notificare->appendChild($this->locTraseu($doc, 'locStartTraseuRutier', $declaratie->loc_start));
        $notificare->appendChild($this->locTraseu($doc, 'locFinalTraseuRutier', $declaratie->loc_final));

        foreach ($declaratie->documente as $document) {
            $element = $doc->createElementNS(self::NAMESPACE, 'documenteTransport');
            $notificare->appendChild($element);

            $element->setAttribute('tipDocument', (string) $document['tip']);

            if (!empty($document['numar'])) {
                $element->setAttribute('numarDocument', $document['numar']);
            }

            $element->setAttribute('dataDocument', $document['data']);

            if (!empty($document['observatii'])) {
                $element->setAttribute('observatii', mb_substr($document['observatii'], 0, 200));
            }
        }

        $xml = $doc->saveXML();

        $this->valideazaPeSchema($doc);

        return $xml;
    }

    protected function locTraseu(DOMDocument $doc, string $nume, array $loc): \DOMElement
    {
        $element = $doc->createElementNS(self::NAMESPACE, $nume);

        if (($loc['tip'] ?? '') === 'ptf') {
            $element->setAttribute('codPtf', (string) $loc['cod_ptf']);

            return $element;
        }

        if (($loc['tip'] ?? '') === 'birou_vamal') {
            $element->setAttribute('codBirouVamal', (string) $loc['cod_birou_vamal']);

            return $element;
        }

        $locatie = $doc->createElementNS(self::NAMESPACE, 'locatie');
        $element->appendChild($locatie);

        $locatie->setAttribute('codJudet', (string) $loc['cod_judet']);
        $locatie->setAttribute('denumireLocalitate', $loc['localitate']);
        $locatie->setAttribute('denumireStrada', $loc['strada']);

        foreach ([
            'numar' => 'numar', 'bloc' => 'bloc', 'scara' => 'scara', 'etaj' => 'etaj',
            'apartament' => 'apartament', 'cod_postal' => 'codPostal', 'alte_info' => 'alteInfo',
        ] as $camp => $atribut) {
            if (!empty($loc[$camp])) {
                $locatie->setAttribute($atribut, (string) $loc[$camp]);
            }
        }

        return $element;
    }

    /**
     * Lipsurile care s-ar vedea abia la ANAF se spun aici, pe românește.
     */
    protected function verifica(EtransportDeclaratie $declaratie): void
    {
        $lipsuri = [];

        if (!$declaratie->cif_declarant) {
            $lipsuri[] = 'CIF-ul declarantului';
        }

        if (!$declaratie->tip_operatiune) {
            $lipsuri[] = 'tipul operațiunii';
        }

        if (!$declaratie->linii) {
            $lipsuri[] = 'liniile cu bunurile transportate';
        }

        if (!$declaratie->partener_tara || !$declaratie->partener_denumire) {
            $lipsuri[] = 'partenerul comercial (țara și denumirea)';
        }

        if (!$declaratie->nr_vehicul) {
            $lipsuri[] = 'numărul de înmatriculare al vehiculului';
        }

        if (!$declaratie->transportator_tara || !$declaratie->transportator_denumire) {
            $lipsuri[] = 'transportatorul (țara și denumirea)';
        }

        if (!$declaratie->data_transport) {
            $lipsuri[] = 'data transportului';
        }

        if (!$declaratie->loc_start || !$declaratie->loc_final) {
            $lipsuri[] = 'locul de început și de sfârșit al traseului';
        }

        if (!$declaratie->documente) {
            $lipsuri[] = 'documentele de transport';
        }

        foreach ($declaratie->linii ?: [] as $numar => $linie) {
            if (empty($linie['denumire']) || empty($linie['cantitate']) || empty($linie['greutate_bruta'])) {
                $lipsuri[] = 'linia ' . ($numar + 1) . ' (denumire, cantitate și greutate brută)';
            }
        }

        /*
         * Regula ANAF BR-026: documentul de tip „Altele" (9999) trebuie sa
         * spuna in observatii ce fel de document e. Prinsa aici, pe romaneste,
         * nu la ANAF cu un cod de eroare.
         */
        foreach ($declaratie->documente ?: [] as $numar => $document) {
            if ((int) ($document['tip'] ?? 0) === 9999 && trim((string) ($document['observatii'] ?? '')) === '') {
                $lipsuri[] = 'observațiile documentului ' . ($numar + 1)
                    . ' — la tipul „Altele" ANAF cere scris în observații ce fel de document este';
            }
        }

        if ($lipsuri !== []) {
            throw new EtransportException('Declarația nu e completă. Lipsesc: ' . implode('; ', $lipsuri) . '.');
        }
    }

    protected function valideazaPeSchema(DOMDocument $doc): void
    {
        $schema = resource_path('anaf/eTransport_v2.xsd');

        if (!is_file($schema)) {
            // Fara schema pe disc, verificarea ramane cea de la ANAF.
            return;
        }

        libxml_use_internal_errors(true);

        if (!$doc->schemaValidate($schema)) {
            $erori = array_map(function ($eroare) {
                return trim($eroare->message);
            }, libxml_get_errors());

            libxml_clear_errors();

            throw new EtransportException(
                'Declarația nu respectă schema ANAF: ' . implode(' | ', array_unique($erori))
            );
        }
    }

    protected function numar($valoare): string
    {
        return rtrim(rtrim(number_format((float) $valoare, 2, '.', ''), '0'), '.');
    }

    protected function inmatriculare(string $numar): string
    {
        return strtoupper(preg_replace('/[^0-9A-Za-z]/', '', $numar));
    }
}
