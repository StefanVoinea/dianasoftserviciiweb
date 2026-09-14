<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [2026-09-14] În ce declarație Intrastat a intrat fiecare transport.
 *
 * Fără însemnarea asta nu se poate ști ce a fost prins într-o lună și ce a
 * rămas pe dinafară. Iar întârziații sunt regula, nu excepția: o factură emisă
 * pe 31 iulie, ajunsă după ce s-a depus declarația lui iulie, se declară în
 * august. La întocmirea lui august ea trebuie să apară ca „mai veche și
 * neintrată nicăieri", ca omul s-o poată lua acum.
 *
 * Ține luna de raportare, scrisă „2026-08". Gol înseamnă că transportul n-a
 * intrat încă în nicio declarație Intrastat.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etransport_declaratii', function (Blueprint $table) {
            if (!Schema::hasColumn('etransport_declaratii', 'intrastat_perioada')) {
                $table->string('intrastat_perioada', 7)->nullable()->after('uit');
                $table->index('intrastat_perioada');
            }
        });
    }

    public function down(): void
    {
        Schema::table('etransport_declaratii', function (Blueprint $table) {
            if (Schema::hasColumn('etransport_declaratii', 'intrastat_perioada')) {
                $table->dropIndex(['intrastat_perioada']);
                $table->dropColumn('intrastat_perioada');
            }
        });
    }
};
