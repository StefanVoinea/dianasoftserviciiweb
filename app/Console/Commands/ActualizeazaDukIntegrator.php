<?php

namespace App\Console\Commands;

use App\Services\Anaf\Declaratii\DeclaratieException;
use App\Services\Anaf\Declaratii\DukIntegrator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

/**
 * Actualizeaza DUKIntegrator (utilitarul ANAF de validare) si nomenclatoarele de
 * declaratii, dupa acelasi mecanism ca aplicatia desktop: se citeste versiuni.xml
 * de pe static.anaf.ro si se descarca fisierele mai noi.
 *
 * Sunt doua lucruri care se schimba deosebit, si tocmai de aici a venit
 * incurcatura: integratorul insusi se schimba rar, iar declaratiile — D112, D300,
 * D394 — se schimba des si fiecare pe socoteala ei. Pana acum se cantarea numai
 * versiunea integratorului: cat timp ea statea pe loc, comanda raspundea „este
 * deja la zi” si nu aducea nimic, desi D112 apucase sa mai faca un pas. Asa a si
 * fost gasita: integrator 1.4.18.3.3 in amandoua partile, si D112 ramas in urma
 * cu o versiune.
 *
 * De acum fiecare declaratie e cantarita separat, si se aduc numai cele care
 * chiar s-au schimbat — nu toate cele o suta saptezeci si trei, de fiecare data.
 */
class ActualizeazaDukIntegrator extends Command
{
    protected $signature = 'anaf:duk-update
                            {--url= : URL-ul versiuni.xml (implicit cel din dist/config/configURL.properties)}
                            {--force : Descarca tot, chiar daca versiunile locale sunt la zi}
                            {--pe-uscat : Arată ce s-ar aduce, fără să descarce nimic}
                            {--fara-proba : Nu mai încearcă o validare după actualizare}';

    protected $description = 'Actualizează DUKIntegrator și declarațiile de la ANAF';

    /**
     * Fisierele inlocuite in rularea aceasta, cu copia lor dinainte.
     *
     * Cheia e fisierul adus; valoarea spune daca acolo era ceva inainte si unde
     * s-a pus copia. Fara ele, o actualizare picata ar lasa validatorul stricat
     * pana cand ar baga cineva de seama.
     *
     * @var array<string, array{exista: bool, rezerva: string|null}>
     */
    protected $rezerve = [];

    public function handle(): int
    {
        $jar = config('anaf.declaratii.duk.jar');
        $dist = dirname($jar);
        $config = $dist . DIRECTORY_SEPARATOR . 'config';

        if (!is_dir($dist)) {
            /*
             * Lipsa folderului nu e o defectiune a actualizarii: pe un server
             * unde DUKIntegrator nu e instalat, n-are ce actualiza. Se spune in
             * jurnal si se iese cu bine — altfel lucrarea programata ar da esec
             * in fiecare noapte, si esecul adevarat s-ar pierde printre ele.
             */
            $this->warn('Folderul DUKIntegrator nu există: ' . $dist);

            Log::warning('anaf:duk-update — DUKIntegrator nu e instalat aici.', ['folder' => $dist]);

            return 0;
        }

        $url = $this->option('url') ?: $this->urlVersiuni($config);

        if (!$url) {
            $this->error('Nu am găsit urlVersiuni în configURL.properties. Folosiți --url=');

            return 1;
        }

        $xml = $this->listaDeVersiuni($url);

        if ($xml === null) {
            return 1;
        }

        $catalog = $this->catalogul($config);

        $descarcate = 0;
        $esuate = 0;

        $integratorul = $this->integratorul($xml, $catalog, $dist, $config, $descarcate, $esuate);
        $schimbate = $this->declaratiile($xml, $catalog, $dist, $descarcate, $esuate);

        if (!$integratorul && $schimbate === []) {
            $this->info('DUKIntegrator și toate declarațiile sunt la zi.');

            return 0;
        }

        if ($this->option('pe-uscat')) {
            $this->newLine();
            $this->comment('Probă pe uscat: nu s-a descărcat nimic.');

            return 0;
        }

        $this->newLine();
        $this->info('S-au descărcat ' . $descarcate . ' fișiere, ' . $esuate . ' eșuate.');

        /*
         * Proba de dupa. Actualizarea se face noaptea, nesupravegheat: daca
         * validatorul iese din ea stricat, dimineata nu se mai valideaza nicio
         * declaratie si nimeni nu stie de ce. De aceea se incearca o validare
         * inainte de a se scrie catalogul, iar daca ea nu tine, se pun la loc
         * fisierele dinainte si catalogul ramane nescris — asa incat rularea
         * urmatoare sa incerce din nou.
         */
        if (!$this->option('fara-proba') && !$this->probaDeDupa($integratorul)) {
            $puse = $this->puneLaLoc();

            $this->error('Proba de după actualizare nu a trecut. Am pus la loc ' . $puse . ' fișiere.');

            Log::error('anaf:duk-update — actualizarea a fost anulată: validatorul nu a mai răspuns.', [
                'declaratii' => array_keys($schimbate),
                'fisiere_puse_la_loc' => $puse,
            ]);

            return 1;
        }

        $this->scrieCatalogul($catalog, trim((string) $xml->integrator->versiune), $schimbate);
        $this->stergeRezervele();

        $this->info('Actualizare încheiată.');

        if ($schimbate !== []) {
            Log::info('anaf:duk-update — s-au înnoit declarații: ' . implode(', ', array_keys($schimbate)));
        }

        return $esuate > 0 && $descarcate === 0 ? 1 : 0;
    }

