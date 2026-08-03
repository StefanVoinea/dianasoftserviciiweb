<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Până când e valabilă licența programului local al acestui certificat.
 *
 * Se vede în „Certificate digitale", ca să se știe dinainte că un calculator
 * rămâne fără licență — nu abia când oprește lucrul.
 */
class AddLicentaToAnafCertificate extends Migration
{
    public function up(): void
    {
        // Pe servere coloana poate exista deja, pusa de mana dupa acest fisier.
        if (Schema::hasColumn('anaf_certificate', 'licenta_pana_la')) {
            return;
        }

        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->timestamp('licenta_pana_la')->nullable()->after('monitorizare_la');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('anaf_certificate', 'licenta_pana_la')) {
            return;
        }

        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->dropColumn('licenta_pana_la');
        });
    }
}
