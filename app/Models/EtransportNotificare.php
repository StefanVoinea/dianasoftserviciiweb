<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

class EtransportNotificare extends Model
{
    use ApartineCompaniei;

    protected $table = 'etransport_notificari';

    protected $guarded = [];

    protected $casts = [
        'modif_veh' => 'array',
        'confirmare' => 'array',
        'mesaje' => 'array',
        'data_transp' => 'date',
        'data_creare' => 'datetime',
        'data_modif' => 'datetime',
    ];

    /** Tipurile de operațiune din documentația ANAF. */
    public const OPERATIUNI = [
        10 => 'AIC — achiziție intracomunitară',
        12 => 'LHI — livrare intracomunitară (stoc la dispoziția clientului)',
        14 => 'SCI — servicii cu bunuri intracomunitare',
        20 => 'LIC — livrare intracomunitară',
        22 => 'LHE — stoc la dispoziția clientului (export)',
        24 => 'SCE — servicii cu bunuri (export)',
        30 => 'TTN — transport pe teritoriul național',
        40 => 'IMP — import',
        50 => 'EXP — export',
        60 => 'DIN — tranzacție intracomunitară (intrare)',
        70 => 'DIE — tranzacție intracomunitară (ieșire)',
    ];

    public const TIPURI = [
        'NOT' => 'Notificare',
        'COR' => 'Corecție',
        'DEL' => 'Ștergere',
        'CON' => 'Confirmare',
        'MVH' => 'Modificare vehicul',
    ];

    public function certificat()
    {
        return $this->belongsTo(AnafCertificat::class, 'certificat_id');
    }

    public function getOperatiuneAttribute(): ?string
    {
        return self::OPERATIUNI[$this->tip_op] ?? ($this->tip_op ? (string) $this->tip_op : null);
    }

    public function getAreEroriAttribute(): bool
    {
        return $this->stare === 'ERR';
    }

    /** Notificările cu erori, care cer intervenția utilizatorului. */
    public function scopeCuErori($query)
    {
        return $query->where('stare', 'ERR');
    }
}
