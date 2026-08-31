<?php

namespace App\Services\Anaf\Declaratii;

use App\Mail\EroareDeclaratieEmail;
use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Models\AnafSocietate;
use App\Models\CertificatUtilizator;
use App\Services\Anaf\Arhiva\ArhivaService;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Spv\CertificatService;
use App\Support\ContextCompanie;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Dosarul urmarit de pe calculatorul clientului.
 *
 * Cine pune acolo o declaratie — programul de contabilitate, un coleg, un
 * script — o gaseste peste cateva minute incarcata, validata si semnata. Ce nu
 * trece de validare nu se pierde: fisierul se muta in „erori", iar oamenii
 * legati de certificatul firmei primesc email cu motivul.
 *
 * Se preiau si XML-uri, si PDF-uri: declaratiile ANAF poarta XML-ul in
 * interiorul PDF-ului, deci se pot valida la fel. Un PDF venit deja semnat trece
 * direct in lista, gata de depus.
 *
 * Semnarea cere tokenul deblocat. Daca driverul lui cere PIN la fiecare
 * semnatura, pasul acesta asteapta un om in fata calculatorului — de aceea
 * esecul de semnare nu opreste restul lotului si se spune limpede in raport.
 */
class MonitorizareFolder
{
    protected $config;
    protected $certificate;
    protected $analizor;
    protected $duk;
    protected $semnare;
    protected $arhiva;
    protected $pdf;
    protected $curatator;
    protected $depunere;

    /** Verificarea de consistenta a SAF-T; lipseste acolo unde n-a fost data. */
    protected $verificare;

    /**
     * Declaratia scrisa in tabel pentru fisierul care se lucreaza acum.
     *
     * Cat timp e goala, fisierul n-a apucat sa aiba rand: daca pica atunci, i se
     * scrie unul anume, ca sa nu ramana doar mutat in dosarul de erori.
     */
    protected $declaratiaInLucru = null;

    public function __construct(
        array $config,
        CertificatService $certificate,
        DeclaratieXml $analizor,
        DukIntegrator $duk,
        SemnareService $semnare,
        ArhivaService $arhiva,
        PdfDeclaratie $pdf,
        CurataXml $curatator,
        DepunereService $depunere,
        ?VerificareSaft $verificare = null
    ) {
        $this->config = $config;
        $this->certificate = $certificate;
        $this->analizor = $analizor;
        $this->duk = $duk;
        $this->semnare = $semnare;
        $this->arhiva = $arhiva;
        $this->pdf = $pdf;
        $this->curatator = $curatator;
        $this->depunere = $depunere;
        $this->verificare = $verificare;
    }

    /**
     * Prelucreaza ce asteapta in dosarul unui certificat.
     *
     * @return array{gasite: int, semnate: int, esuate: int, erori: array<int, string>}
     */
    public function pentruCertificat(AnafCertificat $certificat): array
    {
        $rezultat = ['gasite' => 0, 'semnate' => 0, 'esuate' => 0, 'erori' => []];

        /*
         * Certificatul scos din uz e ignorat cu totul, chiar daca dosarul lui
         * ramane bifat: bifa se pastreaza pentru ziua cand va fi repus, dar
         * pana atunci nimeni nu umbla in calculatorul lui.
         */
        if (!$certificat->activ || !$certificat->monitorizare_activa || !$certificat->monitorizare_cale) {
            return $rezultat;
        }

        $this->certificate->foloseste($certificat);

        try {
            $fisiere = $this->fisiereleCareAsteapta($certificat);
        } catch (DeclaratieException $e) {
            $rezultat['erori'][] = $e->getMessage();

            return $rezultat;
        }

        $rezultat['gasite'] = count($fisiere);

        foreach ($fisiere as $fisier) {
            // Semnarea de la fisierul anterior a lasat activ certificatul firmei.
            $this->certificate->foloseste($certificat);

            try {
                $declaratie = $this->prelucreaza($certificat, $fisier['nume']);

                $rezultat['semnate']++;
                $this->mutaFisierul($certificat, $fisier['nume'], 'prelucrate');

                Jurnal::scrie(
                    'monitorizare_folder',
                    'A preluat din dosarul urmărit declarația ' . $declaratie->tip
                        . ' pentru ' . $declaratie->cui . ' și ' . $this->ispravaDupaPas($declaratie),
                    ['fisier' => $fisier['nume']],
                    $declaratie->cui,
                    Jurnal::BRIDGE
                );
            } catch (\Exception $e) {
                /*
                 * Se prinde orice, nu doar erorile de declaratie: un fisier care
                 * pica din alt motiv (calculatorul inchis intre timp) nu are voie
                 * sa opreasca restul lotului si sa lase totul nemutat.
                 */
                $rezultat['esuate']++;
                $rezultat['erori'][] = $fisier['nume'] . ': ' . $e->getMessage();

                /*
                 * Fisierul picat inainte de a fi inteles — XML de necitit, PDF
                 * fara declaratie inauntru, calculator inchis la mijloc — nu
                 * ajunsese sa aiba rand in tabel. Asa, singurul semn ca s-a
                 * intamplat ceva ramanea fisierul mutat in dosarul „erori", pe
                 * care nu se uita nimeni. I se scrie randul acum.
                 */
                $this->insemneazaEsecul($certificat, $fisier['nume'], $e);

                $this->mutaFisierul($certificat, $fisier['nume'], 'erori');
                $this->anunta(
                    $certificat,
                    $fisier['nume'],
                    $e->getMessage(),
                    $e instanceof DeclaratieException ? $e->cui : null
                );
            }
        }

        $certificat->update(['monitorizare_la' => now()]);

        return $rezultat;
    }

