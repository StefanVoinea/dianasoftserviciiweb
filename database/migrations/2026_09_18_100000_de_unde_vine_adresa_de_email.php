<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * De unde se știe adresa de e-mail a unui contact.
 *
 * Listele CECCAR de firme dau adresa scrisă chiar de firmă. Lista experților
 * contabili n-o dă: acolo coloana se cheamă „Email (probabil)", iar adresa e
 * dedusă — dintr-un telefon comun cu o firmă, dintr-un nume care se potrivește
 * în alt registru. Coloana aceasta ține deducția, așa cum a spus-o fișierul.
 *
 * Goală înseamnă că adresa a venit scrisă în sursă. Plină înseamnă că cineva a
 * ghicit-o, și atunci omul care trimite trebuie să știe asta înainte, nu după
 * ce se întorc scrisorile.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('marketing_contacte', 'email_dedus')) {
            return;
        }

        Schema::table('marketing_contacte', function (Blueprint $tabel) {
            $tabel->string('email_dedus', 190)->nullable()->after('emailuri');
        });
    }

    public function down(): void
    {
        Schema::table('marketing_contacte', function (Blueprint $tabel) {
            $tabel->dropColumn('email_dedus');
        });
    }
};
