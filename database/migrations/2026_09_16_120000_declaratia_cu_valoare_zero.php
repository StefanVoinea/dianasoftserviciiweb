<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [2026-09-16] Transportul care se declară cu valoare zero.
 *
 * Unele transporturi se declară fără valoare, chiar dacă fișierul furnizorului
 * are sume pe fiecare linie: marfă mutată fără vânzare, ambalaje returnate,
 * mostre. Bifa spune asta o dată, pe declarație, în loc să fie ștearsă suma de
 * pe fiecare linie și pusă la loc de fiecare recalculare.
 *
 * Bifată, valorile și cursul rămân goale pe ecran, iar în XML-ul către ANAF
 * fiecare linie pleacă cu valoarea zero.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etransport_declaratii', function (Blueprint $table) {
            if (!Schema::hasColumn('etransport_declaratii', 'valoare_zero')) {
                $table->boolean('valoare_zero')->default(false)->after('curs');
            }
        });
    }

    public function down(): void
    {
        Schema::table('etransport_declaratii', function (Blueprint $table) {
            if (Schema::hasColumn('etransport_declaratii', 'valoare_zero')) {
                $table->dropColumn('valoare_zero');
            }
        });
    }
};