    /**
     * Fisierele gata de luat. Cele abia copiate se lasa pentru rularea
     * urmatoare: altfel s-ar trimite spre validare o declaratie pe jumatate scrisa.
     *
     * @return array<int, array{nume: string, marime: int}>
     */
    protected function fisiereleCareAsteapta(AnafCertificat $certificat): array
    {
        try {
            $raspuns = $this->cerere($certificat)->get($this->url($certificat, '/monitorizare'));
        } catch (ConnectionException $e) {
            /*
             * Calculatorul inchis sau reteaua cazuta e viata de zi cu zi, nu o
             * defectiune la noi: lasata sa urce, eroarea oprea toata comanda si
             * pleca email de alerta la fiecare rulare. Se consemneaza si se
             * trece la certificatul urmator.
             */
            throw new DeclaratieException(
                'Calculatorul cu dosarul urmărit nu a răspuns: ' . $e->getMessage()
            );
        }

        if ($raspuns->failed()) {
            throw new DeclaratieException(
                'Dosarul urmărit nu a putut fi citit: ' . $this->motivul($raspuns)
            );
        }

        return array_values(array_filter($raspuns->json('fisiere') ?: [], function ($fisier) {
            return !empty($fisier['gata']);
        }));
    }

    /**
     * Scrie in tabel esecul unui fisier care n-a apucat sa aiba rand.
     *
     * Cand declaratia fusese deja inregistrata, ea poarta chiar ea eroarea —
     * „eroare_validare" sau „eroare_semnare" — si nu se mai adauga nimic.
     */
    protected function insemneazaEsecul(AnafCertificat $certificat, string $nume, \Exception $e): void
    {
        if ($this->declaratiaInLucru !== null) {
            return;
        }

        $cui = $e instanceof DeclaratieException ? $e->cui : null;
        $societate = $cui ? AnafSocietate::where('cif', $cui)->first() : null;

        try {
            AnafDeclaratie::create([
                'nume_fisier' => $nume,
                // Nu s-a putut afla ce declaratie e; se vede macar ce fisier.
                'tip' => '?',
                'cui' => $cui ?: '-',
                'den_firma' => optional($societate)->denumire,
                'certificat_id' => $societate ? $societate->certificat_id : $certificat->id,
                'pas' => 'eroare_preluare',
                'erori_validare' => mb_substr($e->getMessage(), 0, 2000),
            ]);
        } catch (\Exception $eroareScriere) {
            // Randul din tabel e o inlesnire; jurnalul si emailul raman oricum.
            Log::error('Eșecul fișierului ' . $nume . ' nu a putut fi scris în tabel: ' . $eroareScriere->getMessage());
        }
    }

