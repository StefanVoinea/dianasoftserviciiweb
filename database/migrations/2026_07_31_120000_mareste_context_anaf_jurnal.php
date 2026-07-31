<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Contextul unei intrări din jurnal era TEXT (65.535 octeți).
 *
 * O declarație D406 respinsă la validare aduce zeci de mii de rânduri de erori,
 * iar scrierea în jurnal cădea cu „Data too long" — adică tocmai eșecul pe care
 * jurnalul trebuia să-l consemneze rămânea neconsemnat, iar încărcarea se
 * oprea cu 500. Se lărgește coloana; serviciul taie oricum valorile lungi.
 *
 * ALTER direct: doctrine/dbal nu e instalat, deci $table->change() nu merge.
 */
class MaresteContextAnafJurnal extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `anaf_jurnal` MODIFY `context` LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `anaf_jurnal` MODIFY `context` TEXT NULL');
    }
}
