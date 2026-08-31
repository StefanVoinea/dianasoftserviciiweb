<?php

namespace Tests\Unit;

use App\Models\AnafJurnal;
use App\Models\User;
use App\Services\Anaf\Jurnal;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * Cine scrie in jurnal, cand nu scrie un om.
 *
 * Programul de pe calculatorul clientului lucreaza singur — aduce fisiere din
 * dosarul urmarit, semneaza cu tokenul, isi cere licenta — si nu se legitimeaza
 * cu contul nimanui. Randurile lui se citeau „necunoscut", desi se stie foarte
 * bine cine le-a facut.
 */
class AutorulDinJurnalTest extends TestCase
{
    protected const COMPANIE = 990;

    protected function tearDown(): void
    {
        AnafJurnal::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        request()->headers->remove('Authorization');
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function scrie(?string $autor = null): AnafJurnal
    {
        return ContextCompanie::pentru(self::COMPANIE, function () use ($autor) {
            return Jurnal::scrie('proba_autor', 'Probă', [], null, true, $autor);
        });
    }

    /**
     * Cererea vine chiar de la programul local: el se legitimeaza cu codul lui
     * de instalare, care nu e un jeton al aplicatiei.
     */
    public function test_cererea_programului_local_se_insemneaza_ca_atare(): void
    {
        request()->headers->set('Authorization', 'Bearer cod-de-instalare-al-agentului');

        $this->assertSame(Jurnal::BRIDGE, $this->scrie()->user_nume);
    }

    /**
     * Lucrarile pornite din program — dosarul urmarit, licentierea — se fac tot
     * prin programul local, desi cererea nu vine de la el.
     */
    public function test_lucrarea_facuta_prin_program_isi_spune_autorul(): void
    {
        $this->assertSame(Jurnal::BRIDGE, $this->scrie(Jurnal::BRIDGE)->user_nume);
    }

    /** Cand n-a lucrat nici om, nici program, randul ramane fara nume. */
    public function test_fara_nimeni_randul_ramane_fara_nume(): void
    {
        $this->assertNull($this->scrie()->user_nume);
    }

    /**
     * Omul bate programul: daca lucrarea a fost pornita de cineva anume, numele
     * lui spune mai mult decat „Bridge local".
     */
    public function test_omul_autentificat_ramane_deasupra(): void
    {
        // Jetoanele aplicatiei au trei bucati despartite de puncte; fara asta,
        // insemnarea nici nu mai intreaba gardul cine e omul.
        request()->headers->set('Authorization', 'Bearer parte.a.doua');

        $this->actingAs(new User(['name' => 'Ana Popescu']), 'api');

        $this->assertSame('Ana Popescu', $this->scrie(Jurnal::BRIDGE)->user_nume);
    }
}
