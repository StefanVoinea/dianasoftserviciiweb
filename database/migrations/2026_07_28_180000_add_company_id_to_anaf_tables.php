<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Izolarea pe client (company) a modulului ANAF/SPV.
 *
 * Pe lângă coloana de apartenență, indicii unici globali devin unici pe companie:
 * doi clienți pot administra același CUI sau pot primi mesaje cu același id fără
 * să se blocheze reciproc.
 */
return new class extends Migration
{
    protected $tabele = [
        'anaf_certificate', 'anaf_societati', 'spv_mesaje', 'spv_solicitari',
        'anaf_declaratii', 'vector_fiscal', 'vector_spv', 'anaf_jurnal',
        'certificat_abonati', 'certificat_utilizatori',
    ];

    public function up(): void
    {
        $implicit = $this->companieImplicita();

        foreach ($this->tabele as $tabela) {
            Schema::table($tabela, function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index()->after('id');
            });

            if ($implicit !== null) {
                DB::table($tabela)->update(['company_id' => $implicit]);
            }
        }

        // Unicitatea trebuie să fie per client, nu globală.
        Schema::table('spv_mesaje', function (Blueprint $table) {
            $table->dropUnique('spv_mesaje_mesaj_id_unique');
            $table->unique(['company_id', 'mesaj_id']);
        });

        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->dropUnique('anaf_certificate_thumbprint_unique');
            $table->unique(['company_id', 'thumbprint']);
        });

        Schema::table('anaf_societati', function (Blueprint $table) {
            $table->dropUnique('anaf_societati_cif_unique');
            $table->unique(['company_id', 'cif']);
        });

        Schema::table('vector_fiscal', function (Blueprint $table) {
            $table->dropUnique('vector_fiscal_cui_unique');
            $table->unique(['company_id', 'cui']);
        });

        Schema::table('certificat_abonati', function (Blueprint $table) {
            $table->dropUnique('certificat_abonati_email_certificat_id_unique');
            $table->unique(['company_id', 'email', 'certificat_id'], 'abonati_companie_email_certificat');
        });

        Schema::table('certificat_utilizatori', function (Blueprint $table) {
            $table->dropUnique('certificat_utilizatori_certificat_id_email_unique');
            $table->unique(['company_id', 'certificat_id', 'email'], 'utilizatori_companie_certificat_email');
        });
    }

    public function down(): void
    {
        Schema::table('spv_mesaje', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'mesaj_id']);
            $table->unique('mesaj_id');
        });

        Schema::table('anaf_certificate', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'thumbprint']);
            $table->unique('thumbprint');
        });

        Schema::table('anaf_societati', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'cif']);
            $table->unique('cif');
        });

        Schema::table('vector_fiscal', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'cui']);
            $table->unique('cui');
        });

        Schema::table('certificat_abonati', function (Blueprint $table) {
            $table->dropUnique('abonati_companie_email_certificat');
            $table->unique(['email', 'certificat_id']);
        });

        Schema::table('certificat_utilizatori', function (Blueprint $table) {
            $table->dropUnique('utilizatori_companie_certificat_email');
            $table->unique(['certificat_id', 'email']);
        });

        foreach ($this->tabele as $tabela) {
            Schema::table($tabela, function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }
    }

    /** Datele existente aparțin companiei deja folosite în aplicație. */
    protected function companieImplicita(): ?int
    {
        if (!Schema::hasTable('companies')) {
            return null;
        }

        return optional(DB::table('companies')->orderBy('id')->first())->id;
    }
};
