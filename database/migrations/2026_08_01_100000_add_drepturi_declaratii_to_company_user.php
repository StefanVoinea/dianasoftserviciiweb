<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cine are voie să semneze și cine să depună declarații.
 *
 * Sunt două lucruri deosebite, iar deosebirea contează: semnătura e a persoanei
 * care ține tokenul, iar depunerea nu se mai poate lua înapoi. De aceea stau
 * lângă „administrator", tot pe legătura dintre om și firmă — același om poate
 * avea drepturi diferite la clienți diferiți.
 */
class AddDrepturiDeclaratiiToCompanyUser extends Migration
{
    public function up(): void
    {
        // Pe servere, o parte dintre coloane exista deja, puse de mana in
        // diverse stari; se adauga doar ce lipseste, iar valorile implicite le
        // indreapta migratia de normalizare care urmeaza.
        if (!Schema::hasColumn('company_user', 'poate_semna')) {
            Schema::table('company_user', function (Blueprint $table) {
                $table->boolean('poate_semna')->default(false)->after('administrator');
            });
        }

        if (!Schema::hasColumn('company_user', 'poate_depune')) {
            Schema::table('company_user', function (Blueprint $table) {
                $table->boolean('poate_depune')->default(false)->after('poate_semna');
            });
        }
    }

    public function down(): void
    {
        foreach (['poate_semna', 'poate_depune'] as $coloana) {
            if (Schema::hasColumn('company_user', $coloana)) {
                Schema::table('company_user', function (Blueprint $table) use ($coloana) {
                    $table->dropColumn($coloana);
                });
            }
        }
    }
}
