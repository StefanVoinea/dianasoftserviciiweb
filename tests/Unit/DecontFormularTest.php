<?php

namespace Tests\Unit;

use App\Models\AnafSocietate;
use App\Services\Anaf\Declaratii\D300\DecontFormular;
use App\Services\Anaf\Declaratii\D300\FormularD300;
use Tests\TestCase;

/**
 * Decontul scris pentru formularul inteligent al ANAF („soft A").
 *
 * Formularul e un PDF de tip XFA: datele lui nu stau in atribute, ca in
 * declaratia de depus, ci intr-un arbore de subformulare — fiecare rand e un
 * subformular cu doua casute, „c2" pentru baza si „c3" pentru taxa. Asezarea e
 * scoasa din chiar PDF-ul publicat de ANAF (vezi tools/d300).
 */
class DecontFormularTest extends TestCase
{
    protected function societate(array $peste = []): AnafSocietate
    {
        return new AnafSocietate(array_merge([
            'cif' => '15208744',
            'denumire' => 'DIANA SOFT SRL',
            'adresa' => 'Str. Bradului nr. 13, Năvodari',
            'banca' => 'Banca Transilvania',
            'cont' => 'RO49AAAA1B31007593840000',
            'caen' => '6201',
            'nume_declarant' => 'Voinea',
            'prenume_declarant' => 'Ștefan',
            'functie_declarant' => 'Administrator',
            'd300_tip_decont' => 'L',
        ], $peste));
    }

    /**
     * Un decont ca cel scos din SAF-T.
     *
     * Randul 20 e randul 5 vazut din partea deducerii, iar aplicatia ANAF il
     * umple singura la sfarsitul socotelii („RD20_BAZA = RD5_BAZA"); de aceea
     * sta si aici, ca decontul de proba sa arate ca unul adevarat.
     */
    protected function decont(array $randuri = []): array
    {
        return [
            'cif' => '15208744',
            'denumire' => 'DIANA SOFT SRL',
            'luna' => '6',
            'an' => '2026',
            'linii' => 2,
            'randuri' => array_merge([
                'RD5_BAZA' => 1000.0,
                'RD5_TVA' => 190.0,
                'RD20_BAZA' => 1000.0,
                'RD20_TVA' => 190.0,
            ], $randuri),
        ];
    }

    /** Fisierul, citit ca arbore, ca sa se poata cauta prin el. */
    protected function arborele(string $xml): \SimpleXMLElement
    {
        $date = simplexml_load_string($xml);
        $date->registerXPathNamespace('xfa', 'http://www.xfa.org/schema/xfa-data/1.0/');

        return $date;
    }

    /** @return string|null valoarea de la calea data, sau null */
    protected function valoarea(string $xml, string $cale): ?string
    {
        $gasite = $this->arborele($xml)->xpath('/xfa:datasets/xfa:data/' . $cale);

        return $gasite ? (string) $gasite[0] : null;
    }

    public function test_fisierul_e_un_set_de_date_xfa(): void
    {
        $xml = (new DecontFormular())->scrie($this->decont(), $this->societate());

        $this->assertStringContainsString('xfa:datasets', $xml);
        $this->assertStringContainsString('http://www.xfa.org/schema/xfa-data/1.0/', $xml);
        $this->assertNotNull($this->arborele($xml)->xpath('/xfa:datasets/xfa:data/form1'));
    }

    /**
     * Randurile ajung in casutele lor: „c2" pentru baza, „c3" pentru taxa.
     */
    public function test_randurile_ajung_in_casutele_formularului(): void
    {
        $xml = (new DecontFormular())->scrie($this->decont(), $this->societate());

        $this->assertSame('1000', $this->valoarea($xml, 'form1/date/comert/r5/c2'));
        $this->assertSame('190', $this->valoarea($xml, 'form1/date/comert/r5/c3'));

        // Randul 20 e acelasi, vazut din partea deducerii.
        $this->assertSame('1000', $this->valoarea($xml, 'form1/date/achizitiiRO/r20/c2'));
    }

    /** Ce se stie despre firma intra in antetul formularului. */
    public function test_datele_firmei_intra_in_antet(): void
    {
        $xml = (new DecontFormular())->scrie($this->decont(), $this->societate());

        $this->assertSame('DIANA SOFT SRL', $this->valoarea($xml, 'form1/identifCntr/denumire/den'));
        $this->assertSame('15208744', $this->valoarea($xml, 'form1/identifCntr/denumire/cif'));
        $this->assertSame('Banca Transilvania', $this->valoarea($xml, 'form1/identifCntr/banca/den'));
        $this->assertSame('RO49AAAA1B31007593840000', $this->valoarea($xml, 'form1/identifCntr/banca/iban'));
        $this->assertSame('2026', $this->valoarea($xml, 'form1/Antet/metaDate/an_r'));
        $this->assertSame('6', $this->valoarea($xml, 'form1/Antet/metaDate/luna_r'));
        $this->assertSame('Voinea', $this->valoarea($xml, 'form1/semnatura/nume'));
    }

    /**
     * Totalurile nu se scriu: formularul si le face singur.
     *
     * O cifra pusa de noi peste ele nu s-ar bate decat cu socoteala lui.
     */
    public function test_totalurile_se_lasa_pe_seama_formularului(): void
    {
        $xml = (new DecontFormular())->scrie($this->decont(), $this->societate());

        // Randul 19 e totalul taxei colectate — formularul il aduna singur.
        $this->assertNull($this->valoarea($xml, 'form1/date/livrari/r19/c2'));
    }

    /**
     * Fara datele firmei se scrie tot ce se poate.
     *
     * Deosebire fata de declaratia de depus, care se opreste: formularul tocmai
     * pentru asta e — omul il deschide si completeaza ce lipseste.
     */
    public function test_fara_datele_firmei_se_scrie_tot_ce_se_poate(): void
    {
        $xml = (new DecontFormular())->scrie($this->decont(), null);

        $this->assertSame('1000', $this->valoarea($xml, 'form1/date/comert/r5/c2'));
        $this->assertSame('DIANA SOFT SRL', $this->valoarea($xml, 'form1/identifCntr/denumire/den'));
        $this->assertNull($this->valoarea($xml, 'form1/identifCntr/banca/iban'));
    }

    public function test_numele_fisierului_il_deosebeste_de_declaratie(): void
    {
        $nume = (new DecontFormular())->numeFisier($this->decont());

        $this->assertSame('D300_formular_15208744_202606.xml', $nume);
    }

    /**
     * Paza asezarii: ea vine din PDF-ul ANAF, iar o regenerare o poate schimba.
     */
    public function test_asezarea_formularului_ramane_intreaga(): void
    {
        $this->assertSame(
            ['cale' => 'form1/date/comert/r5', 'baza' => 'c2', 'tva' => 'c3'],
            FormularD300::RANDURI['5']
        );

        // Randurile fara coloana de taxa n-au casuta pentru ea.
        $this->assertNull(FormularD300::RANDURI['1']['tva']);

        $this->assertSame('form1/identifCntr/denumire/den', FormularD300::ANTET['denumire']);
    }
}
