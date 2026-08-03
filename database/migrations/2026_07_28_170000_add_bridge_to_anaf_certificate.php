<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fiecare certificat sta pe alt calculator din retea, cu propriul bridge.
        // Pe servere o parte din coloane pot exista deja, puse de mana.
        Schema::table('anaf_certificate', function (Blueprint $table) {
            if (!Schema::hasColumn('anaf_certificate', 'bridge_url')) {
                $table->string('bridge_url')->nullable()->after('thumbprint');
            }

            if (!Schema::hasColumn('anaf_certificate', 'bridge_token')) {
                $table->string('bridge_token')->nullable();
            }

            // Certificatul folosit cand utilizatorul nu are unul atribuit.
            if (!Schema::hasColumn('anaf_certificate', 'implicit')) {
                $table->boolean('implicit')->default(false)->after('activ');
            }
        });
    }

    public function down(): void
    {
        Schema::table('anaf_certificate', function (Blueprint $table) {
            foreach (['bridge_url', 'bridge_token', 'implicit'] as $coloana) {
                if (Schema::hasColumn('anaf_certificate', $coloana)) {
                    $table->dropColumn($coloana);
                }
            }
        });
    }
};
