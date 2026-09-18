<?php

namespace Tests\Unit;

use App\Imports\FirmeContabilitateImport;
use App\Models\MarketingContact;
use Tests\TestCase;

/**
 * Listele CECCAR intră toate pe aceeași ușă, deși nu arată la fel.
 *
 * Lista de societăți are „Denumire firmă", „CUI" și „Email principal" — adrese
 * scrise chiar de firmă. Lista experților contabili are „Nume" în loc de
 * denumire, n-are CUI deloc, iar adresa stă într-o coloană numită „Email
 * (probabil)": ea nu e declarată, ci dedusă dintr-un telefon comun cu o firmă
 * sau dintr-un nume potrivit în alt registru.
 *
 * Deosebirea contează la trimitere, de aceea se păstrează lângă contact. Și mai
 * contează un lucru: o listă care n-are coloana CUI n-are voie să șteargă
 * CUI-urile aduse de alta.
 */
class ImportExpertiContabiliTest extends TestCase
{
    protected function tearDown(): void
    {
        MarketingContact::query()->where('email', 'like', '%@proba-import.ro')->delete();

        parent::tearDown();
    }

    /**
     * Rândurile trec prin import așa cum le dă Maatwebsite: cu antetul adus la
     * litere mici, fără diacritice și cu liniuțe în loc de spații.
     *
     * @param array<int, array<string, mixed>> $randuri
     */
    protected function importa(array $randuri, string $sursa = 'proba.xlsx'): FirmeContabilitateImport
    {
        $import = new FirmeContabilitateImport($sursa);

        $import->collection(collect($randuri)->map(function ($rand) {
            return collect($rand);
        }));

        return $import;
    }

    /** Un rând din lista experților contabili. */
    protected function randDeExpert(array $peste = []): array
    {
        return array_merge([
            'nr' => 1,
            'judet' => 'Alba',
            'nume' => 'Aftenie Anda Eliza',
            'categorie_registru' => 'cat. IV B',
            'telefon_principal' => '0749 119 809',
            'toate_telefoanele' => '0749 119 809',
            'email_probabil' => 'aftenie@proba-import.ro',
            'toate_emailurile' => 'aftenie@proba-import.ro',
            'sursa_email' => 'telefon comun cu firma (registru CECCAR)',
            'are_email' => 'DA',
        ], $peste);
    }

    /** Un rând din lista de societăți. */
    protected function randDeFirma(array $peste = []): array
    {
        return array_merge([
            'judet' => 'Constanța',
            'denumire_firma' => 'Contabil Priceput SRL',
            'cui' => '12345678',
            'telefon_principal' => '0721 000 111',
            'email_principal' => 'birou@proba-import.ro',
            'viza_ceccar' => '2026',
        ], $peste);
    }

    /** @test */
    public function expertul_contabil_intra_cu_numele_lui_in_loc_de_denumire()
    {
        $import = $this->importa([$this->randDeExpert()]);

        $this->assertSame(1, $import->adaugate);

        $contact = MarketingContact::where('email', 'aftenie@proba-import.ro')->first();

        $this->assertNotNull($contact);
        $this->assertSame('Aftenie Anda Eliza', $contact->denumire);
        $this->assertSame('Alba', $contact->judet);
        $this->assertSame('cat. IV B', $contact->tip);
    }

    /** @test */
    public function adresa_probabila_se_insemneaza_ca_dedusa()
    {
        $this->importa([$this->randDeExpert()]);

        $contact = MarketingContact::where('email', 'aftenie@proba-import.ro')->first();

        $this->assertSame('telefon comun cu firma (registru CECCAR)', $contact->email_dedus);
    }

    /** @test */
    public function adresa_scrisa_in_sursa_ramane_nemarcata()
    {
        $this->importa([$this->randDeFirma()]);

        $contact = MarketingContact::where('email', 'birou@proba-import.ro')->first();

        $this->assertNull($contact->email_dedus, 'adresa declarată n-are de ce să poarte semnul');
        $this->assertSame('12345678', $contact->cui);
    }

    /**
     * Miezul: o listă fără CUI nu golește CUI-ul adus de alta.
     *
     * Cele două liste se întâlnesc des pe aceeași adresă — emailul expertului e
     * de multe ori chiar cutia firmei. Scrisă peste, lista experților ar șterge
     * CUI-ul, viza și restul, degeaba.
     *
     * @test
     */
    public function lista_fara_cui_nu_sterge_cui_ul_stiut_dinainte()
    {
        $this->importa([$this->randDeFirma()]);

        $this->importa([$this->randDeExpert(['email_probabil' => 'birou@proba-import.ro'])]);

        $contact = MarketingContact::where('email', 'birou@proba-import.ro')->first();

        $this->assertSame('12345678', $contact->cui, 'CUI-ul trebuia să rămână');
        $this->assertSame('2026', $contact->viza);
        $this->assertSame('Aftenie Anda Eliza', $contact->denumire, 'denumirea adusă acum se scrie');
    }

    /**
     * Invers: o adresă care era dedusă și acum vine declarată nu mai poartă
     * semnul. Altfel el ar rămâne pe veci, deși între timp am aflat adevărul.
     *
     * @test
     */
    public function adresa_ajunsa_declarata_nu_mai_e_dedusa()
    {
        $this->importa([$this->randDeExpert(['email_probabil' => 'birou@proba-import.ro'])]);

        $this->assertNotNull(MarketingContact::where('email', 'birou@proba-import.ro')->first()->email_dedus);

        $this->importa([$this->randDeFirma()]);

        $this->assertNull(MarketingContact::where('email', 'birou@proba-import.ro')->first()->email_dedus);
    }

    /** Rândurile fără adresă nu intră: lista asta e pentru scris. */
    /** @test */
    public function randul_fara_adresa_ramane_afara()
    {
        $import = $this->importa([
            $this->randDeExpert(['email_probabil' => null, 'toate_emailurile' => null, 'are_email' => 'NU']),
        ]);

        $this->assertSame(0, $import->adaugate);
        $this->assertSame(1, $import->fara_email);
    }

    /** Aceeași adresă de două ori în același fișier se scrie o dată. */
    /** @test */
    public function adresa_repetata_in_fisier_se_ia_o_data()
    {
        $import = $this->importa([
            $this->randDeExpert(),
            $this->randDeExpert(['nume' => 'Altcineva Cu Aceeași Cutie']),
        ]);

        $this->assertSame(1, $import->adaugate);
        $this->assertSame(1, $import->repetate);
    }

    /** Dezabonarea nu se desface la reîncărcare, oricare ar fi lista. */
    /** @test */
    public function dezabonarea_ramane_si_dupa_un_import_nou()
    {
        $this->importa([$this->randDeExpert()]);

        MarketingContact::where('email', 'aftenie@proba-import.ro')
            ->update(['abonat' => false, 'dezabonat_la' => now()]);

        $this->importa([$this->randDeExpert()]);

        $this->assertFalse(
            (bool) MarketingContact::where('email', 'aftenie@proba-import.ro')->first()->abonat
        );
    }
}
