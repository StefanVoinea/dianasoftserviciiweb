<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Certificatele digitale folosite pentru semnare / acces SPV.
        Schema::create('anaf_certificate', function (Blueprint $table) {
            $table->id();
            $table->string('thumbprint', 60)->unique();
            $table->string('serie', 80)->nullable()->index();
            $table->string('serie_anaf', 80)->nullable()->index();
            $table->string('cn')->nullable();
            $table->string('subiect')->nullable();
            $table->string('emitent')->nullable();
            $table->string('email')->nullable();
            $table->string('cnp', 20)->nullable();
            $table->timestamp('valabil_de_la')->nullable();
            $table->timestamp('valabil_pana_la')->nullable()->index();
            $table->timestamp('ultima_utilizare')->nullable();
            $table->boolean('activ')->default(true);
            $table->timestamp('avertizat_la')->nullable();
            $table->timestamps();
        });

        // Adresele care primesc avertizarea de expirare.
        Schema::create('certificat_abonati', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->unsignedBigInteger('certificat_id')->nullable()->index();
            $table->boolean('activ')->default(true);
            $table->timestamps();

            $table->unique(['email', 'certificat_id']);
        });

        // Certificatul cu care a fost obtinut fiecare document / entitate.
        foreach (['anaf_societati', 'spv_mesaje', 'spv_solicitari', 'anaf_declaratii'] as $tabela) {
            Schema::table($tabela, function (Blueprint $table) {
                $table->unsignedBigInteger('certificat_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['anaf_societati', 'spv_mesaje', 'spv_solicitari', 'anaf_declaratii'] as $tabela) {
            Schema::table($tabela, function (Blueprint $table) {
                $table->dropColumn('certificat_id');
            });
        }

        Schema::dropIfExists('certificat_abonati');
        Schema::dropIfExists('anaf_certificate');
    }
};
