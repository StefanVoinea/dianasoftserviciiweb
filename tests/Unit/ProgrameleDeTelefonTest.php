<?php

namespace Tests\Unit;

use App\Services\Mobil\ProgrameleDeTelefon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Ce versiune a aplicației de telefon se dă mai departe clientului.
 *
 * Aplicațiile de Android nu trec prin niciun magazin: clientul le ia din
 * aplicația web, iar telefonul întreabă tot serverul când a apărut una mai nouă.
 * Deci versiunea trebuie știută fără greș, și fără vreun fișier alăturat care
 * să se poată desprinde de arhivă — de aceea ea stă în chiar numele fișierului.
 */
class ProgrameleDeTelefonTest extends TestCase
{
    protected $programele;

    /** Dosarul de probă pentru arhiva care vine o dată cu codul. */
    protected $dinCod;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(config('filesystems.default'));

        /*
         * Arhiva care merge cu codul stă într-un dosar adevărat din depozit, pe
         * care Storage::fake nu-l poate lua deoparte. Fără dosarul acesta al
         * nostru, probele ar da peste versiunea adevărată pusă acolo — și ar
         * cădea sau ar trece după cum se nimerește ea.
         */
        $this->dinCod = 'tests/fixturi/mobil-' . bin2hex(random_bytes(4));
        @mkdir(base_path($this->dinCod), 0777, true);
        config(['mobil.dosar_din_cod' => $this->dinCod]);

        $this->programele = new ProgrameleDeTelefon();
    }

    protected function tearDown(): void
    {
        array_map('unlink', (array) glob(base_path($this->dinCod) . '/*'));
        @rmdir(base_path($this->dinCod));

        parent::tearDown();
    }

    /** O arhivă venită o dată cu codul aplicației web. */
    protected function puneInCod(string $fisier): void
    {
        file_put_contents(base_path($this->dinCod) . '/' . $fisier, 'venită cu codul');
    }

    protected function pune(string $fisier): void
    {
        Storage::put(ProgrameleDeTelefon::DOSAR . '/' . $fisier, 'nu contează ce e înăuntru');
    }

    public function test_numele_se_desface_in_bucatile_lui(): void
    {
        $desfacut = $this->programele->desfaNumele('spv_curier-1.4.0+14.apk');

        $this->assertSame('spv_curier', $desfacut['aplicatia']);
        $this->assertSame('1.4.0', $desfacut['versiune']);
        $this->assertSame(14, $desfacut['cod']);
    }

    /** Ce nu e scris după tipar nu se ia în seamă: mai bine nimic decât greșit. */
    public function test_un_nume_care_nu_spune_versiunea_se_lasa_deoparte(): void
    {
        foreach (['spv_curier.apk', 'spv_curier-1.4.0.apk', 'ceva.zip', 'spv curier-1.0+1.apk'] as $nume) {
            $this->assertNull($this->programele->desfaNumele($nume), $nume . ' n-avea de ce să treacă');
        }
    }

    public function test_fara_nicio_arhiva_nu_se_spune_nimic(): void
    {
        $this->assertNull($this->programele->ceaMaiNoua('spv_curier'));
    }

    /**
     * Se alege după codul versiunii, nu după data fișierului.
     *
     * O arhivă copiată din nou pe server capătă data de azi fără să fie mai
     * nouă; luată după dată, ea ar chema toate telefoanele să se „înnoiască"
     * înapoi, la o versiune de dinainte.
     */
    public function test_se_alege_codul_cel_mai_mare(): void
    {
        $this->pune('spv_curier-1.2.0+9.apk');
        $this->pune('spv_curier-1.10.0+21.apk');
        $this->pune('spv_curier-1.4.0+14.apk');

        $noua = $this->programele->ceaMaiNoua('spv_curier');

        $this->assertSame(21, $noua['cod']);
        $this->assertSame('1.10.0', $noua['versiune']);
    }

    /** Fiecare aplicație își vede numai arhivele ei. */
    public function test_aplicatiile_nu_se_amesteca(): void
    {
        $this->pune('spv_curier-1.0.0+3.apk');
        $this->pune('etransport-2.0.0+40.apk');

        $this->assertSame(3, $this->programele->ceaMaiNoua('spv_curier')['cod']);
        $this->assertSame(40, $this->programele->ceaMaiNoua('etransport')['cod']);
    }

    /** O aplicație pe care n-o știm nu se caută deloc. */
    public function test_o_aplicatie_nestiuta_nu_are_versiune(): void
    {
        $this->pune('altceva-1.0.0+1.apk');

        $this->assertNull($this->programele->ceaMaiNoua('altceva'));
    }

    /**
     * Arhiva din depozit și cea urcată din filă se cântăresc laolaltă.
     *
     * Una vine o dată cu codul, la publicarea aplicației web; cealaltă se urcă
     * între două publicări, când o îndreptare nu poate aștepta. Amândouă sunt
     * bune, iar cea care se dă e cea cu codul mai mare — nu cea din locul pe
     * care l-am privilegia noi.
     */
    public function test_se_cantareste_si_ce_vine_cu_codul_si_ce_s_a_urcat(): void
    {
        $this->puneInCod('spv_curier-9.9.9+99.apk');

        // Urcată din filă, dar mai veche: nu ea se dă.
        $this->pune('spv_curier-1.0.0+3.apk');

        $this->assertSame(99, $this->programele->ceaMaiNoua('spv_curier')['cod']);

        // Iar când cea urcată e mai nouă, ea trece înaintea celei din depozit.
        $this->pune('spv_curier-9.9.10+100.apk');

        $this->assertSame(100, $this->programele->ceaMaiNoua('spv_curier')['cod']);
    }

    /** Numai cea din depozit, când nu s-a urcat nimic: aceea se dă. */
    public function test_arhiva_din_depozit_e_de_ajuns(): void
    {
        $this->puneInCod('spv_curier-2.0.0+30.apk');

        $noua = $this->programele->ceaMaiNoua('spv_curier');

        $this->assertSame(30, $noua['cod']);
        $this->assertStringEndsWith('spv_curier-2.0.0+30.apk', $noua['cale']);
    }

    /** Numele de descărcare se citește de om, deci fără codul din coadă. */
    public function test_numele_de_descarcare_e_pe_intelesul_omului(): void
    {
        $this->assertSame(
            'spv_curier-1.4.0.apk',
            $this->programele->numeDeDescarcare('spv_curier', '1.4.0')
        );
    }
}
