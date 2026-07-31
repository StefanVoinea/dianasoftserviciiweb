<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

/**
 * Un dosar sau un nume de parte pe care aplicatia il urmareste in Portal Just.
 */
class PortalJustMonitorizare extends Model
{
    use ApartineCompaniei;

    protected $table = 'portal_just_monitorizari';

    protected $guarded = [];

    protected $casts = [
        'activ' => 'boolean',
        'ultima_verificare' => 'datetime',
        'ultima_modificare' => 'datetime',
    ];

    public const TIP_DOSAR = 'dosar';
    public const TIP_PARTE = 'parte';

    public const TIPURI = [
        self::TIP_DOSAR => 'Număr dosar',
        self::TIP_PARTE => 'Nume parte',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dosare()
    {
        return $this->hasMany(PortalJustDosar::class, 'monitorizare_id');
    }

    public function modificari()
    {
        return $this->hasMany(PortalJustModificare::class, 'monitorizare_id');
    }

    public function scopeActive($query)
    {
        return $query->where('activ', true);
    }

    /** Criteriile trimise serviciului Portal Just pentru aceasta monitorizare. */
    public function criterii(): array
    {
        $criterii = $this->tip === self::TIP_PARTE
            ? ['nume_parte' => $this->valoare]
            : ['numar_dosar' => $this->valoare];

        if ($this->institutie) {
            $criterii['institutie'] = $this->institutie;
        }

        return $criterii;
    }

    public function getTipEticheteAttribute(): string
    {
        return self::TIPURI[$this->tip] ?? $this->tip;
    }
}
