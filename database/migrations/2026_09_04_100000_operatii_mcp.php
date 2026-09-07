<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [2026-09-01] Evidenta modificarilor facute prin accesul MCP.
 *
 * Tabela `activities` inregistreaza deja fiecare rand modificat, cu before/after.
 * Asta grupeaza randurile unei modificari in MASA intr-o singura operatie, ca sa
 * poata fi vazuta si anulata ca un tot: "a schimbat aliajul pe 1.133 de articole"
 * in loc de 1.133 de intrari separate.
 *
 * `stare`: pregatita (dry-run, asteapta confirmare) | aplicata | anulata
 * `randuri`: JSON cu id-ul fiecarui rand si valorile dinainte, pentru anulare.
 */
class OperatiiMcp extends Migration
{
    public function up()
    {
        if (Schema::hasTable("mcp_operatii")) {
            return;
        }
        Schema::create("mcp_operatii", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("company_id")->nullable();
            $table->unsignedInteger("user_id")->nullable();
            $table->string("token", 64)->unique();       // confirmarea intre dry-run si aplicare
            $table->string("operatie", 20);              // modifica | adauga
            $table->string("tabela", 64);
            $table->text("descriere")->nullable();       // ce a cerut utilizatorul, in cuvinte
            $table->text("filtru")->nullable();          // JSON: pe ce s-a filtrat
            $table->text("valori")->nullable();          // JSON: ce s-a pus
            $table->unsignedInteger("nr_randuri")->default(0);
            $table->longText("randuri")->nullable();     // JSON: [{id, inainte:{...}}]
            $table->string("stare", 20)->default("pregatita");
            $table->timestamp("aplicata_la")->nullable();
            $table->timestamp("anulata_la")->nullable();
            $table->timestamps();

            $table->index(["tabela", "stare"]);
            $table->index("user_id");
        });
    }

    public function down()
    {
        Schema::dropIfExists("mcp_operatii");
    }
}
