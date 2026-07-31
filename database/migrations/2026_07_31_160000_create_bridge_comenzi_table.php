<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Comenzile care așteaptă să fie duse la programul local.
 *
 * Serverul stă în cloud, programul local stă în rețeaua clientului, în spatele
 * unui router pe care nimeni nu vrea să-l deschidă. Așa că nu mai sună serverul
 * la client, ci clientul întreabă serverul „ai ceva pentru mine?" — pe 443, ca
 * orice pagină de internet, deci trece prin orice firewall.
 *
 * Aici stau comenzile între cele două capete. Corpurile (XML-uri de zeci de
 * megaocteți, PDF-uri) nu intră în tabel: se scriu în storage și aici rămâne
 * doar calea lor.
 */
class CreateBridgeComenziTable extends Migration
{
    public function up(): void
    {
        Schema::create('bridge_comenzi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->unsignedBigInteger('certificat_id')->index();

            $table->string('metoda', 10);
            $table->string('cale', 500);
            $table->json('antete')->nullable();
            $table->string('corp_fisier', 300)->nullable();

            // asteapta -> luata -> gata | eroare
            $table->string('stare', 20)->default('asteapta')->index();

            $table->unsignedSmallInteger('status')->nullable();
            $table->json('rezultat_antete')->nullable();
            $table->string('rezultat_fisier', 300)->nullable();
            $table->text('eroare')->nullable();

            $table->timestamp('luata_la')->nullable();
            $table->timestamp('terminata_la')->nullable();
            $table->timestamps();

            $table->index(['certificat_id', 'stare']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bridge_comenzi');
    }
}
