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

    /** Constatarile pe care le poate astepta o alerta. */
    public const CAND_VECTOR_MODIFICAT = 'vector_modificat';
    public const CAND_RESTANTE = 'restante';

    public const CONSTATARI = [
        self::CAND_VECTOR_MODIFICAT => 'când vectorul fiscal s-a modificat',
        self::CAND_RESTANTE => 'când situația sintetică arată restanțe',
    ];

    public function scopeActive($query)
    {
        return $query->where('activ', true);
    }

    /** Alertele care asteapta sosirea unei hartii, nu o constatare anume. */
    public function scopeLaSosire($query)
    {
        return $query->whereNull('doar_cand');
    }

    /** Alertele care asteapta o anume constatare. */
    public function scopeLaConstatare($query, string $ce)
    {
        return $query->where('doar_cand', $ce);
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

        return $this->firmaSePotriveste($mesaj->cif, $cifuriInrolate);
    }

    /**
     * Se potriveste alerta cu o constatare facuta la o firma?
     *
     * Aici nu se cantareste niciun tip de document: constatarea vine din
     * talcuirea lui, iar felul hartiei e dinainte stiut — un vector modificat nu
     * poate iesi decat dintr-un vector fiscal.
     *
     * @param array<int, string> $cifuriInrolate codurile fiscale inrolate certificatului alertei
     */
    public function seAplicaLaConstatare(?string $cif, ?int $certificatId, array $cifuriInrolate): bool
    {
        if (!$this->activ) {
            return false;
        }

        if ($this->certificat_id && (int) $certificatId !== (int) $this->certificat_id) {
            return false;
        }

        return $this->firmaSePotriveste($cif, $cifuriInrolate);
    }

    /**
     * Firma se verifica in doua trepte: daca alerta e legata de una anume, doar
     * ea conteaza; altfel se cere doar sa fie inrolata certificatului alertei.
     *
     * @param array<int, string> $cifuriInrolate
     */
    protected function firmaSePotriveste(?string $cif, array $cifuriInrolate): bool
    {
        if ($this->cif) {
            return trim((string) $cif) === trim($this->cif);
        }

        // Fara firma aleasa: doar firmele inrolate certificatului alertei.
        if ($this->certificat_id && $cifuriInrolate !== []) {
            return in_array(trim((string) $cif), $cifuriInrolate, true);
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
