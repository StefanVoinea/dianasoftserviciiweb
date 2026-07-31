<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Alerta pe telefon se urmareste separat de email: daca una dintre ele esueaza,
 * doar aceea se reia, fara sa se repete cealalta.
 */
class AddPushLaToPortalJustModificari extends Migration
{
    public function up()
    {
        Schema::table('portal_just_modificari', function (Blueprint $table) {
            $table->timestamp('push_la')->nullable()->after('notificat_la');
        });
    }

    public function down()
    {
        Schema::table('portal_just_modificari', function (Blueprint $table) {
            $table->dropColumn('push_la');
        });
    }
}
