<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pe servere tabelul poate exista deja, facut de mana dupa acest fisier.
        if (Schema::hasTable('anaf_societati')) {
            return;
        }

        // Societatile pentru care certificatul digital are drept de semnatura.
        // Lista vine de la ANAF (campul "cui" din raspunsul listaMesaje).
        Schema::create('anaf_societati', function (Blueprint $table) {
            $table->id();
            $table->string('cif', 20)->unique();
            $table->string('denumire')->nullable();
            $table->string('denumire_sursa', 30)->nullable(); // vector | date_identificare | manual
            $table->string('tip', 20)->default('pj');         // pj | pf (CNP de 13 cifre)
            $table->boolean('activ')->default(true);

            $table->string('cnp_reprezentant', 20)->nullable();
            $table->string('serial_certificat', 60)->nullable();

            $table->text('date_identificare')->nullable();
            $table->timestamp('date_identificare_la')->nullable();
            $table->timestamp('vector_la')->nullable();
            $table->timestamp('sincronizat_la')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anaf_societati');
    }
};
