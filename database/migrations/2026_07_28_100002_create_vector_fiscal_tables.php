<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vectorul fiscal asteptat (periodicitati pe tip de declaratie), editabil manual
        Schema::create('vector_fiscal', function (Blueprint $table) {
            $table->id();
            $table->string('cui', 20)->unique();
            $table->string('denumire')->nullable();
            foreach (['D112', 'D300', 'D301', 'D394', 'D100', 'D101', 'D390', 'D205', 'D200', 'BILANT', 'A4200'] as $coloana) {
                $table->string($coloana, 20)->nullable();
            }
            $table->timestamps();
        });

        // Vectorul fiscal citit din documentele SPV (istoric pe obligatii)
        Schema::create('vector_spv', function (Blueprint $table) {
            $table->id();
            $table->string('cui', 20)->index();
            $table->date('data_vector')->nullable();
            $table->string('cod_imp', 20)->index();
            $table->string('semnificatie')->nullable();
            $table->string('perfisc', 20)->nullable();
            $table->date('data_inceput')->nullable();
            $table->date('data_sfarsit')->nullable();
            $table->timestamps();

            $table->index(['cui', 'cod_imp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vector_spv');
        Schema::dropIfExists('vector_fiscal');
    }
};
