<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Evidenta firmelor carora li se poate scrie despre aplicatii, si ce li s-a scris.
 *
 * Nu tine de nicio firma-client: e lista noastra, a celor care fac aplicatia, si
 * o vede numai administratorul aplicatiei. De aceea n-are „company_id" si nu
 * intra sub domeniile obisnuite.
 *
 * Doua lucruri stau scrise aici dinadins, si nu se pot ocoli:
 *
 * Dezabonarea. Fiecare contact isi are jetonul lui, iar din el se face legatura
 * pusa in fiecare scrisoare. Cine apasa acolo nu mai primeste nimic, niciodata,
 * fara sa fie nevoie sa scrie cuiva. Se tine si clipa, si — daca a spus-o —
 * pricina.
 *
 * Urma. Se scrie ce s-a trimis, cui si cand. Fara ea n-am putea nici sa nu
 * trimitem de doua ori acelasi lucru, nici sa raspundem cuiva care intreaba de
 * ce a primit o scrisoare de la noi.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('marketing_contacte')) {
            Schema::create('marketing_contacte', function (Blueprint $table) {
                $table->id();

                $table->string('denumire');
                $table->string('cui', 20)->nullable()->index();

                // Adresa la care se scrie. Una singura pe contact: o lista in
                // care aceeasi firma apare de trei ori inseamna trei scrisori.
                $table->string('email')->unique();

                // Celelalte adrese ale firmei, asa cum au venit din fisier.
                $table->text('emailuri')->nullable();

                $table->string('telefon', 100)->nullable();
                $table->string('judet', 50)->nullable()->index();
                $table->string('tip', 50)->nullable();
                $table->string('viza', 20)->nullable();
                $table->string('membru_din', 20)->nullable();

                // Din ce lista a venit: se raspunde cu ea cand cineva intreaba.
                $table->string('sursa')->nullable();

                $table->boolean('abonat')->default(true)->index();
                $table->timestamp('dezabonat_la')->nullable();
                $table->string('motiv_dezabonare', 500)->nullable();

                // Din el se face legatura de dezabonare pusa in scrisoare.
                $table->string('jeton', 64)->unique();

                $table->timestamp('ultima_trimitere_la')->nullable();
                $table->unsignedInteger('cate_trimiteri')->default(0);

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('marketing_trimiteri')) {
            Schema::create('marketing_trimiteri', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('contact_id')->index();
                $table->string('campanie')->nullable()->index();
                $table->string('subiect');

                $table->boolean('reusit')->default(true);
                $table->string('eroare', 500)->nullable();

                // Cine a apasat butonul de trimitere.
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('user_nume')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_trimiteri');
        Schema::dropIfExists('marketing_contacte');
    }
};
