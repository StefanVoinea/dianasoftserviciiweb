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
        // Pe servere o parte din coloane pot exista deja, puse de mana.
        Schema::table('notificari_aplicatie', function (Blueprint $table) {
            if (!Schema::hasColumn('notificari_aplicatie', 'lot')) {
                $table->uuid('lot')->nullable()->after('id')->index();
            }

            if (!Schema::hasColumn('notificari_aplicatie', 'confirma_citirea')) {
                $table->boolean('confirma_citirea')->default(false)->after('pe_email');
            }

            if (!Schema::hasColumn('notificari_aplicatie', 'este_confirmare')) {
                $table->boolean('este_confirmare')->default(false)->after('confirma_citirea');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notificari_aplicatie', function (Blueprint $table) {
            foreach (['lot', 'confirma_citirea', 'este_confirmare'] as $coloana) {
                if (Schema::hasColumn('notificari_aplicatie', $coloana)) {
                    $table->dropColumn($coloana);
                }
            }
        });
    }
}