    /**
     * Mai raspunde validatorul dupa ce s-au schimbat fisierele?
     *
     * Nu se cantareste daca declaratia de proba e valida — regulile ANAF se
     * schimba, si o proba respinsa pe drept ar opri actualizarea degeaba. Se
     * cantareste doar ca DUKIntegrator a dat un raspuns de validare, oricare ar
     * fi el. Un jar adus pe jumatate sau o clasa mutata nu dau raspuns, ci o
     * urma de java sau nimic.
     */
    protected function probaDeDupa(bool $integratorSchimbat): bool
    {
        /*
         * Cand s-a schimbat integratorul insusi, se cerceteaza si lansatorul
         * nostru pentru D406: el se leaga direct de clase din jar-urile ANAF
         * (validator.Validator, pdf.PdfSuperCreator), iar o mutare de clasa
         * acolo il lasa fara ele. Restul actualizarilor — nomenclatoare de
         * declaratii — n-au cum sa-l atinga, asa ca nu se cheama degeaba.
         */
        if ($integratorSchimbat && !$this->probaLansatoruluiD406()) {
            return false;
        }

        /*
         * Fara integrator n-are ce sa fie probat, si nici n-ar folosi la ceva:
         * lipsa lui nu vine din actualizare, iar punerea la loc n-ar aduce-o
         * inapoi. Asa se intampla pe un server unde stau doar nomenclatoarele.
         */
        if (!is_file((string) config('anaf.declaratii.duk.jar'))) {
            $this->warn('DUKIntegrator.jar nu e aici; sar peste probă.');

            return true;
        }

        $fixtura = base_path('tests' . DIRECTORY_SEPARATOR . 'fixturi' . DIRECTORY_SEPARATOR . 'd100-de-proba.xml');

        if (!is_file($fixtura)) {
            $this->warn('Nu am declarație de probă (' . $fixtura . '); sar peste probă.');

            return true;
        }

        $pdf = tempnam(sys_get_temp_dir(), 'duk') . '.pdf';

        try {
            $rezultat = app(DukIntegrator::class)->valideazaSiGenereazaPdf($fixtura, 'D100', $pdf);
        } catch (DeclaratieException $e) {
            $this->warn('Proba de după actualizare: ' . $e->getMessage());

            return false;
        } finally {
            @unlink($pdf);
        }

        if ($rezultat['valid']) {
            $this->info('Proba de după actualizare: validatorul răspunde.');

            return true;
        }

        $spuse = trim((string) $rezultat['erori']);

        // Asa arata o cadere de java, nu un raspuns de validare.
        foreach (['Exception', 'NoClassDefFound', 'ClassNotFound', 'Error:'] as $semn) {
            if (stripos($spuse, $semn) !== false) {
                $this->warn('Proba de după actualizare a scos: ' . mb_substr($spuse, 0, 300));

                return false;
            }
        }

        if ($spuse === '') {
            $this->warn('Proba de după actualizare: validatorul n-a spus nimic.');

            return false;
        }

        $this->info('Proba de după actualizare: validatorul răspunde (cu erori de validare, ceea ce e în regulă).');

        return true;
    }

