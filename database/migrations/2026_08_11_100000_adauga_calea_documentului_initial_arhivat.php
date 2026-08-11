<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Unde s-a scris in arhiva documentul dat spre lucru.
 *
 * Se tine minte din acelasi motiv pentru care se tin minte si celelalte cai:
 * la o reluare — o resemnare, o arhivare ceruta din nou — documentul isi
 * inlocuieste propriul fisier, in loc sa lase in urma inca un exemplar cu
 * numele umflat de un numar.
 */
class AdaugaCaleaDocumentuluiInitialArhivat extends Migration
{
    public function up(): void
    {
        Schema::table('anaf_declaratii', function (Blueprint $table) {
            $table->string('arhiva_initial', 500)->nullable()->after('arhiva_xml');
        });
    }

    public function down(): void
    {
        Schema::table('anaf_declaratii', function (Blueprint $table) {
            $table->dropColumn('arhiva_initial');
        });
    }
}
