<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ce s-a trimis, cui și când.
 *
 * Fără urma asta n-am putea nici să nu trimitem de două ori același lucru, nici
 * să răspundem cuiva care întreabă de ce a primit o scrisoare de la noi.
 */
class MarketingTrimitere extends Model
{
    protected $table = 'marketing_trimiteri';

    protected $guarded = [];

    protected $casts = [
        'reusit' => 'boolean',
    ];

    public function contact()
    {
        return $this->belongsTo(MarketingContact::class, 'contact_id');
    }
}
