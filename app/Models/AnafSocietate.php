<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

class AnafSocietate extends Model
{
    use ApartineCompaniei;

    protected $table = 'anaf_societati';

    protected $guarded = [];

    protected $casts = [
        'activ' => 'boolean',
        'date_identificare_la' => 'datetime',
        'vector_la' => 'datetime',
        'sincronizat_la' => 'datetime',
    ];

    /** Denumirea din vectorul fiscal are prioritate fata de cea din date identificare. */
    public const PRIORITATE_SURSE = ['manual' => 3, 'vector' => 2, 'date_identificare' => 1];

    public function certificat()
    {
        return $this->belongsTo(AnafCertificat::class, 'certificat_id');
    }

    public function scopeActive($query)
    {
        return $query->where('activ', true);
    }

    /** Un CNP are 13 cifre; restul sunt persoane juridice. */
    public static function tipDupaCif(string $cif): string
    {
        return preg_match('/^\d{13}$/', $cif) ? 'pf' : 'pj';
    }

    /** Actualizeaza denumirea doar daca noua sursa e cel putin la fel de sigura. */
    public function seteazaDenumire(?string $denumire, string $sursa): bool
    {
        if ($denumire === null || trim($denumire) === '') {
            return false;
        }

        $prioritateNoua = self::PRIORITATE_SURSE[$sursa] ?? 0;
        $prioritateVeche = self::PRIORITATE_SURSE[$this->denumire_sursa] ?? 0;

        if ($this->denumire && $prioritateNoua < $prioritateVeche) {
            return false;
        }

        $this->update(['denumire' => trim($denumire), 'denumire_sursa' => $sursa]);

        return true;
    }
}
