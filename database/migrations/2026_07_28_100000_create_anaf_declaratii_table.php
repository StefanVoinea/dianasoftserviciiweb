<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anaf_declaratii', function (Blueprint $table) {
            $table->id();
            $table->string('cui', 20)->index();
            $table->string('den_firma')->nullable();
            $table->string('tip', 20)->index();
            $table->unsignedSmallInteger('luna')->nullable();
            $table->unsignedSmallInteger('anul')->nullable();
            $table->boolean('rectificativa')->default(false);

            $table->string('nume_fisier');
            $table->string('cale_xml')->nullable();
            $table->string('cale_pdf')->nullable();
            $table->string('cale_pdf_semnat')->nullable();
            $table->string('cale_recipisa')->nullable();

            // pas: incarcat -> validat|eroare_validare -> semnat -> depus -> finalizat
            $table->string('pas', 30)->default('incarcat')->index();
            $table->text('erori_validare')->nullable();
            $table->boolean('semnat')->default(false);

            $table->string('index_recipisa')->nullable()->index();
            $table->text('stare_declaratie')->nullable();
            $table->timestamp('data_depunere')->nullable();
            $table->timestamp('data_recipisa')->nullable();

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anaf_declaratii');
    }
};
