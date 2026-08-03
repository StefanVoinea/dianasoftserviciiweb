<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Imprimanta pe care tipareste fiecare om.
 *
 * Se retine si certificatul, pentru ca el spune pe ce calculator se afla
 * imprimanta: acelasi nume („HP LaserJet") poate exista pe mai multe statii,
 * iar hartia trebuie sa iasa pe cea potrivita.
 */
class AddImprimantaToUsers extends Migration
{
    public function up(): void
    {
        // Pe servere o parte din coloane pot exista deja, puse de mana.
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'imprimanta')) {
                $table->string('imprimanta', 191)->nullable()->after('telefon');
            }

            if (!Schema::hasColumn('users', 'imprimanta_certificat_id')) {
                $table->unsignedBigInteger('imprimanta_certificat_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['imprimanta', 'imprimanta_certificat_id'] as $coloana) {
                if (Schema::hasColumn('users', $coloana)) {
                    $table->dropColumn($coloana);
                }
            }
        });
    }
}
