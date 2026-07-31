<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Abonamentul fiecarui client: ce module are, cat plateste si pana cand are
 * acces. Fara un rand aici, clientul lucreaza ca pana acum — asa instalarile
 * existente nu se opresc peste noapte.
 */
class CreateAbonamenteClientiTable extends Migration
{
    public function up(): void
    {
        Schema::create('abonamente_clienti', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->unique();

            $table->decimal('tarif_lunar', 10, 2)->default(0);

            // Perioada de proba: cate zile s-au acordat si pana cand tin
            $table->unsignedSmallInteger('proba_zile')->default(30);
            $table->date('proba_pana_la')->nullable();

            // Pana cand e platit abonamentul; dupa aceasta data accesul se oprește
            $table->date('platit_pana_la')->nullable();

            $table->boolean('blocat')->default(false);
            $table->string('motiv_blocare', 255)->nullable();

            // Modulele la care are acces
            $table->boolean('modul_spv')->default(true);
            $table->boolean('modul_etransport')->default(false);
            $table->boolean('modul_portal_just')->default(false);

            $table->text('observatii')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abonamente_clienti');
    }
}
