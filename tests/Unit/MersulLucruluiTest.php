<?php

namespace Tests\Unit;

use App\Support\Flux;
use Tests\TestCase;

/**
 * Cele trei descarcari lungi isi spun mersul, cat timp se lucreaza.
 *
 * „Descarcă mesaje", „Preia răspunsurile" si „Descarcă recipise" tin fiecare
 * minute: pentru fiecare document se asteapta pauza ceruta de ANAF si drumul
 * pana la tokenul clientului. Pana acum omul vedea o rotita si atat — lucrul si
 * impotmolirea aratau la fel. Acum raspunsul curge, cate un rand pe masura ce
 * se lucreaza, si dupa fiecare document se spune al catelea e din cate.
 */
class MersulLucruluiTest extends TestCase
{
    /**
     * Fiecare pas pleaca pe randul lui, incheiat — altfel fila n-ar sti unde se
     * termina unul si incepe altul, si ar astepta pana la sfarsit.
     *
     * Se cantareste scrierea unui rand, nu ce se aduna in tampon: fluxul isi
     * goleste dinadins toate tampoanele, ca nimic sa nu-i stea in cale.
     */
    public function test_fiecare_pas_pleaca_pe_randul_lui(): void
    {
        $pasi = [
            ['tip' => 'inceput', 'total' => 2],
            ['tip' => 'pas', 'facute' => 1, 'total' => 2, 'ce' => 'DECIZIE 15208744'],
            ['tip' => 'gata', 'descarcate' => 2],
        ];

        $scris = '';

        foreach ($pasi as $pas) {
            $scris .= Flux::rand($pas);
        }

        $randuri = array_values(array_filter(explode("\n", $scris)));

        $this->assertCount(3, $randuri, 'fiecare pas trebuie să plece pe rândul lui');

        $citite = array_map(function ($rand) {
            return json_decode($rand, true);
        }, $randuri);

        $this->assertSame('inceput', $citite[0]['tip']);
        $this->assertSame(2, $citite[0]['total']);
        $this->assertSame(1, $citite[1]['facute']);
        $this->assertSame('gata', $citite[2]['tip']);
    }

    /** Diacriticele raman citibile: numele documentelor le poarta. */
    public function test_randurile_pastreaza_diacriticele(): void
    {
        $this->assertStringContainsString('SOMAȚIE de plată', Flux::rand(['ce' => 'SOMAȚIE de plată']));
    }

    /**
     * Serverul care sta in fata aplicatiei trebuie oprit de la tamponare: fara
     * antetul acesta, totul ar ajunge deodata, la sfarsit, si n-ar mai fi nimic
     * de aratat pe drum.
     */
    public function test_tamponarea_din_serverul_de_web_e_oprita(): void
    {
        $raspuns = Flux::raspunde(function () {
            yield ['tip' => 'gata'];
        });

        $this->assertSame('no', $raspuns->headers->get('X-Accel-Buffering'));
        $this->assertStringContainsString('application/x-ndjson', $raspuns->headers->get('Content-Type'));
        $this->assertStringContainsString('no-store', $raspuns->headers->get('Cache-Control'));
    }

    /** Cele trei rute exista, toate pe flux. */
    public function test_cele_trei_descarcari_au_ruta_lor_de_flux(): void
    {
        $cai = collect(app('router')->getRoutes())->map(function ($ruta) {
            return $ruta->uri();
        });

        foreach ([
            'api/spv/descarca-lipsa/flux',
            'api/spv/solicitari/preia/flux',
            'api/declaratii/recipise/flux',
        ] as $cale) {
            $this->assertTrue($cai->contains($cale), 'lipsește ruta ' . $cale);
        }
    }

    /**
     * Totalul se spune de la bun inceput: fara el, fila n-ar avea din ce sa
     * scrie „al catelea din cate" si ar ramane cu o rotita.
     */
    public function test_fiecare_flux_isi_spune_totalul_inainte_de_lucru(): void
    {
        foreach ([
            app_path('Http/Controllers/Api/SpvController.php'),
            app_path('Services/Anaf/Spv/SolicitareService.php'),
            app_path('Services/Anaf/Declaratii/RecipisaService.php'),
        ] as $fisier) {
            $this->assertStringContainsString(
                "'tip' => 'inceput'",
                file_get_contents($fisier),
                basename($fisier) . ' nu-și spune totalul la început'
            );
        }
    }

    /** Filele arata mersul, nu doar rotita. */
    public function test_filele_arata_mersul_lucrului(): void
    {
        $file = [
            'Mesaje.vue' => 'spv/descarca-lipsa/flux',
            'Solicitari.vue' => 'spv/solicitari/preia/flux',
            'Declaratii.vue' => 'declaratii/recipise/flux',
        ];

        foreach ($file as $nume => $ruta) {
            $continut = file_get_contents(base_path('resources/js/src/views/app_pages/spv/' . $nume));

            $this->assertStringContainsString($ruta, $continut, $nume . ' nu cere fluxul');
            $this->assertStringContainsString('mersul', $continut, $nume . ' nu arată mersul lucrului');
            $this->assertStringContainsString('b-progress', $continut, $nume . ' nu are bară de progres');
        }
    }

    /**
     * Browserele fara fetch cu flux raman pe calea dinainte: mai bine fara mers
     * decat fara descarcare.
     */
    public function test_exista_cale_de_rezerva_fara_flux(): void
    {
        foreach (['Mesaje.vue', 'Solicitari.vue', 'Declaratii.vue'] as $nume) {
            $continut = file_get_contents(base_path('resources/js/src/views/app_pages/spv/' . $nume));

            $this->assertStringContainsString('areFlux()', $continut, $nume . ' n-are cale de rezervă');
        }
    }
}
