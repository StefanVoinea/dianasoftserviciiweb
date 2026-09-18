<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\CerereDemoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Cererea de demonstrație nu se pierde nici când emailul nu poate pleca.
 *
 * Serverul de email cade — porturile blocate de găzduire, creditele epuizate la
 * furnizor —, iar omul care a cerut demonstrația vede doar „încercați din nou".
 * Numele firmei, emailul și telefonul lui ar dispărea fără urmă, iar noi n-am
 * ști niciodată că cineva a cerut ceva. De aceea, când scrisoarea nu pleacă, ea
 * se scrie întreagă într-un jurnal al ei, de unde poate fi luată cu mâna.
 */
class CerereDemoNepierdutaTest extends TestCase
{
    /** @var string */
    protected $jurnal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jurnal = storage_path('logs/proba-cereri-demo-' . getmypid() . '.log');

        config(['logging.channels.cereri_demo.path' => $this->jurnal]);
        config(['logging.channels.cereri_demo.days' => 1]);
        Log::forgetChannel('cereri_demo');
    }

    protected function tearDown(): void
    {
        foreach (glob(str_replace('.log', '*', $this->jurnal)) as $fisier) {
            @unlink($fisier);
        }

        Log::forgetChannel('cereri_demo');

        parent::tearDown();
    }

    /** Ce a scris omul în formular. */
    protected function formular(array $peste = []): array
    {
        return array_merge([
            'email' => 'contabil@proba-demo.ro',
            'telefon' => '0721 000 111',
            'firma' => 'PROBA DEMO SRL',
            'cui_estimare' => '40',
            'interval' => 'dimineața',
        ], $peste);
    }

    protected function trimite(array $campuri)
    {
        $cerere = Request::create('/api/cerere-demo', 'POST', $campuri);

        return (new CerereDemoController())->trimite($cerere);
    }

    /** Tot ce s-a scris în jurnalul cererilor. */
    protected function scrisInJurnal(): string
    {
        $text = '';

        foreach (glob(str_replace('.log', '*', $this->jurnal)) as $fisier) {
            $text .= file_get_contents($fisier);
        }

        return $text;
    }

    /** @test */
    public function cand_emailul_nu_pleaca_cererea_se_scrie_in_jurnal()
    {
        Mail::shouldReceive('raw')->once()->andThrow(new \RuntimeException('serverul de email tace'));

        $raspuns = $this->trimite($this->formular());

        $this->assertSame(502, $raspuns->status());

        $scris = $this->scrisInJurnal();

        $this->assertStringContainsString('PROBA DEMO SRL', $scris);
        $this->assertStringContainsString('contabil@proba-demo.ro', $scris);
        $this->assertStringContainsString('0721 000 111', $scris);
        $this->assertStringContainsString('40', $scris);
        $this->assertStringContainsString('dimineața', $scris);
    }

    /** @test */
    public function cererea_scrisa_numai_cu_telefonul_se_pastreaza_la_fel()
    {
        Mail::shouldReceive('raw')->once()->andThrow(new \RuntimeException('serverul de email tace'));

        $this->trimite($this->formular(['email' => null]));

        $this->assertStringContainsString('0721 000 111', $this->scrisInJurnal());
    }

    /** @test */
    public function cand_emailul_pleaca_nu_se_scrie_nimic_in_jurnalul_cererilor()
    {
        Mail::shouldReceive('raw')->once();

        $raspuns = $this->trimite($this->formular());

        $this->assertSame(200, $raspuns->status());
        $this->assertSame('', $this->scrisInJurnal());
    }

    /** @test */
    public function fara_email_si_fara_telefon_cererea_nici_nu_se_incearca()
    {
        Mail::shouldReceive('raw')->never();

        $raspuns = $this->trimite($this->formular(['email' => null, 'telefon' => null]));

        $this->assertSame(422, $raspuns->status());
        $this->assertSame('', $this->scrisInJurnal());
    }
}
