<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cine are voie să semneze și cine să depună declarații.
 *
 * Sunt două lucruri deosebite, iar deosebirea contează: semnătura e a persoanei
 * care ține tokenul, iar depunerea nu se mai poate lua înapoi. De aceea stau
 * lângă „administrator", tot pe legătura dintre om și firmă — același om poate
 * avea drepturi diferite la clienți diferiți.
 */
class AddDrepturiDeclaratiiToCompanyUser extends Migration
{
    public function up(): void
    {
        Schema::table('company_user', function (Blueprint $table) {
            $table->boolean('poate_semna')->default(false)->after('administrator');
            $table->boolean('poate_depune')->default(false)->after('poate_semna');
        });
    }

    public function down(): void
    {
        Schema::table('company_user', function (Blueprint $table) {
            $table->dropColumn(['poate_semna', 'poate_depune']);
        });
    }
}
