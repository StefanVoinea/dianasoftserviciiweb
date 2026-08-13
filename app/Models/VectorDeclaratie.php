<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

/**
 * O declaratie asteptata pe un CUI: tipul, periodicitatea si valabilitatea.
 *
 * Randurile cu sursa "dedusa" le scrie aplicatia din vectorul fiscal si din
 * istoricul depunerilor; cele cu sursa "manuala" le scrie omul si au
 * intaietate pe acelasi tip.
 */
class VectorDeclaratie extends Model
{
    use ApartineCompaniei;

    protected $table = 'vector_declaratii';

    protected $guarded = [];

    protected $casts = [
        'data_inceput' => 'date',
        'data_sfarsit' => 'date',
    ];

    public const PERIODICITATI = ['Lunar', 'Trimestrial', 'Semestrial', 'Anual'];

    /** Randurile in vigoare intr-un interval (o luna, de obicei). */
    public function scopeValabileIntre($query, $inceput, $sfarsit)
    {
        return $query
            ->where(function ($q) use ($sfarsit) {
                $q->whereNull('data_inceput')->orWhereDate('data_inceput', '<=', $sfarsit);
            })
            ->where(function ($q) use ($inceput) {
                $q->whereNull('data_sfarsit')->orWhereDate('data_sfarsit', '>=', $inceput);
            });
    }
}