    /** Aduce fisierul, il inregistreaza, il valideaza si il semneaza. */
    protected function prelucreaza(AnafCertificat $certificat, string $nume): AnafDeclaratie
    {
        // Se uita ce era de la fisierul dinainte: acesta inca n-are rand in tabel.
        $this->declaratiaInLucru = null;

        $continut = $this->continutul($certificat, $nume);

        $extensie = strtolower(pathinfo($nume, PATHINFO_EXTENSION));

        if ($extensie === 'pdf') {
            $declaratie = $this->dinPdf($certificat, $nume, $continut);
        } elseif ($extensie === 'xml') {
            $declaratie = $this->dinXml($certificat, $nume, $continut);
        } else {
            throw new DeclaratieException(
                'Din dosarul urmărit se preiau declarații XML sau PDF; „' . $nume . '" este ' . $extensie . '.'
            );
        }

        /*
         * Semnarea si depunerea urmeaza bifele de la certificatul care
         * urmareste dosarul: unii vor doar validarea (semneaza omul), altii
         * vor tot drumul pana la ANAF. PDF-ul venit deja semnat nu se mai
         * semneaza o data, iar depunerea se face numai pentru ce e semnat.
         *
         * Bifa de semnare inca nescrisa (rand dinaintea coloanei) inseamna
         * purtarea de pana acum: se semneaza.
         */
        $semneazaVrut = $certificat->monitorizare_semneaza === null || $certificat->monitorizare_semneaza;

        if (!$declaratie->semnat && $semneazaVrut) {
            $this->semneaza($declaratie);
        }

        $declaratie = $declaratie->fresh();

        if ($declaratie->semnat && $certificat->monitorizare_depune) {
            $this->depuneDeclaratia($declaratie);
            $declaratie = $declaratie->fresh();
        }

        $this->arhiveaza($declaratie);

        return $declaratie->fresh();
    }

    /** Ce s-a intamplat cu declaratia, pe limba jurnalului. */
    protected function ispravaDupaPas(AnafDeclaratie $declaratie): string
    {
        if ($declaratie->pas === 'depus') {
            return 'a depus-o (index de încărcare ' . $declaratie->index_recipisa . ')';
        }

        if ($declaratie->pas === 'semnat') {
            return 'a semnat-o';
        }

        // Semnarea e oprita din setarile certificatului: ramane validata.
        return 'a validat-o';
    }

    /**
     * Depune declaratia semnata la ANAF, cand certificatul are bifa de depunere.
     *
     * Depunerea merge pe certificatul cu care e inrolata firma, ca la semnare.
     * Esecul opreste doar declaratia aceasta: fisierul pleaca in „erori" si
     * oamenii firmei afla pe email — declaratia ramane insa semnata, deci se
     * poate depune de mana din fila de declaratii.
     */
    protected function depuneDeclaratia(AnafDeclaratie $declaratie): void
    {
        $this->folosesteCertificatulFirmei($declaratie);

        try {
            $this->depunere->autentificare();
            $rezultat = $this->depunere->depune(Storage::path($declaratie->cale_pdf_semnat));
        } catch (DeclaratieException $e) {
            $eroare = new DeclaratieException('Depunerea a eșuat: ' . $e->getMessage());
            $eroare->cui = $declaratie->cui;

            throw $eroare;
        }

        if ($rezultat['index_recipisa'] === null) {
            $declaratie->update([
                'pas' => 'eroare_depunere',
                'stare_declaratie' => $rezultat['eroare'],
            ]);

            $eroare = new DeclaratieException('ANAF a respins depunerea: ' . $rezultat['eroare']);
            $eroare->cui = $declaratie->cui;

            throw $eroare;
        }

        $declaratie->update([
            'index_recipisa' => $rezultat['index_recipisa'],
            'data_depunere' => now(),
            'pas' => 'depus',
            'stare_declaratie' => null,
        ]);
    }

    /** XML: se valideaza si DUKIntegrator scoate PDF-ul oficial. */
    protected function dinXml(AnafCertificat $certificat, string $nume, string $continut): AnafDeclaratie
    {
        $caleXml = $this->config['storage_path'] . '/xml/' . uniqid('mon_', true) . '.xml';
        Storage::put($caleXml, $this->curatator->curata($continut));

        $declaratie = $this->inregistreaza($certificat, $nume, $caleXml, [], [$caleXml]);

        $calePdf = preg_replace('/\.xml$/i', '', $caleXml) . '.pdf';

        $this->valideaza($declaratie, Storage::path($calePdf));

        $declaratie->update(['pas' => 'validat', 'cale_pdf' => $calePdf, 'erori_validare' => null]);

        return $declaratie->fresh();
    }

