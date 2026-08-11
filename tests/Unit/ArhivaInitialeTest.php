<?php

namespace Tests\Unit;

use App\Services\Anaf\Arhiva\ArhivaService;
use Tests\TestCase;

/**
 * In dosarul tipului raman numai actele; ciornele stau deoparte.
 *
 * Pana acum, in „D100" se strangeau la un loc XML-ul dat spre lucru, PDF-ul
 * venit de la programul de contabilitate, declaratia semnata si recipisa. Cine
 * deschidea dosarul nu mai stia care e actul si care e ciorna lui — iar la un
 * control, tocmai asta se cauta.
 *
 * De acum, in dosarul tipului stau numai declaratia semnata si recipisa ei. Ce
 * s-a dat la intrare merge in „Initiale", si tot pe tipuri: trebuie sa se stie
 * oricand ce s-a semnat, dar nu are ce cauta amestecat printre acte.
 */
class ArhivaInitialeTest extends TestCase
{
    /** Dosarul ciornelor poarta tipul, nu le strange pe toate la un loc. */
    public function test_initialele_se_tin_pe_tipuri(): void
    {
        $this->assertSame('Initiale/D100', ArhivaService::dosarInitiale('D100'));
        $this->assertSame('Initiale/D394', ArhivaService::dosarInitiale('D394'));
    }

    /** Fara tip stiut, ciorna nu se pierde: merge la „Diverse". */
    public function test_fara_tip_stiut_merge_la_diverse(): void
    {
        $this->assertSame('Initiale/Diverse', ArhivaService::dosarInitiale(null));
        $this->assertSame('Initiale/Diverse', ArhivaService::dosarInitiale(''));
    }

    /** Numele de dosar se curata, ca oriunde in arhiva. */
    public function test_tipul_se_curata_ca_orice_nume_de_dosar(): void
    {
        $this->assertStringStartsWith('Initiale/', ArhivaService::dosarInitiale('D100/../..'));
        $this->assertStringNotContainsString('..', ArhivaService::dosarInitiale('D100/../..'));
    }

    /**
     * XML-ul si documentul primit merg in „Initiale"; semnatul ramane in
     * dosarul tipului. Se cantareste in amandoua locurile care arhiveaza:
     * fila de declaratii si dosarul urmarit.
     */
    public function test_ciornele_merg_in_initiale_iar_actul_ramane_la_tip(): void
    {
        $locuri = [
            'Http/Controllers/Api/DeclaratiiController.php' => 'fila de declarații',
            'Services/Anaf/Declaratii/MonitorizareFolder.php' => 'dosarul urmărit',
        ];

        foreach ($locuri as $fisier => $lucrul) {
            $sursa = file_get_contents(app_path($fisier));

            $this->assertSame(
                2,
                substr_count($sursa, 'ArhivaService::dosarInitiale($declaratie->tip)'),
                $lucrul . ': și XML-ul, și documentul primit trebuie să meargă în „Initiale"'
            );

            // Semnatul ramane in dosarul tipului: acolo se cauta actul.
            $inceput = strpos($sursa, "\$cai['arhiva_semnat']");
            $bucata = substr($sursa, $inceput, 260);

            $this->assertStringContainsString(
                '$tip,',
                $bucata,
                $lucrul . ': declarația semnată rămâne în dosarul tipului'
            );
        }
    }

    /**
     * Documentul dat spre lucru nu se sterge de pe server pana n-a ajuns in
     * arhiva. Altfel s-ar pierde tocmai ce trebuia pastrat.
     */
    public function test_ciorna_nu_se_sterge_inainte_de_a_fi_arhivata(): void
    {
        $sursa = file_get_contents(app_path('Http/Controllers/Api/DeclaratiiController.php'));

        $this->assertStringContainsString(
            "isset(\$cai['arhiva_initial']) || \$declaratie->cale_pdf === \$declaratie->cale_pdf_semnat",
            $sursa,
            'ștergerea de pe server trebuie să aștepte arhivarea documentului primit'
        );
    }

