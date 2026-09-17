<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [2026-09-17] Datele din care se scoate contractul de abonament.
 *
 * Contractul cere despre client mai mult decât știe aplicația: adresa, numărul
 * de la Registrul Comerțului, contul bancar, cine semnează și în ce calitate.
 * Se scriu o dată, aici, și de fiecare dată când se cere contractul el iese cu
 * ele — nu se mai completează de mână într-un document Word.
 *
 * Un client are un singur contract în lucru; actele adiționale sunt altă
 * poveste, care se va scrie când va fi nevoie de ea.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('contracte_clienti')) {
            return;
        }

        Schema::create('contracte_clienti', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->unique();

            // Antetul contractului
            $table->string('numar', 40)->nullable();
            $table->date('data')->nullable();

            // Beneficiarul, asa cum se scrie in contract
            $table->string('beneficiar_denumire', 200)->nullable();
            $table->string('beneficiar_adresa', 300)->nullable();
            $table->string('beneficiar_reg_com', 60)->nullable();
            $table->string('beneficiar_cui', 40)->nullable();
            $table->string('beneficiar_iban', 60)->nullable();
            $table->string('beneficiar_banca', 120)->nullable();
            $table->string('beneficiar_email', 190)->nullable();
            $table->string('beneficiar_telefon', 60)->nullable();
            $table->string('beneficiar_reprezentant', 120)->nullable();
            $table->string('beneficiar_functie', 80)->nullable();

            // Planul si felul in care se factureaza
            $table->string('plan', 20)->nullable();
            $table->string('periodicitate', 10)->nullable();
            $table->string('durata', 60)->nullable();
            $table->date('data_activare')->nullable();
            $table->date('data_facturare')->nullable();

            $table->text('observatii')->nullable();

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracte_clienti');
    }
};
