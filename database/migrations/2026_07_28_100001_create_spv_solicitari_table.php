<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spv_solicitari', function (Blueprint $table) {
            $table->id();
            $table->string('cif', 20)->index();
            $table->string('den_firma')->nullable();
            $table->string('tip_document', 100)->index();
            $table->unsignedSmallInteger('an')->nullable();
            $table->unsignedSmallInteger('luna')->nullable();

            $table->string('id_solicitare')->nullable()->index();
            $table->timestamp('data_solicitarii')->nullable();
            $table->timestamp('data_afisare')->nullable();
            $table->string('mesaj_id')->nullable()->index();
            $table->string('cale_fisier')->nullable();
            $table->text('detalii')->nullable();
            $table->text('obs')->nullable();
            $table->string('stare', 30)->default('trimisa')->index();

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spv_solicitari');
    }
};
