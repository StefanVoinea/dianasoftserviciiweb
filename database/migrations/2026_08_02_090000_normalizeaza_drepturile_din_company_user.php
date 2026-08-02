<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pune la loc valorile implicite ale drepturilor din „company_user".
 *
 * Coloanele au fost adăugate cu implicit „nu", dar pe servere ele au ajuns
 * altfel: pe unul fără nicio valoare implicită — și atunci orice legătură
 * scrisă fără ele era refuzată de MySQL („Field 'poate_semna' doesn't have a
 * default value") — iar pe altul cu implicit „da", ceea ce e mai rău: fiecare
 * cont nou legat de o firmă ieșea administrator, cu drept de semnare și de
 * depunere, fără ca cineva să i le fi dat.
 *
 * Un drept nu se ia niciodată de la sine: implicit e „nu", peste tot.
 *
 * Rândurile deja scrise nu se ating — nu se poate ști care drept a fost dat
 * anume și care a venit din valoarea implicită greșită. Ele se verifică din
 * „Utilizatori", unde se și văd.
 */
class NormalizeazaDrepturileDinCompanyUser extends Migration
{
    protected const DREPTURI = ['administrator', 'poate_semna', 'poate_depune'];

    public function up(): void
    {
        foreach (self::DREPTURI as $drept) {
            if (!Schema::hasColumn('company_user', $drept)) {
                // Serverul pe care coloana lipsește cu totul o primește acum.
                Schema::table('company_user', function (Blueprint $table) use ($drept) {
                    $table->boolean($drept)->default(false);
                });

                continue;
            }

            if (DB::connection()->getDriverName() !== 'mysql') {
                continue;
            }

            DB::statement(
                'ALTER TABLE `company_user` MODIFY `' . $drept . '` TINYINT(1) NOT NULL DEFAULT 0'
            );
        }
    }

    /**
     * Nu se întoarce nimic: starea dinainte era chiar cea care strica lucrurile,
     * și diferea de la un server la altul.
     */
    public function down(): void
    {
    }
}
