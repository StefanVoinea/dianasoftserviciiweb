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
        // Pe servere o parte din coloane pot exista deja, puse de mana.
        Schema::table('anaf_declaratii', function (Blueprint $table) {
            if (!Schema::hasColumn('anaf_declaratii', 'arhiva_xml')) {
                $table->string('arhiva_xml', 500)->nullable()->after('cale_recipisa');
            }

            if (!Schema::hasColumn('anaf_declaratii', 'arhiva_semnat')) {
                $table->string('arhiva_semnat', 500)->nullable();
            }

            if (!Schema::hasColumn('anaf_declaratii', 'arhiva_recipisa')) {
                $table->string('arhiva_recipisa', 500)->nullable();
            }
        });

        if (!Schema::hasColumn('spv_mesaje', 'arhiva_cale')) {
            Schema::table('spv_mesaje', function (Blueprint $table) {
                $table->string('arhiva_cale', 500)->nullable()->after('cale_fisier');
            });
        }
    }

    public function down(): void
    {
        Schema::table('anaf_declaratii', function (Blueprint $table) {
            foreach (['arhiva_xml', 'arhiva_semnat', 'arhiva_recipisa'] as $coloana) {
                if (Schema::hasColumn('anaf_declaratii', $coloana)) {
                    $table->dropColumn($coloana);
                }
            }
        });

        if (Schema::hasColumn('spv_mesaje', 'arhiva_cale')) {
            Schema::table('spv_mesaje', function (Blueprint $table) {
                $table->dropColumn('arhiva_cale');
            });
        }
    }
}
