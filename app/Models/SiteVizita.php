<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * O vizită pe pagina de prezentare spvcurier.ro: de la ce adresă, ce pagini a
 * deschis și cât a stat.
 */
class SiteVizita extends Model
{
    protected $table = 'site_vizite';

    protected $guarded = [];

    protected $casts = [
        'pagini' => 'array',
        'durata_secunde' => 'integer',
    ];

    /** Firma din lista de marketing, când vizita a pornit din scrisoarea noastră. */
    public function contact()
    {
        return $this->belongsTo(MarketingContact::class, 'contact_id');
    }
}
