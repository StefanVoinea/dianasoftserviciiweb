<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

/**
 * Utilizator din retea care foloseste un certificat digital.
 * Un certificat poate fi folosit de mai multe persoane; legatura se face dupa
 * adresa de email, chiar daca aceasta nu are inca un cont in aplicatie.
 */
class CertificatUtilizator extends Model
{
    use ApartineCompaniei;

    protected $table = 'certificat_utilizatori';

    protected $guarded = [];

    protected $casts = [
        'activ' => 'boolean',
    ];

    public function certificat()
    {
        return $this->belongsTo(AnafCertificat::class, 'certificat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Contul din aplicatie cu aceasta adresa, daca exista. */
    public static function contDupaEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
