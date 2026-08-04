<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Unde sta raspunsul la o solicitare SPV, acum ca nu mai sta pe server.
 *
 * Documentul vine de la ANAF drept in arhiva de pe calculatorul clientului, iar
 * aici ramane doar calea sub care l-a pus programul local — atat cat sa stim de
 * unde sa-l cerem cand omul apasa pe el. Coloana veche, cale_fisier, ramane
 * pentru documentele aduse inainte, care sunt inca pe server.
 */
class AdaugaArhivaCaleLaSolicitari extends Migration
{
    public function up(): void
    {
        // Pe servere coloana poate exista deja, pusa de mana.
        Schema::table('spv_solicitari', function (Blueprint $table) {
            if (!Schema::hasColumn('spv_solicitari', 'arhiva_cale')) {
                $table->string('arhiva_cale', 500)->nullable()->after('cale_fisier');
            }
        });
    }

    public function down(): void
    {
        Schema::table('spv_solicitari', function (Blueprint $table) {
            if (Schema::hasColumn('spv_solicitari', 'arhiva_cale')) {
                $table->dropColumn('arhiva_cale');
            }
        });
    }
}
