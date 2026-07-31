<?php

namespace Tests\Unit;

use App\Models\AnafJurnal;
use App\Services\Anaf\Jurnal;
use Tests\TestCase;

class JurnalTest extends TestCase
{
    protected function tearDown(): void
    {
        AnafJurnal::where('actiune', 'like', 'test_%')->delete();

        parent::tearDown();
    }

    public function test_intrarea_retine_actiunea_descrierea_si_contextul(): void
    {
        $intrare = Jurnal::scrie(
            'test_actiune',
            'A făcut ceva important',
            ['detaliu' => 42],
            '15208744'
        );

        $this->assertSame('test_actiune', $intrare->actiune);
        $this->assertSame('A făcut ceva important', $intrare->descriere);
        $this->assertSame('15208744', $intrare->cif);
        $this->assertSame(['detaliu' => 42], $intrare->context);
        $this->assertTrue($intrare->reusit);
    }

    public function test_esecurile_sunt_marcate_distinct(): void
    {
        $intrare = Jurnal::esec('test_esec', 'Operația a eșuat');

        $this->assertFalse($intrare->reusit);
    }

    public function test_descrierea_lunga_este_trunchiata_la_limita_coloanei(): void
    {
        $intrare = Jurnal::scrie('test_lung', str_repeat('a', 900));

        $this->assertSame(500, mb_strlen($intrare->descriere));
    }

    /**
     * O declarație D406 respinsă aduce zeci de mii de rânduri de erori. Scrise
     * întregi, ele depășeau coloana și scrierea în jurnal cădea — adică tocmai
     * eșecul rămânea neconsemnat, iar încărcarea se oprea cu eroare.
     */
    public function test_contextul_urias_este_scurtat_si_intrarea_se_scrie(): void
    {
        $erori = str_repeat("E: SourceDocuments (1) sectiune Invoice (1)\n eroare atribut: valoare nepermisa\n", 5000);

        $intrare = Jurnal::scrie('test_context_mare', 'A încărcat o declarație respinsă', [
            'tip' => 'D406',
            'erori' => $erori,
        ]);

        $this->assertNotNull($intrare->id, 'intrarea trebuie să ajungă în jurnal');
        $this->assertSame('D406', $intrare->context['tip'], 'restul contextului rămâne neatins');

        $pastrat = $intrare->fresh()->context['erori'];

        $this->assertLessThan(mb_strlen($erori), mb_strlen($pastrat));
        $this->assertStringContainsString('E: SourceDocuments', $pastrat, 'începutul erorilor se păstrează');
        $this->assertStringContainsString('nu se ține în jurnal', $pastrat, 'se spune că textul e tăiat');
    }

    /** Un context de mărime obișnuită trece neatins. */
    public function test_contextul_scurt_ramane_neschimbat(): void
    {
        $intrare = Jurnal::scrie('test_context_mic', 'Ceva', ['erori' => 'E: o singură eroare']);

        $this->assertSame('E: o singură eroare', $intrare->fresh()->context['erori']);
    }

    /**
     * Fara utilizator autentificat intrarea ramane valida — jurnalul nu trebuie
     * sa blocheze operatiile executate din consola sau din sarcini programate.
     */
    public function test_intrarea_se_scrie_si_fara_utilizator_autentificat(): void
    {
        $intrare = Jurnal::scrie('test_fara_user', 'Rulare din consolă');

        $this->assertNull($intrare->user_id);
        $this->assertNull($intrare->user_nume);
    }

    public function test_toate_actiunile_au_eticheta_lizibila(): void
    {
        foreach (AnafJurnal::ACTIUNI as $cheie => $eticheta) {
            $this->assertNotEmpty($eticheta, 'Acțiunea ' . $cheie . ' nu are etichetă');
            $this->assertNotSame($cheie, $eticheta, 'Eticheta pentru ' . $cheie . ' este identică cu slug-ul');
        }
    }

    public function test_eticheta_lipsa_cade_pe_numele_actiunii(): void
    {
        $intrare = new AnafJurnal(['actiune' => 'actiune_necunoscuta']);

        $this->assertSame('actiune_necunoscuta', $intrare->actiune_etichete);
    }
}
