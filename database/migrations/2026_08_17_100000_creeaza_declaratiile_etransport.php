<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Declarațiile e-Transport lucrate în aplicație, pentru obținerea codului UIT.
 *
 * Până acum aplicația depunea doar XML-uri făcute în altă parte (programul
 * vechi + aplicația din SPV). De aici, declarația se construiește în Dispecer
 * e-Transport: liniile vin din fișierele furnizorului, restul se completează în
 * formular, iar XML-ul se generează și se depune direct.
 *
 * Nomenclatorul codurilor vamale stă în tabelul lui, comun tuturor clienților:
 * e același nomenclator NC pentru toată lumea.
 */
class CreeazaDeclaratiileEtransport extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('etransport_declaratii')) {
            Schema::create('etransport_declaratii', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable();

                // ciorna -> depusa -> validata / respinsa
                $table->string('stare', 20)->default('ciorna')->index();

                $table->string('cif_declarant', 20)->nullable();
                $table->string('referinta_interna', 50)->nullable();
                $table->unsignedSmallInteger('tip_operatiune')->nullable();

                $table->string('partener_tara', 2)->nullable();
                $table->string('partener_cod', 30)->nullable();
                $table->string('partener_denumire', 200)->nullable();

                $table->string('nr_vehicul', 20)->nullable();
                $table->string('nr_remorca1', 20)->nullable();
                $table->string('nr_remorca2', 20)->nullable();
                $table->string('transportator_tara', 2)->nullable();
                $table->string('transportator_cod', 30)->nullable();
                $table->string('transportator_denumire', 200)->nullable();
                $table->date('data_transport')->nullable();

                // {tip: adresa|ptf|birou_vamal, cod_ptf, cod_birou_vamal, cod_judet, localitate, strada, ...}
                $table->json('loc_start')->nullable();
                $table->json('loc_final')->nullable();

                // [{tip, numar, data, observatii}]
                $table->json('documente')->nullable();

                // [{cod_tarifar, denumire, scop_operatiune, cantitate, um, greutate_neta, greutate_bruta, valoare, valoare_lei}]
                $table->json('linii')->nullable();

                $table->string('valuta', 3)->default('RON');
                $table->decimal('curs', 10, 4)->nullable();

                // Din ce fisiere s-au luat liniile, ca sa se stie provenienta.
                $table->json('fisiere_importate')->nullable();

                $table->string('index_incarcare', 40)->nullable();
                $table->string('uit', 20)->nullable()->index();
                $table->json('raspuns_anaf')->nullable();
                $table->timestamp('depusa_la')->nullable();

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('etransport_coduri_vamale')) {
            Schema::create('etransport_coduri_vamale', function (Blueprint $table) {
                $table->id();
                $table->string('cod', 8)->index();
                $table->text('denumire');
                $table->string('denumire_scurta', 255)->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('etransport_declaratii');
        Schema::dropIfExists('etransport_coduri_vamale');
    }
}
