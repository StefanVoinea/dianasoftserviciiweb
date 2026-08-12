<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Actualizarea DUKIntegrator: fiecare declaratie, cantarita pe socoteala ei.
 *
 * Integratorul se schimba rar; declaratiile — D112, D300, D394 — se schimba des
 * si fiecare cand ii vine randul. Pana acum se cantarea numai versiunea
 * integratorului, si cat timp ea statea pe loc comanda raspundea „este deja la
 * zi” fara sa aduca nimic. Asa a fost gasita: integrator 1.4.18.3.3 in amandoua
 * partile, si D112 ramas in urma cu o versiune — adica tocmai declaratia in
 * jurul careia se invarte tot modulul.
 *
 * Fisierul „versiuniCurente.txt” e citit de DUKIntegrator insusi, asa ca se
 * cantareste si forma in care ramane dupa scriere: aceleasi randuri, in aceeasi
 * ordine, cu sfarsitul de rand windows si cu declaratiile scoase din uz
 * neatinse.
 */
class ActualizareaDukTest extends TestCase
{
    /** @var string */
    protected $dist;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dist = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'duk-' . bin2hex(random_bytes(4));

        mkdir($this->dist . DIRECTORY_SEPARATOR . 'config', 0777, true);
        mkdir($this->dist . DIRECTORY_SEPARATOR . 'lib', 0777, true);

        config(['anaf.declaratii.duk.jar' => $this->dist . DIRECTORY_SEPARATOR . 'DUKIntegrator.jar']);

