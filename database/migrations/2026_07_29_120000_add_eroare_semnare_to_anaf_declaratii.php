<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Motivul pentru care semnarea nu a reusit se pastreaza separat de erorile de
 * validare: sunt esecuri diferite, la pasi diferiti, iar unul nu trebuie sa-l
 * stearga pe celalalt.
 */
class AddEroareSemnareToAnafDeclaratii extends Migration
{
    public function up()
    {
        // Pe servere coloana poate exista deja, pusa de mana dupa acest fisier.
        if (Schema::hasColumn('anaf_declaratii', 'eroare_semnare')) {
            return;
        }

        Schema::table('anaf_declaratii', function (Blueprint $table) {
            $table->text('eroare_semnare')->nullable()->after('erori_validare');
        });
    }

    public function down()
    {
        if (!Schema::hasColumn('anaf_declaratii', 'eroare_semnare')) {
            return;
        }

        Schema::table('anaf_declaratii', function (Blueprint $table) {
            $table->dropColumn('eroare_semnare');
        });
    }
}
