<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Starea fiecarei scrisori: pusa in coada, plecata, sau cazuta.
 *
 * „reusit" se scria cand scrisoarea intra in coada, nu cand pleca de pe server.
 * Pana la trimiterea adevarata mai e drum: lucratorul cozii trebuie sa fie
 * pornit, iar furnizorul de email trebuie s-o primeasca. Cand vreunul dintre
 * ele lipsea, fila spunea „trimise" si nu ajungea nimic nicaieri.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('marketing_trimiteri', 'stare')) {
            return;
        }

        Schema::table('marketing_trimiteri', function (Blueprint $tabel) {
            $tabel->string('stare', 20)->default('in_coada')->after('reusit')->index();
            $tabel->timestamp('plecat_la')->nullable()->after('stare');
        });

        /*
         * Randurile scrise inainte: nu se poate sti daca au plecat cu adevarat,
         * dar se stie ca au fost puse in coada. Se lasa asa, nu se pretinde mai
         * mult decat se stie.
         */
        DB::table('marketing_trimiteri')->where('reusit', false)->update(['stare' => 'cazut']);
    }

    public function down(): void
    {
        Schema::table('marketing_trimiteri', function (Blueprint $tabel) {
            $tabel->dropColumn(['stare', 'plecat_la']);
        });
    }
};
