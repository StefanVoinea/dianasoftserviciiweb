<?php

namespace App\Services\Anaf\Etransport;

/**
 * [2026-10-15] Codurile NC8 scoase din nomenclator, cu cele care le-au luat locul.
 *
 * Furnizorul (Teddy) scrie încă, în listele lui de articole, coduri de dinainte
 * de 2022. ANAF le primește la e-Transport, dar INS le respinge în Intrastat:
 * „Codul NC al poziției este invalid". Așa a căzut poziția 40 din expedierile
 * pe septembrie, cu 6201 92 00.
 *
 * În 2022 pozițiile 6201 și 6202 (paltoane, scurte, hanorace) s-au strâns: în
 * loc de „paltoane" (11–19) și „hanorace" (91–99) despărțite, a rămas câte un
 * cod pe material, iar la bumbac și la fibre sintetice se desparte după
 * greutatea pe bucată — până la 1 kg, ori peste.
 */
class CoduriNcVechi
{
    /** Cod vechi => cod nou, ori [până la 1 kg, peste 1 kg]. */
    protected const INLOCUIRI = [
        // Barbati sau baieti
        '62011100' => '62012000', '62019100' => '62012000',
        '62011210' => '62013010', '62011290' => '62013090', '62019200' => ['62013010', '62013090'],
        '62011310' => '62014010', '62011390' => '62014090', '62019300' => ['62014010', '62014090'],
        '62011900' => '62019000', '62019900' => '62019000',
        // Femei sau fete
        '62021100' => '62022000', '62029100' => '62022000',
        '62021210' => '62023010', '62021290' => '62023090', '62029200' => ['62023010', '62023090'],
        '62021310' => '62024010', '62021390' => '62024090', '62029300' => ['62024010', '62024090'],
        '62021900' => '62029000', '62029900' => '62029000',
    ];

    /**
     * Codul de azi pentru un cod vechi; cel care nu e vechi rămâne cum e.
     *
     * @param float|null $kgPeBucata greutatea netă pe bucată, când codul nou depinde de ea
     */
    public static function actual(string $cn8, ?float $kgPeBucata = null): string
    {
        $nou = self::INLOCUIRI[$cn8] ?? null;

        if ($nou === null) {
            return $cn8;
        }

        if (is_array($nou)) {
            return ($kgPeBucata !== null && $kgPeBucata > 1) ? $nou[1] : $nou[0];
        }

        return $nou;
    }
}
