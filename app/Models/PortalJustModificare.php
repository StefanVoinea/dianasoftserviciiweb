<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

/**
 * O modificare sesizata la un dosar urmarit. Ramane in istoric si dupa
 * trimiterea emailului, ca utilizatorul sa poata reciti ce s-a schimbat.
 */
class PortalJustModificare extends Model
{
    use ApartineCompaniei;

    protected $table = 'portal_just_modificari';

    protected $guarded = [];

    protected $casts = [
        'detalii' => 'array',
        'notificat_la' => 'datetime',
        'push_la' => 'datetime',
    ];

    public const TIPURI = [
        'dosar_nou' => 'Dosar nou',
        'termen_nou' => 'Termen nou',
        'solutie' => 'Soluție',
        'stadiu' => 'Stadiu procesual',
        'parte' => 'Parte',
        'cale_atac' => 'Cale de atac',
        'obiect' => 'Obiectul dosarului',
        'actualizare' => 'Actualizare',
    ];

    public function monitorizare()
    {
        return $this->belongsTo(PortalJustMonitorizare::class, 'monitorizare_id');
    }

    public function scopeNenotificate($query)
    {
        return $query->whereNull('notificat_la');
    }

    /** Modificarile pentru care nu s-a trimis inca alerta pe telefon. */
    public function scopeFaraPush($query)
    {
        return $query->whereNull('push_la');
    }

    public function getTipEticheteAttribute(): string
    {
        return self::TIPURI[$this->tip] ?? $this->tip;
    }
}
