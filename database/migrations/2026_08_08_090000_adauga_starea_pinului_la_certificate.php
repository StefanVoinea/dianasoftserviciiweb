<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Starea PIN-ului de pe token, asa cum s-a vazut ultima data.
 *
 * PIN-ul nu se pastreaza nicaieri si nici nu trece prin aplicatie — el ramane
 * intre om si driverul tokenului. Se tine minte doar ce s-a aflat: daca la
 * ultima proba cheia s-a putut folosi, cand a fost aceea si, cand n-a mers, de
 * ce. Atat cat sa se vada in fila certificatelor daca tokenul e gata de lucru
 * sau abia asteapta sa fie deblocat.
 */
class AdaugaStareaPinuluiLaCertificate extends Migration
{
    public function up(): void
    {
        Schema::table('anaf_certificate', function (Blueprint $table) {
            // 'gata' | 'refuzat' | 'lipsa' — sau gol, cand nu s-a incercat inca
            $table->string('pin_stare', 20)->nullable()->after('licenta_pana_la');
            $table->timestamp('pin_verificat_la')->nullable()->after('pin_stare');
            $table->string('pin_motiv', 255)->nullable()->after('pin_verificat_la');
        });
    }

    public function down(): void
    {
        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->dropColumn(['pin_stare', 'pin_verificat_la', 'pin_motiv']);
        });
    }
}
