<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Transportul care intră doar în Intrastat, fără să fie depus la e-Transport.
 *
 * Marfa venită prin curier (DHL), cu factura PDF, nu are nevoie de UIT când nu e
 * cu risc fiscal ridicat, dar intră în Intrastat-ul lunii. Ciorna ei stă în
 * Dispecer ca oricare alta; bifa o ține departe de butonul de depunere, ca să nu
 * plece din greșeală la ANAF.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('etransport_declaratii', 'doar_intrastat')) {
            Schema::table('etransport_declaratii', function (Blueprint $tabel) {
                $tabel->boolean('doar_intrastat')->default(false)->after('valoare_zero');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('etransport_declaratii', 'doar_intrastat')) {
            Schema::table('etransport_declaratii', function (Blueprint $tabel) {
                $tabel->dropColumn('doar_intrastat');
            });
        }
    }
};