    /**
     * Mai gaseste lansatorul D406 clasele ANAF de care se leaga?
     *
     * Se cheama cu un fisier care nu exista: pana sa dea peste el, lansatorul
     * apuca sa ceara clasele din jar-urile ANAF. Daca ele si-au schimbat locul,
     * java raspunde cu „NoClassDefFoundError” — si atunci validarea SAF-T ar fi
     * ramas moarta, desi restul declaratiilor merg mai departe.
     */
    protected function probaLansatoruluiD406(): bool
    {
        $lansator = config('anaf.declaratii.duk.jar_d406')
            ?: dirname((string) config('anaf.declaratii.duk.jar')) . DIRECTORY_SEPARATOR . 'DukD406.jar';

        if (!is_file($lansator)) {
            $this->warn('Lansatorul D406 nu e aici; sar peste proba lui.');

            return true;
        }

        $proces = new Process([
            (string) config('anaf.declaratii.duk.java', 'java'),
            '-jar', $lansator,
            // Un fisier care nu exista: lansatorul cade oricum, dar dupa ce a
            // cerut clasele ANAF — si asta e tot ce se cerceteaza aici.
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'nu-exista-saft.xml',
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'nu-exista-erori.txt',
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'nu-exista.pdf',
            '2026', '6', 'L',
        ]);

        $proces->setTimeout(60);
        $proces->run();

        $spuse = $proces->getErrorOutput() . ' ' . $proces->getOutput();

        foreach (['NoClassDefFound', 'ClassNotFound', 'NoSuchMethod'] as $semn) {
            if (stripos($spuse, $semn) !== false) {
                $this->warn('Lansatorul D406 nu mai găsește clasele ANAF: ' . mb_substr(trim($spuse), 0, 300));

                return false;
            }
        }

        $this->info('Proba lansatorului D406: clasele ANAF sunt la locul lor.');

        return true;
    }

    /** Fisierele dinainte, puse la loc peste cele tocmai aduse. */
    protected function puneLaLoc(): int
    {
        $puse = 0;

        foreach ($this->rezerve as $cale => $copia) {
            // Fisier care n-a fost acolo inainte: n-are ce cauta acolo nici acum.
            if (!$copia['exista']) {
                @unlink($cale);
                $puse++;

                continue;
            }

            if ($copia['rezerva'] === null) {
                // Copia n-a putut fi facuta; se lasa cum e, si se spune.
                $this->warn('  Nu am copie de rezervă pentru ' . basename($cale) . '; rămâne cel nou.');

                continue;
            }

            if (@copy($copia['rezerva'], $cale)) {
                @unlink($copia['rezerva']);
                $puse++;
            }
        }

        $this->rezerve = [];

        return $puse;
    }

    /** Dupa o actualizare care a tinut, copiile nu mai folosesc nimanui. */
    protected function stergeRezervele(): void
    {
        foreach ($this->rezerve as $copia) {
            if ($copia['rezerva'] !== null) {
                @unlink($copia['rezerva']);
            }
        }

        $this->rezerve = [];
    }

    /** Lista de versiuni de la ANAF, sau null cand nu s-a putut lua. */
    protected function listaDeVersiuni(string $url): ?\SimpleXMLElement
    {
        $this->info('Citesc lista de versiuni de la ' . $url);

        try {
            $raspuns = Http::timeout(60)->get($url);
        } catch (\Exception $e) {
            $this->error('Nu am putut ajunge la ANAF: ' . $e->getMessage());

            Log::warning('anaf:duk-update — lista de versiuni nu s-a putut lua: ' . $e->getMessage());

            return null;
        }

        if ($raspuns->failed()) {
            $this->error('Nu am putut descărca versiuni.xml (HTTP ' . $raspuns->status() . ')');

            Log::warning('anaf:duk-update — ANAF a răspuns ' . $raspuns->status(), ['adresa' => $url]);

            return null;
        }

        $anterior = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($raspuns->body());
        libxml_use_internal_errors($anterior);

        if ($xml === false) {
            $this->error('versiuni.xml nu este un XML valid.');

            return null;
        }

        return $xml;
    }

