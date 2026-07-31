<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\DeclaratiiController;
use App\Models\AnafDeclaratie;
use App\Services\Anaf\Declaratii\InterpretareErori;
use App\Support\ContextCompanie;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Un D406/SAF-T respins poate intoarce sute de mii de caractere de erori:
 * validatorul scrie cate un rand pentru fiecare tranzactie gresita, iar un an
 * de contabilitate are zeci de mii.
 *
 * Trebuie sa incapa in baza de date, sa nu umple raspunsul listei si sa nu tina
 * browserul in loc la explicare.
 */
class EroriMariTest extends TestCase
{
    protected const COMPANIE = 983;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);
    }

    protected function tearDown(): void
    {
        AnafDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /**
     * Erori cat produce un SAF-T mare, in forma in care le scrie DUKIntegrator:
     * un rand cu sectiunea, altul cu greseala.
     */
    protected function eroriMulte(int $cate): string
    {
        $randuri = [];

        for ($i = 1; $i <= $cate; $i++) {
            $randuri[] = 'E: GeneralLedgerEntries (1) sectiune Journal (1) sectiune Transaction (' . $i . ') '
                . 'sectiune Description (1)';
            $randuri[] = ' eroare atribut: : atribut prezent dar vid nepermis';
        }

        return implode("\n", $randuri);
    }

    /** Coloana trebuie sa fie destul de larga: cu TEXT, salvarea cadea. */
    public function test_erorile_unui_saf_t_incap_in_baza_de_date(): void
    {
        $erori = $this->eroriMulte(3000);

        $this->assertGreaterThan(65535, strlen($erori), 'proba trebuie să depășească un TEXT');

        $declaratie = AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'D406.xml',
            'tip' => 'D406',
            'cui' => '33486455',
            'pas' => 'eroare_validare',
            'erori_validare' => $erori,
        ]);

        $this->assertSame(strlen($erori), strlen($declaratie->fresh()->erori_validare));
    }

    /** Spre tabel pleaca doar un inceput: altfel raspunsul ar fi de zeci de MB. */
    public function test_lista_nu_duce_toata_eroarea_la_fiecare_rand(): void
    {
        $erori = $this->eroriMulte(3000);

        AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'D406.xml',
            'tip' => 'D406',
            'cui' => '33486455',
            'pas' => 'eroare_validare',
            'erori_validare' => $erori,
        ]);

        $randuri = $this->app->make(DeclaratiiController::class)
            ->index(new Request())->getData(true)['data'];

        $eroareTrimisa = $randuri[0]['eroare'];

        $this->assertLessThan(6000, mb_strlen($eroareTrimisa));
        $this->assertStringContainsString('lista continuă', $eroareTrimisa);
        $this->assertStringContainsString('6000 rânduri', $eroareTrimisa);
    }

    /** O eroare scurta pleaca intreaga, fara nicio insemnare in plus. */
    public function test_o_eroare_scurta_ramane_neatinsa(): void
    {
        AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'D300.xml',
            'tip' => 'D300',
            'cui' => '33486455',
            'pas' => 'eroare_validare',
            'erori_validare' => 'E: op1 (1) eroare atribut: cuiP: atribut prezent dar vid nepermis',
        ]);

        $randuri = $this->app->make(DeclaratiiController::class)
            ->index(new Request())->getData(true)['data'];

        $this->assertStringNotContainsString('lista continuă', $randuri[0]['eroare']);
    }

    /** Explicarea se opreste dupa un numar de probleme si spune cate a sarit. */
    public function test_explicarea_se_opreste_si_spune_cate_a_sarit(): void
    {
        $declaratie = AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'D406.xml',
            'tip' => 'D406',
            'cui' => '33486455',
            'pas' => 'eroare_validare',
        ]);

        $pasi = [];

        foreach ((new InterpretareErori())->pasCuPas($this->eroriMulte(2000), $declaratie) as $pas) {
            $pasi[] = $pas;
        }

        $inceput = $pasi[0];
        $gata = end($pasi);

        $this->assertSame(2000, $inceput['gasite']);
        $this->assertSame(300, $inceput['total'], 'se explică cel mult 300');
        $this->assertSame(1700, $inceput['sarite']);
        $this->assertSame(1700, $gata['sarite']);

        // Un pas de început, unul de sfârșit și câte unul pentru fiecare problemă
        $this->assertCount(302, $pasi);
    }
}
