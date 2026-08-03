<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adresele IP de la care are voie sa se conecteze fiecare om.
 *
 * Gol inseamna „de oriunde" — asa conturile de pana acum nu se blocheaza singure
 * in clipa in care apare aceasta coloana.
 */
class AddIpPermiseToUsers extends Migration
{
    public function up(): void
    {
        // Pe servere coloana poate exista deja, pusa de mana dupa acest fisier.
        if (Schema::hasColumn('users', 'ip_permise')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->text('ip_permise')->nullable()->after('imprimanta_certificat_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('users', 'ip_permise')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ip_permise');
        });
    }
}
