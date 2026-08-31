<?php

namespace App\Services\Anaf\Declaratii\D300;

use App\Models\AnafSocietate;
use App\Services\Anaf\Declaratii\DeclaratieException;
use XMLWriter;

/**
 * Decontul socotit din SAF-T, scris ca declaratie D300.
 *
 * Fisierul care iese de aici se incarca in formularul inteligent al ANAF
 * („soft A"): omul il deschide, se uita peste cifre, completeaza ce mai vrea si
 * il depune de acolo. Tot el trece si prin DUKIntegrator, ca orice declaratie
 * adusa in aplicatie.
 *
 * Trei lucruri de stiut despre ce se scrie:
 *
 *   - randurile intra sub numele lor din schema, care nu e acelasi lucru cu
 *     numarul de pe formular: randul 19 — totalul taxei colectate — sta in
 *     „R17_1". Legatura vine din RanduriD300, scoasa din chiar generatorul de
 *     PDF al ANAF;
 *   - totalurile nu se afla in datele aplicatiei ANAF, care le socoteste in
 *     raport; se aduna aici dupa aceleasi formule, luate tot de acolo;
 *   - sumele se rotunjesc la leu, cum cere schema, iar totalul se aduna din
 *     sumele deja rotunjite — altfel adunarea din declaratie n-ar mai da.
 */
class DecontXml
{
    /** Schema in vigoare, pentru declaratiile din 2026 incoace. */
    protected const NAMESPACE_D300 = 'mfp:anaf:dgti:d300:declaratie:v12';

    /** Codul impozitului din numarul de evidenta, dupa felul decontului. */
    protected const COD_IMPOZIT = ['L' => '301', 'T' => '302', 'S' => '303', 'A' => '304'];

    /**
     * @param array $decont ce a scos DecontDinSaft
     *
     * @throws DeclaratieException
     */
    public function scrie(array $decont, ?AnafSocietate $societate): string
    {
        $antet = AntetD300::pentru($societate);

        if (!$antet['gata']) {
            throw new DeclaratieException(
                'Antetul declarației nu e întreg. Mai lipsesc: ' . implode(', ', $antet['lipsesc'])
                . '. Se completează o dată, la Entități → Date pentru declarații.'
            );
        }

        $luna = (int) $decont['luna'];
        $an = (int) $decont['an'];
        $sume = $this->sumele($decont['randuri']);

        $atribute = array_merge(
            $antet['atribute'],
            [
                'luna' => (string) $luna,
                'an' => (string) $an,
                'cui' => $this->cuiCurat($decont['cif']),
                'den' => $decont['denumire'],
                'nr_evid' => $this->numarDeEvidenta($luna, $an, $antet['atribute']['tip_decont']),
            ],
            $sume,
            // Suma de control, pe care validatorul ANAF o cantareste.
            ['totalPlata_A' => (string) array_sum(array_map('intval', $sume))]
        );

        return $this->xml($atribute);
    }

    /**
     * Numarul de evidenta a platii, cele 23 de cifre ale lui.
     *
     * Nu e un numar liber: validatorul ANAF il desface bucata cu bucata (regula
     * R25) si-l respinge daca nu iese. Se alcatuieste asa:
     *
     *   10   301   01   0626   250726   0000   42
     *   |    |     |    |      |        |      cifra de control
     *   |    |     |    |      |        pozitii fixe
     *   |    |     |    |      scadenta: 25 ale lunii urmatoare
     *   |    |     |    perioada raportata (luna si anul, doua cifre)
     *   |    |     pozitii fixe
     *   |    codul impozitului, dupa felul decontului
     *   pozitii fixe
     *
     * Cifra de control e suma celor 21 de cifre dinaintea ei.
     */
    protected function numarDeEvidenta(int $luna, int $an, string $tipDecont): string
    {
        // Scadenta: ziua 25 a lunii de dupa perioada raportata.
        $lunaScadenta = $luna + 1;
        $anScadenta = $an;

        if ($lunaScadenta > 12) {
            $lunaScadenta = 1;
            $anScadenta++;
        }

        $numar = '10'
            . (self::COD_IMPOZIT[$tipDecont] ?? self::COD_IMPOZIT['L'])
            . '01'
            . sprintf('%02d%02d', $luna, $an % 100)
            . sprintf('25%02d%02d', $lunaScadenta, $anScadenta % 100)
            . '0000';

        return $numar . sprintf('%02d', array_sum(str_split($numar)));
    }

