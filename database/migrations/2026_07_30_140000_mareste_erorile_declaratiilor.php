<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Erorile validatorului nu incap intr-un TEXT.
 *
 * Un D406/SAF-T respins poate intoarce sute de mii de caractere: validatorul
 * scrie cate un rand pentru fiecare tranzactie gresita, iar un an de contabilitate
 * are zeci de mii. TEXT tine 65.535 de octeti, deci salvarea cadea cu „Data too
 * long”, iar declaratia ramanea fara nicio urma a motivului pentru care a picat.
 *
 * Se face cu SQL direct: schimbarea tipului unei coloane prin Schema cere
 * doctrine/dbal, care nu e instalat aici.
 */
class MaresteErorileDeclaratiilor extends Migration
{
    /** Coloanele care primesc texte venite de la ANAF, de lungime necunoscuta. */
    protected const COLOANE = ['erori_validare', 'eroare_semnare', 'stare_declaratie'];

    public function up(): void
    {
        foreach (self::COLOANE as $coloana) {
            DB::statement('ALTER TABLE `anaf_declaratii` MODIFY `' . $coloana . '` LONGTEXT NULL');
        }
    }

    public function down(): void
    {
        foreach (self::COLOANE as $coloana) {
            DB::statement('ALTER TABLE `anaf_declaratii` MODIFY `' . $coloana . '` TEXT NULL');
        }
    }
}
