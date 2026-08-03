<?php

namespace Tests\Unit;

use App\Models\AnafJurnal;
use App\Services\Anaf\Jurnal;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * Jurnalul scrie si cand cererea nu poarta un token al aplicatiei.
 *
 * Agentul de la client se legitimeaza cu codul lui de instalare, nu cu un token
 * Passport. Cand insemnarea in jurnal incerca sa afle cine e omul din spatele
 * cererii, Passport se oprea la citirea codului — „The JWT string must have two
 * dots" — si inrolarea intreaga cadea cu eroare de server, desi certificatele
 * fusesera deja inregistrate.
 *
 * O insemnare n-are voie sa dea peste cap lucrarea pe care doar o consemneaza.
 */
class JurnalFaraTokenTest extends TestCase
{
    protected const COMPANIE = 993;

    protected function tearDown(): void
    {
        AnafJurnal::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        Auth::forgetGuards();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    public function test_insemnarea_se_scrie_chiar_daca_tokenul_nu_e_al_aplicatiei(): void
    {
        /*
         * Se pune la mijloc chiar purtarea de pe server: gardul Passport, pus
         * sa citeasca drept JWT codul de instalare al agentului, se opreste cu
         * excepție. Aici nu se verifică biblioteca de jetoane, ci ce facem noi
         * când ea se supără.
         */
        Auth::shouldReceive('guard')
            ->with('api')
            ->andThrow(new \RuntimeException('The JWT string must have two dots'));

        Auth::shouldReceive('user')->andReturn(null);
        // Curatenia de la sfarsitul probei trece tot pe aici.
        Auth::shouldReceive('forgetGuards')->andReturnNull();

        ContextCompanie::pentru(self::COMPANIE, function () {
            Jurnal::scrie('certificat_inrolare', 'Un calculator s-a înrolat singur, prin tunel');
        });

        $insemnare = AnafJurnal::query()->toateCompaniile()
            ->where('company_id', self::COMPANIE)
            ->latest('id')
            ->first();

        $this->assertNotNull($insemnare, 'Însemnarea nu a fost scrisă.');
        $this->assertSame('certificat_inrolare', $insemnare->actiune);
        $this->assertNull($insemnare->user_id, 'Fără cineva autentificat, rândul rămâne fără nume.');
    }
}
