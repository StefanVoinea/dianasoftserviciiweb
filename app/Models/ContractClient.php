<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Datele din care iese contractul de abonament al unui client.
 *
 * Aplicația știe despre client doar denumirea și CUI-ul; contractul mai cere
 * adresa, numărul de la Registrul Comerțului, contul bancar și cine semnează.
 * Se scriu o dată aici și de aici iese documentul, de câte ori e nevoie.
 */
class ContractClient extends Model
{
    protected $table = 'contracte_clienti';

    protected $guarded = [];

    protected $casts = [
        'data' => 'date',
        'data_activare' => 'date',
        'data_facturare' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /** Contractul clientului, dacă i s-a făcut unul. */
    public static function alClientului($companieId): ?self
    {
        return $companieId ? self::where('company_id', $companieId)->first() : null;
    }

    /**
     * Ce lipsește ca documentul să nu iasă cu goluri.
     *
     * Contractul se poate scoate și neîntreg — uneori tocmai ca să se vadă ce
     * mai trebuie cerut de la client —, dar omul trebuie să știe dinainte ce
     * rămâne necompletat.
     *
     * @return array<int, string>
     */
    public function ceLipseste(): array
    {
        $cerute = [
            'numar' => 'numărul contractului',
            'data' => 'data contractului',
            'beneficiar_denumire' => 'denumirea beneficiarului',
            'beneficiar_adresa' => 'adresa beneficiarului',
            'beneficiar_reg_com' => 'numărul de la Registrul Comerțului',
            'beneficiar_cui' => 'CUI-ul beneficiarului',
            'beneficiar_reprezentant' => 'reprezentantul beneficiarului',
            'beneficiar_functie' => 'funcția reprezentantului',
            'plan' => 'planul ales',
            'periodicitate' => 'periodicitatea facturării',
        ];

        $lipsa = [];

        foreach ($cerute as $camp => $nume) {
            if (trim((string) $this->$camp) === '') {
                $lipsa[] = $nume;
            }
        }

        return $lipsa;
    }

    /** Numele fișierului, ca să se poată găsi între altele. */
    public function numeFisier(): string
    {
        $bucati = array_filter([
            'Contract SPV Curier',
            $this->numar ? 'nr ' . $this->numar : null,
            $this->beneficiar_denumire ?: optional($this->client)->denumire,
        ]);

        $nume = implode(' - ', $bucati);

        // Semnele care supara sistemele de fisiere ies afara.
        return preg_replace('/[\\\\\\/:*?"<>|]+/u', ' ', $nume) . '.pdf';
    }
}
