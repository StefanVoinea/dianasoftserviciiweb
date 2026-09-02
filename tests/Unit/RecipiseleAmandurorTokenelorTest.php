<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Services\Anaf\Declaratii\RecipisaService;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\SpvClient;
use App\Services\Anaf\Spv\SpvStorage;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Recipisele se cauta cu certificatul care a depus declaratia.
 *
 * La un client cu doua tokene pe acelasi calculator, trei declaratii depuse nu
 * si-au mai primit recipisele: lista de mesaje SPV se cerea o singura data, cu
 * certificatul activ intamplator, iar recipisele intrate pe drepturile
 * celuilalt token nu se aflau in ea. Apoi StareD112 a spus „Documentul este
 * valid", starea s-a scris pe declaratie, si cu asta declaratia a iesit din
 * coada de verificare — recipisa ei nu s-a mai cautat niciodata.
 */
class RecipiseleAmandurorTokenelorTest extends TestCase
{
    protected const COMPANIE = 995;

    protected $unul;
    protected $altul;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->unul = $this->certificat('TOKENUL DINTÂI', 'A1', true);
        $this->altul = $this->certificat('TOKENUL AL DOILEA', 'B2', false);
    }

    protected function tearDown(): void
    {
        AnafDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function certificat(string $cn, string $sufix, bool $implicit): AnafCertificat
    {
        return AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => str_pad($sufix, 40, 'F', STR_PAD_LEFT),
            'cn' => $cn,
            'activ' => true,
            'implicit' => $implicit,
            'valabil_pana_la' => now()->addYear(),
        ]);
    }

    protected function declaratie(string $index, ?int $certificatId, array $peste = []): AnafDeclaratie
    {
        return AnafDeclaratie::create(array_merge([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'proba.xml',
            'tip' => 'D112',
            'cui' => '43959232',
            'den_firma' => 'KAIZEN CHOICE S.R.L.',
            'luna' => 1,
            'anul' => 2023,
            'pas' => 'depus',
            'index_recipisa' => $index,
            'certificat_id' => $certificatId,
        ], $peste));
    }

    /**
     * Starea aflata pe StareD112 nu scoate declaratia din coada.
     *
     * „Documentul este valid" e verdictul, nu recipisa: documentul ei vine abia
     * dupa aceea, prin SPV. Cat timp starea o scotea din coada, recipisa nu se
     * mai cauta niciodata — exact ce s-a intamplat la clientul cu doua tokene.
     */
    public function test_starea_finala_nu_scoate_declaratia_din_coada(): void
    {
        $this->declaratie('1198828676', $this->unul->id, [
            'stare_declaratie' => 'D112 Documentul este valid INTERNT-1198828676-2026 din 12.08.2026',
        ]);
        // Doar recipisa adusa incheie asteptarea.
        $this->declaratie('1198828765', $this->unul->id, [
            'pas' => 'finalizat',
            'stare_declaratie' => 'Documentul este valid',
        ]);

        $inCoada = AnafDeclaratie::asteaptaRecipisa()->pluck('index_recipisa')->all();

        $this->assertSame(['1198828676'], $inCoada);
    }

    /** Lista de mesaje se cere cu certificatul declaratiei, o data pe certificat. */
    public function test_lista_de_mesaje_se_cere_cu_certificatul_declaratiei(): void
    {
        // StareD112 raspunde fara randul indicelui: declaratiile raman „In prelucrare".
        Http::fake(['*' => Http::response('<html><body>nimic</body></html>', 200)]);

        $this->declaratie('111', $this->unul->id);
        $this->declaratie('222', $this->altul->id);
        $this->declaratie('333', $this->altul->id);

        $certificate = new CertificatService([]);

        // Un client SPV de proba, care tine minte cu ce certificat a fost intrebat.
        $client = new class($certificate) extends SpvClient {
            public $intrebat = [];
            protected $alegerea;

            public function __construct(CertificatService $alegerea)
            {
                $this->alegerea = $alegerea;
            }

            public function listaMesaje(int $zile = 60, ?string $cif = null): array
            {
                $this->intrebat[] = optional($this->alegerea->activ())->cn;

                return ['mesaje' => []];
            }
        };

        $serviciu = new RecipisaService(
            config('anaf.declaratii'),
            $client,
            app(SpvStorage::class),
            null,
            $certificate
        );

        $serviciu->verificaToate();

        // Cate o lista pe certificat — nu una singura si nu cate una pe declaratie.
        $this->assertSame(['TOKENUL DINTÂI', 'TOKENUL AL DOILEA'], $client->intrebat);
    }

    /**
     * Starile publice se intreaba o data pentru fiecare declaratie, nu de doua ori.
     *
     * Ele se aduc acum toate deodata, inainte de lucrul propriu-zis, fiindca
     * StareD112 e o pagina publica ceruta de pe serverul nostru: n-are nici
     * certificat, nici bridge, nici pauza ceruta de ANAF pentru SPV. Pagina
     * adusa asa trebuie insa si folosita — altfel fiecare declaratie ar fi
     * intrebata a doua oara si n-am fi castigat nimic, ci am fi indoit munca.
     */
    public function test_starea_publica_se_cere_o_singura_data_de_declaratie(): void
    {
        Http::fake(['*' => Http::response('<html><body>nimic</body></html>', 200)]);

        $this->declaratie('111', $this->unul->id);
        $this->declaratie('222', $this->unul->id);
        $this->declaratie('333', $this->unul->id);

        $certificate = new CertificatService([]);

        $client = new class($certificate) extends SpvClient {
            protected $alegerea;

            public function __construct(CertificatService $alegerea)
            {
                $this->alegerea = $alegerea;
            }

            public function listaMesaje(int $zile = 60, ?string $cif = null): array
            {
                return ['mesaje' => []];
            }
        };

        $serviciu = new RecipisaService(
            config('anaf.declaratii'),
            $client,
            app(SpvStorage::class),
            null,
            $certificate
        );

        $serviciu->verificaToate();

        Http::assertSentCount(3);
    }
}