    /**
     * Integratorul: se aduce numai cand versiunea lui s-a schimbat.
     *
     * @return bool daca era ceva de adus
     */
    protected function integratorul(
        \SimpleXMLElement $xml,
        array $catalog,
        string $dist,
        string $config,
        int &$descarcate,
        int &$esuate
    ): bool {
        $versiuneNoua = trim((string) $xml->integrator->versiune);

        $this->line('Integrator — local: ' . ($catalog['integrator'] ?: 'necunoscut') . ', ANAF: ' . $versiuneNoua);

        if ($catalog['integrator'] === $versiuneNoua && !$this->option('force')) {
            return false;
        }

        $this->newLine();
        $this->comment('Integratorul s-a schimbat.');

        // iJars/sJars merg in dist/lib, zJars/dJars in dist, cFisiere in dist/config.
        $grupuri = [
            'iJars' => $dist . DIRECTORY_SEPARATOR . 'lib',
            'sJars' => $dist . DIRECTORY_SEPARATOR . 'lib',
            'zJars' => $dist,
            'dJars' => $dist,
            'cFisiere' => $config,
        ];

        foreach ($grupuri as $grup => $destinatie) {
            if (!isset($xml->integrator->$grup)) {
                continue;
            }

            foreach ($xml->integrator->$grup->children() as $nod) {
                $link = trim((string) $nod);

                if ($link !== '') {
                    $this->descarca($link, $destinatie, $descarcate, $esuate);
                }
            }
        }

        return true;
    }

    /**
     * Declaratiile care s-au schimbat, fiecare cantarita pe socoteala ei.
     *
     * @return array<string, array{j: string, p: string}> ce s-a innoit
     */
    protected function declaratiile(
        \SimpleXMLElement $xml,
        array $catalog,
        string $dist,
        int &$descarcate,
        int &$esuate
    ): array {
        if (!isset($xml->declaratii)) {
            return [];
        }

        $schimbate = [];

        foreach ($xml->declaratii->children() as $nod) {
            $nume = $nod->getName();
            $j = trim((string) $nod->versiuneJ);
            $p = trim((string) $nod->versiuneP);

            $local = $catalog['declaratii'][$nume] ?? null;

            /*
             * Declaratiile trecute cu „#” sunt scoase din uz aici. Nu se stie de
             * ce au fost scoase, deci nu se aduc si nu se ating: cine le-a
             * inchis avea o pricina, iar actualizarea n-are cum s-o cunoasca.
             */
            if ($local !== null && $local['ascuns']) {
                continue;
            }

            if ($local !== null && $local['j'] === $j && $local['p'] === $p && !$this->option('force')) {
                continue;
            }

            $vechi = $local === null ? 'nouă' : $local['j'] . ';' . $local['p'];

            $this->line('  ' . str_pad($nume, 8) . ' ' . str_pad($vechi, 18) . ' → ' . $j . ';' . $p);

            $schimbate[$nume] = ['j' => $j, 'p' => $p];

            foreach ($nod->children() as $fisier) {
                $link = trim((string) $fisier);

                if ($link !== '' && preg_match('#^https?://#i', $link)) {
                    $this->descarca($link, $dist . DIRECTORY_SEPARATOR . 'lib', $descarcate, $esuate, false);
                }
            }
        }

        if ($schimbate !== []) {
            $this->newLine();
            $this->comment(count($schimbate) . ' declarație(i) de înnoit.');
        }

        return $schimbate;
    }

    protected function descarca(string $url, string $destinatie, int &$descarcate, int &$esuate, bool $afiseaza = true): void
    {
        if ($this->option('pe-uscat')) {
            return;
        }

        if (!is_dir($destinatie)) {
            mkdir($destinatie, 0755, true);
        }

        $nume = basename(parse_url($url, PHP_URL_PATH));
        $cale = $destinatie . DIRECTORY_SEPARATOR . $nume;

        try {
            $raspuns = Http::timeout(120)->get($url);

            if ($raspuns->failed()) {
                $esuate++;

                if ($afiseaza) {
                    $this->warn('  ✗ ' . $nume . ' (HTTP ' . $raspuns->status() . ')');
                }

                return;
            }

            $this->pastreazaCopia($cale);

            file_put_contents($cale, $raspuns->body());
            $descarcate++;

            if ($afiseaza) {
                $this->line('  ✓ ' . $nume . ' → ' . $destinatie);
            }
        } catch (\Exception $e) {
            $esuate++;

            if ($afiseaza) {
                $this->warn('  ✗ ' . $nume . ' (' . $e->getMessage() . ')');
            }
        }
    }

