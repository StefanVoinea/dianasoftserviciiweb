<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Vizitele paginii de prezentare spvcurier.ro.
 *
 * Pagina e un fisier static pe alt domeniu si nu are jurnal la care sa ajungem.
 * Ea insasi spune aplicatiei, cat e deschisa, ca cineva o citeste: de aici se
 * vede cati au intrat, de la ce adresa si cat au stat.
 *
 * Un rand e o vizita — o deschidere a paginii, pana la inchiderea ei. Nu se
 * pune niciun cookie: vizita se recunoaste dupa un numar tinut doar in memoria
 * paginii, care piere odata cu ea.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('site_vizite')) {
            return;
        }

        Schema::create('site_vizite', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->string('sesiune', 40)->unique();
            $tabel->string('ip', 45)->index();
            $tabel->string('user_agent', 500)->nullable();
            // calculator, telefon sau tableta — dedus din user agent
            $tabel->string('dispozitiv', 20)->nullable();
            // de unde a venit: pagina care l-a trimis, cand browserul o spune
            $tabel->string('referrer', 300)->nullable();
            $tabel->string('pagina_intrare', 60)->nullable();
            $tabel->json('pagini')->nullable();
            // cat a stat cu pagina in fata, nu cat a tinut-o deschisa intr-o fila uitata
            $tabel->unsignedInteger('durata_secunde')->default(0);
            // firma din lista de marketing, cand a venit din scrisoarea noastra
            $tabel->unsignedBigInteger('contact_id')->nullable()->index();
            $tabel->timestamps();

            $tabel->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_vizite');
    }
};
