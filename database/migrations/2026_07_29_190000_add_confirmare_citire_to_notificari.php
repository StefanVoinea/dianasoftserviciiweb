<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Confirmarea de citire: expeditorul poate cere sa fie instiintat cand cineva
 * a citit ce a trimis.
 *
 * „lot" leaga randurile plecate deodata, ca sa se poata vedea dintr-o privire
 * cati dintre destinatari au citit; „este_confirmare" opreste bucla — citirea
 * unei confirmari nu mai naste alta.
 */
class AddConfirmareCitireToNotificari extends Migration
{
    public function up(): void
    {
        Schema::table('notificari_aplicatie', function (Blueprint $table) {
            $table->uuid('lot')->nullable()->after('id')->index();
            $table->boolean('confirma_citirea')->default(false)->after('pe_email');
            $table->boolean('este_confirmare')->default(false)->after('confirma_citirea');
        });
    }

    public function down(): void
    {
        Schema::table('notificari_aplicatie', function (Blueprint $table) {
            $table->dropColumn(['lot', 'confirma_citirea', 'este_confirmare']);
        });
    }
}
