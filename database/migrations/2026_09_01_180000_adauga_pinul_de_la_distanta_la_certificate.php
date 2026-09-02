<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trimiterea PIN-ului de la distanță, pornită anume pentru fiecare token.
 *
 * Nu e pornită din start și nu se pornește singură: e alegerea celui care ține
 * tokenul, făcută pentru tokenul lui. Cât timp e stinsă, aplicația nici nu
 * întreabă de cod — spune doar că fereastra e deschisă, și atât.
 *
 * PIN-ul nu se păstrează nicăieri: trece o dată, prin cererea omului, și se
 * uită. Aici nu se ține codul, ci numai voia lui de a-l putea trimite.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('anaf_certificate')) {
            return;
        }

        if (Schema::hasColumn('anaf_certificate', 'pin_de_la_distanta')) {
            return;
        }

        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->boolean('pin_de_la_distanta')->default(false)->after('pin_verificat_la');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('anaf_certificate') || !Schema::hasColumn('anaf_certificate', 'pin_de_la_distanta')) {
            return;
        }

        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->dropColumn('pin_de_la_distanta');
        });
    }
};
