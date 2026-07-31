<?php

namespace Tests\Unit;

use App\Services\Anaf\Declaratii\DeclaratieException;
use App\Services\Anaf\Declaratii\DeclaratieXml;
use App\Services\Anaf\Declaratii\DepunereService;
use App\Services\Anaf\Declaratii\DukIntegrator;
use App\Services\Anaf\Declaratii\RecipisaService;
use App\Services\Anaf\Declaratii\SemnareService;
use Tests\TestCase;

class DeclaratiiModuleTest extends TestCase
{
    public function test_serviciile_sunt_inregistrate(): void
    {
        foreach ([DukIntegrator::class, SemnareService::class, DepunereService::class, RecipisaService::class] as $serviciu) {
            $this->assertTrue($this->app->bound($serviciu), $serviciu . ' nu este înregistrat');
            $this->assertInstanceOf($serviciu, $this->app->make($serviciu));
        }
    }

    /** @dataProvider radaciniXml */
    public function test_tipul_declaratiei_din_radacina(string $radacina, string $asteptat): void
    {
        $this->assertSame($asteptat, (new DeclaratieXml())->tipDinRadacina($radacina));
    }

    public function radaciniXml(): array
    {
        return [
            'D112 (declaratie unica)' => ['declaratieUnica', 'D112'],
            'D100 generic' => ['declaratie100', 'D100'],
            'D300 generic' => ['declaratie300', 'D300'],
            'D101 generic' => ['declaratie101', 'D101'],
            'D394 generic' => ['declaratie394', 'D394'],
            'D107 explicit' => ['d107', 'D107'],
            'C801 explicit' => ['c801', 'C801'],
            'Bilant devine S' => ['Bilant1046', 'S1046'],
            'formular ANAF' => ['f4101', 'F4101'],
            'D406 SAF-T' => ['AuditFile', 'D406'],
        ];
    }

    public function test_radacina_softa_este_respinsa(): void
    {
        $this->expectException(DeclaratieException::class);
        (new DeclaratieXml())->tipDinRadacina('form1');
    }

    public function test_tip_necunoscut_este_respins(): void
    {
        $this->expectException(DeclaratieException::class);
        (new DeclaratieXml())->tipDinRadacina('declaratie999');
    }

    /** @dataProvider namespaceuriAnaf */
    public function test_tipul_se_deduce_din_namespace(string $radacina, string $namespace, string $asteptat): void
    {
        $this->assertSame($asteptat, (new DeclaratieXml())->tipDinRadacina($radacina, $namespace));
    }

    public function namespaceuriAnaf(): array
    {
        return [
            // Namespace-ul are prioritate: radacina "D110" nu spune nimic despre versiune
            'D110' => ['D110', 'mfp:anaf:dgti:d110:declaratie:v1', 'D110'],
            'D300' => ['declaratie300', 'mfp:anaf:dgti:d300:declaratie:v10', 'D300'],
            'D406 cu sufix t' => ['AuditFile', 'mfp:anaf:dgti:d406t:declaratie:v1', 'D406'],
            'P2000 patrimoniu' => ['Patrimoniu', 'mfp:anaf:patrimoniu:v1', 'P2000'],
            // Radacina "msj" e comuna A4201/A4202/A4203 — doar namespace-ul le separa
            'A4201' => ['msj', 'mfp:anaf:dgti:a4201:declaratie:v1', 'A4201'],
            'A4202' => ['msj', 'mfp:anaf:dgti:a4202:declaratie:v1', 'A4202'],
            'A4203' => ['msj', 'mfp:anaf:dgti:a4203:declaratie:v1', 'A4203'],
            'D169n litera mica' => ['d169n', 'mfp:anaf:dgti:d169n:declaratie:v1', 'D169n'],
        ];
    }

    public function test_radacina_ambigua_fara_namespace_este_respinsa(): void
    {
        $this->expectException(DeclaratieException::class);
        (new DeclaratieXml())->tipDinRadacina('msj');
    }

    public function test_toate_tipurile_duk_sunt_recunoscute_dupa_namespace(): void
    {
        $analizor = new DeclaratieXml();

        foreach (DeclaratieXml::TIPURI as $tip) {
            $namespace = 'mfp:anaf:dgti:' . strtolower($tip) . ':declaratie:v1';

            $this->assertSame($tip, $analizor->tipDinRadacina('oricare', $namespace), 'Tipul ' . $tip . ' nu e recunoscut');
        }
    }

    public function test_indicele_de_incarcare_este_extras_din_raspunsul_anaf(): void
    {
        $serviciu = $this->app->make(DepunereService::class);

        $rezultat = $serviciu->extrageIndice('<html><b style="color: #000000">1160245317</b></html>');
        $this->assertSame('1160245317', $rezultat['index_recipisa']);
        $this->assertNull($rezultat['eroare']);
    }

    public function test_eroarea_de_depunere_este_extrasa_din_raspunsul_anaf(): void
    {
        $serviciu = $this->app->make(DepunereService::class);

        $rezultat = $serviciu->extrageIndice('<html><span class="red">Fisierul nu este semnat</span></html>');
        $this->assertNull($rezultat['index_recipisa']);
        $this->assertSame('Fisierul nu este semnat', $rezultat['eroare']);
    }

    /** @dataProvider stariRecipisa */
    public function test_clasificarea_starii_recipisei(?string $stare, string $asteptat): void
    {
        $this->assertSame($asteptat, RecipisaService::clasifica($stare));
    }

    public function stariRecipisa(): array
    {
        return [
            'valid' => ['Documentul este valid', 'valid'],
            'valid fara erori' => ['Nu exista erori de validare', 'valid'],
            'invalid' => ['Documentul are erori de validare: campul X', 'invalid'],
            'atentionari' => ['ATENTIONARI: verificati campul Y', 'valid_cu_atentionari'],
            'in prelucrare' => ['In prelucrare', 'in_prelucrare'],
            'gol' => [null, 'in_asteptare'],
        ];
    }
}
