<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fiecare certificat sta pe alt calculator din retea, cu propriul bridge.
        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->string('bridge_url')->nullable()->after('thumbprint');
            $table->string('bridge_token')->nullable()->after('bridge_url');
            // Certificatul folosit cand utilizatorul nu are unul atribuit.
            $table->boolean('implicit')->default(false)->after('activ');
        });
    }

    public function down(): void
    {
        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->dropColumn(['bridge_url', 'bridge_token', 'implicit']);
        });
    }
};
