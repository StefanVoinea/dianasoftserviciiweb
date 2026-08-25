<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Gestiunile (magazinele) clientului, pentru Dispecer e-Transport.
 *
 * Furnizorul scrie în distinta D01 doar codul lui de magazin (NEG*); aici stă
 * corespondența cu denumirea din contabilitatea clientului și prescurtarea
 * folosită pe foile formularului pentru transportator. Lista pornește de la
 * fișierul gestiunilor Emporio și crește din aplicație: la un cod nou întâlnit
 * la import, utilizatorul e întrebat cum se numește gestiunea.
 */
class CreeazaGestiunileEtransport extends Migration
{
    /** Gestiunile Emporio de la pornire, din fișierul primit de la client. */
    protected const GESTIUNI = [
        ['548 Magheru', '0548', 'NEG0000548', 'Magheru'],
        ['654 Oradea', '2313', 'NEG0002313', 'Oradea'],
        ['1437 Suceava Iulius Mall', '1437', 'NEG0001437', 'Suceava Iulius Mall'],
        ['1438 Timisoara Iulius Mall', '1438', 'NEG0001438', 'Timisoara Iulius Mall'],
        ['1474 Brasov Coresi', '1474', 'NEG0001474', 'Brasov Coresi'],
        ['1628 Sun Plazza', '1628', 'NEG0001628', 'Sun Plaza'],
        ['2276 Pitesti', '2276', 'NEG0002276', 'Pitesti'],
        ['2360 Moldova Mall Iasi', '2360', 'NEG0002360', 'Iasi Moldova Mall'],
        ['2450 Electroputere Craiova', '2450', 'NEG0002450', 'Electroputere Craiova'],
        ['2486 Iasi Iulius Mall', '2486', 'NEG0002486', 'Iasi Iulius Mall'],
        ['2521 Baia Mare', '2521', 'NEG0002521', 'Baia Mare'],
    ];

    public function up(): void
    {
        if (!Schema::hasTable('etransport_gestiuni')) {
            Schema::create('etransport_gestiuni', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable()->index();

                // Codul din contabilitatea clientului (poate incepe cu 0).
                $table->string('cod', 20)->nullable();

                // Codul magazinului la furnizor, cel din distinta D01 (NEG*).
                $table->string('cod_furnizor', 30)->index();

                $table->string('denumire', 200);
                $table->string('prescurtare', 100)->nullable();

                $table->timestamps();
            });
        }

        $this->gestiunileEmporio();
    }

    public function down(): void
    {
        Schema::dropIfExists('etransport_gestiuni');
    }

    /** Umple gestiunile Emporio, dacă clientul există în această bază. */
    protected function gestiunileEmporio(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasTable('company_user')) {
            return;
        }

        $user = DB::table('users')->where('email', 'emporiocom@yahoo.com')->first();
        $companie = $user ? DB::table('company_user')->where('user_id', $user->id)->value('company_id') : null;

        if (!$companie || DB::table('etransport_gestiuni')->where('company_id', $companie)->exists()) {
            return;
        }

        foreach (self::GESTIUNI as [$denumire, $cod, $codFurnizor, $prescurtare]) {
            DB::table('etransport_gestiuni')->insert([
                'company_id' => $companie,
                'cod' => $cod,
                'cod_furnizor' => $codFurnizor,
                'denumire' => $denumire,
                'prescurtare' => $prescurtare,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
