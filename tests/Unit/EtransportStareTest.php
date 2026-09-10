<?php

namespace Tests\Unit;

use App\Models\EtransportDeclaratie;
use App\Models\EtransportNotificare;
use App\Services\Anaf\Etransport\EtransportClient;
use App\Services\Anaf\Etransport\EtransportSincronizare;
use App\Services\Anaf\Etransport\StareDeclaratie;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * Starea unei declarații e-Transport depuse.
 *
 * Miezul: codul UIT vine de la încărcare și nu spune nimic despre soarta
 * declarației — ANAF scrie chiar în răspunsul de atunci că el „este valabil din
 * momentul in care apare ca valid dupa apelul de stare". Verdictul se citește
 * din `stare`: `ok`, `in prelucrare` sau `nok`, ultimul cu motivul scris.
 */
class EtransportStareTest extends TestCase
{
    /** Firma de probă, ca datele să nu se amestece cu ale vreunui client. */
    protected const FIRMA = 900051;

    protected function tearDown(): void
    {
        EtransportDeclaratie::query()->toateCompaniile()->where('company_id', self::FIRMA)->delete();
        EtransportNotificare::query()->toateCompaniile()->where('company_id', self::FIRMA)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /** O declarație depusă, care așteaptă verdictul. */
    protected function declaratie(array $campuri = []): EtransportDeclaratie
    {
        // `$campuri` intai: la „+" pe tablouri ramane cheia din stanga.
        return EtransportDeclaratie::create($campuri + [
            'stare' => 'depusa',
            'cif_declarant' => '15196216',
            'referinta_interna' => 'Retur de probă',
            'tip_operatiune' => 20,
            'index_incarcare' => '19196306666',
            'uit' => '5K8H075149740174',
            'depusa_la' => now(),
        ]);
    }

    /** Serviciul, cu un ANAF mimat care întoarce răspunsul dat. */
    protected function stariCu(array $raspuns): StareDeclaratie
    {
        $this->mock(EtransportClient::class, function ($mock) use ($raspuns) {
            $mock->shouldReceive('stareMesaj')->andReturn($raspuns);
        });

        return $this->app->make(StareDeclaratie::class);
    }

    public function test_starea_ok_valideaza_declaratia(): void
    {
        ContextCompanie::pentru(self::FIRMA, function () {
            $declaratie = $this->declaratie();

            $rezultat = $this->stariCu(['stare' => 'ok', 'ExecutionStatus' => 0])->verifica($declaratie);

            $this->assertSame('validata', $declaratie->fresh()->stare);
            $this->assertTrue($rezultat['schimbata']);
            $this->assertSame([], $rezultat['erori']);
        });
    }

    /**
     * Cazul din 10.09.2026: șase retururi arătau „Validată — are UIT", iar ANAF
     * le refuzase pentru data transportului. Răspunsul lui e „nok".
     */
    public function test_starea_nok_respinge_declaratia_cu_motivul_scris(): void
    {
        ContextCompanie::pentru(self::FIRMA, function () {
            $declaratie = $this->declaratie();
            $motiv = 'Data declarată a transportului trebuie să fie în intervalul 10 - 13.09.2026 (data transmisă: 14.09.2026).';

            $rezultat = $this->stariCu([
                'stare' => 'nok',
                'Errors' => [['errorMessage' => $motiv]],
                'ExecutionStatus' => 1,
            ])->verifica($declaratie);

            $this->assertSame('respinsa', $declaratie->fresh()->stare);
            $this->assertTrue($rezultat['schimbata']);
            $this->assertSame([$motiv], $rezultat['erori']);
            $this->assertSame([$motiv], $declaratie->fresh()->erori_anaf);
        });
    }

    /** „XML cu erori nepreluat de sistem" e tot o respingere, altfel scrisă. */
    public function test_xmlul_nepreluat_respinge_declaratia(): void
    {
        ContextCompanie::pentru(self::FIRMA, function () {
            $declaratie = $this->declaratie();

            $this->stariCu([
                'stare' => 'XML cu erori nepreluat de sistem',
                'Errors' => [['errorMessage' => 'Structura XML invalida']],
            ])->verifica($declaratie);

            $this->assertSame('respinsa', $declaratie->fresh()->stare);
        });
    }

    /** Cât timp ANAF prelucrează, declarația rămâne cum era. */
    public function test_in_prelucrare_lasa_declaratia_depusa(): void
    {
        ContextCompanie::pentru(self::FIRMA, function () {
            $declaratie = $this->declaratie();

            $rezultat = $this->stariCu(['stare' => 'in prelucrare'])->verifica($declaratie);

            $this->assertSame('depusa', $declaratie->fresh()->stare);
            $this->assertFalse($rezultat['schimbata']);
        });
    }

    /** Un cod UIT în răspuns nu ține loc de verdict: hotărăște `stare`. */
    public function test_codul_uit_nu_valideaza_singur_declaratia(): void
    {
        ContextCompanie::pentru(self::FIRMA, function () {
            $declaratie = $this->declaratie();

            $this->stariCu([
                'stare' => 'nok',
                'UIT' => '5K8H075149740174',
                'Errors' => [['errorMessage' => 'Data transportului e în afara intervalului']],
            ])->verifica($declaratie);

            $this->assertSame('respinsa', $declaratie->fresh()->stare);
        });
    }

    /** Notificarea cu eroare îndreaptă declarația, fără nicio interogare de stare. */
    public function test_notificarea_cu_eroare_respinge_declaratia(): void
    {
        ContextCompanie::pentru(self::FIRMA, function () {
            $declaratie = $this->declaratie(['stare' => 'validata']);

            $notificare = EtransportNotificare::create([
                'uit' => $declaratie->uit,
                'tip' => 'NOT',
                'stare' => 'ERR',
                'id_incarcare' => $declaratie->index_incarcare,
                'mesaje' => [['tip' => 'ERR', 'mesaj' => 'Data declarată a transportului este greșită.']],
            ]);

            $indreptata = $this->app->make(StareDeclaratie::class)->dupaNotificare($notificare);

            $this->assertNotNull($indreptata);
            $this->assertSame('respinsa', $declaratie->fresh()->stare);
            $this->assertSame(
                ['Data declarată a transportului este greșită.'],
                $declaratie->fresh()->erori_anaf
            );
        });
    }

    /** O ciornă nedepusă nu se atinge: notificarea e a altei depuneri. */
    public function test_notificarea_nu_atinge_o_ciorna(): void
    {
        ContextCompanie::pentru(self::FIRMA, function () {
            $ciorna = $this->declaratie(['stare' => 'ciorna', 'index_incarcare' => null, 'uit' => 'UIT-PROBA-9']);

            $notificare = EtransportNotificare::create([
                'uit' => 'UIT-PROBA-9', 'tip' => 'NOT', 'stare' => 'ERR', 'id_incarcare' => null,
            ]);

            $this->assertNull($this->app->make(StareDeclaratie::class)->dupaNotificare($notificare));
            $this->assertSame('ciorna', $ciorna->fresh()->stare);
        });
    }

    /** „Preia" din fila Notificări îndreaptă declarațiile pe care le găsește. */
    public function test_preluarea_notificarilor_indreapta_declaratiile(): void
    {
        ContextCompanie::pentru(self::FIRMA, function () {
            $respinsa = $this->declaratie(['stare' => 'validata', 'index_incarcare' => '111', 'uit' => 'UIT-P-1']);
            $buna = $this->declaratie(['stare' => 'depusa', 'index_incarcare' => '112', 'uit' => 'UIT-P-2']);

            $this->mock(EtransportClient::class, function ($mock) {
                $mock->shouldReceive('lista')->andReturn(['mesaje' => [
                    [
                        'uit' => 'UIT-P-1', 'tip' => 'NOT', 'stare' => 'ERR', 'id_incarcare' => '111',
                        'mesaje' => [['tip' => 'ERR', 'mesaj' => 'Data transportului e greșită.']],
                    ],
                    ['uit' => 'UIT-P-2', 'tip' => 'NOT', 'stare' => 'OK', 'id_incarcare' => '112'],
                ]]);
            });

            $rezultat = $this->app->make(EtransportSincronizare::class)->preia(30, '15196216');

            $this->assertSame(2, $rezultat['declaratii_indreptate']);
            $this->assertSame('respinsa', $respinsa->fresh()->stare);
            $this->assertSame('validata', $buna->fresh()->stare);
        });
    }

    /** O declarație respinsă nu mai poate trimite codul UIT șoferului. */
    public function test_declaratia_respinsa_ramane_fara_uit_de_trimis(): void
    {
        ContextCompanie::pentru(self::FIRMA, function () {
            $declaratie = $this->declaratie();

            $this->stariCu([
                'stare' => 'nok',
                'Errors' => [['errorMessage' => 'Refuzată']],
            ])->verifica($declaratie);

            $proaspata = $declaratie->fresh();

            // Codul ramane scris, ca sa se stie despre ce depunere e vorba, dar
            // starea e cea care hotaraste daca pleaca mai departe.
            $this->assertSame('respinsa', $proaspata->stare);
            $this->assertNotSame('validata', $proaspata->stare);
            $this->assertTrue($proaspata->poate_fi_modificata, 'O declarație respinsă se poate îndrepta și redepune');
        });
    }
}
