<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

class AnafCertificat extends Model
{
    use ApartineCompaniei;

    protected $table = 'anaf_certificate';

    protected $guarded = [];

    protected $casts = [
        'activ' => 'boolean',
        'valabil_de_la' => 'datetime',
        'valabil_pana_la' => 'datetime',
        'ultima_utilizare' => 'datetime',
        'avertizat_la' => 'datetime',
        'monitorizare_la' => 'datetime',
        'licenta_pana_la' => 'datetime',
        // Ultima dată când agentul de la client a întrebat dacă are ceva de lucru
        'agent_vazut_la' => 'datetime',
    ];

    public function abonati()
    {
        return $this->hasMany(CertificatAbonat::class, 'certificat_id');
    }

    public function societati()
    {
        return $this->hasMany(AnafSocietate::class, 'certificat_id');
    }

    public function utilizatori()
    {
        return $this->hasMany(CertificatUtilizator::class, 'certificat_id');
    }

    /** Zile ramase pana la expirare (negativ daca a expirat deja). */
    public function getZileRamaseAttribute(): ?int
    {
        return $this->valabil_pana_la ? now()->startOfDay()->diffInDays($this->valabil_pana_la->startOfDay(), false) : null;
    }

    public function getExpiratAttribute(): bool
    {
        return $this->zile_ramase !== null && $this->zile_ramase < 0;
    }

    /** Certificatele care expira in intervalul de avertizare si nu au fost anuntate. */
    public function scopeDeAvertizat($query, int $zile)
    {
        return $query->where('activ', true)
            ->whereNotNull('valabil_pana_la')
            ->whereBetween('valabil_pana_la', [now(), now()->addDays($zile)]);
    }

    /** Cadentele din care se poate alege pentru dosarul urmarit, in minute. */
    public const CADENTE_MONITORIZARE = [1, 3, 5, 10, 15, 30, 60];

    /**
     * A venit vremea sa fie verificat dosarul urmarit al acestui certificat?
     *
     * Planificatorul bate din minut in minut, dar fiecare certificat isi are
     * cadenta lui. Se lasa o jumatate de minut ingaduinta: bataia
     * planificatorului nu cade la aceeasi secunda de fiecare data, iar fara ea
     * o cadenta de 5 minute ar sari cate o bataie si ar ajunge una de 10.
     */
    public function monitorizareaEsteScadenta(): bool
    {
        if (!$this->monitorizare_activa || !$this->monitorizare_cale) {
            return false;
        }

        if (!$this->monitorizare_la) {
            return true;
        }

        $cadenta = (int) ($this->monitorizare_cadenta ?: 5);

        return $this->monitorizare_la->addMinutes($cadenta)->subSeconds(30)->lte(now());
    }
}
