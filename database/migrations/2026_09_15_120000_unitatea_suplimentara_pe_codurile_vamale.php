<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [2026-09-15] Unitatea de măsură suplimentară a fiecărui cod vamal.
 *
 * Nomenclatorul Combinat stabilește pentru unele coduri o unitate în afară de
 * kilogram — numărul de bucăți (`p/st`), perechile (`pa`), metrii pătrați —, iar
 * Intrastat cere cantitatea în ea. Din 9797 de coduri, 2711 au una.
 *
 * Fără coloana asta, declarația Intrastat pleca fără unitățile suplimentare, iar
 * INS o respingea: „Cod Unitate de Măsură Suplimentară invalid", câte o eroare
 * pe fiecare linie al cărei cod cere o asemenea unitate.
 *
 * Valorile vin din CN_2026.xml al INS (elementul `RefCode`), prin CSV-ul din
 * `database/nomenclatoare`. Se umple cu `php artisan anaf:coduri-vamale`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etransport_coduri_vamale', function (Blueprint $table) {
            if (!Schema::hasColumn('etransport_coduri_vamale', 'um_suplimentara')) {
                $table->string('um_suplimentara', 20)->nullable()->after('denumire_scurta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('etransport_coduri_vamale', function (Blueprint $table) {
            if (Schema::hasColumn('etransport_coduri_vamale', 'um_suplimentara')) {
                $table->dropColumn('um_suplimentara');
            }
        });
    }
};
