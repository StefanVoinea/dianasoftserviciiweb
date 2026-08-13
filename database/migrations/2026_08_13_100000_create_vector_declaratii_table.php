<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Declaratiile asteptate pe fiecare CUI: ce se depune, cat de des si de cand
 * pana cand.
 *
 * Randurile "dedusa" le scrie aplicatia, din vectorul fiscal si din istoricul
 * depunerilor, la fiecare intocmire a raportului lunar. Randurile "manuala" le
 * scrie omul, pentru ce nu se poate deduce — bilantul semestrial, de pilda —
 * si bat deductia pe acelasi tip.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Pe servere tabelul poate exista deja, facut de mana dupa acest fisier.
        if (Schema::hasTable('vector_declaratii')) {
            return;
        }

        Schema::create('vector_declaratii', function (Blueprint $table) {
            $table->id();
            $table->string('cui', 20)->index();
            $table->string('tip', 20);
            $table->string('perfisc', 20);
            $table->date('data_inceput')->nullable();
            $table->date('data_sfarsit')->nullable();
            $table->string('sursa', 10)->default('manuala');
            // Din ce s-a dedus: codurile de obligatii sau „din istoricul depunerilor”
            $table->string('obligatii')->nullable();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->timestamps();

            $table->index(['cui', 'tip']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vector_declaratii');
    }
};
