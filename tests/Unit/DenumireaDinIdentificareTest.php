<?php

namespace Tests\Unit;

use App\Models\AnafSocietate;
use App\Models\SpvSolicitare;
use App\Services\Anaf\Spv\SolicitareService;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * „Solicită datele lipsă" citeste intai ce e adus, si abia pe urma cere.
 *
 * Pentru fiecare firma se ia ULTIMUL document „DATE IDENTIFICARE" care are
 * fisier, se scoate din el denumirea si se strang dosarele. De la ANAF se cere
 * numai pentru firmele care n-au un asemenea document.
 *
 * Pana acum se reciteau toate solicitarile, de orice tip — tinea minute intregi
 * si nu aducea nimic in plus pentru denumire —, iar documentul de identificare
 * nici nu se citea, fiindca in cod statea o variabila care nu exista.
 *
 * Citirea PDF-ului se inlocuieste aici cu textul lui: ea e cantarita in
 * VectorFiscalParserTest, pe textele scoase din documente adevarate. Aici se
 * cantareste alegerea documentului si ce se face cu ce s-a citit din el.
 */
class DenumireaDinIdentificareTest extends TestCase
{
    protected const COMPANIE = 994;

    /** @var array<string, string> calea documentului => textul lui */
    protected $texte = [];

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);
    }

    protected function tearDown(): void
    {
        SpvSolicitare::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafSocietate::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /** Solicitarea, cu textul documentului ei pus deoparte pentru citire. */
    protected function documentul(string $cif, string $tip, string $text, $cand = null): SpvSolicitare
    {
        $cale = 'spv/' . bin2hex(random_bytes(4)) . '.pdf';
        $this->texte[$cale] = $text;

        return SpvSolicitare::create([
            'company_id' => self::COMPANIE,
            'cif' => $cif,
            'tip_document' => $tip,
            'cale_fisier' => $cale,
            'created_at' => $cand ?: now(),
        ]);
    }

    /**
     * Serviciul adevarat, cu o singura schimbare: citirea PDF-ului intoarce
     * textul pus deoparte, in loc sa deschida un fisier.
     */
    protected function serviciu(): SolicitareService
    {
        $texte = &$this->texte;

        return new class(
            app(\App\Services\Anaf\Spv\SpvClient::class),
            app(\App\Services\Anaf\Spv\SpvStorage::class),
            app(\App\Services\Anaf\Spv\VectorFiscalParser::class),
            app(\App\Services\Anaf\Spv\CertificatService::class),
            app(\App\Services\Anaf\Arhiva\ArhivaService::class),
            $texte
        ) extends SolicitareService {
            protected $texteDeProba;

            public function __construct($client, $storage, $parser, $certificate, $arhiva, &$texte)
            {
                parent::__construct($client, $storage, $parser, $certificate, $arhiva);
                $this->texteDeProba = &$texte;
            }

            protected function textulRaspunsului(SpvSolicitare $solicitare): ?string
            {
                return $this->texteDeProba[$solicitare->cale_fisier] ?? null;
            }
        };
    }

    /** Textul unui document de identificare, cu numele deasupra etichetei lui. */
    protected function textIdentificare(string $nume = 'CRISTI & DANA INSTAL SRL'): string
    {
        return implode("\n", [
            'DATE PRIVIND SOCIETATEA',
            ' CE ARE CUI-ul 22489650',
            'LA DATA DE',
            $nume,
            'Denumire',
            'JUD. ARAD, MUN. ARAD',
            'Domiciliul Fiscal',
        ]);
    }

    /** Denumirea se ia din document, fara sa se ceara nimic de la ANAF. */
    public function test_denumirea_se_ia_din_documentul_adus(): void
    {
        AnafSocietate::create([
            'company_id' => self::COMPANIE,
            'cif' => '22489650',
            'denumire' => 'SRL',
            'denumire_sursa' => 'vector',
            'tip' => 'pj',
            'activ' => true,
        ]);

        $this->documentul('22489650', 'DATE IDENTIFICARE', $this->textIdentificare());

        $rezultat = $this->serviciu()->citesteDenumirileDinIdentificare();

        $this->assertSame(1, $rezultat['citite']);
        $this->assertContains('22489650', $rezultat['cu_document']);

        $this->assertSame(
            'CRISTI & DANA INSTAL SRL',
            AnafSocietate::where('cif', '22489650')->first()->denumire,
            'numele citit greșit trebuie îndreptat de documentul care îl știe'
        );
    }

    /** Se ia cel mai nou document, nu primul gasit. */
    public function test_se_ia_ultimul_document_al_firmei(): void
    {
        $this->documentul('22489650', 'DATE IDENTIFICARE', $this->textIdentificare('NUME VECHI SRL'), now()->subYear());
        $this->documentul('22489650', 'DATE IDENTIFICARE', $this->textIdentificare('NUME NOU SRL'), now());

        $rezultat = $this->serviciu()->citesteDenumirileDinIdentificare();

        $this->assertSame(1, $rezultat['citite'], 'se citește unul singur pentru fiecare firmă');
        $this->assertSame('NUME NOU SRL', AnafSocietate::where('cif', '22489650')->first()->denumire);
    }

    /** Se umbla numai prin documentele de acest fel. */
    public function test_celelalte_feluri_de_document_nu_se_ating(): void
    {
        $this->documentul('22489650', 'VECTOR FISCAL', $this->textIdentificare('DIN VECTOR SRL'));

        $rezultat = $this->serviciu()->citesteDenumirileDinIdentificare();

        $this->assertSame(0, $rezultat['citite']);
        $this->assertSame([], $rezultat['cu_document']);
    }

    /** Solicitarea fara document adus nu se ia in seama. */
    public function test_solicitarea_fara_document_nu_conteaza(): void
    {
        SpvSolicitare::create([
            'company_id' => self::COMPANIE,
            'cif' => '22489650',
            'tip_document' => 'DATE IDENTIFICARE',
        ]);

        $rezultat = $this->serviciu()->citesteDenumirileDinIdentificare();

        $this->assertSame([], $rezultat['cu_document'], 'fără fișier, firma tot are nevoie să ceară de la ANAF');
    }

    /** Se poate cere numai pentru anumite firme, cand fila lucreaza pe transe. */
    public function test_se_poate_cere_numai_pentru_anumite_firme(): void
    {
        $this->documentul('22489650', 'DATE IDENTIFICARE', $this->textIdentificare());
        $this->documentul('15208744', 'DATE IDENTIFICARE', $this->textIdentificare('ALTA FIRMA SRL'));

        $rezultat = $this->serviciu()->citesteDenumirileDinIdentificare(['22489650']);

        $this->assertSame(['22489650'], $rezultat['cu_document']);
    }

    /**
     * Firma care are documentul nu mai e intrebata la ANAF; cea fara el, da.
     *
     * Asta e chiar rostul butonului: cere datele lipsa, nu pe cele avute.
     */
    public function test_se_cere_numai_pentru_firmele_fara_document(): void
    {
        $sursa = file_get_contents(app_path('Services/Anaf/Spv/SocietatiService.php'));

        $this->assertStringContainsString('citesteDenumirileDinIdentificare($cifuri)', $sursa);
        $this->assertStringContainsString(
            'isset($auDocument[$societate->cif])',
            $sursa,
            'documentul adus deja n-are de ce să fie cerut din nou'
        );

        // Iar recitirea tuturor solicitarilor nu mai are ce cauta la buton.
        $this->assertStringNotContainsString('solicitari->reinterpreteaza()', $sursa);
    }

    /** Dosarele firmei se strang dupa ce se afla numele. */
    public function test_dosarele_se_strang_dupa_ce_se_afla_numele(): void
    {
        $sursa = file_get_contents(app_path('Services/Anaf/Spv/SolicitareService.php'));

        $inceput = strpos($sursa, 'public function citesteDenumirileDinIdentificare');
        $bucata = substr($sursa, $inceput, 3000);

        $this->assertStringContainsString('$this->arhiva->uneste($cif', $bucata);
        $this->assertStringContainsString('ArhivaService::dosarFirma', $bucata);
    }
}
