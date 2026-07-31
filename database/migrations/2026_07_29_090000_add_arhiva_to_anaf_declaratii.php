<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Documentele fiscale stau pe calculatorul clientului, nu pe server. Aici se
 * retin doar caile din arhiva lui, ca sa se stie de unde sunt cerute inapoi.
 */
class AddArhivaToAnafDeclaratii extends Migration
{
    public function up(): void
    {
        Schema::table('anaf_declaratii', function (Blueprint $table) {
            $table->string('arhiva_xml', 500)->nullable()->after('cale_recipisa');
            $table->string('arhiva_semnat', 500)->nullable()->after('arhiva_xml');
            $table->string('arhiva_recipisa', 500)->nullable()->after('arhiva_semnat');
        });

        Schema::table('spv_mesaje', function (Blueprint $table) {
            $table->string('arhiva_cale', 500)->nullable()->after('cale_fisier');
        });
    }

    public function down(): void
    {
        Schema::table('anaf_declaratii', function (Blueprint $table) {
            $table->dropColumn(['arhiva_xml', 'arhiva_semnat', 'arhiva_recipisa']);
        });

        Schema::table('spv_mesaje', function (Blueprint $table) {
            $table->dropColumn('arhiva_cale');
        });
    }
}
