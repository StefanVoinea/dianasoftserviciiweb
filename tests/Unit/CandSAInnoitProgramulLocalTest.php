<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * De când are programul local versiunea pe care o are.
 *
 * Versiunea se știa de mult — o spune singur la fiecare pândă —, dar nu și de
 * când o are. Iar asta e tocmai ce se întreabă omul când ceva nu merge: a apucat
 * calculatorul acesta să ia îndreptarea de ieri, sau a rămas în urmă?
 *
 * Ziua se scrie numai când versiunea chiar s-a schimbat. Pusă la fiecare pândă,
 * ea ar fi arătat mereu „acum un minut" și n-ar fi răspuns la nimic.
 */
class CandSAInnoitProgramulLocalTest extends TestCase
{
    protected const COMPANIE = 999;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);
    }

    protected function tearDown(): void
    {
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function certificat(array $peste = []): AnafCertificat
    {
        return AnafCertificat::create(array_merge([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'POPESCU ION',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
        ], $peste));
    }

    /**
     * Ce face puntea la fiecare pândă a agentului.
     *
     * Se scrie aici, nu se cheamă controllerul: acolo ar trebui un agent
     * adevărat, cu cod de instalare și tot dichisul, iar ce se probează e numai
     * regula de mai jos.
     */
    protected function panda(AnafCertificat $certificat, string $versiunea): void
    {
        AnafCertificat::query()->toateCompaniile()
            ->whereIn('id', [$certificat->id])
            ->where(function ($intrebare) use ($versiunea) {
                $intrebare->where('versiune_bridge', '!=', $versiunea)
                    ->orWhereNull('versiune_bridge')
                    ->orWhereNull('versiune_la');
            })
            ->update(['versiune_bridge' => $versiunea, 'versiune_la' => now()]);
    }

    /** Prima dată când se aude versiunea, se scrie și ziua. */
    public function test_prima_versiune_isi_scrie_ziua(): void
    {
        $certificat = $this->certificat();

        $this->assertNull($certificat->versiune_la);

        $this->panda($certificat, 'abcdef0123456789');

        $proaspat = $certificat->fresh();

        $this->assertSame('abcdef0123456789', $proaspat->versiune_bridge);
        $this->assertNotNull($proaspat->versiune_la);
    }

    /** Aceeași versiune, la o pândă mai târziu, nu mută ziua. */
    public function test_aceeasi_versiune_nu_muta_ziua(): void
    {
        $ieri = now()->subDay();

        $certificat = $this->certificat([
            'versiune_bridge' => 'abcdef0123456789',
            'versiune_la' => $ieri,
        ]);

        $this->panda($certificat, 'abcdef0123456789');

        $this->assertSame(
            $ieri->toDateTimeString(),
            $certificat->fresh()->versiune_la->toDateTimeString(),
            'ziua trebuia să rămână cea a înnoirii, nu a ultimei pânde'
        );
    }

    /** O versiune nouă mută ziua: atunci s-a înnoit. */
    public function test_versiunea_noua_muta_ziua(): void
    {
        $certificat = $this->certificat([
            'versiune_bridge' => 'abcdef0123456789',
            'versiune_la' => now()->subDays(3),
        ]);

        $this->panda($certificat, 'fedcba9876543210');

        $proaspat = $certificat->fresh();

        $this->assertSame('fedcba9876543210', $proaspat->versiune_bridge);
        $this->assertTrue($proaspat->versiune_la->isToday());
    }

    /** Instalările vechi, care aveau versiunea dar nu și ziua, o capătă acum. */
    public function test_instalarea_veche_isi_capata_ziua(): void
    {
        $certificat = $this->certificat([
            'versiune_bridge' => 'abcdef0123456789',
            'versiune_la' => null,
        ]);

        $this->panda($certificat, 'abcdef0123456789');

        $this->assertNotNull($certificat->fresh()->versiune_la);
    }
}
