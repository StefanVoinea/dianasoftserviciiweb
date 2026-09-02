<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Comanda dusă prin tunel ține minte cine a pornit-o și de unde.
 *
 * Se știa deja la certificat, dar se pierdea pe drum: când programul local
 * găsea o fereastră de cod deschisă, agentul spunea „cineva, de undeva", iar
 * codul se cerea în toate părțile deodată — și în fila din browser, și pe
 * telefon, deși lucrarea plecase de pe unul singur dintre ele.
 *
 * Însemnat pe comandă, drumul se poate urma înapoi: fereastra o deschide chiar
 * cererea care e atunci în lucru, deci cel care a apăsat butonul e întrebat
 * acolo unde l-a apăsat.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bridge_comenzi')) {
            return;
        }

        Schema::table('bridge_comenzi', function (Blueprint $table) {
            if (!Schema::hasColumn('bridge_comenzi', 'cerut_de')) {
                $table->unsignedBigInteger('cerut_de')->nullable()->after('cale');
            }

            if (!Schema::hasColumn('bridge_comenzi', 'cerut_din')) {
                $table->string('cerut_din', 10)->nullable()->after('cerut_de');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('bridge_comenzi')) {
            return;
        }

        Schema::table('bridge_comenzi', function (Blueprint $table) {
            foreach (['cerut_de', 'cerut_din'] as $coloana) {
                if (Schema::hasColumn('bridge_comenzi', $coloana)) {
                    $table->dropColumn($coloana);
                }
            }
        });
    }
};
