<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Instiintari pe email cand intra in SPV un anumit fel de document.
 *
 * O alerta se poate ingusta in doua feluri: la un singur certificat digital
 * (deci la firmele inrolate lui) si, mai departe, la o singura firma. Lasate
 * goale, ele inseamna „oricare".
 */
class CreateAlerteMesajeSpvTable extends Migration
{
    public function up(): void
    {
        // Pe servere tabelul poate exista deja, facut de mana dupa acest fisier.
        if (Schema::hasTable('alerte_mesaje_spv')) {
            return;
        }

        Schema::create('alerte_mesaje_spv', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();

            $table->string('email');

            // Gol = orice certificat la care are acces cel care a facut alerta
            $table->unsignedBigInteger('certificat_id')->nullable()->index();

            // Gol = orice fel de document
            $table->string('tip_document', 100)->nullable()->index();

            // Gol = orice firma inrolata certificatului de mai sus
            $table->string('cif', 20)->nullable()->index();

            $table->boolean('activ')->default(true);

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamp('ultima_alerta_la')->nullable();
            $table->unsignedInteger('trimise')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerte_mesaje_spv');
    }
}
