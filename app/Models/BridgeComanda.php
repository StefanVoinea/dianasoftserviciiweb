<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * O comandă în drum spre programul local al unui client.
 *
 * Trăiește câteva secunde: se scrie, e luată de program, se completează cu
 * răspunsul și e ștearsă. Fișierele ei stau în storage, nu în tabel.
 */
class BridgeComanda extends Model
{
    protected $table = 'bridge_comenzi';

    protected $guarded = [];

    protected $casts = [
        'antete' => 'array',
        'rezultat_antete' => 'array',
        'luata_la' => 'datetime',
        'terminata_la' => 'datetime',
    ];

    public const DOSAR = 'punte';

    /** Cât timp se mai ține o comandă terminată, ca să nu crească tabelul. */
    public const PASTREAZA_MINUTE = 30;

    public function certificat()
    {
        return $this->belongsTo(AnafCertificat::class, 'certificat_id');
    }

    public function corpul(): ?string
    {
        return $this->corp_fisier && Storage::exists($this->corp_fisier)
            ? Storage::get($this->corp_fisier)
            : null;
    }

    public function rezultatul(): ?string
    {
        return $this->rezultat_fisier && Storage::exists($this->rezultat_fisier)
            ? Storage::get($this->rezultat_fisier)
            : null;
    }

    /** Șterge și fișierele, nu doar rândul: altfel storage-ul crește la nesfârșit. */
    public function curata(): void
    {
        Storage::delete(array_filter([$this->corp_fisier, $this->rezultat_fisier]));

        $this->delete();
    }

    /**
     * Comenzile rămase în urmă — programul local închis la mijlocul unei
     * operații, sau o comandă pe care nimeni n-a mai citit-o.
     */
    public static function curataVechile(): int
    {
        $sterse = 0;

        static::where('updated_at', '<', now()->subMinutes(self::PASTREAZA_MINUTE))
            ->get()
            ->each(function (self $comanda) use (&$sterse) {
                $comanda->curata();
                $sterse++;
            });

        return $sterse;
    }
}
