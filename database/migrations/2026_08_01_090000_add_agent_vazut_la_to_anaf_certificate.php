<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Când a întrebat ultima oară agentul acestui certificat dacă are ceva de lucru.
 *
 * Fără asta, o comandă trimisă către un calculator închis stă în coadă până
 * expiră răbdarea celui care a cerut-o, iar omul primește o eroare de rețea
 * după un minut de așteptare. Cu ea, i se spune din prima ce se întâmplă.
 */
class AddAgentVazutLaToAnafCertificate extends Migration
{
    public function up(): void
    {
        // Pe unele servere coloana exista deja (pusa de mana sau de o rulare
        // care n-a apucat sa fie scrisa in tabelul migrations).
        if (Schema::hasColumn('anaf_certificate', 'agent_vazut_la')) {
            return;
        }

        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->timestamp('agent_vazut_la')->nullable()->after('mod_legatura');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('anaf_certificate', 'agent_vazut_la')) {
            return;
        }

        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->dropColumn('agent_vazut_la');
        });
    }
}
