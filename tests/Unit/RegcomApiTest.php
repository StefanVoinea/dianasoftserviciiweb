<?php

namespace Tests\Unit;

use App\Models\Datefirmeregcom;
use Tests\TestCase;

/**
 * API-ul de consultare a datelor firmelor (Reg. Com.) pentru celelalte aplicatii DianaSoft: cheie de aplicatie
 * obligatorie (401 fara ea sau cu una straina), firma dupa CUI, cautarea dupa denumire / CUI / telefon si lista
 * paginata in forma asteptata de tabelele din aplicatii. Ruleaza pe tabela reala, fara sa scrie in ea.
 */
class RegcomApiTest extends TestCase
{
    private const CHEIE = 'test:cheie-de-proba-regcom';

    protected function setUp(): void
    {
        parent::setUp();
        config(['regcom.chei' => ['alta:x', self::CHEIE]]);
    }

    private function antete(): array
    {
        return ['Authorization' => 'Bearer '.self::CHEIE, 'Accept' => 'application/json'];
    }

    public function test_fara_cheie_sau_cu_cheie_straina_401(): void
    {
        $this->getJson('/api/regcom/cui/123')->assertStatus(401);
        $this->withHeaders(['Authorization' => 'Bearer gresit', 'Accept' => 'application/json'])->getJson('/api/regcom/cui/123')->assertStatus(401);
        config(['regcom.chei' => []]);
        $this->withHeaders($this->antete())->getJson('/api/regcom/cui/123')->assertStatus(401);
    }

    public function test_firma_dupa_cui_cautare_si_lista(): void
    {
        $firma = Datefirmeregcom::whereNotNull('cui')->where('cui', '<>', '')->whereNotNull('denumire')->orderBy('id')->first();
        if (! $firma) {
            $this->markTestSkipped('tabela datefirmeregcom e goala');
        }

        // CUI-ul cu prefix si spatii: conteaza doar cifrele
        $r = $this->withHeaders($this->antete())->getJson('/api/regcom/cui/RO '.$firma->cui)->assertOk();
        $this->assertSame($firma->denumire, $r->json('denumire'));
        $this->assertSame((int) $firma->id, (int) $r->json('id'));
        $this->withHeaders($this->antete())->getJson('/api/regcom/cui/0000000000999')->assertStatus(404);
        $this->withHeaders($this->antete())->getJson('/api/regcom/'.$firma->id)->assertOk()->assertJsonPath('cui', $firma->cui);

        // cautarea ca la partener: denumirea sau CUI-ul contin textul
        $r = $this->withHeaders($this->antete())->getJson('/api/regcom/cauta?text='.urlencode($firma->cui).'&ordine=denumire')->assertOk();
        $this->assertContains((int) $firma->id, array_map('intval', array_column($r->json(), 'id')));
        $this->assertLessThanOrEqual(config('regcom.limita_cautare'), count($r->json()));
        $this->withHeaders($this->antete())->getJson('/api/regcom/cauta?text=')->assertOk()->assertExactJson([]);
        if ($firma->telefon) {
            $r = $this->withHeaders($this->antete())->getJson('/api/regcom/cauta?camp=telefon&text='.urlencode($firma->telefon))->assertOk();
            $this->assertContains((int) $firma->id, array_map('intval', array_column($r->json(), 'id')));
        }

        // lista paginata: forma paginatorului Laravel (data, total, current_page), cautare pe o coloana
        $r = $this->withHeaders($this->antete())->postJson('/api/regcom/lista', ['pageLength' => 5, 'page' => 1, 'searchModel' => $firma->cui, 'cautareDupa' => 'cui'])->assertOk();
        $this->assertSame(1, $r->json('current_page'));
        $this->assertSame(5, $r->json('per_page'));
        $this->assertGreaterThanOrEqual(1, $r->json('total'));
        $this->assertContains((int) $firma->id, array_map('intval', array_column($r->json('data'), 'id')));
    }
}
