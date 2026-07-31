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
        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->string('monitorizare_cale', 300)->nullable()->after('arhiva_cale');
            $table->boolean('monitorizare_activa')->default(false)->after('monitorizare_cale');
            $table->timestamp('monitorizare_la')->nullable()->after('monitorizare_activa');
        });
    }

    public function down(): void
    {
        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->dropColumn(['monitorizare_cale', 'monitorizare_activa', 'monitorizare_la']);
        });
    }
}
