<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Entitatea scoasa din uz de om, deosebita de cea la care ANAF nu mai da drepturi.
 *
 * „activ" spunea pana acum cuvantul ANAF-ului: sincronizarea il pune pe adevarat
 * pentru fiecare entitate din raspuns si pe fals pentru cele scoase de acolo.
 * Daca aceeasi coloana ar purta si alegerea omului, prima sincronizare i-ar
 * sterge-o — exact greseala din care certificatele dezactivate inviau singure.
 *
 * De aceea alegerea lui sta deoparte. O entitate se ia in seama numai cand sunt
 * amandoua bune: ANAF ii da drepturi si omul o vrea.
 */
class AdaugaScoatereaDinUzLaSocietati extends Migration
{
    public function up(): void
    {
        Schema::table('anaf_societati', function (Blueprint $table) {
            $table->boolean('scos_din_uz')->default(false)->after('activ');
        });
    }

    public function down(): void
    {
        Schema::table('anaf_societati', function (Blueprint $table) {
            $table->dropColumn('scos_din_uz');
        });
    }
}
