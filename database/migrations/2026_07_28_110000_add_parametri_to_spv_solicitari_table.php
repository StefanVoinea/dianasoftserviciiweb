<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pe servere o parte din coloane pot exista deja, puse de mana.
        Schema::table('spv_solicitari', function (Blueprint $table) {
            // Parametri ceruti de ANAF doar pentru anumite tipuri de cereri
            if (!Schema::hasColumn('spv_solicitari', 'motiv')) {
                $table->string('motiv')->nullable()->after('luna');                // Adeverinte Venit
            }

            if (!Schema::hasColumn('spv_solicitari', 'numar_inregistrare')) {
                $table->string('numar_inregistrare')->nullable();                  // Duplicat Recipisa
            }

            if (!Schema::hasColumn('spv_solicitari', 'cui_pui')) {
                $table->string('cui_pui', 20)->nullable();                         // Fisa Rol punct de lucru
            }
        });
    }

    public function down(): void
    {
        Schema::table('spv_solicitari', function (Blueprint $table) {
            foreach (['motiv', 'numar_inregistrare', 'cui_pui'] as $coloana) {
                if (Schema::hasColumn('spv_solicitari', $coloana)) {
                    $table->dropColumn($coloana);
                }
            }
        });
    }
};
