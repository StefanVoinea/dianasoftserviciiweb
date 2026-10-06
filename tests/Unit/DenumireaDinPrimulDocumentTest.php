<?php

namespace Tests\Unit;

use App\Models\AnafSocietate;
use App\Models\SpvMesaj;
use App\Services\Anaf\Spv\SpvStorage;
use App\Services\Anaf\Spv\VectorFiscalParser;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * Denumirea firmei se ia din primul document descărcat care o spune.
 *
 * Cine descarcă doar mesajele, fără „Solicită datele lipsă", rămânea cu firme
 * fără nume. Acum, cât timp firma n-are nume, textul fiecărui document adus se
 * citește o dată și, dacă scrie „Denumire: …", de acolo se ia. Un nume știut nu
 * se atinge, iar un document care nu spune nimic nu lasă nimic.
 */
class DenumireaDinPrimulDocumentTest extends TestCase
{
    protected const COMPANIE = 991;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);
    }

    protected function tearDown(): void
    {
        SpvMesaj::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafSocietate::query()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function societate(string $cif, array $peste = []): AnafSocietate
    {
        return AnafSocietate::create(array_merge([
            'company_id' => self::COMPANIE,
            'cif' => $cif,
            'tip' => 'pj',
            'activ' => true,
        ], $peste));
    }

    protected function mesaj(string $cif): SpvMesaj
    {
        return SpvMesaj::create([
            'company_id' => self::COMPANIE,
            'mesaj_id' => 'M' . self::COMPANIE . '-' . random_int(1000, 999999),
            'cif' => $cif,
            'tip' => 'RECIPISA',
        ]);
    }

    /** Cheamă pasul de după descărcare, cu textul dat în locul documentului. */
    protected function invata(SpvStorage $depozit, SpvMesaj $mesaj, $text): void
    {
        $metoda = new \ReflectionMethod($depozit, 'invataDenumirea');
        $metoda->setAccessible(true);
        $metoda->invoke($depozit, $mesaj, function () use ($text) {
            return $text;
        });
    }

    public function test_firma_fara_nume_il_ia_din_recipisa(): void
    {
        $cif = '99' . random_int(100000, 999999);
        $societate = $this->societate($cif);

        $this->invata(new SpvStorage(), $this->mesaj($cif), implode("\n", [
            'RECIPISĂ',
            'Nr. înregistrare: 912239948',
            'Denumire/Nume: CABINET DE PROBĂ SRL',
            'CUI/CNP: ' . $cif,
        ]));

        $societate->refresh();

        $this->assertSame('CABINET DE PROBĂ SRL', $societate->denumire);
        $this->assertSame('document', $societate->denumire_sursa);
    }

    /** Numele deja știut nu se atinge, nici măcar nu se mai citește documentul. */
    public function test_numele_stiut_nu_se_atinge(): void
    {
        $cif = '98' . random_int(100000, 999999);
        $this->societate($cif, ['denumire' => 'Numele Vechi SRL', 'denumire_sursa' => 'vector']);

        $citit = false;

        $metoda = new \ReflectionMethod(SpvStorage::class, 'invataDenumirea');
        $metoda->setAccessible(true);
        $metoda->invoke(new SpvStorage(), $this->mesaj($cif), function () use (&$citit) {
            $citit = true;

            return "Denumire: ALT NUME SRL\n";
        });

        $this->assertFalse($citit, 'documentul nu trebuia citit');
        $this->assertSame('Numele Vechi SRL', AnafSocietate::where('cif', $cif)->value('denumire'));
    }

    /** Un document care nu spune numele nu lasă nimic — și nu ghicește după CUI. */
    public function test_documentul_fara_nume_nu_lasa_nimic(): void
    {
        $cif = '97' . random_int(100000, 999999);
        $this->societate($cif);

        $this->invata(new SpvStorage(), $this->mesaj($cif), implode("\n", [
            'Obligații de plată',
            'CUI ' . $cif . ' Declarația D300 pe luna august',
            'Total: 0,00 lei',
        ]));

        $this->assertNull(AnafSocietate::where('cif', $cif)->value('denumire'));
    }

    /** Într-o lucrare, documentul unei firme se citește o singură dată. */
    public function test_textul_se_cere_o_singura_data_pe_firma(): void
    {
        $cif = '96' . random_int(100000, 999999);
        $this->societate($cif);

        $depozit = new SpvStorage();
        $cereri = 0;

        $metoda = new \ReflectionMethod($depozit, 'invataDenumirea');
        $metoda->setAccessible(true);

        for ($i = 0; $i < 3; $i++) {
            $metoda->invoke($depozit, $this->mesaj($cif), function () use (&$cereri) {
                $cereri++;

                return "Nimic de citit aici\n";
            });
        }

        $this->assertSame(1, $cereri);
    }

    /** Ce venea de la ANAF anume bate ce s-a citit dintr-un document oarecare. */
    public function test_datele_de_identificare_bat_documentul(): void
    {
        $cif = '95' . random_int(100000, 999999);
        $societate = $this->societate($cif, ['denumire' => 'Din Recipisă SRL', 'denumire_sursa' => 'document']);

        $this->assertTrue($societate->seteazaDenumire('Din Identificare SRL', 'date_identificare'));
        $this->assertFalse($societate->refresh()->seteazaDenumire('Din Altă Recipisă SRL', 'document'));
        $this->assertSame('Din Identificare SRL', $societate->refresh()->denumire);
    }

    public function test_cititorul_stie_si_nume_denumire(): void
    {
        $parser = new VectorFiscalParser();

        $this->assertSame('FIRMA ÎNTÂI SRL', $parser->citesteDenumire("Nume/Denumire: FIRMA ÎNTÂI SRL\n", null, false));
        $this->assertSame('FIRMA A DOUA SA', $parser->citesteDenumire("Denumire / Nume  FIRMA A DOUA SA\n", null, false));
        $this->assertNull($parser->citesteDenumire("Denumire: SRL\n", null, false), 'forma juridică singură nu e nume');
        $this->assertNull($parser->citesteDenumire("CUI 12345678 Declarația D300\n", '12345678', false), 'după CUI nu se ghicește');
    }
}
