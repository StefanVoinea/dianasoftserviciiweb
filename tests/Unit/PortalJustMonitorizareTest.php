<?php

namespace Tests\Unit;

use App\Mail\ModificariPortalJustEmail;
use App\Models\PortalJustDosar;
use App\Models\PortalJustModificare;
use App\Models\PortalJustMonitorizare;
use App\Services\Just\ImportMonitorizari;
use App\Services\Just\MonitorizarePortalJust;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Monitorizarea dosarelor: citirea listei din Excel și detectarea modificărilor
 * față de starea cunoscută.
 */
class PortalJustMonitorizareTest extends TestCase
{
    protected const COMPANIE = 991;

    protected function tearDown(): void
    {
        $monitorizari = PortalJustMonitorizare::query()->toateCompaniile()
            ->where('company_id', self::COMPANIE)->pluck('id');

        PortalJustModificare::query()->toateCompaniile()->whereIn('monitorizare_id', $monitorizari)->delete();
        PortalJustDosar::query()->toateCompaniile()->whereIn('monitorizare_id', $monitorizari)->delete();
        PortalJustMonitorizare::query()->toateCompaniile()->whereIn('id', $monitorizari)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function parser(): ImportMonitorizari
    {
        return $this->app->make(ImportMonitorizari::class);
    }

    protected function serviciu(): MonitorizarePortalJust
    {
        return $this->app->make(MonitorizarePortalJust::class);
    }

    /* ------------------------------------------------------------------ */
    /* Citirea fișierului Excel                                            */
    /* ------------------------------------------------------------------ */

    public function test_fisierul_cu_cap_de_tabel_este_citit_pe_coloane(): void
    {
        $randuri = [
            ['Numar dosar', 'Nume parte', 'Email', 'Instanta'],
            ['1234/3/2024', '', 'avocat@exemplu.ro', 'TribunalulBUCURESTI'],
            ['', 'SC EXEMPLU SRL', '', ''],
        ];

        $rezultat = $this->parser()->dinRanduri($randuri);

        $this->assertCount(2, $rezultat['intrari']);

        $this->assertSame('dosar', $rezultat['intrari'][0]['tip']);
        $this->assertSame('1234/3/2024', $rezultat['intrari'][0]['valoare']);
        $this->assertSame('avocat@exemplu.ro', $rezultat['intrari'][0]['email']);
        $this->assertSame('TribunalulBUCURESTI', $rezultat['intrari'][0]['institutie']);

        $this->assertSame('parte', $rezultat['intrari'][1]['tip']);
        $this->assertSame('SC EXEMPLU SRL', $rezultat['intrari'][1]['valoare']);
    }

    /** Capul de tabel se recunoaște și scris cu diacritice sau cu spații. */
    public function test_capul_de_tabel_se_recunoaste_indiferent_de_scriere(): void
    {
        $randuri = [
            ['Nr. Dosar', 'Instanța'],
            ['500/117/2023', 'TribunalulCLUJ'],
        ];

        $rezultat = $this->parser()->dinRanduri($randuri);

        $this->assertCount(1, $rezultat['intrari']);
        $this->assertSame('500/117/2023', $rezultat['intrari'][0]['valoare']);
        $this->assertSame('TribunalulCLUJ', $rezultat['intrari'][0]['institutie']);
    }

    /**
     * Fără cap de tabel, tipul se deduce din formă: numerele de dosar au un
     * format fix, restul sunt nume de părți.
     */
    public function test_fara_cap_de_tabel_tipul_se_deduce_din_forma_valorii(): void
    {
        $randuri = [
            ['1234/3/2024'],
            ['POPESCU ION', 'ion@exemplu.ro'],
            ['500/2004'],
            [''],
            ['SC ALFA SRL'],
        ];

        $rezultat = $this->parser()->dinRanduri($randuri);

        $tipuri = array_column($rezultat['intrari'], 'tip');
        $this->assertSame(['dosar', 'parte', 'dosar', 'parte'], $tipuri);

        // Adresa de pe aceeași linie devine destinatarul înștiințărilor.
        $this->assertSame('ion@exemplu.ro', $rezultat['intrari'][1]['email']);
    }

    public function test_liniile_repetate_se_iau_o_singura_data(): void
    {
        $randuri = [
            ['1234/3/2024'],
            ['1234/3/2024'],
            ['popescu ion'],
            ['POPESCU ION'],
        ];

        $rezultat = $this->parser()->dinRanduri($randuri);

        $this->assertCount(2, $rezultat['intrari']);
    }

    public function test_recunoasterea_numarului_de_dosar(): void
    {
        $parser = $this->parser();

        $this->assertTrue($parser->esteNumarDosar('1234/3/2024'));
        $this->assertTrue($parser->esteNumarDosar('50/117/2023'));
        $this->assertTrue($parser->esteNumarDosar('500/2004'));
        $this->assertFalse($parser->esteNumarDosar('POPESCU ION'));
        $this->assertFalse($parser->esteNumarDosar('SC 24/7 SRL'));
    }

    /* ------------------------------------------------------------------ */
    /* Detectarea modificărilor                                            */
    /* ------------------------------------------------------------------ */

    protected function monitorizare(): PortalJustMonitorizare
    {
        return PortalJustMonitorizare::create([
            'company_id' => self::COMPANIE,
            'tip' => PortalJustMonitorizare::TIP_DOSAR,
            'valoare' => '1234/3/2024',
            'email' => 'avocat@exemplu.ro',
            'activ' => true,
        ]);
    }

    /**
     * Plicul de răspuns cu un dosar în starea descrisă.
     *
     * @param array $termene fiecare ca [data, ora, solutie]
     */
    protected function raspunsCu(array $termene, string $stadiu = 'Fond', array $parti = ['POPESCU ION'], array $caiAtac = []): string
    {
        $xmlTermene = '';

        foreach ($termene as $termen) {
            $xmlTermene .= '<DosarSedinta>'
                . '<complet>C1</complet><data>' . $termen[0] . 'T00:00:00</data><ora>' . $termen[1] . '</ora>'
                . '<solutie>' . $termen[2] . '</solutie><solutieSumar />'
                . '<dataPronuntare xsi:nil="true" /><documentSedinta xsi:nil="true" />'
                . '<numarDocument /><dataDocument xsi:nil="true" />'
                . '</DosarSedinta>';
        }

        $xmlParti = '';

        foreach ($parti as $nume) {
            $xmlParti .= '<DosarParte><nume>' . $nume . '</nume><calitateParte>Reclamant</calitateParte></DosarParte>';
        }

        $xmlCai = '';

        foreach ($caiAtac as $cale) {
            $xmlCai .= '<DosarCaleAtac><dataDeclarare>' . $cale[1] . 'T00:00:00</dataDeclarare>'
                . '<parteDeclaratoare>POPESCU ION</parteDeclaratoare><tipCaleAtac>' . $cale[0] . '</tipCaleAtac>'
                . '</DosarCaleAtac>';
        }

        $dosar = '<Dosar>'
            . '<parti>' . $xmlParti . '</parti>'
            . '<sedinte>' . $xmlTermene . '</sedinte>'
            . '<caiAtac>' . $xmlCai . '</caiAtac>'
            . '<numar>1234/3/2024</numar><numarVechi /><data>2024-02-10T00:00:00</data>'
            . '<institutie>CurteadeApelBUCURESTI</institutie><departament>Secția a VII-a</departament>'
            . '<obiect>pretenții</obiect><dataModificare>2026-01-05T10:00:00</dataModificare>'
            . '<categorieCazNume>Civil</categorieCazNume><stadiuProcesualNume>' . $stadiu . '</stadiuProcesualNume>'
            . '</Dosar>';

        return $this->plic($dosar);
    }

    protected function plic(string $continut): string
    {
        return '<?xml version="1.0" encoding="utf-8"?>'
            . '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<soap:Body><CautareDosareResponse xmlns="portalquery.just.ro">'
            . '<CautareDosareResult>' . $continut . '</CautareDosareResult>'
            . '</CautareDosareResponse></soap:Body></soap:Envelope>';
    }

    /**
     * Răspunsurile se dau în ordine: `Http::fake` apelat de mai multe ori
     * păstrează primul șablon, deci a doua verificare ar primi aceleași date.
     */
    protected function raspunsuriInOrdine(array $plicuri): void
    {
        $secventa = Http::fakeSequence();

        foreach ($plicuri as $plic) {
            $secventa->push($plic, 200);
        }
    }

    /**
     * Prima verificare doar fixează punctul de referință: altfel utilizatorul ar
     * primi pe email tot istoricul dosarului ca noutate.
     */
    public function test_prima_verificare_doar_retine_starea(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            $monitorizare = $this->monitorizare();
            $this->raspunsuriInOrdine([$this->raspunsCu([['2025-11-28', '10:00', 'Amânat']])]);

            $modificari = $this->serviciu()->verifica($monitorizare);

            $this->assertSame([], $modificari);
            $this->assertSame(1, $monitorizare->fresh()->dosare_urmarite);
            $this->assertCount(1, $monitorizare->dosare()->get());
        });
    }

    public function test_termenul_nou_si_schimbarea_stadiului_sunt_sesizate(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            $monitorizare = $this->monitorizare();

            $this->raspunsuriInOrdine([
                $this->raspunsCu([['2025-11-28', '10:00', 'Amânat']]),
                $this->raspunsCu([['2026-02-10', '09:00', ''], ['2025-11-28', '10:00', 'Amânat']], 'Apel'),
            ]);

            $this->serviciu()->verifica($monitorizare);
            $modificari = $this->serviciu()->verifica($monitorizare);

            $tipuri = array_map(function ($m) {
                return $m->tip;
            }, $modificari);

            $this->assertContains('termen_nou', $tipuri);
            $this->assertContains('stadiu', $tipuri);

            $descrieri = implode(' | ', array_map(function ($m) {
                return $m->descriere;
            }, $modificari));

            // Datele apar zz.ll.aaaa, ca peste tot în aplicație.
            $this->assertStringContainsString('10.02.2026', $descrieri);
            $this->assertStringContainsString('Apel', $descrieri);
        });
    }

    public function test_solutia_aparuta_la_un_termen_cunoscut_este_sesizata(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            $monitorizare = $this->monitorizare();

            $this->raspunsuriInOrdine([
                $this->raspunsCu([['2026-02-10', '09:00', '']]),
                $this->raspunsCu([['2026-02-10', '09:00', 'Admite cererea']]),
            ]);

            $this->serviciu()->verifica($monitorizare);
            $modificari = $this->serviciu()->verifica($monitorizare);

            $this->assertCount(1, $modificari);
            $this->assertSame('solutie', $modificari[0]->tip);
            $this->assertStringContainsString('Admite cererea', $modificari[0]->descriere);
        });
    }

    public function test_partea_noua_si_calea_de_atac_sunt_sesizate(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            $monitorizare = $this->monitorizare();

            $this->raspunsuriInOrdine([
                $this->raspunsCu([['2026-02-10', '09:00', '']]),
                $this->raspunsCu(
                    [['2026-02-10', '09:00', '']],
                    'Fond',
                    ['POPESCU ION', 'SC BETA SRL'],
                    [['Apel', '2026-03-01']]
                ),
            ]);

            $this->serviciu()->verifica($monitorizare);
            $modificari = $this->serviciu()->verifica($monitorizare);

            $tipuri = array_map(function ($m) {
                return $m->tip;
            }, $modificari);

            $this->assertContains('parte', $tipuri);
            $this->assertContains('cale_atac', $tipuri);
        });
    }

    /** Fără schimbări reale nu se trimite nimic — altfel emailul devine zgomot. */
    public function test_dosarul_neschimbat_nu_produce_modificari(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            $monitorizare = $this->monitorizare();

            $this->raspunsuriInOrdine([
                $this->raspunsCu([['2026-02-10', '09:00', 'Amânat']]),
                $this->raspunsCu([['2026-02-10', '09:00', 'Amânat']]),
            ]);

            $this->serviciu()->verifica($monitorizare);
            $modificari = $this->serviciu()->verifica($monitorizare);

            $this->assertSame([], $modificari);
        });
    }

    /** Pentru un nume de parte, dosarele apărute între verificări sunt noutăți. */
    public function test_dosarul_aparut_ulterior_este_anuntat(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            $monitorizare = PortalJustMonitorizare::create([
                'company_id' => self::COMPANIE,
                'tip' => PortalJustMonitorizare::TIP_PARTE,
                'valoare' => 'POPESCU ION',
                'email' => 'avocat@exemplu.ro',
                'activ' => true,
            ]);

            // Prima verificare: niciun dosar. A doua: dosarul a apărut.
            $this->raspunsuriInOrdine([
                $this->plic(''),
                $this->raspunsCu([['2026-02-10', '09:00', '']]),
            ]);

            $this->serviciu()->verifica($monitorizare);
            $modificari = $this->serviciu()->verifica($monitorizare);

            $this->assertCount(1, $modificari);
            $this->assertSame('dosar_nou', $modificari[0]->tip);
            $this->assertStringContainsString('1234/3/2024', $modificari[0]->descriere);
        });
    }

    /**
     * Destinatarul primește un singur email cu toate modificările lui, iar
     * acestea se marchează ca trimise, ca să nu se repete a doua zi.
     */
    public function test_comanda_trimite_un_email_cu_toate_modificarile(): void
    {
        Mail::fake();

        $monitorizare = ContextCompanie::pentru(self::COMPANIE, function () {
            $monitorizare = $this->monitorizare();

            $this->raspunsuriInOrdine([
                $this->raspunsCu([['2026-02-10', '09:00', '']]),
                $this->raspunsCu([['2026-03-15', '10:00', ''], ['2026-02-10', '09:00', 'Admite cererea']], 'Apel'),
            ]);

            $this->serviciu()->verifica($monitorizare);

            return $monitorizare;
        });

        Artisan::call('portaljust:monitorizeaza', ['--monitorizare' => $monitorizare->id]);

        Mail::assertSent(ModificariPortalJustEmail::class, function ($mail) {
            return $mail->hasTo('avocat@exemplu.ro')
                && $mail->total >= 3
                && count($mail->dosare) === 1
                && $mail->dosare[0]['numar'] === '1234/3/2024';
        });

        ContextCompanie::pentru(self::COMPANIE, function () {
            $this->assertSame(0, PortalJustModificare::nenotificate()->count());
        });
    }

    /** Emailul trebuie să se compună fără erori și să conțină ce s-a schimbat. */
    public function test_emailul_contine_dosarul_si_modificarile(): void
    {
        $email = new ModificariPortalJustEmail([[
            'numar' => '1234/3/2024',
            'institutie' => 'Curtea de Apel BUCURESTI',
            'urmarit_pentru' => 'Număr dosar „1234/3/2024”',
            'modificari' => ['Termen nou: 10.02.2026, ora 09:00', 'Soluție la termenul din 05.01.2026: Admite cererea'],
        ]], 2);

        $continut = $email->render();

        $this->assertStringContainsString('1234/3/2024', $continut);
        $this->assertStringContainsString('Curtea de Apel BUCURESTI', $continut);
        $this->assertStringContainsString('Termen nou: 10.02.2026', $continut);
        $this->assertStringContainsString('Admite cererea', $continut);
    }

    /** Modificările poartă clientul monitorizării, ca să nu ajungă la altcineva. */
    public function test_modificarile_raman_la_clientul_monitorizarii(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            $monitorizare = $this->monitorizare();

            $this->raspunsuriInOrdine([
                $this->raspunsCu([['2026-02-10', '09:00', '']]),
                $this->raspunsCu([['2026-02-10', '09:00', 'Admite cererea']]),
            ]);

            $this->serviciu()->verifica($monitorizare);
            $modificari = $this->serviciu()->verifica($monitorizare);

            $this->assertSame(self::COMPANIE, (int) $modificari[0]->company_id);
        });

        // Alt client nu vede nimic.
        ContextCompanie::pentru(self::COMPANIE + 1, function () {
            $this->assertSame(0, PortalJustModificare::count());
            $this->assertSame(0, PortalJustMonitorizare::count());
        });
    }
}
