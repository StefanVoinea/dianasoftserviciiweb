<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Nomenclatorul codurilor vamale (NC, 8 cifre) pentru declarațiile e-Transport.
 *
 * Comun tuturor clienților; se încarcă cu comanda „anaf:coduri-vamale".
 */
class EtransportCodVamal extends Model
{
    protected $table = 'etransport_coduri_vamale';

    public $timestamps = false;

    protected $guarded = [];

    /** Căutare pentru autocomplete: după bucata de cod sau de denumire. */
    public function scopeCauta($query, string $termen)
    {
        $termen = trim($termen);

        if (preg_match('/^\d+$/', $termen)) {
            return $query->where('cod', 'like', $termen . '%');
        }

        return $query->where(function ($q) use ($termen) {
            $q->where('denumire', 'like', '%' . $termen . '%')
                ->orWhere('denumire_scurta', 'like', '%' . $termen . '%');
        });
    }
}
