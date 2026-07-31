<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Monitorizarea dosarelor din Portal Just.
 *
 * Trei tabele: ce se urmareste, starea cunoscuta a fiecarui dosar gasit si
 * istoricul modificarilor sesizate (pentru email si pentru afisare).
 */
class CreatePortalJustMonitorizariTable extends Migration
{
    public function up()
    {
        Schema::create('portal_just_monitorizari', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable()->index();
            $table->integer('user_id')->nullable()->index();

            // Ce se urmareste: un numar de dosar sau numele unei parti.
            $table->string('tip', 20)->default('dosar');
            $table->string('valoare', 200);
            $table->string('institutie', 100)->nullable();

            // Adresa la care se trimit modificarile (implicit, cea a utilizatorului).
            $table->string('email', 150);

            $table->boolean('activ')->default(true);
            $table->timestamp('ultima_verificare')->nullable();
            $table->timestamp('ultima_modificare')->nullable();
            $table->unsignedInteger('dosare_urmarite')->default(0);
            $table->string('ultima_eroare', 255)->nullable();

            $table->timestamps();

            $table->index(['company_id', 'tip', 'valoare'], 'pj_mon_client_tip_val');
        });

        Schema::create('portal_just_dosare', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable()->index();
            $table->unsignedInteger('monitorizare_id')->index();

            $table->string('numar', 100);
            $table->string('institutie', 100)->nullable();

            // Amprenta starii, ca sa se vada dintr-o privire daca s-a schimbat ceva.
            $table->string('amprenta', 40);
            $table->text('stare')->nullable();
            $table->timestamp('vazut_la')->nullable();

            $table->timestamps();

            $table->index(['monitorizare_id', 'numar'], 'pj_dos_mon_numar');
        });

        Schema::create('portal_just_modificari', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable()->index();
            $table->unsignedInteger('monitorizare_id')->index();

            $table->string('dosar_numar', 100);
            $table->string('institutie', 100)->nullable();
            $table->string('tip', 40);
            $table->text('descriere');
            $table->text('detalii')->nullable();

            $table->timestamp('notificat_la')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('portal_just_modificari');
        Schema::dropIfExists('portal_just_dosare');
        Schema::dropIfExists('portal_just_monitorizari');
    }
}
