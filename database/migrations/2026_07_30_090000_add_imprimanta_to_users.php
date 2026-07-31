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
        Schema::table('users', function (Blueprint $table) {
            $table->string('imprimanta', 191)->nullable()->after('telefon');
            $table->unsignedBigInteger('imprimanta_certificat_id')->nullable()->after('imprimanta');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['imprimanta', 'imprimanta_certificat_id']);
        });
    }
}
