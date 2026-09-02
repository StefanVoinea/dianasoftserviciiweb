<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\SpvMesaj;
use App\Services\Anaf\Arhiva\ArhivaService;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\ProgramLocalVechiException;
use App\Services\Anaf\Spv\SpvClient;
use App\Services\Anaf\Spv\SpvStorage;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * Documentele se cer programului local în transe, nu unul câte unul.
 *
 * Fiecare document însemna un drum întreg până la calculatorul clientului:
 * comanda dusă, răspunsul adus înapoi. La cincizeci de documente, cincizeci de
 * drumuri pentru o lucrare care e, de fapt, una singură.
 *
 * Pauza cerută de ANAF nu dispare — o ține acum programul local, unde e și
 * apelul.
 */
class DocumenteleInTranseTest extends TestCase
{
    protected const COMPANIE = 996;

    protected $unul;
    protected $altul;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->unul = $this->certificat('TOKENUL DINTÂI');
        $this->altul = $this->certificat('TOKENUL AL DOILEA');
    }

    protected function tearDown(): void
    {
        SpvMesaj::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function certificat(string $cn): AnafCertificat
    {
        return AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => $cn,
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
        ]);
    }

    protected function mesaj(string $id, ?int $certificatId): SpvMesaj
    {
        return SpvMesaj::create([
            'company_id' => self::COMPANIE,
            'mesaj_id' => $id,
            'cif' => '15208744',
            'tip' => 'CERTIFICAT FISCAL',
            'data_creare' => '2026-08-20 09:00:00',
            'certificat_id' => $certificatId,
        ]);
    }

    /** O arhivă care spune doar că e pornită: nimic nu pleacă nicăieri. */
    protected function arhiva(): ArhivaService
    {
        return new class(['activa' => true], new CertificatService([])) extends ArhivaService {
            public function uneste(?string $cif, string $firma): void
            {
            }
        };
    }

    /**
     * Un client SPV de probă, care ține minte ce i s-a cerut.
     *
     * @param callable|null $lot ce întoarce pentru o transă
     */
    protected function client(?callable $lot = null): SpvClient
    {
        return new class($lot) extends SpvClient {
            public $transe = [];
            public $singure = [];
            protected $lot;

            public function __construct(?callable $lot)
            {
                $this->lot = $lot;
            }

            public function descarcaLotInArhiva(array $documente): array
            {
                $this->transe[] = count($documente);

                if ($this->lot === null) {
                    throw new ProgramLocalVechiException('program vechi');
                }

                return ($this->lot)($documente);
            }

            public function descarcaInArhiva(string $id, array $destinatie): array
            {
                $this->singure[] = $id;

                return ['cale' => 'F/SPV/' . $id . '.pdf', 'extensie' => 'pdf', 'marime' => 1, 'hash' => 'h'];
            }
        };
    }

    protected function magazia(SpvClient $client): SpvStorage
    {
        return new SpvStorage(
            $this->app->make(CertificatService::class),
            $this->arhiva(),
            null,
            $client
        );
    }

    /** Ce întoarce programul local pentru o transă izbutită. */
    protected function izbanda(): callable
    {
        return function (array $documente) {
            $iesite = [];

            foreach ($documente as $cerinta) {
                $iesite[(string) $cerinta['id']] = [
                    'stare' => 200,
                    'id' => $cerinta['id'],
                    'cale' => 'F/SPV/' . $cerinta['id'] . '.pdf',
                    'extensie' => 'pdf',
                    'marime' => 10,
                    'hash' => 'h' . $cerinta['id'],
                ];
            }

            return $iesite;
        };
    }

    public function test_documentele_aceluiasi_token_pleaca_intr_o_transa(): void
    {
        $mesaje = [
            $this->mesaj('900001', $this->unul->id),
            $this->mesaj('900002', $this->unul->id),
            $this->mesaj('900003', $this->unul->id),
        ];

        $client = $this->client($this->izbanda());

        $pasi = iterator_to_array($this->magazia($client)->aduceLotul($mesaje, 10), false);

        $this->assertSame([3], $client->transe, 'trebuia o singură transă');
        $this->assertSame([], $client->singure);
        $this->assertCount(3, $pasi);
        $this->assertTrue($pasi[0]['reusit']);

        $this->assertSame('F/SPV/900001.pdf', $mesaje[0]->fresh()->arhiva_cale);
    }

    /** Transa nu trece de măsura cerută: mersul lucrării trebuie să se vadă. */
    public function test_transa_nu_trece_de_masura_ceruta(): void
    {
        $mesaje = [];

        foreach (range(1, 5) as $i) {
            $mesaje[] = $this->mesaj('90001' . $i, $this->unul->id);
        }

        $client = $this->client($this->izbanda());

        iterator_to_array($this->magazia($client)->aduceLotul($mesaje, 2), false);

        $this->assertSame([2, 2, 1], $client->transe);
    }

    /**
     * Cu două tokene se rămâne la document și document.
     *
     * Pauza cerută de ANAF se ține pe fiecare certificat, deci cât așteaptă
     * unul, celălalt lucrează — iar transele, cerute una după alta, ar pierde
     * tocmai suprapunerea asta.
     */
    public function test_cu_doua_tokene_se_ramane_la_document_si_document(): void
    {
        $mesaje = [
            $this->mesaj('900021', $this->unul->id),
            $this->mesaj('900022', $this->altul->id),
        ];

        $client = $this->client($this->izbanda());

        iterator_to_array($this->magazia($client)->aduceLotul($mesaje, 10), false);

        $this->assertSame([], $client->transe, 'transele n-aveau ce căuta aici');
        $this->assertSame(['900021', '900022'], $client->singure);
    }

    /** Un program local mai vechi nu oprește lucrarea: se face ca înainte. */
    public function test_programul_vechi_lucreaza_ca_inainte(): void
    {
        $mesaje = [
            $this->mesaj('900031', $this->unul->id),
            $this->mesaj('900032', $this->unul->id),
        ];

        // Fără callback, transa se plânge că programul e vechi.
        $client = $this->client(null);

        $pasi = iterator_to_array($this->magazia($client)->aduceLotul($mesaje, 10), false);

        $this->assertSame([2], $client->transe, 'transa a fost încercată o dată');
        $this->assertSame(['900031', '900032'], $client->singure);
        $this->assertCount(2, $pasi);
        $this->assertTrue($pasi[1]['reusit']);
    }

    /** Un document respins în transă își spune pricina și n-o oprește. */
    public function test_documentul_respins_nu_opreste_transa(): void
    {
        $mesaje = [
            $this->mesaj('900041', $this->unul->id),
            $this->mesaj('900042', $this->unul->id),
        ];

        $client = $this->client(function (array $documente) {
            return [
                '900041' => ['stare' => 502, 'id' => '900041', 'eroare' => 'ANAF nu dă documentul'],
                '900042' => [
                    'stare' => 200, 'id' => '900042', 'cale' => 'F/SPV/900042.pdf',
                    'extensie' => 'pdf', 'marime' => 10, 'hash' => 'h',
                ],
            ];
        });

        $pasi = iterator_to_array($this->magazia($client)->aduceLotul($mesaje, 10), false);

        $this->assertFalse($pasi[0]['reusit']);
        $this->assertStringContainsString('ANAF nu dă documentul', $pasi[0]['eroare']);
        $this->assertTrue($pasi[1]['reusit']);

        // Eșecul se scrie pe mesaj: după atâtea încercări, el nu se mai cere.
        $this->assertSame(1, $mesaje[0]->fresh()->incercari);
        $this->assertSame('F/SPV/900042.pdf', $mesaje[1]->fresh()->arhiva_cale);
    }
}
