<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Potrivirea dintre decontul depus si cel care iese din SAF-T.
 *
 * Amandoua declaratiile vorbesc despre aceeasi luna a aceleiasi firme: D300
 * spune cat TVA a iesit, D406 spune din ce. Cand nu se potrivesc, una din ele e
 * gresita — iar ANAF face chiar comparatia asta. De aceea rezultatul ei se tine
 * pe declaratie, ca sa se vada in tabel, nu numai in clipa validarii.
 *
 * Randurile care difera se tin ca JSON: sunt tabelare (randul, ce spune fiecare
 * declaratie, diferenta) si se arata ca atare.
 */
class AdaugaPotrivireaD300Saft extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('anaf_declaratii', 'potrivire_stare')) {
            return;
        }

        Schema::table('anaf_declaratii', function (Blueprint $table) {
            // potrivit | diferente | fara_pereche | imposibil
            $table->string('potrivire_stare', 20)->nullable()->after('verificare_la');
            $table->unsignedInteger('potrivire_numar')->nullable()->after('potrivire_stare');
            $table->longText('potrivire_detalii')->nullable()->after('potrivire_numar');
            $table->timestamp('potrivire_la')->nullable()->after('potrivire_detalii');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('anaf_declaratii', 'potrivire_stare')) {
            return;
        }

        Schema::table('anaf_declaratii', function (Blueprint $table) {
            $table->dropColumn(['potrivire_stare', 'potrivire_numar', 'potrivire_detalii', 'potrivire_la']);
        });
    }
}
