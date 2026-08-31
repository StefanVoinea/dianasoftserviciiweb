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
        'scos_din_uz' => 'boolean',
        'date_identificare_la' => 'datetime',
        'vector_la' => 'datetime',
        'sincronizat_la' => 'datetime',

        // Datele din antetul declaratiilor (vezi AntetD300)
        'prin_reprezentant' => 'boolean',
        'd300_bifa_interne' => 'boolean',
        'd300_bifa_cereale' => 'boolean',
        'd300_bifa_mob' => 'boolean',
        'd300_bifa_disp' => 'boolean',
        'd300_bifa_cons' => 'boolean',
        'd300_solicit_ramb' => 'boolean',
    ];

    /** Denumirea din vectorul fiscal are prioritate fata de cea din date identificare. */
    /**
     * Cat de multa incredere are fiecare izvor al denumirii.
     *
     * Datele de identificare o au scrisa pe eticheta ei — un rand cu „Denumire"
     * si numele intreg alaturi. In vectorul fiscal, numele sta in antet si vine
     * rupt in bucati de extractorul de PDF; de acolo s-a si inregistrat o firma
     * cu numele „SRL". De aceea documentul de identificare trece inaintea lui:
     * el poate indrepta un nume gresit, nu doar sa completeze unul lipsa.
     *
     * Ce a scris omul ramane deasupra tuturor.
     */
    public const PRIORITATE_SURSE = ['manual' => 3, 'date_identificare' => 2, 'vector' => 1];

    public function certificat()
    {
        return $this->belongsTo(AnafCertificat::class, 'certificat_id');
    }

    public function scopeActive($query)
    {
        return $query->where('activ', true);
    }

    /**
     * Entitatile cu care se lucreaza cu adevarat.
     *
     * Doua lucruri trebuie sa fie amandoua bune: ANAF sa dea drepturi pe ea
     * („activ", pus de sincronizare) si omul s-o vrea („scos_din_uz", pus de el).
     * Un client are adesea in certificat firme pe care nu le mai tine — ele
     * incarcau degeaba fiecare interogare si fiecare lista.
     */
    public function scopeInLucru($query)
    {
        return $query->where('activ', true)->where('scos_din_uz', false);
    }

    /**
     * E luata in seama? Aceeasi judecata, pentru un singur rand.
     *
     * Se numeste altfel decat filtrul dinadins: cu acelasi nume, „AnafSocietate::inLucru()"
     * ar fi chemat metoda asta ca statica, in loc sa ajunga la filtru.
     */
    public function esteInLucru(): bool
    {
        return (bool) $this->activ && !$this->scos_din_uz;
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
