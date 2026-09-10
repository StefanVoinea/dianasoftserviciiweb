<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

/**
 * O declarație e-Transport lucrată în aplicație, pentru obținerea UIT-ului.
 *
 * Trăiește ca ciornă cât timp se completează, apoi se depune la ANAF prin
 * serviciul web.
 *
 * [2026-09-10] Codul UIT vine încă de la încărcare, dar el NU înseamnă că
 * declarația a fost primită. ANAF o scrie chiar în răspunsul de atunci: „Codul
 * UIT este valabil din momentul in care apare ca valid dupa apelul de stare".
 * Prelucrarea se face după aceea și poate respinge declarația. De aceea starea
 * rămâne „depusă" până când ANAF spune `ok`, și trece pe „respinsă" cu motivul
 * scris, când spune `nok`.
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

    /**
     * Notificarea ANAF a acestei depuneri, dacă a fost preluată.
     *
     * Se leagă prin indexul de încărcare: fiecare depunere (inclusiv o corecție)
     * are indexul ei, deci perechea e sigură.
     */
    public function notificare()
    {
        return $this->hasOne(EtransportNotificare::class, 'id_incarcare', 'index_incarcare');
    }

    /** Declarațiile care așteaptă verdictul ANAF. */
    public function scopeInPrelucrare($query)
    {
        return $query->where('stare', 'depusa')->whereNotNull('index_incarcare');
    }

    /**
     * Motivele pentru care ANAF a respins declarația, cum le-a scris el.
     *
     * Vin pe două căi, care spun același lucru: interogarea de stare
     * (`Errors[].errorMessage`) și notificarea preluată în fila Notificări
     * (`mesaje[].mesaj`, la cele cu starea ERR).
     *
     * @return array<int, string>
     */
    public function getEroriAnafAttribute(): array
    {
        $raspuns = (array) $this->raspuns_anaf;
        $brute = $raspuns['Errors'] ?? ($raspuns['errors'] ?? []);

        $erori = [];

        foreach ((array) $brute as $eroare) {
            $text = is_array($eroare)
                ? ($eroare['errorMessage'] ?? ($eroare['mesaj'] ?? null))
                : (string) $eroare;

            if ($text) {
                $erori[] = trim($text);
            }
        }

        $notificare = $this->relationLoaded('notificare') ? $this->getRelation('notificare') : $this->notificare;

        if ($notificare && $notificare->stare === 'ERR') {
            foreach ((array) $notificare->mesaje as $mesaj) {
                $text = is_array($mesaj) ? ($mesaj['mesaj'] ?? null) : (string) $mesaj;

                if ($text) {
                    $erori[] = trim($text);
                }
            }
        }

        return array_values(array_unique(array_filter($erori)));
    }
}
