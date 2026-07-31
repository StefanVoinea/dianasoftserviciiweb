<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Unde tine fiecare calculator arhiva de documente. Se seteaza din aplicatie,
 * la certificatul care raspunde de acel calculator, ca sa nu mai fie nevoie de
 * umblat prin bridge.env pe fiecare statie in parte.
 */
class AddArhivaCaleToAnafCertificate extends Migration
{
    public function up(): void
    {
        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->string('arhiva_cale', 300)->nullable()->after('bridge_token');
        });
    }

    public function down(): void
    {
        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->dropColumn('arhiva_cale');
        });
    }
}
