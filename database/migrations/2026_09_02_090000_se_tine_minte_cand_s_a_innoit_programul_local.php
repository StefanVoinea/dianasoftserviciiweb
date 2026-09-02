<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Când s-a înnoit ultima oară programul local de pe calculatorul clientului.
 *
 * Versiunea lui se știa de mult — o spune singur la fiecare pândă —, dar nu și
 * de când o are. Iar asta e tocmai ce se întreabă omul când ceva nu merge: „a
 * apucat să ia îndreptarea de ieri, sau a rămas în urmă?".
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('anaf_certificate') || Schema::hasColumn('anaf_certificate', 'versiune_la')) {
            return;
        }

        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->timestamp('versiune_la')->nullable()->after('versiune_bridge');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('anaf_certificate') || !Schema::hasColumn('anaf_certificate', 'versiune_la')) {
            return;
        }

        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->dropColumn('versiune_la');
        });
    }
};
