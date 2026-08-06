<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modulele la care are acces contul, în firma aceea.
 *
 * Abonamentul spune ce a cumpărat clientul; coloana aceasta spune cine dintre
 * oamenii lui vede fiecare modul. Cele două se citesc împreună: un modul se
 * arată doar dacă e și în abonament, și dat omului.
 *
 * Gol înseamnă „toate modulele abonamentului" — așa conturile de până acum
 * rămân neschimbate, fără să fie nevoie să le umble cineva pe fiecare.
 */
class AdaugaModuleLaCompanyUser extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('company_user', 'module')) {
            return;
        }

        Schema::table('company_user', function (Blueprint $table) {
            $table->text('module')->nullable()->after('poate_depune');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('company_user', 'module')) {
            return;
        }

        Schema::table('company_user', function (Blueprint $table) {
            $table->dropColumn('module');
        });
    }
}
