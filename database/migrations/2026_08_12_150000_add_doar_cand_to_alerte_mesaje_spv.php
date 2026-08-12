<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Alerte care se uita la ce scrie in document, nu doar la felul lui.
 *
 * Alertele de pana acum se declanseaza cand intra in SPV un document de un
 * anumit tip. Pentru vectorul fiscal asta nu foloseste: la o descarcare de
 * doua sute cincizeci de firme ar pleca doua sute cincizeci de emailuri, desi
 * numai la trei s-a schimbat ceva. Zgomot din care nu se mai citeste nimic.
 *
 * Aplicatia stia deja care sunt cele trei — compara vectorul de azi cu cel de
 * ieri, rand cu rand — dar scria raspunsul intr-o celula din tabel, pe care la
 * atatea firme n-o citeste nimeni.
 *
 * Cu „doar_cand” alerta asteapta o anume constatare, nu sosirea unei hartii.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('alerte_mesaje_spv') || Schema::hasColumn('alerte_mesaje_spv', 'doar_cand')) {
            return;
        }

        Schema::table('alerte_mesaje_spv', function (Blueprint $table) {
            /*
             * Gol inseamna alerta de pana acum: la orice document de tipul ales.
             * Altfel: „vector_modificat” sau „restante”.
             */
            $table->string('doar_cand', 30)->nullable()->after('tip_document')->index();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('alerte_mesaje_spv') || !Schema::hasColumn('alerte_mesaje_spv', 'doar_cand')) {
            return;
        }

        Schema::table('alerte_mesaje_spv', function (Blueprint $table) {
            $table->dropColumn('doar_cand');
        });
    }
};
