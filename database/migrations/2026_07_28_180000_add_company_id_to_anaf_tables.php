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
 *
 * Fiecare pas se face doar dacă mai e de făcut: pe servere, o parte din coloane
 * și din indici au fost puși de mână după acest fișier, iar datele existente nu
 * se ating — completarea cu compania implicită se face numai la coloanele
 * adăugate acum.
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
            if (Schema::hasColumn($tabela, 'company_id')) {
                continue;
            }

            Schema::table($tabela, function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index()->after('id');
            });

            if ($implicit !== null) {
                DB::table($tabela)->update(['company_id' => $implicit]);
            }
        }

        // Unicitatea trebuie să fie per client, nu globală.
        $this->unicPeCompanie('spv_mesaje', 'spv_mesaje_mesaj_id_unique', ['company_id', 'mesaj_id']);
        $this->unicPeCompanie('anaf_certificate', 'anaf_certificate_thumbprint_unique', ['company_id', 'thumbprint']);
        $this->unicPeCompanie('anaf_societati', 'anaf_societati_cif_unique', ['company_id', 'cif']);
        $this->unicPeCompanie('vector_fiscal', 'vector_fiscal_cui_unique', ['company_id', 'cui']);
        $this->unicPeCompanie(
            'certificat_abonati',
            'certificat_abonati_email_certificat_id_unique',
            ['company_id', 'email', 'certificat_id'],
            'abonati_companie_email_certificat'
        );
        $this->unicPeCompanie(
            'certificat_utilizatori',
            'certificat_utilizatori_certificat_id_email_unique',
            ['company_id', 'certificat_id', 'email'],
            'utilizatori_companie_certificat_email'
        );
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

    /**
     * Muta unicitatea de pe indicele global pe cel cu company_id, fara sa se
     * impiedice de ce s-a facut deja de mana: indicele vechi se lasa doar daca
     * mai exista, iar cel nou se face doar daca lipseste.
     */
    protected function unicPeCompanie(string $tabela, string $indiceVechi, array $coloane, ?string $numeNou = null): void
    {
        $numeNou = $numeNou ?: $tabela . '_' . implode('_', $coloane) . '_unique';

        if ($this->areIndicele($tabela, $indiceVechi)) {
            Schema::table($tabela, function (Blueprint $table) use ($indiceVechi) {
                $table->dropUnique($indiceVechi);
            });
        }

        if (!$this->areIndicele($tabela, $numeNou)) {
            Schema::table($tabela, function (Blueprint $table) use ($coloane, $numeNou) {
                $table->unique($coloane, $numeNou);
            });
        }
    }

    protected function areIndicele(string $tabela, string $indice): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            // Pe alte motoare nu s-a umblat de mana; se merge pe drumul obisnuit.
            return false;
        }

        return DB::select('SHOW INDEX FROM `' . $tabela . '` WHERE Key_name = ?', [$indice]) !== [];
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
