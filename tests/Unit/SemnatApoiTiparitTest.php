<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Models\User;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * „Semnează declarațiile valide" urmat de „Imprimă declarațiile semnate".
 *
 * Sunt doua cereri, una dupa alta: intai se semneaza fiecare declaratie, apoi
 * se cer la tiparire cele izbutite. Intre ele, semnarea arhiveaza documentul la
 * client si — cand asa e configurat — il sterge de pe server, mutand calea din
 * „cale_pdf_semnat" in „arhiva_semnat".
 *
 * Tiparirea trebuie sa se uite la amandoua. Cauta numai in una, ea nu gaseste
 * nimic si raspunde „Niciuna dintre declaratiile cerute nu are PDF semnat" —
 * desi in tabel declaratia scrie limpede ca e semnata.
 */
class SemnatApoiTiparitTest extends TestCase
{
    protected const COMPANIE = 998;

    protected $omul;
    protected $certificat;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(config('filesystems.default'));

        DB::table('companies')->insert([
            'id' => self::COMPANIE,
            'denumire' => 'FIRMA DE PROBĂ 998',
        ]);

        $this->omul = User::create([
            'name' => 'Contabilul',
            'email' => 'tiparire998@example.test',
            'password' => bcrypt('proba'),
        ]);

        DB::table('company_user')->insert([
            'user_id' => $this->omul->id,
            'company_id' => self::COMPANIE,
            'administrator' => true,
            'module' => json_encode(['spv']),
        ]);

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'POPESCU ION',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
        ]);
    }

    protected function tearDown(): void
    {
        AnafDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        DB::table('company_user')->where('company_id', self::COMPANIE)->delete();
        DB::table('companies')->where('id', self::COMPANIE)->delete();
        User::where('email', 'tiparire998@example.test')->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function declaratia(array $peste = []): AnafDeclaratie
    {
        return AnafDeclaratie::create(array_merge([
            'company_id' => self::COMPANIE,
            'user_id' => $this->omul->id,
            'nume_fisier' => 'D100_15208744_2026-07.xml',
            'tip' => 'D100',
            'cui' => '15208744',
            'certificat_id' => $this->certificat->id,
            'pas' => 'semnat',
            'semnat' => true,
        ], $peste));
    }

    protected function cereTiparirea(array $id)
    {
        return $this->actingAs($this->omul, 'api')
            ->withHeader('AuthorizationHeader', (string) self::COMPANIE)
            ->postJson('/api/declaratii/concateneaza', ['id' => $id, 'tip' => 'semnat']);
    }

    /** Documentul semnat stă pe server: se găsește. */
    public function test_semnatul_de_pe_server_se_gaseste(): void
    {
        $declaratie = $this->declaratia(['cale_pdf_semnat' => 'spv/d100_semnat.pdf']);

        $raspuns = $this->cereTiparirea([$declaratie->id]);

        $this->assertNotSame(
            422,
            $raspuns->status(),
            'declarația semnată nu trebuie să fie declarată nesemnată: ' . $raspuns->json('message')
        );
    }

    /**
     * Documentul semnat a plecat in arhiva clientului si s-a sters de pe server.
     *
     * Asa ramane cand „sterge_de_pe_server" e pornit — si asa arata declaratia
     * imediat dupa semnare, fiindca arhivarea se face chiar acolo, inainte ca
     * fila sa apuce sa ceara tiparirea.
     */
    public function test_semnatul_din_arhiva_clientului_se_gaseste(): void
    {
        $declaratie = $this->declaratia([
            'cale_pdf_semnat' => null,
            'arhiva_semnat' => 'FIRMA (15208744)/D100/D100_15208744_2026-07_semnata.pdf',
        ]);

        $raspuns = $this->cereTiparirea([$declaratie->id]);

        $this->assertNotSame(
            422,
            $raspuns->status(),
            'declarația arhivată la client nu trebuie să fie declarată nesemnată: ' . $raspuns->json('message')
        );
    }

    /**
     * Cand chiar nu e semnata nicaieri, mesajul trebuie sa fie cel de „nu e
     * semnata" — nu altul. Aici se cantareste ca proba de mai sus inseamna ceva.
     */
    public function test_nesemnata_ramane_nesemnata(): void
    {
        $declaratie = $this->declaratia([
            'pas' => 'validat',
            'semnat' => false,
            'cale_pdf' => 'spv/d100_duk.pdf',
        ]);

        $raspuns = $this->cereTiparirea([$declaratie->id]);

        $raspuns->assertStatus(422);
        $this->assertStringContainsString('nu are PDF semnat', $raspuns->json('message'));
    }

    /**
     * Semnarea scrie una dintre cele doua cai, niciodata niciuna.
     *
     * Daca arhivarea reuseste, calea de pe server se schimba in cea din arhiva;
     * daca nu, ramane cea de pe server. Un „pas = semnat" fara nicio cale ar
     * insemna o declaratie care arata semnata si nu se poate nici tipari, nici
     * depune.
     */
    public function test_semnarea_nu_lasa_declaratia_fara_nicio_cale(): void
    {
        $sursa = file_get_contents(app_path('Http/Controllers/Api/DeclaratiiController.php'));

        $inceput = strpos($sursa, "if (isset(\$cai['arhiva_semnat'])) {");
        $bucata = substr($sursa, $inceput, 600);

        $this->assertNotFalse($inceput, 'nu s-a găsit ștergerea de pe server');
        $this->assertStringContainsString(
            "\$sterse['cale_pdf_semnat'] = null;",
            $bucata,
            'calea de pe server se șterge numai odată cu scrierea celei din arhivă'
        );
    }
}
