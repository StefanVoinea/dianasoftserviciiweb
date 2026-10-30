<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Gestiunea NEG0002534 (Mega Mall București) a lui Emporio Com, îndreptată.
 *
 * La prima ei apariție, fereastra „Gestiune nouă" a propus ca denumire numele
 * găsit în distinta D01 — „S.C. EMPORIO COM SRL", numele firmei, nu al
 * magazinului —, iar numele adevărat a ajuns în prescurtare. De atunci, fiecare
 * ciornă a magazinului apărea ca „S.C. EMPORIO COM SRL". Se scrie ca la
 * celelalte gestiuni: codul urmat de nume.
 *
 * Ciornele nedepuse ale magazinului primesc numele îndreptat și adresa din
 * declarația lui validată; cele depuse rămân cum le-a primit ANAF.
 *
 * Se atinge numai rândul greșit: dacă între timp a fost îndreptat de mână, nu
 * se face nimic.
 */
return new class extends Migration
{
    public function up(): void
    {
        $gestiuni = DB::table('etransport_gestiuni')
            ->where('cod_furnizor', 'NEG0002534')
            ->where('denumire', 'S.C. EMPORIO COM SRL')
            ->get();

        foreach ($gestiuni as $gestiune) {
            DB::table('etransport_gestiuni')->where('id', $gestiune->id)->update([
                'denumire' => '2534 MEGA MALL BUCURESTI',
                'cod' => $gestiune->cod ?: '2534',
                'prescurtare' => 'MEGA MALL BUCURESTI',
                'updated_at' => now(),
            ]);

            $validata = DB::table('etransport_declaratii')
                ->where('company_id', $gestiune->company_id)
                ->where('stare', 'validata')
                ->where('loc_final->magazin_cod', 'NEG0002534')
                ->orderByDesc('id')
                ->first();

            $adresa = $validata ? (array) json_decode((string) $validata->loc_final, true) : [];
            $campuri = ['cod_judet', 'localitate', 'strada', 'numar', 'cod_postal', 'alte_info'];
            $adresa = trim((string) ($adresa['strada'] ?? '')) !== ''
                ? array_intersect_key($adresa, array_flip($campuri))
                : [];

            $ciorne = DB::table('etransport_declaratii')
                ->where('company_id', $gestiune->company_id)
                ->where('stare', 'ciorna')
                ->where('loc_final->magazin_cod', 'NEG0002534')
                ->get();

            foreach ($ciorne as $ciorna) {
                $loc = (array) json_decode((string) $ciorna->loc_final, true);
                $loc = ['magazin_denumire' => '2534 MEGA MALL BUCURESTI']
                    + array_diff_key($loc, array_flip(array_merge($campuri, ['magazin_denumire'])))
                    + ($adresa ?: array_intersect_key($loc, array_flip($campuri)));

                DB::table('etransport_declaratii')->where('id', $ciorna->id)->update([
                    'loc_final' => json_encode($loc, JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Indreptarea unei greseli de date nu se desface.
    }
};
