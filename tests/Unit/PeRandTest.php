<?php

namespace Tests\Unit;

use App\Support\PeRand;
use Tests\TestCase;

/**
 * Lucrările se iau pe rând de la fiecare certificat.
 *
 * Pauza cerută de ANAF se ține pe fiecare certificat în parte. Din asta nu se
 * câștigă însă nimic dacă lucrările merg în bloc — toate ale unui token, apoi
 * toate ale celuilalt: fiecare apel își așteaptă pauza întreagă, și un client
 * cu două tokene ține exact cât unul cu unul singur.
 */
class PeRandTest extends TestCase
{
    /** Cheia de despărțire: al doilea caracter din nume. */
    protected function dupaToken(): callable
    {
        return function (string $rand) {
            return $rand[0];
        };
    }

    public function test_randurile_se_intrepatrund_intre_cozi(): void
    {
        $iesit = PeRand::intercalat(['A1', 'A2', 'A3', 'B1', 'B2', 'B3'], $this->dupaToken());

        $this->assertSame(['A1', 'B1', 'A2', 'B2', 'A3', 'B3'], $iesit);
    }

    /** Ordinea dinăuntrul fiecărui token rămâne neatinsă. */
    public function test_ordinea_fiecarui_token_ramane_aceeasi(): void
    {
        $iesit = PeRand::intercalat(['A1', 'B1', 'A2', 'A3', 'B2'], $this->dupaToken());

        $aleLuiA = array_values(array_filter($iesit, function ($rand) {
            return $rand[0] === 'A';
        }));

        $this->assertSame(['A1', 'A2', 'A3'], $aleLuiA);
    }

    /** Coada mai lungă își duce restul la sfârșit, fără să piardă nimic. */
    public function test_coada_mai_lunga_isi_duce_restul_la_sfarsit(): void
    {
        $iesit = PeRand::intercalat(['A1', 'A2', 'A3', 'A4', 'B1'], $this->dupaToken());

        $this->assertSame(['A1', 'B1', 'A2', 'A3', 'A4'], $iesit);
    }

    /** Cu un singur token nu e nimic de întrepătruns. */
    public function test_un_singur_token_ramane_cum_era(): void
    {
        $randuri = ['A1', 'A2', 'A3'];

        $this->assertSame($randuri, PeRand::intercalat($randuri, $this->dupaToken()));
    }

    public function test_nimic_ramane_nimic(): void
    {
        $this->assertSame([], PeRand::intercalat([], $this->dupaToken()));
    }

    /** Trei tokene se întrepătrund la fel de bine ca două. */
    public function test_trei_tokene_se_intrepatrund_la_fel(): void
    {
        $iesit = PeRand::intercalat(['A1', 'A2', 'B1', 'B2', 'C1'], $this->dupaToken());

        $this->assertSame(['A1', 'B1', 'C1', 'A2', 'B2'], $iesit);
    }
}
