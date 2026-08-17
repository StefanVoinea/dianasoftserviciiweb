<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

/**
 * O declarație e-Transport lucrată în aplicație, pentru obținerea UIT-ului.
 *
 * Trăiește ca ciornă cât timp se completează, apoi se depune la ANAF prin
 * serviciul web. UIT-ul vine la validare, prin verificarea stării.
 */
class EtransportDeclaratie extends Model
{
    use ApartineCompaniei;

    protected $table = 'etransport_declaratii';

    protected $guarded = [];

    protected $casts = [
        'loc_start' => 'array',
        'loc_final' => 'array',
        'documente' => 'array',
        'linii' => 'array',
        'fisiere_importate' => 'array',
        'raspuns_anaf' => 'array',
        'data_transport' => 'date',
        'depusa_la' => 'datetime',
        'curs' => 'float',
    ];

    public const STARI = [
        'ciorna' => 'Ciornă',
        'depusa' => 'Depusă — în prelucrare',
        'validata' => 'Validată — are UIT',
        'respinsa' => 'Respinsă',
    ];

    public function getPoateFiModificataAttribute(): bool
    {
        return in_array($this->stare, ['ciorna', 'respinsa'], true);
    }
}