        $this->scrieCatalogul([
            '1.4.18.3.3',
            'B230;J3.0.0;P3.0.0',
            'D112;J27.0.1;P3.0.1',
            '#A4201;J1.0.2;X1.0.0',
        ]);
    }

    protected function tearDown(): void
    {
        $this->stergeDosarul($this->dist);

        parent::tearDown();
    }

    /** Catalogul local, scris cu sfarsit de rand windows, ca la ANAF. */
    protected function scrieCatalogul(array $randuri): void
    {
        file_put_contents(
            $this->dist . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'versiuniCurente.txt',
            implode("\r\n", $randuri) . "\r\n"
        );
    }

    protected function catalogul(): string
    {
        return file_get_contents(
            $this->dist . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'versiuniCurente.txt'
        );
    }

    /**
     * Lista de versiuni de la ANAF, cu integratorul neschimbat si D112 mai nou —
     * chiar situatia din care s-a nascut indreptarea.
     */
    protected function listaAnaf(string $integrator = '1.4.18.3.3', string $d112 = 'J27.0.2'): string
    {
        return '<versiuni>
            <integrator>
                <versiune>' . $integrator . '</versiune>
                <zJars><jarURL>http://static.anaf.ro/z/DUKIntegrator.jar</jarURL></zJars>
            </integrator>
            <declaratii>
                <B230>
                    <versiuneJ>J3.0.0</versiuneJ>
                    <versiuneP>P3.0.0</versiuneP>
                    <JURL>http://static.anaf.ro/B230/B230Validator.jar</JURL>
                </B230>
                <D112>
                    <versiuneJ>' . $d112 . '</versiuneJ>
                    <versiuneP>P3.0.1</versiuneP>
                    <JURL>http://static.anaf.ro/D112/D112Validator.jar</JURL>
                    <PURL>http://static.anaf.ro/D112/D112Pdf.jar</PURL>
                </D112>
                <A4201>
                    <versiuneJ>J9.9.9</versiuneJ>
                    <versiuneP>X1.0.0</versiuneP>
                    <JURL>http://static.anaf.ro/A4201/A4201Validator.jar</JURL>
                </A4201>
            </declaratii>
        </versiuni>';
    }

    protected function pregatesteAnaf(string $lista = null): void
    {
        Http::fake([
            '*versiuni.xml' => Http::response($lista ?: $this->listaAnaf(), 200),
            '*' => Http::response('continutul unui jar', 200),
        ]);
    }

    /**
     * Miezul: integratorul e la zi, dar D112 nu — si totusi se aduce.
     */
    public function test_declaratia_innoita_se_aduce_desi_integratorul_e_la_zi(): void
    {
        $this->pregatesteAnaf();

        $this->artisan('anaf:duk-update', ['--url' => 'http://static.anaf.ro/versiuni.xml'])
            ->assertExitCode(0);

        $this->assertStringContainsString(
            'D112;J27.0.2;P3.0.1',
            $this->catalogul(),
            'declarația înnoită trebuie scrisă în catalog'
        );

        $this->assertFileExists(
            $this->dist . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'D112Validator.jar'
        );
    }

    /** Ce n-a fost schimbat nu se aduce: nu se descarca o suta de declaratii degeaba. */
    public function test_declaratiile_neschimbate_nu_se_aduc(): void
    {
        $this->pregatesteAnaf();

        $this->artisan('anaf:duk-update', ['--url' => 'http://static.anaf.ro/versiuni.xml']);

        $this->assertFileDoesNotExist(
            $this->dist . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'B230Validator.jar',
            'B230 e la aceeași versiune — n-avea de ce să fie adusă'
        );
    }

    /** Cand nimic nu s-a schimbat, nu se atinge nici catalogul. */
    public function test_cand_totul_e_la_zi_nu_se_schimba_nimic(): void
    {
        $this->pregatesteAnaf($this->listaAnaf('1.4.18.3.3', 'J27.0.1'));

        $inainte = $this->catalogul();

        $this->artisan('anaf:duk-update', ['--url' => 'http://static.anaf.ro/versiuni.xml'])
            ->assertExitCode(0);

        $this->assertSame($inainte, $this->catalogul());

        // Si nu s-a adus niciun fisier: nici macar al integratorului.
        $this->assertSame(
            [],
            array_diff(scandir($this->dist . DIRECTORY_SEPARATOR . 'lib'), ['.', '..'])
        );
    }

    /**
     * Declaratiile scoase din uz raman scoase.
     *
     * Cine le-a inchis avea o pricina, iar actualizarea n-are cum s-o cunoasca.
     */
    public function test_declaratia_scoasa_din_uz_nu_se_atinge(): void
    {
        $this->pregatesteAnaf();

        $this->artisan('anaf:duk-update', ['--url' => 'http://static.anaf.ro/versiuni.xml']);

        $this->assertStringContainsString(
            '#A4201;J1.0.2;X1.0.0',
            $this->catalogul(),
            'rândul scos din uz trebuie să rămână întocmai, deși ANAF are altă versiune'
        );

        $this->assertFileDoesNotExist(
            $this->dist . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'A4201Validator.jar'
        );
    }

    /** Forma fisierului se pastreaza: DUKIntegrator il citeste asa cum il stie. */
    public function test_forma_catalogului_ramane_neschimbata(): void
    {
        $this->pregatesteAnaf();

        $this->artisan('anaf:duk-update', ['--url' => 'http://static.anaf.ro/versiuni.xml']);

        $dupa = $this->catalogul();

        $this->assertSame(4, substr_count($dupa, "\r\n"), 'aceleași rânduri, cu sfârșit de rând windows');
        $this->assertStringStartsWith('1.4.18.3.3', $dupa, 'versiunea integratorului rămâne pe primul rând');
        $this->assertStringContainsString('B230;J3.0.0;P3.0.0', $dupa, 'rândurile neatinse rămân la locul lor');
    }

    /** Proba pe uscat nu scrie nimic si nu descarca nimic. */
    public function test_proba_pe_uscat_nu_atinge_nimic(): void
    {
        $this->pregatesteAnaf();

        $inainte = $this->catalogul();

        $this->artisan('anaf:duk-update', [
            '--url' => 'http://static.anaf.ro/versiuni.xml',
            '--pe-uscat' => true,
        ])->assertExitCode(0);

        $this->assertSame($inainte, $this->catalogul());

        $this->assertFileDoesNotExist(
            $this->dist . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'D112Validator.jar'
        );
    }

    /**
     * Fara DUKIntegrator instalat, lucrarea programata nu da esec in fiecare
     * noapte: spune ce e si iese cu bine.
     */
    public function test_fara_duk_instalat_nu_se_da_esec(): void
    {
        config(['anaf.declaratii.duk.jar' => $this->dist . '-care-nu-exista' . DIRECTORY_SEPARATOR . 'DUKIntegrator.jar']);

        $this->artisan('anaf:duk-update')->assertExitCode(0);
    }

    /** Comanda chiar e programata; altfel ar ramane tot pe seama cuiva care isi aminteste. */
    public function test_actualizarea_e_programata(): void
    {
        $sursa = file_get_contents(app_path('Console/Kernel.php'));

        $this->assertMatchesRegularExpression(
            "/command\('anaf:duk-update'\)[^;]*->dailyAt/",
            $sursa,
            'actualizarea trebuie să ruleze singură, în fiecare noapte'
        );
    }

    protected function stergeDosarul(string $cale): void
    {
        if (!is_dir($cale)) {
            return;
        }

        foreach (array_diff(scandir($cale), ['.', '..']) as $intrare) {
            $plin = $cale . DIRECTORY_SEPARATOR . $intrare;

            is_dir($plin) ? $this->stergeDosarul($plin) : unlink($plin);
        }

        rmdir($cale);
    }
}
