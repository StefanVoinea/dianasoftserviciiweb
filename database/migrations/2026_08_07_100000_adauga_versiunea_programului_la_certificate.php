<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ce versiune a programului rulează pe calculatorul clientului.
 *
 * Agentul o spune la fiecare pândă, iar aici rămâne scrisă: așa se vede în
 * aplicație cine a rămas în urmă, fără să sune cineva să întrebe.
 */
class AdaugaVersiuneaProgramuluiLaCertificate extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('anaf_certificate', 'versiune_bridge')) {
            return;
        }

        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->string('versiune_bridge', 32)->nullable()->after('agent_vazut_la');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('anaf_certificate', 'versiune_bridge')) {
            return;
        }

        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->dropColumn('versiune_bridge');
        });
    }
}
