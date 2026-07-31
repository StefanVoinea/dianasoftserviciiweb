<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

/**
 * Starea cunoscuta a unui dosar urmarit: fata de ea se compara ce vine de la
 * Portal Just la fiecare verificare.
 */
class PortalJustDosar extends Model
{
    use ApartineCompaniei;

    protected $table = 'portal_just_dosare';

    protected $guarded = [];

    protected $casts = [
        'stare' => 'array',
        'vazut_la' => 'datetime',
    ];

    public function monitorizare()
    {
        return $this->belongsTo(PortalJustMonitorizare::class, 'monitorizare_id');
    }
}
