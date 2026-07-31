<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * O notificare trimisa unui utilizator din zona de administrare.
 *
 * Nu are izolare pe client: notificarile sunt personale, iar destinatarul le
 * vede oriunde ar lucra, indiferent de firma selectata in acel moment.
 */
class NotificareAplicatie extends Model
{
    protected $table = 'notificari_aplicatie';

    protected $guarded = [];

    protected $casts = [
        'citita_la' => 'datetime',
        'trimis_email_la' => 'datetime',
        'pe_email' => 'boolean',
        'confirma_citirea' => 'boolean',
        'este_confirmare' => 'boolean',
    ];

    public const IMPORTANTE = ['informare', 'avertizare', 'urgenta'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeNecitite($query)
    {
        return $query->whereNull('citita_la');
    }

    public function scopeAle($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
