<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Administratorul din firma clientului. Dreptul e per client, nu per persoana:
 * acelasi om poate fi administrator la o firma si utilizator obisnuit la alta.
 */
class AddAdministratorToCompanyUser extends Migration
{
    public function up(): void
    {
        // Pe servere coloana poate exista deja, pusa de mana dupa acest fisier.
        if (Schema::hasColumn('company_user', 'administrator')) {
            return;
        }

        Schema::table('company_user', function (Blueprint $table) {
            $table->boolean('administrator')->default(false)->after('user_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('company_user', 'administrator')) {
            return;
        }

        Schema::table('company_user', function (Blueprint $table) {
            $table->dropColumn('administrator');
        });
    }
}
