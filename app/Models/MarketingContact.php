<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * O firmă căreia i se poate scrie despre aplicațiile noastre.
 *
 * Lista e a noastră, nu a vreunui client, deci nu poartă „company_id" și n-are
 * domeniile obișnuite: o vede numai administratorul aplicației.
 */
class MarketingContact extends Model
{
    protected $table = 'marketing_contacte';

    protected $guarded = [];

    protected $casts = [
        'abonat' => 'boolean',
        'dezabonat_la' => 'datetime',
        'ultima_trimitere_la' => 'datetime',
        'cate_trimiteri' => 'integer',
        'demo_cerut_la' => 'datetime',
    ];

    /**
     * Jetonul se face singur la creare.
     *
     * Din el iese legătura de dezabonare pusă în fiecare scrisoare. Fiind
     * întâmplător și lung, nimeni nu poate dezabona pe altcineva ghicindu-l.
     */
    protected static function booted(): void
    {
        static::creating(function (self $contact) {
            if (empty($contact->jeton)) {
                $contact->jeton = Str::random(48);
            }
        });
    }

    public function trimiteri()
    {
        return $this->hasMany(MarketingTrimitere::class, 'contact_id');
    }

    /** Cine s-a dezabonat nu mai primește nimic, niciodată. */
    public function scopeCaroraLiSePoateScrie($query)
    {
        return $query->where('abonat', true);
    }

    /**
     * Numele cu care i se scrie.
     *
     * Denumirea firmei, curățată de forma juridică: „Abi Accounting SRL" devine
     * „Abi Accounting". Într-o scrisoare, „Bună ziua, Abi Accounting SRL" sună a
     * scrisoare de la robot; fără coadă, sună a om.
     */
    public function getNumeDePotrivitAttribute(): string
    {
        $nume = trim((string) $this->denumire);

        $nume = preg_replace('/\s+(S\.?R\.?L\.?|S\.?A\.?|P\.?F\.?A\.?|I\.?I\.?|S\.?N\.?C\.?|S\.?C\.?S\.?)\s*$/iu', '', $nume);

        return trim($nume) !== '' ? trim($nume) : trim((string) $this->denumire);
    }
}
