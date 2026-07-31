<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use App\Support\ContextUtilizator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SpvMesaj extends Model
{
    use ApartineCompaniei;

    protected $table = 'spv_mesaje';

    protected $guarded = [];

    /**
     * Mesajele nu sunt „ale" cuiva: ele vin de la ANAF pentru un certificat.
     * Un utilizator obisnuit le vede deci pe cele ale certificatelor la care i
     * s-a dat acces, nu pe cele pe care le-a adus chiar el.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('certificat_permis', function (Builder $query) {
            if (ContextUtilizator::limitatLa() === null) {
                return;
            }

            $permise = ContextUtilizator::certificateAccesibile();

            if ($permise === []) {
                // Fara niciun certificat atribuit nu are ce vedea.
                $query->whereRaw('1 = 0');

                return;
            }

            $query->whereIn($query->getModel()->getTable() . '.certificat_id', $permise);
        });
    }

    /** Toate mesajele clientului — pentru operatii interne (salvare, potrivire). */
    public function scopeToateCertificatele(Builder $query): Builder
    {
        return $query->withoutGlobalScope('certificat_permis');
    }

    public function certificat()
    {
        return $this->belongsTo(AnafCertificat::class, 'certificat_id');
    }
}
