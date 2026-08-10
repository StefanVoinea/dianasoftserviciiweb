<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Models\User;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Ce pune dosarul urmarit se vede de toata lumea din firma.
 *
 * Dosarul urmarit lucreaza singur, fara om in spate: declaratia gasita acolo se
 * inregistreaza cu „user_id" gol, fiindca nu e nimeni caruia sa i se puna in
 * seama. Filtrul „vezi doar ce ai lucrat tu" cerea insa potrivire pe id, iar un
 * rand fara stapan nu se potriveste cu nimeni — asa ca declaratia se ascundea
 * de toti, in afara administratorului firmei.
 *
 * Instiintarea plecata pe email spunea totusi: „Detaliile complete se vad in
 * aplicatie, la SPV Curier → Declaratii fiscale". Omul se ducea acolo si nu
 * gasea nimic — nici randul, nici butonul SPV Wizard care i-ar fi talmacit
 * eroarea.
 */
class DocumentulFaraStapanTest extends TestCase
{
    protected const COMPANIE = 996;

    protected $certificat;
    protected $omul;
    protected $colegul;

    protected function setUp(): void
    {
        parent::setUp();

        $this->omul = User::create([
            'name' => 'Contabilul',
            'email' => 'fara-stapan996@example.test',
            'password' => bcrypt('proba'),
        ]);

        $this->colegul = User::create([
            'name' => 'Colegul',
            'email' => 'coleg996@example.test',
            'password' => bcrypt('proba'),
        ]);

        foreach ([$this->omul->id, $this->colegul->id] as $id) {
            DB::table('company_user')->insert([
                'user_id' => $id,
                'company_id' => self::COMPANIE,
                'administrator' => false,
            ]);
        }

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'Elena-Carmen Filimon',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
        ]);
    }

    protected function tearDown(): void
    {
        AnafDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        DB::table('company_user')->where('company_id', self::COMPANIE)->delete();
        User::whereIn('email', ['fara-stapan996@example.test', 'coleg996@example.test'])->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /** Declaratia asa cum o scrie dosarul urmarit: fara stapan. */
    protected function dinDosarulUrmarit(): AnafDeclaratie
    {
        return AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'D112_26245985_042023.xml',
            'tip' => 'D112',
            'cui' => '26245985',
            'certificat_id' => $this->certificat->id,
            'pas' => 'eroare_validare',
            'erori_validare' => 'Validarea ANAF a respins declarația.',
        ]);
    }

    /** @return AnafDeclaratie o declaratie lucrata de coleg */
    protected function aColegului(): AnafDeclaratie
    {
        return AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'user_id' => $this->colegul->id,
            'nume_fisier' => 'D394_26245985_042023.xml',
            'tip' => 'D394',
            'cui' => '26245985',
            'certificat_id' => $this->certificat->id,
            'pas' => 'incarcat',
        ]);
    }

    /** Chiar asa o scrie dosarul urmarit: fara nimeni in spate. */
    public function test_dosarul_urmarit_scrie_fara_stapan(): void
    {
        $sursa = file_get_contents(app_path('Services/Anaf/Declaratii/MonitorizareFolder.php'));

        $this->assertStringNotContainsString(
            "'user_id'",
            $sursa,
            'dacă dosarul ar pune un stăpân, proba aceasta n-ar mai avea rost'
        );

        $this->assertNull($this->dinDosarulUrmarit()->user_id);
    }

    /** Declaratia venita din dosar se vede de utilizatorul obisnuit. */
    public function test_declaratia_din_dosar_se_vede_de_oricine_din_firma(): void
    {
        $declaratia = $this->dinDosarulUrmarit();

        $this->actingAs($this->omul, 'api');

        $this->assertNotNull(
            AnafDeclaratie::find($declaratia->id),
            'declarația pusă de dosarul urmărit trebuie să se vadă: e a firmei, nu a nimănui'
        );
    }

    /** Se vede si de celalalt coleg: e a firmei, nu a unuia dintre ei. */
    public function test_se_vede_si_de_celalalt_coleg(): void
    {
        $declaratia = $this->dinDosarulUrmarit();

        $this->actingAs($this->colegul, 'api');

        $this->assertNotNull(AnafDeclaratie::find($declaratia->id));
    }

    /**
     * Ce a lucrat un om anume ramane insa al lui: indreptarea nu deschide si
     * documentele cu stapan.
     */
    public function test_documentul_cu_stapan_ramane_al_lui(): void
    {
        $aColegului = $this->aColegului();

        $this->actingAs($this->omul, 'api');

        $this->assertNull(
            AnafDeclaratie::find($aColegului->id),
            'documentul lucrat de altcineva n-are ce căuta aici'
        );
    }

    /** In lista se vad amandoua felurile: ale lui si ale firmei. */
    public function test_lista_le_arata_pe_ale_lui_si_pe_ale_firmei(): void
    {
        $aFirmei = $this->dinDosarulUrmarit();
        $aColegului = $this->aColegului();

        $aLui = AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'user_id' => $this->omul->id,
            'nume_fisier' => 'D300_26245985_042023.xml',
            'tip' => 'D300',
            'cui' => '26245985',
            'certificat_id' => $this->certificat->id,
            'pas' => 'incarcat',
        ]);

        $this->actingAs($this->omul, 'api');

        $vazute = AnafDeclaratie::pluck('id')->all();

        $this->assertContains($aFirmei->id, $vazute);
        $this->assertContains($aLui->id, $vazute);
        $this->assertNotContains($aColegului->id, $vazute);
    }
}
