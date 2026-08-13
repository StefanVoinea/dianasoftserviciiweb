<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

class AnafJurnal extends Model
{
    use ApartineCompaniei;

    protected $table = 'anaf_jurnal';

    protected $guarded = [];

    protected $casts = [
        'context' => 'array',
        'reusit' => 'boolean',
    ];

    /** Etichetele lizibile ale acțiunilor, pentru filtre și afișare. */
    public const ACTIUNI = [
        'entitati_sincronizare' => 'Actualizare listă entități',
        'entitati_solicitare' => 'Solicitare documente entități',
        'entitate_redenumire' => 'Redenumire entitate',
        'mesaje_citire' => 'Citire mesaje SPV',
        'mesaj_descarcare' => 'Descărcare fișier mesaj',
        'mesaj_deschidere' => 'Deschidere fișier mesaj',
        'solicitare_trimitere' => 'Trimitere solicitare SPV',
        'solicitare_preluare' => 'Preluare răspunsuri SPV',
        'solicitare_stergere' => 'Ștergere solicitare',
        'solicitare_deschidere' => 'Deschidere document solicitat',
        'declaratie_incarcare' => 'Încărcare declarație',
        'declaratie_validare' => 'Validare declarație',
        'declaratie_semnare' => 'Semnare declarație',
        'declaratie_depunere' => 'Depunere declarație',
        'declaratie_recipise' => 'Verificare recipise',
        'declaratie_stergere' => 'Ștergere declarație',
        'declaratie_deschidere' => 'Deschidere fișier declarație',
        'certificat_sincronizare' => 'Citire certificat de pe token',
        'certificat_abonare' => 'Înscriere la avertizări',
        'certificat_dezabonare' => 'Retragere de la avertizări',
        'certificat_configurare' => 'Configurare certificat / bridge',
        'kit_descarcare' => 'Descărcare kit instalare',
        'etransport_sincronizare' => 'Preluare notificări e-Transport',
        'etransport_depunere' => 'Depunere declarație e-Transport',
        'etransport_autorizare' => 'Autorizare OAuth2 la ANAF',
        'certificat_utilizator_atasare' => 'Atașare utilizator la certificat',
        'certificat_utilizator_detasare' => 'Eliminare utilizator de la certificat',
        'vector_modificare' => 'Modificare vector fiscal',
        'vector_stergere' => 'Ștergere din vectorul fiscal',
        'vector_raport_lunar' => 'Extragere vector fiscal lunar',
        'vector_import' => 'Import vector din programul vechi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getActiuneEticheteAttribute(): string
    {
        return self::ACTIUNI[$this->actiune] ?? $this->actiune;
    }
}