    /** Calea ciornei se tine minte, ca o reluare sa nu lase inca un exemplar. */
    public function test_calea_ciornei_se_tine_minte(): void
    {
        $coloane = \Illuminate\Support\Facades\Schema::getColumnListing('anaf_declaratii');

        $this->assertContains('arhiva_initial', $coloane);
    }

    /** Recipisa sta langa act, in dosarul tipului — acolo ii e locul. */
    public function test_recipisa_ramane_langa_declaratia_semnata(): void
    {
        $sursa = file_get_contents(app_path('Services/Anaf/Spv/SpvStorage.php'));

        $inceput = strpos($sursa, 'protected function punaLangaDeclaratie');
        $bucata = substr($sursa, $inceput, 700);

        $this->assertStringContainsString('ArhivaService::curata($declaratie->tip)', $bucata);
        $this->assertStringNotContainsString('dosarInitiale', $bucata);
    }

    /**
     * Dosarele firmei se strang dupa cod, oricum s-ar chema.
     *
     * Denumirea se afla pe parcurs — din vectorul fiscal, din datele de
     * identificare, sau scrisa de om —, iar pana atunci documentele apuca sa
     * intre in dosare cu numele de-atunci. Inclusiv unul citit gresit: o firma a
     * ajuns cu dosarul „SRL (22489650)". Ramaneau asa, imprastiate, iar omul le
     * cauta prin trei locuri.
     *
     * Codul nu se schimba niciodata, deci dupa el se recunosc.
     */
    public function test_dosarele_se_strang_dupa_cod(): void
    {
        $server = file_get_contents(base_path('spv-bridge/server.php'));

        $inceput = strpos($server, "\$calea === '/arhiva/uneste-dosarul'");
        $bucata = substr($server, $inceput, 2500);

        $this->assertStringContainsString("\$_POST['cui']", $bucata, 'unirea nu primește codul');
        $this->assertStringContainsString("'(' . \$cui . ')'", $bucata, 'nu recunoaște dosarele după cod');
        $this->assertStringContainsString('arhiva_uneste_doua', $bucata);

        // Iar aplicatia chiar il trimite.
        $arhiva = file_get_contents(app_path('Services/Anaf/Arhiva/ArhivaService.php'));

        $this->assertStringContainsString("'cui' => \$cui,", $arhiva);
    }

    /** Dosarul altei firme nu se atinge: se cere potrivire pe cod intreg. */
    public function test_dosarul_altei_firme_nu_se_atinge(): void
    {
        $server = file_get_contents(base_path('spv-bridge/server.php'));

        $inceput = strpos($server, "\$calea === '/arhiva/uneste-dosarul'");
        $bucata = substr($server, $inceput, 2500);

        // Nu „contine codul", ci „se termina cu (cod)" sau „e chiar codul".
        $this->assertStringContainsString("\$intrare === \$cui ||", $bucata);
        $this->assertStringNotContainsString('strpos($intrare, $cui)', $bucata);
    }

    /**
     * Documentele deja descarcate se recitesc, iar denumirile se indreapta —
     * fara sa se ceara nimic de la ANAF.
     */
    public function test_recitirea_indreapta_denumirile_fara_sa_intrebe_anaf(): void
    {
        $sursa = file_get_contents(app_path('Services/Anaf/Spv/SolicitareService.php'));

        $inceput = strpos($sursa, 'public function reinterpreteaza');
        $bucata = substr($sursa, $inceput, 900);

        // Citeste de pe disc si interpreteaza; nu cheama clientul si nici ANAF.
        $this->assertStringContainsString('textulRaspunsului', $bucata);
        $this->assertStringNotContainsString('listaMesaje', $bucata);
        $this->assertStringNotContainsString('->client->', $bucata);

        // Iar interpretarea chiar foloseste textul, nu o variabila inexistenta.
        $this->assertStringNotContainsString('$calePdf,', $sursa, 'a rămas variabila care nu există');
    }
}
