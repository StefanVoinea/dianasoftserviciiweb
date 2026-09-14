<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [2026-09-14] Luna de sfârșit, pentru cererea care cere un interval de luni.
 *
 * „NeconcordanteD394" e singurul tip de raport pe care ANAF nu-l dă pe o lună,
 * ci pe un interval: cere `lunai` și `lunas`, nu `luna`. Cererea trimisă cu o
 * singură lună se întorcea cu „Pentru tip raport= NeconcordanteD394 parametrii
 * cui, an, lunai si lunas sunt obligatorii".
 *
 * `luna` rămâne luna cererii, adică începutul intervalului la tipul acesta;
 * coloana nouă ține sfârșitul lui și stă goală la toate celelalte.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spv_solicitari', function (Blueprint $table) {
            if (!Schema::hasColumn('spv_solicitari', 'luna_sfarsit')) {
                $table->unsignedSmallInteger('luna_sfarsit')->nullable()->after('luna');
            }
        });
    }

    public function down(): void
    {
        Schema::table('spv_solicitari', function (Blueprint $table) {
            if (Schema::hasColumn('spv_solicitari', 'luna_sfarsit')) {
                $table->dropColumn('luna_sfarsit');
            }
        });
    }
};
