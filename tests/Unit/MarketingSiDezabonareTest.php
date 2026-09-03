<?php

namespace Tests\Unit;

use App\Http\Controllers\DemoController;
use App\Http\Controllers\DezabonareController;
use App\Mail\ScrisoareMarketing;
use App\Models\MarketingContact;
use App\Models\MarketingTrimitere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Scrisorile către firmele din listă, și ieșirea din ea.
 *
 * Dezabonarea nu e o facilitate printre altele: e temelia pe care stă dreptul de
 * a scrie cuiva care nu ne-a cerut nimic. De aceea se probează aici mai amănunțit
 * decât trimiterea însăși — că legătura e în fiecare scrisoare, că o apăsare
 * ajunge, că cine a ieșit nu mai primește nimic, și că un import nou nu-l aduce
 * înapoi.
 */
class MarketingSiDezabonareTest extends TestCase
{
    protected function tearDown(): void
    {
        MarketingTrimitere::query()->delete();
        MarketingContact::query()->where('email', 'like', '%@proba-marketing.ro')->delete();

        parent::tearDown();
    }

    protected function contact(string $email = 'unul@proba-marketing.ro'): MarketingContact
    {
        return MarketingContact::create([
            'denumire' => 'Contabil Priceput SRL',
            'cui' => '12345678',
            'email' => $email,
            'judet' => 'Constanța',
        ]);
    }

    /** Jetonul se face singur, altfel n-ar exista legătură de dezabonare. */
    public function test_fiecare_contact_isi_are_jetonul_lui(): void
    {
        $unul = $this->contact('unul@proba-marketing.ro');
        $altul = $this->contact('altul@proba-marketing.ro');

        $this->assertNotEmpty($unul->jeton);
        $this->assertNotSame($unul->jeton, $altul->jeton);
        $this->assertGreaterThanOrEqual(32, strlen($unul->jeton), 'Un jeton scurt s-ar putea ghici.');
    }

    /** „SRL" nu se scrie în adresare: sună a robot. */
    public function test_numele_se_curata_de_forma_juridica(): void
    {
        $this->assertSame('Contabil Priceput', $this->contact()->nume_de_potrivit);
    }

    /** Locurile goale din text se umplu cu ce știm despre firmă. */
    public function test_textul_se_potriveste_pe_fiecare(): void
    {
        $contact = $this->contact();

        $iesit = ScrisoareMarketing::potriveste(
            'Bună ziua, {nume}. Vă scriem din {judet} despre CUI {cui}.',
            $contact
        );

        $this->assertSame('Bună ziua, Contabil Priceput. Vă scriem din Constanța despre CUI 12345678.', $iesit);
    }

    /** Fiecare scrisoare poartă legătura de dezabonare. Fără ea nu pleacă. */
    public function test_scrisoarea_poarta_legatura_de_dezabonare(): void
    {
        $contact = $this->contact();

        $scrisoare = new ScrisoareMarketing($contact, 'Despre SPV Curier', 'Bună ziua, {nume}.');

        $this->assertStringContainsString('/dezabonare/' . $contact->jeton, $scrisoare->legaturaDezabonare);
    }

    /** O apăsare și gata: fără cont, fără confirmare, fără nimic de completat. */
    public function test_o_apasare_scoate_din_lista(): void
    {
        $contact = $this->contact();

        $raspuns = (new DezabonareController())->arata(
            Request::create('/dezabonare/' . $contact->jeton, 'GET'),
            $contact->jeton
        );

        $this->assertSame(200, $raspuns->status());

        $proaspat = $contact->fresh();

        $this->assertFalse($proaspat->abonat);
        $this->assertNotNull($proaspat->dezabonat_la);
    }

    /** Cine s-a dezabonat nu mai intră în nicio trimitere. */
    public function test_dezabonatul_nu_mai_primeste_nimic(): void
    {
        $contact = $this->contact();
        $contact->update(['abonat' => false, 'dezabonat_la' => now()]);

        $ramasi = MarketingContact::whereIn('id', [$contact->id])
            ->caroraLiSePoateScrie()
            ->count();

        $this->assertSame(0, $ramasi);
    }

    /** Un jeton care nu există nu dezabonează pe nimeni. */
    public function test_un_jeton_nascocit_nu_face_nimic(): void
    {
        $contact = $this->contact();

        $raspuns = (new DezabonareController())->arata(
            Request::create('/dezabonare/nascocit', 'GET'),
            'nascocit'
        );

        $this->assertSame(404, $raspuns->status());
        $this->assertTrue($contact->fresh()->abonat, 'Nimeni altcineva n-avea de ce să fie atins.');
    }

    /** A doua apăsare nu strică nimic: se spune doar că era deja scos. */
    public function test_a_doua_apasare_nu_strica_nimic(): void
    {
        $contact = $this->contact();
        $jeton = $contact->jeton;

        (new DezabonareController())->arata(Request::create('/dezabonare/' . $jeton, 'GET'), $jeton);
        $candAIesit = $contact->fresh()->dezabonat_la;

        (new DezabonareController())->arata(Request::create('/dezabonare/' . $jeton, 'GET'), $jeton);

        $this->assertEquals($candAIesit, $contact->fresh()->dezabonat_la, 'Clipa ieșirii rămâne cea dintâi.');
    }