    /**
     * Copia fisierului dinaintea inlocuirii lui.
     *
     * O singura generatie, langa fisier: destul cat sa se poata da inapoi
     * actualizarea care tocmai s-a facut, si nimic mai mult. Copiile se sterg
     * cand actualizarea a trecut proba.
     */
    protected function pastreazaCopia(string $cale): void
    {
        if (isset($this->rezerve[$cale])) {
            return;
        }

        if (!is_file($cale)) {
            $this->rezerve[$cale] = ['exista' => false, 'rezerva' => null];

            return;
        }

        $rezerva = $cale . '.rezerva';

        $this->rezerve[$cale] = [
            'exista' => true,
            'rezerva' => @copy($cale, $rezerva) ? $rezerva : null,
        ];
    }

    protected function urlVersiuni(string $config): ?string
    {
        foreach (['configURL.properties', 'config.properties'] as $nume) {
            $cale = $config . DIRECTORY_SEPARATOR . $nume;

            if (!is_file($cale)) {
                continue;
            }

            foreach (file($cale, FILE_IGNORE_NEW_LINES) as $linie) {
                if (strpos(trim($linie), 'urlVersiuni=') === 0) {
                    return str_replace('\\:', ':', trim(substr(trim($linie), strlen('urlVersiuni='))));
                }
            }
        }

        return null;
    }

    /**
     * Ce scrie in „versiuniCurente.txt”.
     *
     * Pe primul rand sta versiunea integratorului, apoi cate un rand pentru
     * fiecare declaratie: „D112;J27.0.1;P3.0.1”. Randurile care incep cu „#”
     * sunt scoase din uz.
     *
     * Fisierul e citit de DUKIntegrator insusi, deci se tine minte si forma lui:
     * randurile se pastreaza in aceeasi ordine, iar cele pe care nu le intelegem
     * raman neatinse. Se rescrie doar ce chiar s-a innoit.
     */
    protected function catalogul(string $config): array
    {
        foreach (['versiuniCurente.txt', 'versiunicurente.txt'] as $nume) {
            $cale = $config . DIRECTORY_SEPARATOR . $nume;

            if (!is_file($cale)) {
                continue;
            }

            $brut = file_get_contents($cale);
            $randuri = preg_split('/\r\n|\n|\r/', rtrim($brut, "\r\n"));

            $declaratii = [];

            foreach ($randuri as $i => $rand) {
                if ($i === 0) {
                    continue;
                }

                $curat = trim($rand);
                $ascuns = strpos($curat, '#') === 0;
                $bucati = explode(';', ltrim($curat, '#'));

                if (count($bucati) < 3) {
                    continue;
                }

                $declaratii[$bucati[0]] = [
                    'rand' => $i,
                    'j' => $bucati[1],
                    'p' => $bucati[2],
                    'ascuns' => $ascuns,
                ];
            }

            return [
                'cale' => $cale,
                // Fisierul vine cu sfarsit de rand windows; asa se si lasa.
                'sfarsit' => strpos($brut, "\r\n") !== false ? "\r\n" : "\n",
                'randuri' => $randuri,
                'integrator' => trim($randuri[0] ?? ''),
                'declaratii' => $declaratii,
            ];
        }

        return [
            'cale' => $config . DIRECTORY_SEPARATOR . 'versiuniCurente.txt',
            'sfarsit' => PHP_EOL,
            'randuri' => [],
            'integrator' => null,
            'declaratii' => [],
        ];
    }

    /** @param array<string, array{j: string, p: string}> $schimbate */
    protected function scrieCatalogul(array $catalog, string $versiuneIntegrator, array $schimbate): void
    {
        $randuri = $catalog['randuri'];

        if ($randuri === []) {
            $randuri = [$versiuneIntegrator];
        } else {
            $randuri[0] = $versiuneIntegrator;
        }

        foreach ($schimbate as $nume => $versiuni) {
            $rand = $nume . ';' . $versiuni['j'] . ';' . $versiuni['p'];

            if (isset($catalog['declaratii'][$nume])) {
                $randuri[$catalog['declaratii'][$nume]['rand']] = $rand;

                continue;
            }

            // Declaratie noua la ANAF: se adauga la sfarsit.
            $randuri[] = $rand;
        }

        file_put_contents($catalog['cale'], implode($catalog['sfarsit'], $randuri) . $catalog['sfarsit']);
    }
}
