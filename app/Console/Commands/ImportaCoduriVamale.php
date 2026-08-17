<?php

namespace App\Console\Commands;

use App\Models\EtransportCodVamal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Încarcă nomenclatorul codurilor vamale pentru e-Transport.
 *
 * Fără argument se ia fișierul din depozit (database/nomenclatoare), așa că pe
 * server e de ajuns „php artisan anaf:coduri-vamale" după instalare. Cu un
 * fișier CSV primit de la client (cod;denumire;denumire_scurta), nomenclatorul
 * se înlocuiește cu acela.
 *
 * CSV-ul din depozit vine din NC8 oficial INS (intrastat.ro, foaia NC8_2026,
 * toate cele 9797 de coduri de 8 cifre), cu denumirea compusă din lanțul
 * ierarhic al nomenclatorului. La ediția pe anul următor se ia nomenclatoare.xls
 * de pe intrastat.ro și se regenerează CSV-ul.
 */
class ImportaCoduriVamale extends Command
{
    protected $signature = 'anaf:coduri-vamale {fisier? : CSV cu separator ; (implicit cel din depozit)}';

    protected $description = 'Încarcă nomenclatorul codurilor vamale pentru declarațiile e-Transport';

    public function handle(): int
    {
        $fisier = $this->argument('fisier') ?: database_path('nomenclatoare/coduri_vamale.csv');

        if (!is_file($fisier)) {
            $this->error('Fișierul nu există: ' . $fisier);

            return 1;
        }

        $sursa = fopen($fisier, 'r');
        $antet = fgetcsv($sursa, 0, ';');

        if (!$antet || !in_array('cod', $antet, true)) {
            $this->error('Fișierul nu are antetul așteptat (cod;denumire;denumire_scurta).');

            return 1;
        }

        $coloane = array_flip($antet);
        $randuri = [];
        $total = 0;

        DB::table('etransport_coduri_vamale')->truncate();

        while (($rand = fgetcsv($sursa, 0, ';')) !== false) {
            $cod = preg_replace('/\D/', '', $rand[$coloane['cod']] ?? '');

            if ($cod === '') {
                continue;
            }

            $randuri[] = [
                'cod' => str_pad($cod, 8, '0', STR_PAD_LEFT),
                'denumire' => $rand[$coloane['denumire']] ?? '',
                'denumire_scurta' => $rand[$coloane['denumire_scurta']] ?? null,
            ];

            if (count($randuri) >= 500) {
                EtransportCodVamal::insert($randuri);
                $total += count($randuri);
                $randuri = [];
            }
        }

        fclose($sursa);

        if ($randuri !== []) {
            EtransportCodVamal::insert($randuri);
            $total += count($randuri);
        }

        $this->info('Nomenclator încărcat: ' . $total . ' coduri vamale.');

        return 0;
    }
}
