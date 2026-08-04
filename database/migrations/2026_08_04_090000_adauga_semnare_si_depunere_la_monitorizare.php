<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ce face dosarul urmarit cu o declaratie valida: o si semneaza? o si depune?
 *
 * Pana acum semna mereu si nu depunea niciodata. Un birou vrea insa uneori doar
 * validarea (semneaza omul, cand isi verifica lucrarea), iar altul vrea tot
 * drumul pana la ANAF, fara nicio atingere. Amandoua devin bife pe certificat:
 *
 *   monitorizare_semneaza — implicit da, purtarea de pana acum;
 *   monitorizare_depune   — implicit nu: depunerea nu se porneste singura,
 *                           fiindca nu se mai poate lua inapoi.
 */
class AdaugaSemnareSiDepunereLaMonitorizare extends Migration
{
    public function up(): void
    {
        // Pe servere coloanele pot exista deja, puse de mana.
        Schema::table('anaf_certificate', function (Blueprint $table) {
            if (!Schema::hasColumn('anaf_certificate', 'monitorizare_semneaza')) {
                $table->boolean('monitorizare_semneaza')->default(true)->after('monitorizare_cadenta');
            }

            if (!Schema::hasColumn('anaf_certificate', 'monitorizare_depune')) {
                $table->boolean('monitorizare_depune')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('anaf_certificate', function (Blueprint $table) {
            foreach (['monitorizare_semneaza', 'monitorizare_depune'] as $coloana) {
                if (Schema::hasColumn('anaf_certificate', $coloana)) {
                    $table->dropColumn($coloana);
                }
            }
        });
    }
}