    /**
     * PDF: declaratiile ANAF poarta XML-ul in interior, deci se poate valida
     * fara fisierul separat. Se pastreaza PDF-ul primit, nu cel scos de
     * DUKIntegrator: numai el poarta semnatura, cand vine deja semnat.
     */
    protected function dinPdf(AnafCertificat $certificat, string $nume, string $continut): AnafDeclaratie
    {
        $trunchi = $this->config['storage_path'] . '/pdf/' . uniqid('mon_', true);
        $calePdf = $trunchi . '.pdf';
        Storage::put($calePdf, $continut);

        try {
            $info = $this->pdf->citeste(Storage::path($calePdf));
        } catch (DeclaratieException $e) {
            Storage::delete($calePdf);

            throw $e;
        }

        if (empty($info['xml'])) {
            Storage::delete($calePdf);

            throw new DeclaratieException(
                'PDF-ul nu conține declarația în format XML (nu pare o declarație ANAF).'
            );
        }

        $caleXml = $trunchi . '.xml';
        Storage::put($caleXml, $this->curatator->curata($info['xml']));

        $declaratie = $this->inregistreaza(
            $certificat,
            $nume,
            $caleXml,
            ['semnat' => (bool) $info['semnat']],
            [$caleXml, $calePdf]
        );

        $caleGenerat = Storage::path($trunchi . '_duk.pdf');

        $this->valideaza($declaratie, $caleGenerat);

        /*
         * Cel semnat se pastreaza asa cum a venit: semnatura e pe el. Cel
         * nesemnat se inlocuieste cu PDF-ul scos de DUKIntegrator din acelasi
         * XML — vezi lamurirea din DeclaratiiController::dinPdf().
         */
        if (!$info['semnat'] && is_file($caleGenerat)) {
            $declaratie->update([
                'pas' => 'validat',
                'cale_pdf' => $trunchi . '_duk.pdf',
                'erori_validare' => null,
            ]);

            return $declaratie->fresh();
        }

        if (is_file($caleGenerat)) {
            @unlink($caleGenerat);
        }

        $declaratie->update($info['semnat']
            ? ['pas' => 'semnat', 'cale_pdf' => $calePdf, 'cale_pdf_semnat' => $calePdf, 'erori_validare' => null]
            : ['pas' => 'validat', 'cale_pdf' => $calePdf, 'erori_validare' => null]);

        return $declaratie->fresh();
    }

    /**
     * Scrie declaratia in lista, cu denumirea firmei luata din Entitati
     * inrolate. La un XML de neînțeles nu ramane nimic pe disc.
     *
     * @param array<string, mixed> $inPlus
     * @param array<int, string> $deSters fisierele de curatat daca XML-ul nu se poate citi
     */
    protected function inregistreaza(
        AnafCertificat $certificat,
        string $nume,
        string $caleXml,
        array $inPlus,
        array $deSters
    ): AnafDeclaratie {
        try {
            $meta = $this->analizor->analizeaza(Storage::path($caleXml));
        } catch (DeclaratieException $e) {
            Storage::delete($deSters);

            throw $e;
        }

        $societate = $meta['cui'] ? AnafSocietate::where('cif', $meta['cui'])->first() : null;

        return $this->declaratiaInLucru = AnafDeclaratie::create(array_merge([
            'nume_fisier' => $nume,
            'cale_xml' => $caleXml,
            'pas' => 'incarcat',
            'cui' => $meta['cui'],
            'den_firma' => $societate && $societate->denumire ? $societate->denumire : $meta['den_firma'],
            'certificat_id' => $societate ? $societate->certificat_id : $certificat->id,
            'tip' => $meta['tip'],
            'luna' => $meta['luna'],
            'anul' => $meta['anul'],
            'perioada_tip' => $meta['perioada_tip'] ?? null,
            'rectificativa' => $meta['rectificativa'],
        ], $inPlus));
    }

    protected function valideaza(AnafDeclaratie $declaratie, string $calePdfGenerat): void
    {
        $rezultat = $this->duk->valideazaSiGenereazaPdf(
            Storage::path($declaratie->cale_xml),
            $declaratie->tip,
            $calePdfGenerat,
            null,
            // D406 se valideaza cu perioada raportata; restul le ignora
            $declaratie->anul,
            $declaratie->luna,
            $declaratie->perioada_tip
        );

        if (!$rezultat['valid']) {
            $declaratie->update(['pas' => 'eroare_validare', 'erori_validare' => $rezultat['erori']]);

            $eroare = new DeclaratieException('Validarea ANAF a respins declarația.');
            $eroare->cui = $declaratie->cui;

            throw $eroare;
        }

        $this->verificaConsistenta($declaratie);
    }

