<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Randurile vechi ale programului local capata si ele un nume.
 *
 * Pana acum, ce facea programul de pe calculatorul clientului — aducea fisiere
 * din dosarul urmarit, isi cerea licenta — se scria in jurnal fara nimeni in
 * dreptul lui, si se citea „necunoscut", desi se stia foarte bine cine a lucrat.
 *
 * Se indreapta numai randurile a caror actiune vine doar de la el si care n-au
 * niciun nume scris: unde a lucrat un om, numele lui e deja acolo si nu se
 * atinge. Nu se schimba nicio fapta, doar cine a facut-o.
 */
class JurnalulStieCaALucratBridgeUl extends Migration
{
    /** Actiunile scrise numai de partea care lucreaza prin programul local. */
    protected const ALE_BRIDGE_ULUI = ['monitorizare_folder', 'licenta_bridge'];

    public function up(): void
    {
        if (!Schema::hasTable('anaf_jurnal')) {
            return;
        }

        DB::table('anaf_jurnal')
            ->whereIn('actiune', self::ALE_BRIDGE_ULUI)
            ->whereNull('user_nume')
            ->update(['user_nume' => \App\Services\Anaf\Jurnal::BRIDGE]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('anaf_jurnal')) {
            return;
        }

        DB::table('anaf_jurnal')
            ->whereIn('actiune', self::ALE_BRIDGE_ULUI)
            ->where('user_nume', \App\Services\Anaf\Jurnal::BRIDGE)
            ->update(['user_nume' => null]);
    }
}
