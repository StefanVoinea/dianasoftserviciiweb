<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PIN-ul se cere de unde a plecat lucrarea.
 *
 * Fără asta, fereastra de cod apărea oriunde se uita cineva: cel care apăsase
 * butonul pe telefon era întrebat într-o filă din browser pe care poate n-o
 * avea nimeni în față, iar codul rămânea nescris.
 *
 * Se ține minte deci cine a pornit lucrarea și de unde — din filă sau de pe
 * telefon. Lucrările pornite de la sine — dosarul urmărit, sarcina de noapte —
 * n-au pe nimeni în spate: acelea se arată oriunde, fiindcă oricine e prin
 * preajmă le poate dezlega.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('anaf_certificate')) {
            return;
        }

        Schema::table('anaf_certificate', function (Blueprint $table) {
            if (!Schema::hasColumn('anaf_certificate', 'pin_cerut_de')) {
                $table->unsignedBigInteger('pin_cerut_de')->nullable()->after('pin_de_la_distanta');
            }

            if (!Schema::hasColumn('anaf_certificate', 'pin_cerut_din')) {
                $table->string('pin_cerut_din', 10)->nullable()->after('pin_cerut_de');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('anaf_certificate')) {
            return;
        }

        Schema::table('anaf_certificate', function (Blueprint $table) {
            foreach (['pin_cerut_de', 'pin_cerut_din'] as $coloana) {
                if (Schema::hasColumn('anaf_certificate', $coloana)) {
                    $table->dropColumn($coloana);
                }
            }
        });
    }
};
