<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Cand cad multe documente la rand, aducerea se opreste si spune de ce.
 *
 * Un client cu doua sute cincizeci de entitati inrolate pe un certificat a adus
 * 390 de documente din 568, iar de acolo incolo au cazut toate. Fila le-a
 * parcurs pe celelalte 178 una cate una, aratand la fiecare „nu s-a putut
 * aduce" — fara sa spuna de ce, si fara sa aduca nimic.
 *
 * Doua lucruri lipseau. Intai, pricina: ea se scria pe fiecare mesaj si in
 * jurnal, dar nu ajungea in fila, asa ca omul vedea o suta optzeci de esecuri
 * fara nicio vorba despre ce le doboara. Apoi, oprirea: cand legatura cu ANAF
 * s-a stricat, e stricata pentru toate, iar incercarile de dupa nu au ce
 * descoperi — dar ard apeluri care sunt numarate.
 */
class AducereaSeOpresteTest extends TestCase
{
    /** @var string */
    protected $sursa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sursa = file_get_contents(app_path('Http/Controllers/Api/SpvController.php'));
    }

    /** Pricina merge odata cu pasul, nu doar la sfarsit. */
    public function test_pricina_se_trimite_la_fiecare_pas(): void
    {
        $inceput = strpos($this->sursa, "'tip' => 'pas'");
        // Fereastra e larga: intre pas si pricina sta lamurirea de ce e acolo.
        $bucata = substr($this->sursa, $inceput, 1500);

        $this->assertStringContainsString(
            "'de_ce' => \$deCe",
            $bucata,
            'pasul căzut trebuie să spună și de ce a căzut'
        );
    }

    /** Se numara esecurile la rand, iar o izbanda le sterge. */
    public function test_esecurile_la_rand_se_numara_de_la_capat_dupa_o_izbanda(): void
    {
        $this->assertStringContainsString('$cazuteLaRand = 0;', $this->sursa);
        $this->assertStringContainsString('$cazuteLaRand++;', $this->sursa);

        /*
         * Numaratoarea trebuie stearsa de o izbanda: altfel zece esecuri
         * imprastiate de-a lungul intregii aduceri ar opri o lucrare care de
         * fapt merge — se intampla ca un document sa fie sters la ANAF.
         */
        $izbanda = strpos($this->sursa, '$descarcate++;');
        $bucata = substr($this->sursa, $izbanda, 120);

        $this->assertStringContainsString(
            '$cazuteLaRand = 0;',
            $bucata,
            'un document adus trebuie să șteargă numărătoarea'
        );
    }

    /** Se opreste, si spune limpede ca s-a oprit. */
    public function test_se_opreste_dupa_pragul_de_esecuri(): void
    {
        $this->assertStringContainsString('CAZUTE_LA_RAND = 10', $this->sursa);

        $inceput = strpos($this->sursa, 'if ($cazuteLaRand >= self::CAZUTE_LA_RAND)');

        $this->assertNotFalse($inceput, 'lipsește oprirea');

        $bucata = substr($this->sursa, $inceput, 420);

        $this->assertStringContainsString("'tip' => 'oprit'", $bucata, 'fila trebuie să afle că s-a oprit');
        $this->assertStringContainsString("'de_ce' => \$deCe", $bucata);
        $this->assertStringContainsString('break;', $bucata, 'trebuie chiar să se oprească');
    }

    /** In jurnal ramane scris ca s-a oprit, si din ce pricina. */
    public function test_oprirea_se_scrie_in_jurnal(): void
    {
        /*
         * Se cauta scrierea din fluxul de aducere, nu prima aparitie a numelui:
         * el se foloseste si mai sus, la esecul intregii cereri.
         */
        $inceput = strpos($this->sursa, 'Aducerea s-a oprit după');

        $this->assertNotFalse($inceput, 'jurnalul nu spune că s-a oprit');

        $bucata = substr($this->sursa, $inceput - 300, 900);

        $this->assertStringContainsString('eșecuri la rând', $bucata);
        $this->assertStringContainsString("'oprit' => \$oprit", $bucata);
    }

    /** Iar fila arata pricina, nu doar „nu s-a putut aduce". */
    public function test_fila_arata_pricina(): void
    {
        $fila = file_get_contents(
            resource_path('js/src/views/app_pages/spv/Mesaje.vue')
        );

        $this->assertStringContainsString('nu s-a putut aduce: ${pas.de_ce', $fila);
        $this->assertStringContainsString("pas.tip === 'oprit'", $fila);
        $this->assertStringContainsString('eșecuri la rând', $fila);
    }
}
