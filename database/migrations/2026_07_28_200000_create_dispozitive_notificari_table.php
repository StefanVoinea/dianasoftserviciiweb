<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Telefoanele pe care se trimit alertele (Firebase Cloud Messaging).
 *
 * Un utilizator poate avea mai multe dispozitive; tokenul FCM se schimba
 * periodic si la reinstalare, de aceea este cheia unica, nu dispozitivul.
 */
class CreateDispozitiveNotificariTable extends Migration
{
    public function up()
    {
        // Pe servere tabelul poate exista deja, facut de mana dupa acest fisier.
        if (Schema::hasTable('dispozitive_notificari')) {
            return;
        }

        Schema::create('dispozitive_notificari', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->integer('company_id')->nullable()->index();

            $table->string('token', 255)->unique();
            $table->string('platforma', 20)->default('android');
            $table->string('model', 100)->nullable();

            $table->timestamp('ultima_folosire')->nullable();
            // Cate trimiteri consecutive au esuat: dupa cateva, tokenul se sterge.
            $table->unsignedSmallInteger('esecuri')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dispozitive_notificari');
    }
}
