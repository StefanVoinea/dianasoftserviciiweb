<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Cine pe cine a adus, și dacă luna gratuită a fost dată fiecăruia.
 *
 * Oferta: clientul care recomandă primește o lună gratuită, și tot o lună
 * primește și cel recomandat. Luna se dă o singură dată fiecăruia, iar data
 * acordării rămâne scrisă — cât timp e goală, luna n-a fost dată încă.
 */
class RecomandareClient extends Model
{
    protected $table = 'recomandari_clienti';

    protected $guarded = [];

    protected $casts = [
        'acordata_recomandantului_la' => 'date',
        'acordata_recomandatului_la' => 'date',
    ];

    /** Clientul adus. */
    public function client()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /** Clientul care l-a adus. */
    public function recomandant()
    {
        return $this->belongsTo(Company::class, 'recomandat_de_id');
    }

    /** A primit cel care a recomandat luna lui? */
    public function recomandantulEPlatit(): bool
    {
        return $this->acordata_recomandantului_la !== null;
    }

    /** A primit cel recomandat luna lui? */
    public function recomandatulEPlatit(): bool
    {
        return $this->acordata_recomandatului_la !== null;
    }

    /** Mai are cineva de primit ceva pe recomandarea asta? */
    public function maiEDeDat(): bool
    {
        return !$this->recomandantulEPlatit() || !$this->recomandatulEPlatit();
    }

    /** Recomandările făcute de un client, cu tot cu cei aduși. */
    public static function aleRecomandantului(int $companyId)
    {
        return static::with('client')->where('recomandat_de_id', $companyId)->get();
    }
}
