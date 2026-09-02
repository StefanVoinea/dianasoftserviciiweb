<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\AnafSocietate;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * Entitatea scoasa din uz e ignorata peste tot, si ramane asa.
 *
 * Un client are adesea in certificat firme pe care nu le mai tine: ele incarcau
 * degeaba fiecare interogare la ANAF si fiecare lista de ales. Acum se scot din
 * uz dintr-un buton, si nu se mai iau in seama nicaieri — dar raman in evidenta,
 * cu documentele lor.
 *
 * Alegerea omului sta deoparte de „activ", care e cuvantul ANAF-ului. Daca ar
 * sta amandoua in aceeasi coloana, prima sincronizare i-ar sterge-o, iar
 * entitatea ar invia singura — exact greseala din care certificatele
 * dezactivate se intorceau in lucru.
 */
class EntitateScoasaDinUzTest extends TestCase
{
    protected const COMPANIE = 993;

    protected $certificat;

    protected function setUp(): void
    {
        parent::setUp();

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
        AnafSocietate::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function entitate(string $cif, array $peste = []): AnafSocietate
    {
        return AnafSocietate::create(array_merge([
            'company_id' => self::COMPANIE,
            'cif' => $cif,
            'denumire' => 'FIRMA ' . $cif,
            'tip' => 'pj',
            'activ' => true,
            'scos_din_uz' => false,
            'certificat_id' => $this->certificat->id,
        ], $peste));
    }

    /** Cele trei stari se deosebesc: nu tot ce nu e in lucru e la fel. */
    public function test_starile_se_deosebesc_intre_ele(): void
    {
        $inLucru = $this->entitate('15208744');
        $scoasa = $this->entitate('15208745', ['scos_din_uz' => true]);
        $faraDrepturi = $this->entitate('15208746', ['activ' => false]);

        $this->assertTrue($inLucru->esteInLucru());
        $this->assertFalse($scoasa->esteInLucru(), 'omul a scos-o din uz');
        $this->assertFalse($faraDrepturi->esteInLucru(), 'ANAF nu mai dă drepturi pe ea');
    }

    /** Interogarea nu mai ia in seama decat entitatile in lucru. */
    public function test_interogarea_ia_doar_entitatile_in_lucru(): void
    {
        $this->entitate('15208744');
        $this->entitate('15208745', ['scos_din_uz' => true]);
        $this->entitate('15208746', ['activ' => false]);

        $cifuri = AnafSocietate::inLucru()->pluck('cif')->all();

        $this->assertSame(['15208744'], $cifuri);
    }

    /**
     * Sincronizarea nu sterge alegerea omului.
     *
     * ANAF intoarce mereu toate entitatile certificatului, deci si pe cea scoasa
     * din uz. Daca sincronizarea ar pune-o inapoi in lucru, butonul n-ar tine
     * pana maine dimineata.
     */
    public function test_sincronizarea_nu_invie_entitatea_scoasa_din_uz(): void
    {
        $scoasa = $this->entitate('15208745', ['scos_din_uz' => true]);

        // Asa lucreaza sincronizarea: pune „activ" pe adevarat pentru fiecare
        // entitate din raspunsul ANAF.
        $scoasa->fill(['activ' => true, 'sincronizat_la' => now()])->save();

        $this->assertTrue($scoasa->fresh()->scos_din_uz, 'sincronizarea a șters alegerea omului');
        $this->assertFalse($scoasa->fresh()->esteInLucru());
    }

    /**
     * Sursa care sincronizeaza nu atinge deloc coloana omului.
     *
     * Scrierea listei venite de la ANAF sta acum in `scrieCifurile()` — mai
     * inainte era o bucla in `sincronizeaza()`. Paza e aceeasi: orice ar scrie
     * acolo, „scos_din_uz" nu e treaba lui.
     */
    public function test_sincronizarea_nu_scrie_in_coloana_omului(): void
    {
        $sursa = file_get_contents(app_path('Services/Anaf/Spv/SocietatiService.php'));

        $inceput = strpos($sursa, 'protected function scrieCifurile');
        $sfarsit = strpos($sursa, 'Lista de CIF-uri din raspunsul ANAF', $inceput ?: 0);

        $this->assertNotFalse($inceput, 'scrierea listei si-a schimbat numele');
        $this->assertNotFalse($sfarsit);
        $this->assertStringNotContainsString(
            'scos_din_uz',
            substr($sursa, $inceput, $sfarsit - $inceput),
            'sincronizarea n-are ce căuta în alegerea omului'
        );
    }

    /** Entitatea ramane in evidenta: documentele ei nu se pierd. */
    public function test_entitatea_scoasa_din_uz_ramane_in_evidenta(): void
    {
        $scoasa = $this->entitate('15208745', ['scos_din_uz' => true]);

        $this->assertNotNull(AnafSocietate::find($scoasa->id));
        $this->assertSame('FIRMA 15208745', AnafSocietate::find($scoasa->id)->denumire);
    }

    /** Locurile care aleg entitati cer cele in lucru, nu doar cele active. */
    public function test_locurile_care_aleg_entitati_cer_cele_in_lucru(): void
    {
        $locuri = [
            'Services/Anaf/Spv/SocietatiService.php' => 'interogarea firmelor',
            'Services/Anaf/Spv/AlerteMesaje.php' => 'alertele pe email',
            'Http/Controllers/Api/AlerteMesajeController.php' => 'lista din fereastra de alerte',
            'Http/Controllers/Api/AnafSocietatiController.php' => 'lista cerută de file',
        ];

        foreach ($locuri as $fisier => $lucrul) {
            $this->assertStringContainsString(
                'inLucru()',
                file_get_contents(app_path($fisier)),
                $lucrul . ' încă ia în seamă entitățile scoase din uz'
            );
        }
    }

    /** Fila arata starea ca buton si cere implicit doar cele in lucru. */
    public function test_fila_are_buton_de_stare_si_arata_implicit_doar_cele_in_lucru(): void
    {
        $fila = file_get_contents(base_path('resources/js/src/views/app_pages/spv/Societati.vue'));

        $this->assertStringContainsString('schimbaUzul', $fila, 'starea nu se poate schimba din filă');
        $this->assertStringContainsString('Scoasă din uz', $fila);
        $this->assertStringContainsString('arataToate', $fila);
        $this->assertStringContainsString(
            'if (!this.arataToate) params.doar_active = 1',
            $fila,
            'fila trebuie să ceară implicit doar entitățile în lucru'
        );
    }

    /**
     * „Fără drepturi" nu se apasa: acolo vorbeste ANAF, nu noi. Entitatea se
     * intoarce singura cand drepturile revin.
     */
    public function test_fara_drepturi_ramane_insigna_nu_buton(): void
    {
        $fila = file_get_contents(base_path('resources/js/src/views/app_pages/spv/Societati.vue'));

        $inceput = strpos($fila, "#cell(activ)");
        $sfarsit = strpos($fila, "#cell(date)");

        $bucata = substr($fila, $inceput, $sfarsit - $inceput);

        $this->assertStringContainsString('v-if="rand.item.activ"', $bucata, 'butonul trebuie legat de drepturi');
        $this->assertStringContainsString('b-badge', $bucata, '„Fără drepturi" rămâne insignă');
        $this->assertStringContainsString('Fără drepturi', $bucata);
    }
}
