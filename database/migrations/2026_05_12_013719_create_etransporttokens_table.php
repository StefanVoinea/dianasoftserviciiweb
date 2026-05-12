<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtransporttokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etransporttokens', function (Blueprint $table) {
            $table->id();
            $table->string('cui', 13); // CIF - max 13 digits
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->date('data_obtinerii')->nullable();
            $table->date('data_expirare')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->timestamps();

            $table->index(['cui', 'company_id']);
            $table->index('data_expirare');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etransporttokens');
    }
}
