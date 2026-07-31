<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cum ajunge serverul la programul local al acestui certificat.
 *
 *   direct — serverul îl cheamă la adresa lui (rețea proprie, IP public, VPN)
 *   tunel  — programul local întreabă serverul ce are de făcut, pe 443
 *
 * „direct" rămâne implicit: instalările existente merg mai departe neschimbate.
 */
class AddModLegaturaToAnafCertificate extends Migration
{
    public function up(): void
    {
        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->string('mod_legatura', 10)->default('direct')->after('bridge_token');
        });
    }

    public function down(): void
    {
        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->dropColumn('mod_legatura');
        });
    }
}
