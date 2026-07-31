<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Actualizeaza DUKIntegrator (utilitarul ANAF de validare) si nomenclatoarele de
 * declaratii, dupa acelasi mecanism ca aplicatia desktop: se citeste versiuni.xml
 * de pe static.anaf.ro si se descarca fisierele mai noi.
 */
class ActualizeazaDukIntegrator extends Command
{
    protected $signature = 'anaf:duk-update
                            {--url= : URL-ul versiuni.xml (implicit cel din dist/config/configURL.properties)}
                            {--force : Descarca chiar daca versiunea locala e la zi}';

    protected $description = 'Actualizează DUKIntegrator și declarațiile de la ANAF';

    public function handle(): int
    {
        $jar = config('anaf.declaratii.duk.jar');
        $dist = dirname($jar);
        $config = $dist . DIRECTORY_SEPARATOR . 'config';

        if (!is_dir($dist)) {
            $this->error('Folderul DUKIntegrator nu există: ' . $dist);

            return 1;
        }

        $url = $this->option('url') ?: $this->urlVersiuni($config);

        if (!$url) {
            $this->error('Nu am găsit urlVersiuni în configURL.properties. Folosiți --url=');

            return 1;
        }

        $this->info('Descarc lista de versiuni de la ' . $url);

        $raspuns = Http::timeout(60)->get($url);

        if ($raspuns->failed()) {
            $this->error('Nu am putut descărca versiuni.xml (HTTP ' . $raspuns->status() . ')');

            return 1;
        }

        $anterior = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($raspuns->body());
        libxml_use_internal_errors($anterior);

        if ($xml === false) {
            $this->error('versiuni.xml nu este un XML valid.');

            return 1;
        }

        $versiuneNoua = trim((string) $xml->integrator->versiune);
        $versiuneLocala = $this->versiuneLocala($config);

        $this->line('Versiune locală: ' . ($versiuneLocala ?: 'necunoscută'));
        $this->line('Versiune ANAF:   ' . $versiuneNoua);

        if ($versiuneLocala === $versiuneNoua && !$this->option('force')) {
            $this->info('DUKIntegrator este deja la zi.');

            return 0;
        }

        // iJars/sJars merg in dist/lib, zJars/dJars in dist, cFisiere in dist/config.
        $grupuri = [
            'iJars' => $dist . DIRECTORY_SEPARATOR . 'lib',
            'sJars' => $dist . DIRECTORY_SEPARATOR . 'lib',
            'zJars' => $dist,
            'dJars' => $dist,
            'cFisiere' => $config,
        ];

        $descarcate = 0;
        $esuate = 0;

        foreach ($grupuri as $grup => $destinatie) {
            if (!isset($xml->integrator->$grup)) {
                continue;
            }

            foreach ($xml->integrator->$grup->children() as $nod) {
                $linkFisier = trim((string) $nod);

                if ($linkFisier === '') {
                    continue;
                }

                $this->descarca($linkFisier, $destinatie, $descarcate, $esuate);
            }
        }

        // Nomenclatoarele fiecarei declaratii (D112, D300, ...)
        if (isset($xml->declaratii)) {
            $declaratii = $xml->declaratii->children();
            $bara = $this->output->createProgressBar(count($declaratii));
            $bara->start();

            foreach ($declaratii as $declaratie) {
                foreach ($declaratie->children() as $nod) {
                    $linkFisier = trim((string) $nod);

                    if ($linkFisier !== '' && preg_match('#^https?://#i', $linkFisier)) {
                        $this->descarca($linkFisier, $dist . DIRECTORY_SEPARATOR . 'lib', $descarcate, $esuate, false);
                    }
                }

                $bara->advance();
            }

            $bara->finish();
            $this->newLine();
        }

        $this->scrieVersiune($config, $versiuneNoua);

        $this->info('Actualizare finalizată: ' . $descarcate . ' fișiere descărcate, ' . $esuate . ' eșuate.');

        return $esuate > 0 && $descarcate === 0 ? 1 : 0;
    }

    protected function descarca(string $url, string $destinatie, int &$descarcate, int &$esuate, bool $afiseaza = true): void
    {
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

    protected function versiuneLocala(string $config): ?string
    {
        foreach (['versiuniCurente.txt', 'versiunicurente.txt'] as $nume) {
            $cale = $config . DIRECTORY_SEPARATOR . $nume;

            if (is_file($cale)) {
                $linii = file($cale, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

                return $linii ? trim($linii[0]) : null;
            }
        }

        return null;
    }

    protected function scrieVersiune(string $config, string $versiune): void
    {
        $cale = $config . DIRECTORY_SEPARATOR . 'versiuniCurente.txt';
        $linii = is_file($cale) ? file($cale, FILE_IGNORE_NEW_LINES) : [];

        if ($linii) {
            $linii[0] = $versiune;
        } else {
            $linii = [$versiune];
        }

        file_put_contents($cale, implode(PHP_EOL, $linii) . PHP_EOL);
    }
}
