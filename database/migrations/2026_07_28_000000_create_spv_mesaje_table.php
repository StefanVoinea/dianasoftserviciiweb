<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pe servere tabelul poate exista deja, facut de mana dupa acest fisier.
        if (Schema::hasTable('spv_mesaje')) {
            return;
        }

        Schema::create('spv_mesaje', function (Blueprint $table) {
            $table->id();
            $table->string('mesaj_id')->unique();
            $table->string('cif', 20)->index();
            $table->string('tip', 60)->index();
            $table->text('detalii')->nullable();
            $table->string('id_solicitare')->nullable();
            $table->timestamp('data_creare')->nullable()->index();
            $table->string('cale_fisier')->nullable();
            $table->string('hash_fisier', 64)->nullable();
            $table->timestamp('descarcat_la')->nullable();
            $table->unsignedTinyInteger('incercari')->default(0);
            $table->text('ultima_eroare')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spv_mesaje');
    }
};
