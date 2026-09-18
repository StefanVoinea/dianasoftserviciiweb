<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\MarketingController;
use App\Mail\ScrisoareMarketing;
use App\Models\MarketingContact;
use App\Models\MarketingTrimitere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Șabloanele de scrisori și trimiterea către câteva firme luate la întâmplare.
 *
 * Alegerea cu mâna e bună pentru zece firme, nu pentru două mii. De aceea se
 * poate spune și „scrie astăzi la douăzeci și cinci, din filtrul acesta" — iar
 * partea care contează e că sacul din care se alege nu cuprinde pe nimeni care
 * s-a dezabonat, și că filtrul de pe ecran e același cu cel după care se alege.
 * Altfel omul crede că scrie firmelor pe care le vede, iar scrisorile pleacă
 * altundeva.
 */
class ScrisoriLaIntamplareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    protected function tearDown(): void
    {
        MarketingTrimitere::query()->delete();
        MarketingContact::query()->where('email', 'like', '%@proba-intamplare.ro')->delete();

        parent::tearDown();
    }

    protected function contact(array $peste = []): MarketingContact
    {
        static $numar = 0;
        $numar++;

        return MarketingContact::create(array_merge([
            'denumire' => 'Firma ' . $numar . ' SRL',
            'cui' => (string) (10000000 + $numar),
            'email' => 'firma' . $numar . '@proba-intamplare.ro',
            'judet' => 'Constanța',
            'abonat' => true,
        ], $peste));
    }

    /** @param array<string, mixed> $date */
    protected function trimite(array $date)
    {
        return (new MarketingController())->trimite(
            Request::create('/api/marketing/trimite', 'POST', array_merge([
                'subiect' => 'Proba',
                'text' => 'Bună ziua, {nume}.',
            ], $date))
        );
    }

    /** Câte au plecat cu adevărat. */
    protected function catePlecate(): int
    {
        return Mail::queued(ScrisoareMarketing::class)->count();
    }

    /** @test */
    public function sabloanele_se_dau_filei_cu_subiect_si_text()
    {
        $raspuns = (new MarketingController())->sabloane();
        $date = $raspuns->getData(true);

        $this->assertTrue($date['success']);
        $this->assertNotEmpty($date['data'], 'trebuie să existe șabloane de la care să pornească omul');

        foreach ($date['data'] as $sablon) {
            foreach (['cheie', 'nume', 'descriere', 'subiect', 'text'] as $camp) {
                $this->assertArrayHasKey($camp, $sablon);
                $this->assertNotSame('', trim((string) $sablon[$camp]), $camp . ' gol în „' . $sablon['nume'] . '”');
            }
        }
    }

    /**
     * Legătura de dezabonare se pune singură, deci n-are ce căuta în șabloane —
     * scrisă de două ori, ar arăta a greșeală.
     *
     * @test
     */
    public function sabloanele_nu_scriu_ele_dezabonarea_si_nici_butonul()
    {
        foreach (config('marketing.sabloane') as $sablon) {
            $this->assertStringNotContainsStringIgnoringCase('dezabon', $sablon['text'], $sablon['cheie']);
            $this->assertStringNotContainsString('http', $sablon['text'], $sablon['cheie']);
        }
    }

    /** @test */
    public function la_intamplare_pleaca_exact_cate_s_au_cerut()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->contact();
        }

        $raspuns = $this->trimite(['cati' => 4, 'filtre' => ['judet' => 'Constanța']]);

        $this->assertSame(200, $raspuns->status());
        $this->assertSame(4, $raspuns->getData(true)['trimise']);
        $this->assertSame(4, $this->catePlecate());
    }

    /** Cine s-a dezabonat nu intră în sac, deci n-are cum să iasă din el. */
    /** @test */
    public function dezabonatii_nu_pot_fi_alesi_la_intamplare()
    {
        $this->contact(['abonat' => true]);

        for ($i = 0; $i < 6; $i++) {
            $this->contact(['abonat' => false, 'dezabonat_la' => now()]);
        }

        $raspuns = $this->trimite(['cati' => 7, 'filtre' => ['judet' => 'Constanța']]);

        $this->assertSame(1, $raspuns->getData(true)['trimise'], 'numai cel abonat avea voie');
    }

    /** @test */
    public function alegerea_la_intamplare_tine_seama_de_filtrul_de_pe_ecran()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->contact(['judet' => 'Constanța']);
        }

        $altundeva = $this->contact(['judet' => 'Cluj']);

        $this->trimite(['cati' => 10, 'filtre' => ['judet' => 'Cluj']]);

        Mail::assertQueued(ScrisoareMarketing::class, function ($scrisoare) use ($altundeva) {
            return $scrisoare->contact->id === $altundeva->id;
        });

        $this->assertSame(1, $this->catePlecate(), 'din Cluj era una singură');
    }

    /** @test */
    public function se_spune_cand_s_au_gasit_mai_putine_decat_s_au_cerut()
    {
        $this->contact();
        $this->contact();

        $raspuns = $this->trimite(['cati' => 50, 'filtre' => ['judet' => 'Constanța']]);
        $date = $raspuns->getData(true);

        $this->assertSame(2, $date['trimise']);
        $this->assertStringContainsString('nu 50', $date['message']);
    }

    /** @test */
    public function filtrul_gol_de_firme_se_spune_pe_nume()
    {
        $raspuns = $this->trimite(['cati' => 5, 'filtre' => ['judet' => 'Județ care nu există']]);

        $this->assertSame(422, $raspuns->status());
        $this->assertStringContainsString('nicio firmă', $raspuns->getData(true)['message']);
    }

    /** @test */
    public function fara_alesi_si_fara_numar_nu_pleaca_nimic()
    {
        $this->contact();

        $raspuns = $this->trimite(['contacte' => []]);

        $this->assertSame(422, $raspuns->status());
        $this->assertSame(0, $this->catePlecate());
    }

    /** Alegerea cu mâna merge mai departe, neschimbată. */
    /** @test */
    public function firmele_alese_cu_mana_primesc_si_ele()
    {
        $unul = $this->contact();
        $altul = $this->contact();
        $this->contact();

        $raspuns = $this->trimite(['contacte' => [$unul->id, $altul->id]]);

        $this->assertSame(2, $raspuns->getData(true)['trimise']);
        $this->assertSame(2, $this->catePlecate());
    }

    /**
     * Un număr peste plafon nu trece: o greșeală de tastare n-are voie să
     * golească lista într-o apăsare.
     *
     * @test
     */
    public function numarul_cerut_nu_poate_trece_de_plafon()
    {
        $this->contact();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $this->trimite(['cati' => (int) config('marketing.cat_la_intamplare') + 1]);
    }

    /** Trimiterea la întâmplare se scrie în evidență ca oricare alta. */
    /** @test */
    public function ce_s_a_pus_in_coada_ramane_scris()
    {
        $this->contact();

        $this->trimite(['cati' => 1, 'campanie' => 'proba-intamplare', 'filtre' => []]);

        $scris = MarketingTrimitere::where('campanie', 'proba-intamplare')->get();

        $this->assertCount(1, $scris);
    }

    /**
     * Pusă în coadă nu înseamnă plecată.
     *
     * Aici e miezul: cât timp scrisoarea stă în coadă, evidența nu are voie să
     * spună „reușit". Altfel fila arată campanii trimise în vreme ce lucrătorul
     * cozii e oprit și nu pleacă nimic nicăieri — fără email și fără eroare.
     *
     * @test
     */
    public function scrisoarea_pusa_in_coada_nu_se_scrie_ca_plecata()
    {
        $this->contact();

        $raspuns = $this->trimite(['cati' => 1, 'filtre' => []]);
        $date = $raspuns->getData(true);

        $randul = MarketingTrimitere::latest('id')->first();

        $this->assertSame('in_coada', $randul->stare);
        $this->assertFalse((bool) $randul->reusit);
        $this->assertNull($randul->plecat_la);
        $this->assertSame(0, $date['plecate'], 'nimic n-a plecat: coada e falsă în probe');
        $this->assertStringContainsString('puse în coadă', $date['message']);
    }

    /** Scrisoarea își poartă numărul din evidență, ca să se știe ce a plecat. */
    /** @test */
    public function scrisoarea_isi_poarta_numarul_din_evidenta()
    {
        $this->contact();

        $this->trimite(['cati' => 1, 'filtre' => []]);

        $randul = MarketingTrimitere::latest('id')->first();

        Mail::assertQueued(ScrisoareMarketing::class, function ($scrisoare) use ($randul) {
            return $scrisoare->trimitereaId === $randul->id;
        });
    }

    /**
     * Când serverul de email o primește cu adevărat, rândul se însemnează.
     *
     * @test
     */
    public function plecarea_adevarata_se_insemneaza_in_evidenta()
    {
        $contact = $this->contact();

        $randul = MarketingTrimitere::create([
            'contact_id' => $contact->id,
            'campanie' => 'proba-plecare',
            'subiect' => 'Proba',
            'reusit' => false,
            'stare' => 'in_coada',
        ]);

        $mesaj = new \Swift_Message('Proba');
        $mesaj->getHeaders()->addTextHeader(ScrisoareMarketing::ANTETUL, (string) $randul->id);

        (new \App\Listeners\InsemneazaScrisoareaPlecata())
            ->handle(new \Illuminate\Mail\Events\MessageSent($mesaj));

        $randul->refresh();

        $this->assertSame('plecat', $randul->stare);
        $this->assertTrue((bool) $randul->reusit);
        $this->assertNotNull($randul->plecat_la);
    }

    /** Orice alt email trece pe la ascultător: el nu are voie să se atingă de nimic. */
    /** @test */
    public function un_email_oarecare_nu_atinge_evidenta()
    {
        $contact = $this->contact();

        $randul = MarketingTrimitere::create([
            'contact_id' => $contact->id,
            'subiect' => 'Proba',
            'reusit' => false,
            'stare' => 'in_coada',
        ]);

        (new \App\Listeners\InsemneazaScrisoareaPlecata())
            ->handle(new \Illuminate\Mail\Events\MessageSent(new \Swift_Message('Alt email')));

        $this->assertSame('in_coada', $randul->refresh()->stare);
    }

    /** Fila trebuie să poată spune câte stau pe loc. */
    /** @test */
    public function fila_afla_cate_scrisori_stau_in_coada()
    {
        $this->contact();
        $this->trimite(['cati' => 1, 'filtre' => []]);

        $date = (new MarketingController())
            ->index(Request::create('/api/marketing/contacte', 'GET'))
            ->getData(true);

        $this->assertArrayHasKey('scrisori', $date);
        $this->assertGreaterThanOrEqual(1, $date['scrisori']['in_coada']);
        $this->assertNotNull($date['scrisori']['cea_mai_veche_in_coada']);
    }
}