    /**
     * Consistenta unui SAF-T venit din dosarul urmarit.
     *
     * Aici declaratia merge singura mai departe — se semneaza si se depune —,
     * asa ca verificarea nu opreste nimic: ea doar scrie pe declaratie ce a
     * gasit, ca omul sa vada in tabel daca are de indreptat ceva. Nici un esec
     * al ei nu are voie sa rupa lantul: dosarul urmarit trebuie sa mearga
     * inainte si cand unealta ANAF lipseste de pe masina.
     */
    protected function verificaConsistenta(AnafDeclaratie $declaratie): void
    {
        if (!$this->verificare || $declaratie->tip !== 'D406') {
            return;
        }

        if (!Schema::hasColumn('anaf_declaratii', 'verificare_stare')) {
            return;
        }

        try {
            $rezultat = $this->verificare->verifica(Storage::path($declaratie->cale_xml));

            $declaratie->update([
                'verificare_stare' => $rezultat['stare'],
                'verificare_numar' => $rezultat['numar'],
                'verificare_erori' => json_encode($rezultat, JSON_UNESCAPED_UNICODE),
                'verificare_la' => now(),
            ]);
        } catch (DeclaratieException $e) {
            $declaratie->update([
                'verificare_stare' => 'imposibil',
                'verificare_numar' => null,
                'verificare_erori' => json_encode(['mesaj' => $e->getMessage()], JSON_UNESCAPED_UNICODE),
                'verificare_la' => now(),
            ]);
        }
    }

    /** Semnarea si depunerea merg pe tokenul cu care e inrolata firma. */
    protected function folosesteCertificatulFirmei(AnafDeclaratie $declaratie): void
    {
        if (!$declaratie->certificat_id) {
            return;
        }

        $alFirmei = AnafCertificat::find($declaratie->certificat_id);

        if ($alFirmei) {
            $this->certificate->foloseste($alFirmei);
        }
    }

    protected function semneaza(AnafDeclaratie $declaratie): void
    {
        $caleSemnat = preg_replace('/\.pdf$/i', '', $declaratie->cale_pdf) . '_semnat.pdf';

        $this->folosesteCertificatulFirmei($declaratie);

        try {
            $this->semnare->semneaza(Storage::path($declaratie->cale_pdf), Storage::path($caleSemnat));
        } catch (DeclaratieException $e) {
            $declaratie->update(['pas' => 'eroare_semnare', 'eroare_semnare' => $e->getMessage()]);

            $eroare = new DeclaratieException('Semnarea a eșuat: ' . $e->getMessage());
            $eroare->cui = $declaratie->cui;

            throw $eroare;
        }

        $declaratie->update([
            'cale_pdf_semnat' => $caleSemnat,
            'semnat' => true,
            'pas' => 'semnat',
            'eroare_semnare' => null,
            'certificat_id' => $this->certificate->idCurent(),
        ]);
    }

