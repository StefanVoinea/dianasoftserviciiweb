<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cat de des se verifica dosarul urmarit al fiecarui certificat, in minute.
 *
 * Pana acum toata lumea era verificata din cinci in cinci minute, cadenta
 * planificatorului. Un birou care depune zilnic zeci de declaratii vrea insa
 * raspuns intr-un minut, iar unul care depune o data pe luna nu are nevoie sa-i
 * fie intrebat calculatorul la fiecare cinci. De aceea cadenta devine a
 * certificatului: 1, 3, 5, 10, 15, 30 sau 60 de minute.
 */
class AdaugaCadentaMonitorizariiLaCertificate extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('anaf_certificate', 'monitorizare_cadenta')) {
            return;
        }

        Schema::table('anaf_certificate', function (Blueprint $table) {
            // Implicit 5: exact ce primeau toti inainte sa existe alegerea.
            $table->unsignedSmallInteger('monitorizare_cadenta')->default(5)->after('monitorizare_activa');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('anaf_certificate', 'monitorizare_cadenta')) {
            return;
        }

        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->dropColumn('monitorizare_cadenta');
        });
    }
}
