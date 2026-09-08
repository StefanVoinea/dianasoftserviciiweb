<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

/**
 * O declarație e-Transport lucrată în aplicație, pentru obținerea UIT-ului.
 *
 * Trăiește ca ciornă cât timp se completează, apoi se depune la ANAF prin
 * serviciul web. UIT-ul vine la validare, prin verificarea stării.
 */
class EtransportDeclaratie extends Model
{
    use ApartineCompaniei;

    protected $table = 'etransport_declaratii';

    protected $guarded = [];

    protected $casts = [
        'loc_start' => 'array',
        'loc_final' => 'array',
        'documente' => 'array',
        'linii' => 'array',
        'fisiere_importate' => 'array',
        'raspuns_anaf' => 'array',
        'data_transport' => 'date',
        'depusa_la' => 'datetime',
        'curs' => 'float',
    ];

    public const STARI = [
        'ciorna' => 'Ciornă',
        'depusa' => 'Depusă — în prelucrare',
        'validata' => 'Validată — are UIT',
        'respinsa' => 'Respinsă',
    ];

    public function getPoateFiModificataAttribute(): bool
    {
        return in_array($this->stare, ['ciorna', 'respinsa'], true);
    }

    /**
     * Tipurile de operațiune la care marfa PLEACĂ din țară: livrare
     * intracomunitară (retururile), lohn și stocuri la ieșire, export, ieșire
     * după depozitare. La ele magazinul stă la plecare, nu la sosire.
     */
    public const OPERATIUNI_DE_IESIRE = [20, 22, 24, 50, 70];

    /** Care din cele două locuri ale traseului poartă magazinul clientului. */
    public function getCampulMagazinuluiAttribute(): string
    {
        return in_array((int) $this->tip_operatiune, self::OPERATIUNI_DE_IESIRE, true) ? 'loc_start' : 'loc_final';
    }

    /** Locul cu magazinul: destinația la livrări, plecarea la retururi. */
    public function getLocMagazinAttribute(): array
    {
        return (array) $this->{$this->campul_magazinului};
    }
}
