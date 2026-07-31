<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\DeclaratiiController;
use App\Models\AnafDeclaratie;
use App\Services\Anaf\Declaratii\DeclaratieException;
use App\Services\Anaf\Declaratii\SemnareService;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * O semnare eșuată trebuie să rămână scrisă pe declarație: altfel, în tabel,
 * ea ar părea în continuare doar „validată", iar utilizatorul n-ar ști de ce
 * nu poate fi depusă.
 */
class EroareSemnareTest extends TestCase
{
    protected const COMPANIE = 994;

    protected function tearDown(): void
    {
        AnafDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function declaratie(): AnafDeclaratie
    {
        return AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'cui' => '15208744',
            'tip' => 'D394',
            'nume_fisier' => 'proba.xml',
            'pas' => 'validat',
            'cale_pdf' => 'declaratii/xml/inexistent.pdf',
        ]);
    }

    /** Un SemnareService care esueaza, ca la un token neconectat. */
    protected function semnareEsuata(string $mesaj): SemnareService
    {
        return new class($mesaj) extends SemnareService {
            protected $mesaj;

            public function __construct(string $mesaj)
            {
                $this->mesaj = $mesaj;
            }

            public function semneaza(string $calePdf, string $calePdfSemnat): string
            {
                throw new DeclaratieException($this->mesaj);
            }
        };
    }

    public function test_esecul_semnarii_este_scris_pe_declaratie(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            $declaratie = $this->declaratie();

            $controller = $this->app->make(DeclaratiiController::class);
            $raspuns = $controller->semneaza(
                $declaratie,
                $this->semnareEsuata('Tokenul nu este conectat la acest calculator.')
            );

            $this->assertSame(500, $raspuns->getStatusCode());

            $proaspata = AnafDeclaratie::find($declaratie->id);

            $this->assertSame('eroare_semnare', $proaspata->pas);
            $this->assertStringContainsString('Tokenul nu este conectat', $proaspata->eroare_semnare);
            $this->assertFalse((bool) $proaspata->semnat);
        });
    }

    /** Coloana „Eroare" arată orice eșec, dar explicația e doar pentru validare. */
    public function test_coloana_de_eroare_arata_si_esecul_semnarii(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            $declaratie = $this->declaratie();
            $declaratie->update(['pas' => 'eroare_semnare', 'eroare_semnare' => 'Certificat negăsit']);

            $controller = $this->app->make(DeclaratiiController::class);
            $date = $controller->index(new \Illuminate\Http\Request())->getData(true)['data'];

            $randul = collect($date)->firstWhere('id', $declaratie->id);

            $this->assertSame('Certificat negăsit', $randul['eroare']);
            $this->assertFalse($randul['eroare_de_validare']);
        });
    }

    /** Erorile de validare rămân cele explicabile. */
    public function test_eroarea_de_validare_ramane_explicabila(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            $declaratie = $this->declaratie();
            $declaratie->update([
                'pas' => 'eroare_validare',
                'erori_validare' => "E: validari globale\n eroare atribut: cui: CUI invalid ('1')",
            ]);

            $controller = $this->app->make(DeclaratiiController::class);
            $date = $controller->index(new \Illuminate\Http\Request())->getData(true)['data'];

            $randul = collect($date)->firstWhere('id', $declaratie->id);

            $this->assertStringContainsString('CUI invalid', $randul['eroare']);
            $this->assertTrue($randul['eroare_de_validare']);
        });
    }
}
