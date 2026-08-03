<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dosarul urmarit de pe fiecare calculator: declaratiile puse acolo se incarca,
 * se valideaza si se semneaza singure.
 *
 * Gol inseamna „nu urmari nimic" — purtarea de pana acum.
 */
class AddMonitorizareToAnafCertificate extends Migration
{
    public function up(): void
    {
        // Pe servere o parte din coloane pot exista deja, puse de mana.
        Schema::table('anaf_certificate', function (Blueprint $table) {
            if (!Schema::hasColumn('anaf_certificate', 'monitorizare_cale')) {
                $table->string('monitorizare_cale', 300)->nullable()->after('arhiva_cale');
            }

            if (!Schema::hasColumn('anaf_certificate', 'monitorizare_activa')) {
                $table->boolean('monitorizare_activa')->default(false);
            }

            if (!Schema::hasColumn('anaf_certificate', 'monitorizare_la')) {
                $table->timestamp('monitorizare_la')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('anaf_certificate', function (Blueprint $table) {
            foreach (['monitorizare_cale', 'monitorizare_activa', 'monitorizare_la'] as $coloana) {
                if (Schema::hasColumn('anaf_certificate', $coloana)) {
                    $table->dropColumn($coloana);
                }
            }
        });
    }
}
