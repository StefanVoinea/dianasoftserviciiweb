<?php

namespace App\Services\Anaf\Declaratii\D300;

use App\Models\AnafSocietate;
use XMLWriter;

/**
 * Decontul scris pentru formularul inteligent al ANAF („soft A").
 *
 * Formularul acela e un PDF de tip XFA: datele lui nu stau in atribute, ca in
 * declaratia de depus, ci intr-un arbore de subformulare — fiecare rand al
 * decontului e un subformular cu doua casute, „c2" pentru baza si „c3" pentru
 * taxa. Un fisier care se incarca in el trebuie sa aiba aceeasi asezare; ea se
 * ia din FormularD300, scoasa din chiar PDF-ul publicat de ANAF.
 *
 * Deosebirea fata de DecontXml, care scrie declaratia de depus:
 *
 *   - acolo se scriu atribute („R5_1"), aici casute („form1/date/comert/r5/c2");
 *   - acolo lipsa unui camp din antet opreste scrierea, fiindca declaratia ar fi
 *     respinsa la validare; aici nu, fiindca formularul tocmai pentru asta e —
 *     omul deschide PDF-ul, vede cifrele venite din SAF-T si completeaza restul;
 *   - acolo totalurile se socotesc dupa regulile validatorului; aici nu se
 *     ating: formularul isi face singur adunarile, la deschidere.
 *
 * Fisierul se incarca in Acrobat Reader, din „Import Data" — ANAF n-a pus buton
 * de incarcare in formular.
 */
class DecontFormular
{
    /** Radacina fisierelor de date XFA. */
    protected const NAMESPACE_XFA = 'http://www.xfa.org/schema/xfa-data/1.0/';

    /** @param array $decont ce a scos DecontDinSaft */
    public function scrie(array $decont, ?AnafSocietate $societate): string
    {
        $date = [];
        $antet = $this->campurileAntetului($decont, $societate);

        /*
         * Se merge in ordinea din formular, nu in a noastra: asa iese fisierul
         * asezat ca formularul insusi, si e mai lesne de citit cand cineva se
         * uita in el.
         */
        foreach (FormularD300::ANTET as $nume => $cale) {
            if (($antet[$nume] ?? '') === '') {
                continue;
            }

            $this->aseaza($date, $cale, $antet[$nume]);
        }

        foreach ($this->randurile($decont['randuri']) as $cale => $valoare) {
            $this->aseaza($date, $cale, $valoare);
        }

        return $this->xml($date);
    }

    /** Numele fisierului: acelasi cu al declaratiei, dar pentru formular. */
    public function numeFisier(array $decont): string
    {
        return sprintf(
            'D300_formular_%s_%04d%02d.xml',
            (string) (int) preg_replace('/\D/', '', $decont['cif']),
            (int) $decont['an'],
            (int) $decont['luna']
        );
    }

    /**
     * Ce se poate completa din antet.
     *
     * Ce lipseste de pe fisa firmei ramane necompletat in formular — nu se
     * opreste scrierea pentru atat. Adresa se scrie intreaga in casuta strazii:
     * formularul o cere rupta in bucati (strada, numarul, blocul, scara), iar
     * noi o tinem ca un singur rand.
     *
     * @return array<string, string>
     */
    protected function campurileAntetului(array $decont, ?AnafSocietate $societate): array
    {
        $luna = (int) $decont['luna'];
        $an = (int) $decont['an'];

        $campuri = [
            'denumire' => (string) $decont['denumire'],
            'cif' => (string) (int) preg_replace('/\D/', '', $decont['cif']),
            'an' => (string) $an,
            'luna' => (string) $luna,
        ];

        if ($societate === null) {
            return $campuri;
        }

        return $campuri + [
            'adresa' => (string) $societate->adresa,
            'telefon' => (string) $societate->telefon,
            'fax' => (string) $societate->fax,
            'email' => (string) $societate->email,
            'banca' => (string) $societate->banca,
            'iban' => (string) $societate->cont,
            'caen' => (string) $societate->caen,
            'tip_decont' => (string) $societate->d300_tip_decont,
            'pro_rata' => $societate->d300_pro_rata === null
                ? ''
                : number_format((float) $societate->d300_pro_rata, 2, '.', ''),
            'nume_declarant' => (string) $societate->nume_declarant,
            'prenume_declarant' => (string) $societate->prenume_declarant,
            'functie_declarant' => (string) $societate->functie_declarant,
            'temei' => $societate->prin_reprezentant ? '2' : '0',
            'prin_reprezentant' => $societate->prin_reprezentant ? '1' : '0',
        ];
    }

    /**
     * Randurile decontului, asezate in casutele formularului.
     *
     * Totalurile nu se scriu: formularul si le face singur din randurile de
     * deasupra, iar o cifra pusa de noi peste ele n-ar face decat sa se bata cu
     * socoteala lui.
     *
     * @return array<string, string> calea casutei => suma, in lei intregi
     */
    protected function randurile(array $randuri): array
    {
        $casute = [];

        foreach (RanduriD300::RANDURI as $camp => $rand) {
            if (isset(RanduriD300::TOTALURI[$rand['atribut']])) {
                continue;
            }

            $leu = (int) round((float) ($randuri[$camp] ?? 0));

            if ($leu === 0 || !isset(FormularD300::RANDURI[$rand['rand']])) {
                continue;
            }

            $unde = FormularD300::RANDURI[$rand['rand']];
            $casuta = substr($camp, -5) === '_BAZA' ? $unde['baza'] : $unde['tva'];

            if ($casuta === null) {
                continue;
            }

            $casute[$unde['cale'] . '/' . $casuta] = (string) $leu;
        }

        return $casute;
    }

    /** Pune valoarea la locul ei in arborele de date. */
    protected function aseaza(array &$date, string $cale, string $valoare): void
    {
        $bucati = explode('/', $cale);
        $unde = &$date;

        foreach ($bucati as $bucata) {
            if (!isset($unde[$bucata])) {
                $unde[$bucata] = [];
            }

            $unde = &$unde[$bucata];
        }

        $unde = $valoare;
    }

    /** @param array<string, mixed> $date */
    protected function xml(array $date): string
    {
        $scriitor = new XMLWriter();
        $scriitor->openMemory();
        $scriitor->setIndent(true);
        $scriitor->setIndentString('  ');
        $scriitor->startDocument('1.0', 'UTF-8');

        $scriitor->startElementNS('xfa', 'datasets', self::NAMESPACE_XFA);
        $scriitor->startElementNS('xfa', 'data', null);

        $this->scrieArborele($scriitor, $date);

        $scriitor->endElement();
        $scriitor->endElement();
        $scriitor->endDocument();

        return $scriitor->outputMemory();
    }

    /** @param array<string, mixed> $ramuri */
    protected function scrieArborele(XMLWriter $scriitor, array $ramuri): void
    {
        foreach ($ramuri as $nume => $continut) {
            $scriitor->startElement($nume);

            if (is_array($continut)) {
                $this->scrieArborele($scriitor, $continut);
            } else {
                $scriitor->text((string) $continut);
            }

            $scriitor->endElement();
        }
    }
}
