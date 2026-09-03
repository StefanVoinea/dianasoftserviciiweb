<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cine a apasat „Solicita demo" in scrisoare.
 *
 * E singurul semn cinstit de interes pe care il putem avea. Deschiderile se
 * numara prost — multe programe de posta blocheaza imaginea dupa care se
 * numara, iar altele o cer singure, fara ca omul sa fi vazut ceva. O apasare pe
 * buton e insa o fapta: cineva a citit si a vrut mai departe.
 *
 * Se tine si ce a scris acolo despre sine, daca a scris: numele, telefonul,
 * cateva vorbe. Fara ele, ramane doar „cineva de la firma asta a apasat", ceea
 * ce e destul ca sa suni, dar nu si ca sa stii pe cine ceri.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('marketing_contacte')) {
            return;
        }

        Schema::table('marketing_contacte', function (Blueprint $table) {
            if (!Schema::hasColumn('marketing_contacte', 'demo_cerut_la')) {
                $table->timestamp('demo_cerut_la')->nullable()->after('cate_trimiteri')->index();
            }

            if (!Schema::hasColumn('marketing_contacte', 'demo_persoana')) {
                $table->string('demo_persoana')->nullable()->after('demo_cerut_la');
            }

            if (!Schema::hasColumn('marketing_contacte', 'demo_telefon')) {
                $table->string('demo_telefon', 60)->nullable()->after('demo_persoana');
            }

            if (!Schema::hasColumn('marketing_contacte', 'demo_mesaj')) {
                $table->string('demo_mesaj', 1000)->nullable()->after('demo_telefon');
            }

            // Din ce campanie a venit apasarea: asa se vede care scrisoare a prins.
            if (!Schema::hasColumn('marketing_contacte', 'demo_campanie')) {
                $table->string('demo_campanie')->nullable()->after('demo_mesaj');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('marketing_contacte')) {
            return;
        }

        Schema::table('marketing_contacte', function (Blueprint $table) {
            foreach (['demo_cerut_la', 'demo_persoana', 'demo_telefon', 'demo_mesaj', 'demo_campanie'] as $coloana) {
                if (Schema::hasColumn('marketing_contacte', $coloana)) {
                    $table->dropColumn($coloana);
                }
            }
        });
    }
};
