<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rezultatul verificarii de consistenta a D406 (SAF-T).
 *
 * Validatorul ANAF spune daca declaratia e corecta ca forma — schema, coduri,
 * nomenclatoare. Aplicatia de verificare, tot de la ANAF, se uita la fond: daca
 * TVA-ul de pe linii se potriveste cu baza si cota, daca operatiunile taxabile
 * chiar au TVA, daca informatia de taxa sta pe linia care trebuie. O declaratie
 * poate trece de validare si sa fie tot gresita; de aceea rezultatul acesta sta
 * separat de „erori_validare", nu peste el.
 *
 * Liniile gresite se tin ca JSON: sunt tabelare (cont, cod TVA, baza, TVA) si
 * se arata ca atare in tabel, nu ca text de citit.
 */
class AddVerificareSaftToAnafDeclaratii extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('anaf_declaratii', 'verificare_stare')) {
            return;
        }

        Schema::table('anaf_declaratii', function (Blueprint $table) {
            // curata | erori | imposibil
            $table->string('verificare_stare', 20)->nullable()->after('erori_validare');
            $table->unsignedInteger('verificare_numar')->nullable()->after('verificare_stare');
            $table->longText('verificare_erori')->nullable()->after('verificare_numar');
            $table->timestamp('verificare_la')->nullable()->after('verificare_erori');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('anaf_declaratii', 'verificare_stare')) {
            return;
        }

        Schema::table('anaf_declaratii', function (Blueprint $table) {
            $table->dropColumn(['verificare_stare', 'verificare_numar', 'verificare_erori', 'verificare_la']);
        });
    }
}
