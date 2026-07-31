<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Abonamentul unui client: modulele la care are acces, tariful lunar si pana
 * cand are voie sa lucreze.
 *
 * Accesul tine de doua date: „platit_pana_la" si sfarsitul perioadei de proba.
 * Cat timp una dintre ele e in viitor, clientul lucreaza; dupa ce trec amandoua,
 * modulele se inchid, dar datele raman — nu se sterge nimic.
 */
class AbonamentClient extends Model
{
    protected $table = 'abonamente_clienti';

    protected $guarded = [];

    protected $casts = [
        'tarif_lunar' => 'float',
        'proba_zile' => 'integer',
        'proba_pana_la' => 'date',
        'platit_pana_la' => 'date',
        'blocat' => 'boolean',
        'modul_spv' => 'boolean',
        'modul_etransport' => 'boolean',
        'modul_portal_just' => 'boolean',
    ];

    /** Numele scurt al modulului si coloana care spune daca e permis. */
    public const MODULE = [
        'spv' => 'modul_spv',
        'etransport' => 'modul_etransport',
        'portal_just' => 'modul_portal_just',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function areModul(string $modul): bool
    {
        $coloana = self::MODULE[$modul] ?? null;

        return $coloana !== null && (bool) $this->$coloana;
    }

    /** In proba cat timp data de sfarsit nu a trecut. */
    public function inProba(): bool
    {
        return $this->proba_pana_la !== null && $this->proba_pana_la->endOfDay()->isFuture();
    }

    public function plataLaZi(): bool
    {
        return $this->platit_pana_la !== null && $this->platit_pana_la->endOfDay()->isFuture();
    }

    public function activ(): bool
    {
        return !$this->blocat && ($this->plataLaZi() || $this->inProba());
    }

    /** Cate zile mai are de lucrat, dupa data care tine cel mai mult. */
    public function zileRamase(): ?int
    {
        $date = array_filter([$this->platit_pana_la, $this->proba_pana_la]);

        if ($date === []) {
            return null;
        }

        $ultima = max($date);

        return (int) now()->startOfDay()->diffInDays($ultima->endOfDay(), false);
    }

    /** De ce nu are acces, spus pe intelesul celui care primeste raspunsul. */
    public function motiv(): ?string
    {
        if ($this->activ()) {
            return null;
        }

        if ($this->blocat) {
            return $this->motiv_blocare ?: 'Accesul a fost oprit. Luați legătura cu furnizorul aplicației.';
        }

        if ($this->proba_pana_la !== null && !$this->plataLaZi()) {
            return 'Perioada de probă s-a încheiat la ' . $this->proba_pana_la->format('d.m.Y')
                . '. Pentru a continua, abonamentul trebuie achitat.';
        }

        return 'Abonamentul a expirat'
            . ($this->platit_pana_la ? ' la ' . $this->platit_pana_la->format('d.m.Y') : '') . '.';
    }

    /** Abonamentul clientului, daca i s-a facut unul. */
    public static function alClientului($companieId): ?self
    {
        return $companieId ? self::where('company_id', $companieId)->first() : null;
    }
}
