<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

class CertificatAbonat extends Model
{
    use ApartineCompaniei;

    protected $table = 'certificat_abonati';

    protected $guarded = [];

    protected $casts = [
        'activ' => 'boolean',
    ];

    public function certificat()
    {
        return $this->belongsTo(AnafCertificat::class, 'certificat_id');
    }

    /**
     * Adresele care primesc avertizarea pentru un certificat: cele abonate
     * explicit la el, plus cele abonate global (fara certificat_id).
     */
    public static function pentruCertificat(?int $certificatId): array
    {
        return static::where('activ', true)
            ->where(function ($query) use ($certificatId) {
                $query->whereNull('certificat_id');

                if ($certificatId !== null) {
                    $query->orWhere('certificat_id', $certificatId);
                }
            })
            ->pluck('email')
            ->unique()
            ->values()
            ->all();
    }
}
