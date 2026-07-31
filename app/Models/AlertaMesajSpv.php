<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

/**
 * Instiintarea pe email cand intra in SPV un anumit fel de document.
 *
 * Campurile goale inseamna „oricare": fara certificat, alerta prinde mesajele
 * oricarui certificat al clientului; fara CIF, pe ale oricarei firme inrolate
 * certificatului ales; fara tip, orice document.
 */
class AlertaMesajSpv extends Model
{
    use ApartineCompaniei;

    protected $table = 'alerte_mesaje_spv';

    protected $guarded = [];

    protected $casts = [
        'activ' => 'boolean',
        'ultima_alerta_la' => 'datetime',
    ];

    public function certificat()
    {
        return $this->belongsTo(AnafCertificat::class, 'certificat_id');
    }

    public function scopeActive($query)
    {
        return $query->where('activ', true);
    }

    /**
     * Se potriveste alerta cu mesajul tocmai intrat?
     *
     * Firma se verifica in doua trepte: daca alerta e legata de o firma anume,
     * doar ea conteaza; altfel se cere doar ca firma sa fie inrolata
     * certificatului alertei — nu orice firma din SPV.
     *
     * @param array<int, string> $cifuriInrolate codurile fiscale inrolate certificatului alertei
     */
    public function seAplica(SpvMesaj $mesaj, array $cifuriInrolate): bool
    {
        if (!$this->activ) {
            return false;
        }

        if ($this->certificat_id && (int) $mesaj->certificat_id !== (int) $this->certificat_id) {
            return false;
        }

        if ($this->tip_document && !$this->tipulPotrivit($mesaj->tip)) {
            return false;
        }

        if ($this->cif) {
            return trim((string) $mesaj->cif) === trim($this->cif);
        }

        // Fara firma aleasa: doar firmele inrolate certificatului alertei.
        if ($this->certificat_id && $cifuriInrolate !== []) {
            return in_array(trim((string) $mesaj->cif), $cifuriInrolate, true);
        }

        return true;
    }

    /**
     * ANAF nu scrie tipurile mereu la fel („RECIPISA" / „Recipisa"), asa ca
     * potrivirea nu tine cont de litere mari sau mici si accepta si o bucata
     * din denumire.
     */
    protected function tipulPotrivit(?string $tip): bool
    {
        $tip = mb_strtolower(trim((string) $tip));
        $cautat = mb_strtolower(trim($this->tip_document));

        return $tip !== '' && mb_strpos($tip, $cautat) !== false;
    }
}
