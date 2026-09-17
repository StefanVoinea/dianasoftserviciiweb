<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [2026-09-17] Cine pe cine a adus, și luna gratuită cuvenită fiecăruia.
 *
 * Oferta de pe pagina de prezentare: un client care aduce alt client primește o
 * lună gratuită, și tot o lună primește și cel adus. Până acum n-avea unde fi
 * scrisă, așa că nu se putea nici ține minte cine a recomandat pe cine, nici
 * dovedi mai târziu de ce s-a dat luna.
 *
 * Fiecare client poate fi recomandat o singură dată — de aceea „company_id" e
 * unic —, dar poate recomanda pe câți vrea. Lunile se acordă una câte una, iar
 * data acordării rămâne scrisă: cât timp e goală, luna n-a fost dată.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('recomandari_clienti')) {
            return;
        }

        Schema::create('recomandari_clienti', function (Blueprint $table) {
            $table->id();

            // Cel adus: un client e recomandat o singura data.
            $table->unsignedBigInteger('company_id')->unique();

            // Cel care l-a adus.
            $table->unsignedBigInteger('recomandat_de_id')->index();

            // Cat timp sunt goale, luna cuvenita inca n-a fost data.
            $table->date('acordata_recomandantului_la')->nullable();
            $table->date('acordata_recomandatului_la')->nullable();

            $table->string('observatii', 500)->nullable();

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('recomandat_de_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recomandari_clienti');
    }
};
