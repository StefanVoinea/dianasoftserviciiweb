<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

/**
 * O gestiune (magazin) a clientului, pentru Dispecer e-Transport.
 *
 * Leagă codul de magazin al furnizorului (NEG*, din distinta D01) de denumirea
 * din contabilitatea clientului și de prescurtarea pusă pe foile formularului
 * pentru transportator.
 */
class EtransportGestiune extends Model
{
    use ApartineCompaniei;

    protected $table = 'etransport_gestiuni';

    protected $guarded = [];

    /** Gestiunile companiei curente, cu codul furnizorului drept cheie. */
    public static function peCodFurnizor()
    {
        return static::get()->keyBy(function (self $gestiune) {
            return mb_strtoupper((string) $gestiune->cod_furnizor);
        });
    }
}
