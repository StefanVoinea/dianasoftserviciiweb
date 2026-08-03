<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Notificarile trimise utilizatorilor din administrare.
 *
 * Se scrie cate un rand pentru fiecare destinatar, nu unul pentru tot lotul:
 * asa se stie cine a citit si cui i-a plecat mailul, iar stergerea unuia nu-i
 * atinge pe ceilalti.
 */
class CreateNotificariAplicatieTable extends Migration
{
    public function up(): void
    {
        // Pe servere tabelul poate exista deja, facut de mana dupa acest fisier.
        if (Schema::hasTable('notificari_aplicatie')) {
            return;
        }

        Schema::create('notificari_aplicatie', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('company_id')->nullable()->index();

            $table->string('titlu');
            $table->text('mesaj');
            // informare | avertizare | urgenta — de ea tine culoarea din interfata
            $table->string('importanta', 20)->default('informare');

            $table->timestamp('citita_la')->nullable();

            // Pe email s-a trimis doar daca a fost cerut si a si reusit
            $table->boolean('pe_email')->default(false);
            $table->timestamp('trimis_email_la')->nullable();
            $table->string('eroare_email', 500)->nullable();

            $table->unsignedBigInteger('trimis_de')->nullable();
            $table->string('trimis_de_nume')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'citita_la']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificari_aplicatie');
    }
}