    /** Numele fisierului, dupa obiceiul declaratiilor: tip, cod fiscal, perioada. */
    public function numeFisier(array $decont): string
    {
        return sprintf(
            'D300_%s_%04d%02d.xml',
            $this->cuiCurat($decont['cif']),
            (int) $decont['an'],
            (int) $decont['luna']
        );
    }

    /**
     * Randurile decontului, sub numele lor din schema.
     *
     * Cele ramase la zero nu se scriu: declaratia are peste o suta de randuri,
     * iar intr-o luna obisnuita se umplu cateva. Ce lipseste e zero si pentru
     * ANAF.
     *
     * @return array<string, string>
     */
    protected function sumele(array $randuri): array
    {
        $valori = [];

        foreach (RanduriD300::RANDURI as $camp => $rand) {
            $valori[$rand['atribut']] = $this->leiIntregi($randuri[$camp] ?? 0);
        }

        $this->socotesteRandurileScoase($valori);

        // Ce a ramas zero nu se scrie: pentru ANAF, lipsa inseamna tot zero.
        return array_map('strval', array_filter($valori, function ($leu) {
            return $leu !== 0;
        }));
    }

    /**
     * Totalurile si soldurile, socotite dupa regulile validatorului ANAF.
     *
     * Se aduna din randurile deja rotunjite, nu din cifrele dinaintea
     * rotunjirii: validatorul cantareste chiar adunarea randurilor din
     * declaratie, iar o diferenta de un leu din rotunjiri ar face-o sa nu dea.
     *
     * Unele se sprijina pe altele — taxa de plata iese din totaluri, iar soldul
     * de la sfarsit iese din ea —, asa ca se trece de mai multe ori peste ele,
     * pana cand nu se mai schimba nimic. Sunt patru randuri in lant; cinci
     * treceri ajung cu prisosinta.
     */
    protected function socotesteRandurileScoase(array &$valori): void
    {
        for ($trecere = 0; $trecere < 5; $trecere++) {
            $inainte = $valori;

            /*
             * Randurile care se copiaza unul din altul: randul 20 e randul 5
             * vazut din partea deducerii. Aplicatia ANAF le tine pe amandoua,
             * dar nu pe toate le si tipareste — iar declaratia le cere scrise
             * deopotriva.
             */
            foreach (RanduriD300::EGALITATI as $atribut => $dupa) {
                if (isset($valori[$dupa])) {
                    $valori[$atribut] = $valori[$dupa];
                }
            }

            foreach (RanduriD300::TOTALURI as $atribut => $termeni) {
                $total = 0;

                foreach ($termeni as $termen) {
                    $total += $valori[$termen] ?? 0;
                }

                $valori[$atribut] = $total;
            }

            /*
             * Soldurile se scad si se taie la zero: ori iese suma de recuperat,
             * ori taxa de plata — niciodata amandoua.
             */
            foreach (RanduriD300::DIFERENTE as $atribut => $termeni) {
                $valori[$atribut] = max(($valori[$termeni[0]] ?? 0) - ($valori[$termeni[1]] ?? 0), 0);
            }

            if ($valori === $inainte) {
                return;
            }
        }
    }

    /** Decontul se depune in lei intregi. */
    protected function leiIntregi($valoare): int
    {
        return (int) round((float) $valoare);
    }

    /**
     * Codul fiscal, fara „RO" si fara zerourile din fata.
     *
     * In SAF-T el vine cum l-a scris programul de contabilitate — „RO14385411"
     * la unul, „0014385411" la altul —, iar schema D300 il cere numar.
     */
    protected function cuiCurat(string $cif): string
    {
        $cifre = preg_replace('/\D/', '', $cif);

        return (string) (int) $cifre;
    }

    /** @param array<string, string> $atribute */
    protected function xml(array $atribute): string
    {
        $scriitor = new XMLWriter();
        $scriitor->openMemory();
        $scriitor->setIndent(true);
        $scriitor->setIndentString('  ');
        $scriitor->startDocument('1.0', 'UTF-8');

        $scriitor->startElement('declaratie300');
        $scriitor->writeAttribute('xmlns', self::NAMESPACE_D300);

        foreach ($atribute as $nume => $valoare) {
            $scriitor->writeAttribute($nume, (string) $valoare);
        }

        $scriitor->endElement();
        $scriitor->endDocument();

        return $scriitor->outputMemory();
    }
}