    /**
     * Documentul semnat pleaca in arhiva de la client, ca si cel semnat de mana
     * din fila de declaratii — altfel dosarul urmarit ar produce documente care
     * raman doar pe server.
     */
    protected function arhiveaza(AnafDeclaratie $declaratie): void
    {
        if (!$this->arhiva->activa()) {
            return;
        }

        $dosar = ArhivaService::dosarFirma($declaratie->den_firma, $declaratie->cui);
        $tip = ArhivaService::curata($declaratie->tip) ?: 'Diverse';
        $cai = [];

        // Documentele scrise cat timp denumirea firmei nu era stiuta stau intr-un
        // dosar purtand doar codul ei; se strang la un loc inainte de a scrie aici.
        $this->arhiva->uneste($declaratie->cui, $dosar);

        // Depusa deja, documentul poarta din prima indicele de incarcare in nume.
        $stare = $declaratie->index_recipisa ? 'depusa' : 'semnata';

        try {
            if ($declaratie->cale_pdf_semnat && Storage::exists($declaratie->cale_pdf_semnat)) {
                $cai['arhiva_semnat'] = $this->arhiva->pune(
                    Storage::get($declaratie->cale_pdf_semnat),
                    $dosar,
                    $tip,
                    ArhivaService::numeDeclaratie($declaratie, $stare, 'pdf'),
                    $declaratie->arhiva_semnat
                );

                // Declaratia depusa sta si in dosarul comun „Toate”.
                if ($stare === 'depusa') {
                    $this->arhiva->copiazaInToate(
                        $cai['arhiva_semnat'],
                        ArhivaService::numeDeclaratie($declaratie, 'depusa', 'pdf')
                    );
                }
            }

            // Ce s-a dat spre lucru sta deoparte — vezi DeclaratiiController::arhiveaza().
            if ($declaratie->cale_xml && Storage::exists($declaratie->cale_xml)) {
                $cai['arhiva_xml'] = $this->arhiva->pune(
                    Storage::get($declaratie->cale_xml),
                    $dosar,
                    ArhivaService::dosarInitiale($declaratie->tip),
                    ArhivaService::numeDeclaratie($declaratie, '', 'xml'),
                    $declaratie->arhiva_xml
                );
            }

            /*
             * Coloana se cere abia dupa migrare — vezi DeclaratiiController.
             * Mai bine fara insemnarea caii decat cu toata arhivarea cazuta.
             */
            if ($declaratie->cale_pdf
                && $declaratie->cale_pdf !== $declaratie->cale_pdf_semnat
                && Schema::hasColumn('anaf_declaratii', 'arhiva_initial')
                && Storage::exists($declaratie->cale_pdf)) {
                $cai['arhiva_initial'] = $this->arhiva->pune(
                    Storage::get($declaratie->cale_pdf),
                    $dosar,
                    ArhivaService::dosarInitiale($declaratie->tip),
                    ArhivaService::numeDeclaratie($declaratie, '', 'pdf'),
                    $declaratie->arhiva_initial
                );
            }
        } catch (\Throwable $e) {
            /*
             * Orice esec — arhiva plina, calculatorul inchis intre timp, o
             * coloana lipsa fiindca pe server n-a rulat inca migrarea — nu are
             * voie sa desfaca ce s-a reusit: declaratia ramane semnata, iar
             * documentul poate fi arhivat mai tarziu, din fila de declaratii.
             */
            Jurnal::esec(
                'declaratie_arhivare',
                'Arhivarea locală a declarației ' . $declaratie->tip . ' pentru ' . $declaratie->cui
                    . ' a eșuat: ' . $e->getMessage(),
                [],
                $declaratie->cui,
                Jurnal::BRIDGE
            );
        }

        if ($cai === []) {
            return;
        }

        $declaratie->update($cai);

        // Documentele stau la client; copia de lucru pleaca doar dupa ce a ajuns acolo.
        if (!$this->arhiva->stergeDePeServer()) {
            return;
        }

        $sterse = [];

        if (isset($cai['arhiva_semnat'])) {
            Storage::delete(array_filter([$declaratie->cale_pdf, $declaratie->cale_pdf_semnat]));
            $sterse['cale_pdf'] = null;
            $sterse['cale_pdf_semnat'] = null;
        }

        if (isset($cai['arhiva_xml'])) {
            Storage::delete($declaratie->cale_xml);
            $sterse['cale_xml'] = null;
        }

        $declaratie->update($sterse);
    }

