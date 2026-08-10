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
 *
 * Documentele fara stapan sunt insa ale firmei, nu ale nimanui.
 *
 * Dosarul urmarit lucreaza singur, fara om in spate: declaratia pusa acolo se
 * inregistreaza cu „user_id" gol, fiindca nu e nimeni caruia sa i se puna in
 * seama. Cerand potrivire pe id, filtrul le ascundea de toata lumea in afara
 * administratorului — iar instiintarea trimisa pe email spunea totusi „detaliile
 * se vad in aplicatie, la Declaratii fiscale". Omul se ducea acolo si nu gasea
 * nimic.
 *
 * Ele se vad deci de oricine lucreaza la firma aceea: dosarul urmarit e o
 * inlesnire a firmei, nu a unei persoane.
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

            $tabel = $query->getModel()->getTable();

            $query->where(function ($intrebare) use ($tabel, $utilizator) {
                $intrebare->where($tabel . '.user_id', $utilizator)
                    ->orWhereNull($tabel . '.user_id');
            });
        });
    }

    /** Interogare peste tot ce are clientul — pentru operatii interne. */
    public function scopeTotiUtilizatorii(Builder $query): Builder
    {
        return $query->withoutGlobalScope('utilizator');
    }
}
