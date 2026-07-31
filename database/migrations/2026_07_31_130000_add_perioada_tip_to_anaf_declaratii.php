<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Felul perioadei raportate: L (lunar), T (trimestrial), A (anual).
 *
 * Numai D406/SAF-T îl folosește, dar acolo e obligatoriu: validatorul ANAF îl
 * cere, împreună cu luna și anul, ca să aleagă versiunea de reguli a perioadei.
 */
class AddPerioadaTipToAnafDeclaratii extends Migration
{
    public function up(): void
    {
        Schema::table('anaf_declaratii', function (Blueprint $table) {
            $table->string('perioada_tip', 1)->nullable()->after('anul');
        });
    }

    public function down(): void
    {
        Schema::table('anaf_declaratii', function (Blueprint $table) {
            $table->dropColumn('perioada_tip');
        });
    }
}
