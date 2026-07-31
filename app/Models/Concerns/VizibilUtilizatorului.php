<?php

namespace App\Models\Concerns;

use App\Support\ContextUtilizator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Un utilizator obisnuit vede doar ce a lucrat el.
 *
 * Filtrarea se pune pe model, nu in fiecare controller, ca si izolarea pe
 * client: asa ramane valabila si pentru interogarile scrise mai tarziu,
 * inclusiv cautarea dupa id din rute — o cerere pentru documentul altcuiva nu
 * gaseste nimic si se incheie cu 404.
 *
 * Administratorul firmei si administratorul serviciului vad tot.
 */
trait VizibilUtilizatorului
{
    public static function bootVizibilUtilizatorului(): void
    {
        static::addGlobalScope('utilizator', function (Builder $query) {
            $utilizator = ContextUtilizator::limitatLa();

            if ($utilizator === null) {
                return;
            }

            $query->where($query->getModel()->getTable() . '.user_id', $utilizator);
        });
    }

    /** Interogare peste tot ce are clientul — pentru operatii interne. */
    public function scopeTotiUtilizatorii(Builder $query): Builder
    {
        return $query->withoutGlobalScope('utilizator');
    }
}
