<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Notificările e-Transport preluate de la ANAF, cu structura din documentația
 * serviciului „lista”.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etransport_notificari', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();

            $table->string('uit', 40)->nullable()->index();
            $table->string('id_incarcare', 40)->nullable()->index();
            // NOT=notificare, COR=corectie, DEL=stergere, CON=confirmare, MVH=modificare vehicul
            $table->string('tip', 5)->nullable()->index();
            $table->string('stare', 5)->nullable()->index();   // OK sau ERR
            $table->string('cod_decl', 20)->nullable()->index();
            $table->string('ref_decl')->nullable();
            $table->string('post_avarie', 2)->nullable();
            $table->string('sursa', 2)->nullable();             // A=api, I=interfata web

            $table->unsignedSmallInteger('tip_op')->nullable(); // 10=AIC, 20=LIC, 40=IMP, 50=EXP...
            $table->date('data_transp')->nullable()->index();
            $table->timestamp('data_creare')->nullable()->index();
            $table->timestamp('data_modif')->nullable();

            // Partener comercial
            $table->string('pc_tara', 2)->nullable();
            $table->string('pc_cod', 20)->nullable();
            $table->string('pc_den')->nullable();

            // Organizator transport / transportator
            $table->string('tr_tara', 2)->nullable();
            $table->string('tr_cod', 20)->nullable();
            $table->string('tr_den')->nullable();

            $table->string('nr_veh', 20)->nullable();
            $table->string('nr_rem1', 20)->nullable();
            $table->string('nr_rem2', 20)->nullable();

            $table->unsignedInteger('nr_linii')->nullable();
            $table->decimal('gr_tot_neta', 15, 3)->nullable();
            $table->decimal('gr_tot_bruta', 15, 3)->nullable();
            $table->decimal('val_tot', 15, 2)->nullable();

            // Structuri care rămân ca atare: modificări de vehicul, confirmare, mesaje
            $table->text('modif_veh')->nullable();
            $table->text('confirmare')->nullable();
            $table->text('mesaje')->nullable();

            $table->unsignedBigInteger('certificat_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamps();

            // Aceeași notificare nu se dublează la reinterogare
            $table->unique(['company_id', 'uit', 'tip', 'id_incarcare'], 'etransport_unic');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etransport_notificari');
    }
};
