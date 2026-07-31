<?php

namespace App\Models\Concerns;

use App\Support\ContextCompanie;
use Illuminate\Database\Eloquent\Builder;

/**
 * Izolarea datelor pe client (company).
 *
 * Filtrarea se aplică automat, la nivel de model, nu în fiecare controller:
 * astfel și o interogare scrisă ulterior — inclusiv căutarea după id din rutele
 * cu route model binding — rămâne limitată la clientul curent. O cerere pentru
 * un id al altui client nu găsește nimic și se încheie cu 404.
 */
trait ApartineCompaniei
{
    public static function bootApartineCompaniei(): void
    {
        static::addGlobalScope('companie', function (Builder $query) {
            $companie = ContextCompanie::curenta();

            if ($companie === null) {
                // Fără context (administrator sau rulare din consolă) nu se filtrează.
                return;
            }

            $query->where($query->getModel()->getTable() . '.company_id', $companie);
        });

        static::creating(function ($model) {
            if ($model->company_id === null) {
                $model->company_id = ContextCompanie::curenta();
            }
        });
    }

    /** Interogare peste toți clienții — doar pentru administrare și sarcini programate. */
    public function scopeToateCompaniile(Builder $query): Builder
    {
        return $query->withoutGlobalScope('companie');
    }

    public function scopePentruCompanie(Builder $query, $companieId): Builder
    {
        return $query->withoutGlobalScope('companie')
            ->where($query->getModel()->getTable() . '.company_id', $companieId);
    }
}
