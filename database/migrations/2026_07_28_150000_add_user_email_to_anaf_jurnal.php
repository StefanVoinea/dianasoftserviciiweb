<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pe servere coloana poate exista deja, pusa de mana dupa acest fisier.
        if (Schema::hasColumn('anaf_jurnal', 'user_email')) {
            return;
        }

        Schema::table('anaf_jurnal', function (Blueprint $table) {
            $table->string('user_email')->nullable()->after('user_nume');
        });

        // Completeaza adresele pentru intrarile existente, unde utilizatorul mai exista.
        if (Schema::hasTable('users')) {
            DB::table('anaf_jurnal')
                ->join('users', 'users.id', '=', 'anaf_jurnal.user_id')
                ->update(['anaf_jurnal.user_email' => DB::raw('users.email')]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('anaf_jurnal', 'user_email')) {
            return;
        }

        Schema::table('anaf_jurnal', function (Blueprint $table) {
            $table->dropColumn('user_email');
        });
    }
};
