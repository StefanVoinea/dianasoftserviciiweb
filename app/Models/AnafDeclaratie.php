<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use App\Models\Concerns\VizibilUtilizatorului;
use Illuminate\Database\Eloquent\Model;

class AnafDeclaratie extends Model
{
    use ApartineCompaniei;
    use VizibilUtilizatorului;

    protected $table = 'anaf_declaratii';

    protected $guarded = [];

    protected $casts = [
        'rectificativa' => 'boolean',
        'semnat' => 'boolean',
        'data_depunere' => 'datetime',
        'data_recipisa' => 'datetime',
    ];

    public function certificat()
    {
        return $this->belongsTo(AnafCertificat::class, 'certificat_id');
    }

    /**
     * Declaratiile depuse care asteapta recipisa (sau au primit una intermediara).
     */
    public function scopeAsteaptaRecipisa($query)
    {
        return $query->whereNotNull('index_recipisa')
            ->where(function ($q) {
                $q->whereNull('stare_declaratie')
                    ->orWhere('stare_declaratie', '')
                    ->orWhere('stare_declaratie', 'like', 'In prelucrare%');
            });
    }
}
