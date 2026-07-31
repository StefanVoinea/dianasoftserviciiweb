<?php

namespace Tests\Unit;

use App\Models\AnafDeclaratie;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * O declarație ajunsă la ANAF nu se mai șterge: ea și recipisa ei sunt dovada
 * depunerii. Interfața nu arată butonul, dar oprirea trebuie să fie și pe
 * server — altfel ar fi de ajuns o cerere directă.
 */
class StergereDeclaratieTest extends TestCase
{
    protected const COMPANIE = 993;

    protected function tearDown(): void
    {
        AnafDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function declaratie(array $atribute): AnafDeclaratie
    {
        return AnafDeclaratie::create(array_merge([
            'company_id' => self::COMPANIE,
            'cui' => '15208744',
            'tip' => 'D394',
            'nume_fisier' => 'proba.xml',
            'pas' => 'validat',
        ], $atribute));
    }

    protected function controller(): \App\Http\Controllers\Api\DeclaratiiController
    {
        return $this->app->make(\App\Http\Controllers\Api\DeclaratiiController::class);
    }

    public function test_declaratia_depusa_nu_poate_fi_stearsa(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            $declaratie = $this->declaratie(['pas' => 'depus']);

            $raspuns = $this->controller()->destroy($declaratie);

            $this->assertSame(422, $raspuns->getStatusCode());
            $this->assertStringContainsString('depusă', $raspuns->getData()->message);
            $this->assertNotNull(AnafDeclaratie::find($declaratie->id));
        });
    }

    /** Indexul de recipisă e dovada depunerii, chiar dacă pasul a rămas altul. */
    public function test_declaratia_cu_index_de_recipisa_nu_poate_fi_stearsa(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            $declaratie = $this->declaratie(['pas' => 'semnat', 'index_recipisa' => '912239948']);

            $raspuns = $this->controller()->destroy($declaratie);

            $this->assertSame(422, $raspuns->getStatusCode());
            $this->assertNotNull(AnafDeclaratie::find($declaratie->id));
        });
    }

    public function test_declaratia_nedepusa_se_poate_sterge(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            $declaratie = $this->declaratie(['pas' => 'eroare_validare']);
            $id = $declaratie->id;

            $raspuns = $this->controller()->destroy($declaratie);

            $this->assertSame(200, $raspuns->getStatusCode());
            $this->assertNull(AnafDeclaratie::find($id));
        });
    }
}
