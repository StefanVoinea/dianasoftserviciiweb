<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Deschiderea paginii „Solicită demo" nu e o cerere de demonstrație.
 *
 * Pana acum „a cerut demo" se insemna la simpla deschidere a legaturii din
 * scrisoare. Numai ca legaturile din e-mail le deschid si filtrele de
 * securitate ale cutiilor postale, inainte ca omul sa fi vazut scrisoarea: asa
 * au aparut trei „cereri" fara nume si fara telefon, iar la telefon omul a spus
 * ca n-a cerut nimic.
 *
 * Deschiderea se tine de acum deoparte, ca semn slab; cererea e doar formularul
 * trimis. Marcajele vechi fara formular trec la deschideri.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('marketing_contacte', 'demo_deschis_la')) {
            Schema::table('marketing_contacte', function (Blueprint $tabel) {
                $tabel->timestamp('demo_deschis_la')->nullable()->after('demo_campanie');
            });
        }

        DB::table('marketing_contacte')
            ->whereNotNull('demo_cerut_la')
            ->whereNull('demo_persoana')
            ->whereNull('demo_telefon')
            ->whereNull('demo_mesaj')
            ->update([
                'demo_deschis_la' => DB::raw('demo_cerut_la'),
                'demo_cerut_la' => null,
            ]);
    }

    public function down(): void
    {
        Schema::table('marketing_contacte', function (Blueprint $tabel) {
            $tabel->dropColumn('demo_deschis_la');
        });
    }
};
