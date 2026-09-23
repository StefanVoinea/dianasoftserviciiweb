<?php

namespace Tests\Unit;

use App\Models\AnafJurnal;
use App\Services\Anaf\Bridge\Licente;
use App\Services\Anaf\Jurnal;
use App\Support\ContextCompanie;
use Illuminate\Http\Request;
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

    /**
     * [2026-09-23] Jetonul nostru de înrolare are și el două puncte.
     *
     * „i1.<date>.<semnătură>" arată întocmai ca un JWT, așa că Passport îl lua
     * drept token al aplicației. Nu-i recunoștea semnătura, raporta „acces
     * refuzat" — și abia apoi tăcea. Înrolarea se făcea cum trebuie, dar la
     * fiecare calculator nou pleca o înștiințare de eroare către noi, iar
     * erorile adevărate se pierdeau printre ele.
     *
     * Aici se probează că Passport nici nu mai e întrebat: gardul e pus să
     * strige dacă cineva bate la ușa lui.
     */
    public function test_jetonul_de_inrolare_nu_ajunge_la_passport(): void
    {
        $jeton = app(Licente::class)->jetonInrolare(self::COMPANIE);

        // Are două puncte, ca un JWT: tocmai de aici venea încurcătura.
        $this->assertSame(2, substr_count($jeton, '.'), 'jetonul de înrolare arată ca un JWT');

        $cerere = Request::create('/api/punte/agent/inrolare', 'POST');
        $cerere->headers->set('Authorization', 'Bearer ' . $jeton);
        $this->app->instance('request', $cerere);

        /*
         * Passport se cheamă „guard('api')": dacă se ajunge la el, proba cade.
         * „Auth::user()" poate fi întrebat liniștit — el caută o sesiune, iar
         * pe cererea agentului nu e nicio sesiune de găsit.
         */
        Auth::shouldReceive('guard')->never();
        Auth::shouldReceive('user')->andReturn(null);
        Auth::shouldReceive('forgetGuards')->andReturnNull();

        ContextCompanie::pentru(self::COMPANIE, function () {
            Jurnal::scrie('certificat_inrolare', 'Un calculator s-a înrolat singur, prin tunel');
        });

        $insemnare = AnafJurnal::query()->toateCompaniile()
            ->where('company_id', self::COMPANIE)
            ->latest('id')
            ->first();

        $this->assertNotNull($insemnare, 'Însemnarea trebuia scrisă.');
        $this->assertNull($insemnare->user_id);
    }

    /**
     * Și pe orice altă cale a punții: acolo nu vine niciodată un token al
     * aplicației — nici de la agent, nici de la server.
     *
     * @test
     */
    public function nici_pe_celelalte_cai_ale_puntii_nu_se_intreaba_passport(): void
    {
        /*
         * Passport se cheamă „guard('api')": dacă se ajunge la el, proba cade.
         * „Auth::user()" poate fi întrebat liniștit — el caută o sesiune, iar
         * pe cererea agentului nu e nicio sesiune de găsit.
         */
        Auth::shouldReceive('guard')->never();
        Auth::shouldReceive('user')->andReturn(null);
        Auth::shouldReceive('forgetGuards')->andReturnNull();

        foreach (['/api/punte/agent/asteapta', '/api/punte/agent/licenta', '/api/punte/7/spv/listaMesaje'] as $cale) {
            $cerere = Request::create($cale, 'POST');
            $cerere->headers->set('Authorization', 'Bearer aaaa.bbbb.cccc');
            $this->app->instance('request', $cerere);

            ContextCompanie::pentru(self::COMPANIE, function () use ($cale) {
                Jurnal::scrie('certificat_inrolare', 'Cerere pe ' . $cale);
            });
        }

        $this->assertSame(
            3,
            AnafJurnal::query()->toateCompaniile()->where('company_id', self::COMPANIE)->count(),
            'toate cele trei însemnări trebuiau scrise'
        );
    }
}
