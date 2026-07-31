<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use App\Models\Concerns\VizibilUtilizatorului;
use Illuminate\Database\Eloquent\Model;

class SpvSolicitare extends Model
{
    use ApartineCompaniei;
    use VizibilUtilizatorului;

    protected $table = 'spv_solicitari';

    protected $guarded = [];

    protected $casts = [
        'data_solicitarii' => 'datetime',
        'data_afisare' => 'datetime',
    ];

    /**
     * Solicitarile pentru care raspunsul nu a fost inca preluat din SPV.
     */
    public function certificat()
    {
        return $this->belongsTo(AnafCertificat::class, 'certificat_id');
    }

    public function scopeInAsteptare($query)
    {
        return $query->whereNull('data_afisare');
    }
}
