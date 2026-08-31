<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Datele firmei care intra in antetul declaratiilor.
 *
 * Decontul de TVA scos din SAF-T are cifrele din jurnale, dar antetul lui cere
 * lucruri care nu se afla nicaieri in fisierul SAF-T: adresa, banca si contul,
 * codul CAEN, cine semneaza si in ce calitate, pro-rata, bifele de la inceputul
 * formularului. Ele nu se schimba de la o luna la alta, asa ca stau pe fisa
 * firmei si se iau de acolo — nu se cer la fiecare declaratie.
 *
 * O parte din ele — adresa, telefonul, banca — sunt ale firmei si vor folosi si
 * altor declaratii; de aceea poarta nume simple. Cele care tin numai de decont
 * poarta „d300_" inainte.
 */
class AdaugaDateleDeDeclaratieLaSocietati extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('anaf_societati', 'adresa')) {
            return;
        }

        Schema::table('anaf_societati', function (Blueprint $table) {
            // Ale firmei, bune la orice declaratie
            $table->string('adresa', 1000)->nullable()->after('tip');
            $table->string('telefon', 15)->nullable()->after('adresa');
            $table->string('fax', 15)->nullable()->after('telefon');
            $table->string('email', 200)->nullable()->after('fax');
            $table->string('banca', 50)->nullable()->after('email');
            $table->string('cont', 50)->nullable()->after('banca');
            $table->string('caen', 10)->nullable()->after('cont');

            // Cine semneaza declaratia si in ce calitate
            $table->string('nume_declarant', 75)->nullable()->after('caen');
            $table->string('prenume_declarant', 75)->nullable()->after('nume_declarant');
            $table->string('functie_declarant', 50)->nullable()->after('prenume_declarant');

            /*
             * Declaratia depusa de imputernicit se insemneaza ca atare, si
             * atunci temeiul nu mai e „declaratie proprie" (0), ci „prin
             * imputernicit" (2). Cele doua merg impreuna.
             */
            $table->boolean('prin_reprezentant')->default(false)->after('functie_declarant');

            // Numai ale decontului de TVA
            $table->string('d300_tip_decont', 1)->nullable()->after('prin_reprezentant');
            $table->decimal('d300_pro_rata', 5, 2)->nullable()->after('d300_tip_decont');
            $table->boolean('d300_bifa_interne')->default(false)->after('d300_pro_rata');
            $table->boolean('d300_bifa_cereale')->default(false)->after('d300_bifa_interne');
            $table->boolean('d300_bifa_mob')->default(false)->after('d300_bifa_cereale');
            $table->boolean('d300_bifa_disp')->default(false)->after('d300_bifa_mob');
            $table->boolean('d300_bifa_cons')->default(false)->after('d300_bifa_disp');
            $table->boolean('d300_solicit_ramb')->default(false)->after('d300_bifa_cons');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('anaf_societati', 'adresa')) {
            return;
        }

        Schema::table('anaf_societati', function (Blueprint $table) {
            $table->dropColumn([
                'adresa', 'telefon', 'fax', 'email', 'banca', 'cont', 'caen',
                'nume_declarant', 'prenume_declarant', 'functie_declarant', 'prin_reprezentant',
                'd300_tip_decont', 'd300_pro_rata', 'd300_bifa_interne', 'd300_bifa_cereale',
                'd300_bifa_mob', 'd300_bifa_disp', 'd300_bifa_cons', 'd300_solicit_ramb',
            ]);
        });
    }
}
