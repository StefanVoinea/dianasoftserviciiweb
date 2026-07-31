<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Un telefon pe care utilizatorul primeste alerte.
 *
 * Nu poarta scopul pe client (company): alertele sunt personale, iar tokenul
 * apartine dispozitivului, nu societatii. Societatea se retine doar informativ,
 * ca sa se vada de unde a fost inregistrat.
 */
class DispozitivNotificare extends Model
{
    protected $table = 'dispozitive_notificari';

    protected $guarded = [];

    protected $casts = [
        'ultima_folosire' => 'datetime',
    ];

    /** Dupa atatea esecuri consecutive tokenul se considera mort. */
    public const ESECURI_MAXIME = 3;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePentruUtilizatori($query, array $useri)
    {
        return $query->whereIn('user_id', $useri);
    }
}
