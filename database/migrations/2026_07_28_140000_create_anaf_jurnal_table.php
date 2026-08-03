<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pe servere tabelul poate exista deja, facut de mana dupa acest fisier.
        if (Schema::hasTable('anaf_jurnal')) {
            return;
        }

        // Jurnal de activitate al modulului ANAF/SPV: cine ce a facut si cand.
        Schema::create('anaf_jurnal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            // Numele se pastreaza si el, ca jurnalul sa ramana lizibil chiar
            // daca utilizatorul e redenumit sau sters ulterior.
            $table->string('user_nume')->nullable();
            $table->string('actiune', 50)->index();
            $table->string('descriere', 500);
            $table->string('cif', 20)->nullable()->index();
            $table->unsignedBigInteger('certificat_id')->nullable()->index();
            $table->text('context')->nullable();
            $table->string('ip', 45)->nullable();
            $table->boolean('reusit')->default(true);
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anaf_jurnal');
    }
};