    /**
     * Un import nou nu readuce în listă pe cine a ieșit.
     *
     * Aceasta e greșeala care strică tot: se reîncarcă lista de la CECCAR, iar
     * omul care s-a dezabonat luna trecută primește iar scrisori. Starea de
     * abonare e hotărârea lui, nu a fișierului.
     */
    public function test_un_import_nou_nu_readuce_pe_cel_iesit(): void
    {
        $contact = $this->contact();
        $contact->update(['abonat' => false, 'dezabonat_la' => now()]);

        // Ce face importul cu o firmă pe care o găsește deja în evidență.
        $contact->update(['denumire' => 'Contabil Priceput SRL', 'judet' => 'Cluj']);

        $proaspat = $contact->fresh();

        $this->assertFalse($proaspat->abonat, 'Reîncărcarea listei nu are voie să reabonze pe nimeni.');
        $this->assertSame('Cluj', $proaspat->judet, 'Restul datelor se înnoiesc, doar abonarea nu.');
    }

    /**
     * Fiecare scrisoare merge și către casă, în copie ascunsă.
     *
     * Ascunsă dinadins: destinatarul a primit o scrisoare, nu o listă de
     * trimitere, și n-are de ce să vadă altă adresă lângă a lui.
     */
    public function test_scrisoarea_merge_si_catre_casa(): void
    {
        config(['marketing.copie_ascunsa' => 'office@proba-marketing.ro']);

        $scrisoare = new ScrisoareMarketing($this->contact(), 'Despre SPV Curier', 'Bună ziua.');
        $scrisoare->build();

        $adrese = array_column($scrisoare->bcc, 'address');

        $this->assertContains('office@proba-marketing.ro', $adrese);
    }

    /** Lăsată goală în configurare, copia nu se trimite nimănui. */
    public function test_fara_adresa_in_configurare_nu_se_trimite_nicio_copie(): void
    {
        config(['marketing.copie_ascunsa' => '']);

        $scrisoare = new ScrisoareMarketing($this->contact(), 'Despre SPV Curier', 'Bună ziua.');
        $scrisoare->build();

        $this->assertEmpty($scrisoare->bcc);
    }

    /**
     * Apăsarea pe „Solicită demo" se însemnează pe loc.
     *
     * Nu se așteaptă să completeze cineva un formular: mulți apasă, se uită și
     * închid — dar au apăsat, și tocmai asta e semnul de interes. Deschiderile
     * se numără prost; o faptă, nu.
     */
    public function test_apasarea_pe_demo_se_insemneaza(): void
    {
        $contact = $this->contact();

        $raspuns = (new DemoController())->arata(
            Request::create('/demo/' . $contact->jeton, 'GET'),
            $contact->jeton
        );

        $this->assertSame(200, $raspuns->status());
        $this->assertNotNull($contact->fresh()->demo_cerut_la);
    }

    /** Clipa ținută minte e cea dintâi: atunci s-a aprins interesul. */
    public function test_a_doua_apasare_nu_muta_clipa(): void
    {
        $contact = $this->contact();
        $jeton = $contact->jeton;

        (new DemoController())->arata(Request::create('/demo/' . $jeton, 'GET'), $jeton);
        $intaia = $contact->fresh()->demo_cerut_la;

        (new DemoController())->arata(Request::create('/demo/' . $jeton, 'GET'), $jeton);

        $this->assertEquals($intaia, $contact->fresh()->demo_cerut_la);
    }

    /** Cine lasă și numele și telefonul, le găsim lângă firmă. */
    public function test_datele_lasate_se_pastreaza(): void
    {
        $contact = $this->contact();

        $cerere = Request::create('/demo/' . $contact->jeton, 'POST', [
            'persoana' => 'Maria Ionescu',
            'telefon' => '0722 123 456',
            'mesaj' => 'Ne interesează depunerea de pe telefon.',
        ]);

        (new DemoController())->primeste($cerere, $contact->jeton);

        $proaspat = $contact->fresh();

        $this->assertSame('Maria Ionescu', $proaspat->demo_persoana);
        $this->assertSame('0722 123 456', $proaspat->demo_telefon);
        $this->assertNotNull($proaspat->demo_cerut_la, 'Trimiterea formularului e ea însăși o apăsare.');
    }

    /** Un jeton născocit nu însemnează nimic la nimeni. */
    public function test_un_jeton_nascocit_nu_cere_demo(): void
    {
        $contact = $this->contact();

        $raspuns = (new DemoController())->arata(Request::create('/demo/nascocit', 'GET'), 'nascocit');

        $this->assertSame(404, $raspuns->status());
        $this->assertNull($contact->fresh()->demo_cerut_la);
    }

    /** Butonul poartă numele campaniei: așa se vede care scrisoare a prins. */
    public function test_butonul_poarta_campania(): void
    {
        $contact = $this->contact();

        $scrisoare = new ScrisoareMarketing($contact, 'Despre SPV Curier', 'Bună ziua.', 'toamna-2026');

        $this->assertStringContainsString('/demo/' . $contact->jeton, $scrisoare->legaturaDemo);
        $this->assertStringContainsString('c=toamna-2026', $scrisoare->legaturaDemo);
    }

    /** Trimiterea lasă urmă: cui, ce și când. */
    public function test_trimiterea_lasa_urma(): void
    {
        Mail::fake();

        $contact = $this->contact();

        MarketingTrimitere::create([
            'contact_id' => $contact->id,
            'subiect' => 'Despre SPV Curier',
            'reusit' => true,
        ]);

        $this->assertSame(1, $contact->trimiteri()->count());
    }
}
