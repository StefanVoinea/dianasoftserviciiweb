<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spv_solicitari', function (Blueprint $table) {
            // Parametri ceruti de ANAF doar pentru anumite tipuri de cereri
            $table->string('motiv')->nullable()->after('luna');                // Adeverinte Venit
            $table->string('numar_inregistrare')->nullable()->after('motiv');  // Duplicat Recipisa
            $table->string('cui_pui', 20)->nullable()->after('numar_inregistrare'); // Fisa Rol punct de lucru
        });
    }

    public function down(): void
    {
        Schema::table('spv_solicitari', function (Blueprint $table) {
            $table->dropColumn(['motiv', 'numar_inregistrare', 'cui_pui']);
        });
    }
};
