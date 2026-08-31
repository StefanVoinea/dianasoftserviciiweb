<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use App\Models\Concerns\VizibilUtilizatorului;
use Illuminate\Database\Eloquent\Model;

class AnafDeclaratie extends Model
{
    use ApartineCompaniei;
    use VizibilUtilizatorului;

    protected $table = 'anaf_declaratii';

    protected $guarded = [];

    protected $casts = [
        'rectificativa' => 'boolean',
        'semnat' => 'boolean',
        'data_depunere' => 'datetime',
        'data_recipisa' => 'datetime',
        'verificare_la' => 'datetime',
        'potrivire_la' => 'datetime',
    ];

    /**
     * Liniile gasite la verificarea de consistenta nu pleaca odata cu tabelul.
     *
     * La un SAF-T incalcit sunt cu miile; trimise pentru fiecare rand din
     * lista, ar face raspunsul de zeci de MB. Se cer separat, cand se deschide
     * fereastra cu ele (vezi DeclaratiiController@consistenta).
     */
    protected $hidden = ['verificare_erori', 'potrivire_detalii'];

    public function certificat()
    {
        return $this->belongsTo(AnafCertificat::class, 'certificat_id');
    }

    /**
     * Declaratiile depuse care isi asteapta inca recipisa.
     *
     * Asteptarea tine pana cand recipisa e adusa din SPV (pas "finalizat"), nu
     * pana cand se afla starea: StareD112 spune devreme "Documentul este valid",
     * dar documentul recipisei vine abia dupa aceea, prin SPV. Judecata dupa
     * stare scotea declaratia din coada la primul raspuns — si recipisa ei nu
     * se mai descarca niciodata.
     */
    public function scopeAsteaptaRecipisa($query)
    {
        return $query->whereNotNull('index_recipisa')->where('pas', '!=', 'finalizat');
    }
}
