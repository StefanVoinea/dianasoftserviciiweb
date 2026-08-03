<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pe servere tabelul poate exista deja, facut de mana dupa acest fisier.
        if (Schema::hasTable('certificat_utilizatori')) {
            return;
        }

        // Utilizatorii din retea care folosesc acelasi certificat digital.
        Schema::create('certificat_utilizatori', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('certificat_id')->index();
            $table->string('email')->index();
            // Completat cand adresa corespunde unui cont din aplicatie.
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('nume')->nullable();
            $table->boolean('activ')->default(true);
            $table->timestamps();

            $table->unique(['certificat_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificat_utilizatori');
    }
};