    /**
     * Instiinteaza oamenii legati de certificatul firmei.
     *
     * Daca declaratia n-a apucat sa spuna pentru ce firma este, se anunta
     * oamenii certificatului care urmareste dosarul — altfel eroarea n-ar afla-o
     * nimeni.
     */
    protected function anunta(AnafCertificat $certificat, string $fisier, string $motiv, ?string $cui): void
    {
        $alFirmei = $certificat;

        if ($cui) {
            $societate = AnafSocietate::where('cif', $cui)->first();

            if ($societate && $societate->certificat) {
                $alFirmei = $societate->certificat;
            }
        }

        $adrese = $this->adresele($alFirmei);

        /*
         * Firma poate fi inrolata pe un certificat de care nu e legat inca
         * nimeni. Atunci afla macar oamenii certificatului care urmareste
         * dosarul — eroarea trebuie sa ajunga la cineva.
         */
        if ($adrese === [] && $alFirmei->id !== $certificat->id) {
            $adrese = $this->adresele($certificat);
        }

        /*
         * Si daca nu e nimeni legat de niciun certificat, tot trebuie sa afle
         * cineva: eroarea pleaca la administratorii firmei. Altfel declaratia
         * ramane in dosarul de erori si toata lumea o crede depusa.
         */
        if ($adrese === []) {
            $adrese = $this->administratorii();
        }

        Jurnal::esec(
            'monitorizare_folder',
            'Declarația „' . $fisier . '" din dosarul urmărit nu a putut fi prelucrată: ' . $motiv,
            ['fisier' => $fisier, 'anuntati' => $adrese],
            $cui,
            Jurnal::BRIDGE
        );

        foreach ($adrese as $adresa) {
            try {
                Mail::to($adresa)->send(new EroareDeclaratieEmail($fisier, $motiv, $cui, $alFirmei->cn));
            } catch (\Exception $e) {
                // Un email picat nu opreste restul lotului.
                Log::error('Înștiințarea de eroare nu a plecat către ' . $adresa . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Adresele administratorilor firmei clientului.
     *
     * Ultima poarta: cand niciun om nu e legat de certificate, ei sunt cei care
     * pot lega pe cineva — deci tot la ei trebuie sa ajunga vestea.
     *
     * @return array<int, string>
     */
    protected function administratorii(): array
    {
        $companie = ContextCompanie::curenta();

        if (!$companie) {
            return [];
        }

        return DB::table('company_user')
            ->join('users', 'users.id', '=', 'company_user.user_id')
            ->where('company_user.company_id', $companie)
            ->where('company_user.administrator', true)
            ->where('users.blocat', '!=', 'Da')
            ->pluck('users.email')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /** @return array<int, string> */
    protected function adresele(AnafCertificat $certificat): array
    {
        return CertificatUtilizator::where('certificat_id', $certificat->id)
            ->where('activ', true)
            ->pluck('email')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function continutul(AnafCertificat $certificat, string $nume): string
    {
        $raspuns = $this->cerere($certificat)
            ->get($this->url($certificat, '/monitorizare/fisier'), ['nume' => $nume]);

        if ($raspuns->failed()) {
            throw new DeclaratieException('Fișierul nu a putut fi adus: ' . $this->motivul($raspuns));
        }

        return $raspuns->body();
    }

    /** Scoate fisierul din dosar, ca sa nu fie luat a doua oara. */
    protected function mutaFisierul(AnafCertificat $certificat, string $nume, string $unde): void
    {
        try {
            $this->cerere($certificat)->asForm()
                ->post($this->url($certificat, '/monitorizare/mutat'), ['nume' => $nume, 'unde' => $unde]);
        } catch (\Exception $e) {
            /*
             * Nemutat, fisierul ar fi luat iar la rularea urmatoare. Se scrie in
             * jurnal ca sa se vada de ce apar prelucrari repetate.
             */
            Jurnal::esec(
                'monitorizare_folder',
                'Fișierul „' . $nume . '" nu a putut fi mutat din dosarul urmărit: ' . $e->getMessage(),
                [],
                null,
                Jurnal::BRIDGE
            );
        }
    }

    protected function cerere(AnafCertificat $certificat)
    {
        $bridge = $this->bridgeUrmarit($certificat);

        return Http::withToken($bridge['token'])
            ->withHeaders(['X-Monitorizare-Cale' => $certificat->monitorizare_cale])
            ->timeout($this->config['timeout'] ?? 120);
    }

    protected function url(AnafCertificat $certificat, string $cale): string
    {
        return rtrim($this->bridgeUrmarit($certificat)['url'], '/') . $cale;
    }

    /**
     * Coordonatele calculatorului care tine dosarul urmarit.
     *
     * Semnarea comuta certificatul activ pe cel cu care e inrolata firma din
     * declaratie; dosarul insa ramane al certificatului care il urmareste, asa
     * ca fisierele nu se cer si nu se muta dupa certificatul activ de moment.
     */
    protected function bridgeUrmarit(AnafCertificat $certificat): array
    {
        $anterior = $this->certificate->activ();

        $this->certificate->foloseste($certificat);
        $bridge = $this->certificate->bridge();
        $this->certificate->foloseste($anterior);

        return $bridge;
    }

    protected function motivul($raspuns): string
    {
        $payload = json_decode($raspuns->body(), true);

        return $payload['detalii'] ?? $payload['eroare'] ?? 'HTTP ' . $raspuns->status();
    }
}
