<?php

namespace App\Services\Anaf\Bridge;

use App\Models\AnafCertificat;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Spv\CertificatService;
use Illuminate\Support\Facades\Http;

/**
 * Punerea licenței pe calculatorul clientului.
 *
 * Programul local nu iese niciodată în internet: serverul îl caută, îi cere
 * amprenta calculatorului și îi trimite licența semnată pentru ea. De aceea
 * „reînnoirea" nu e un puls dinspre client, ci o vizită dinspre server.
 */
class LicentiereBridge
{
    /** Cu câte zile înainte de expirare se reînnoiește. */
    protected const REINNOIRE_CU = 10;

    protected $licente;
    protected $certificate;
    protected $punte;
    protected $config;

    /** Certificatul de acum merge prin tunel? Se află la fiecare reînnoire. */
    protected $esteTunel = false;

    public function __construct(Licente $licente, CertificatService $certificate, Punte $punte, array $config)
    {
        $this->licente = $licente;
        $this->certificate = $certificate;
        $this->punte = $punte;
        $this->config = $config;
    }

    /**
     * @return array{emisa: bool, expira: ?string, motiv: ?string}
     */
    public function reinnoieste(AnafCertificat $certificat, bool $forteaza = false): array
    {
        $this->certificate->foloseste($certificat);
        $this->esteTunel = $this->punte->esteTunel($certificat);
        $bridge = $this->certificate->bridge();

        if (empty($bridge['url'])) {
            return ['emisa' => false, 'expira' => null, 'motiv' => 'nu are calculator configurat'];
        }

        try {
            $identitate = $this->identitatea($bridge);
        } catch (\Exception $e) {
            return ['emisa' => false, 'expira' => null, 'motiv' => $e->getMessage()];
        }

        if (empty($identitate['masina'])) {
            /*
             * Programele dinaintea licențierii nu știu de ruta aceasta. Nu e o
             * eroare: ele merg mai departe cu codul de acces, până la actualizare.
             */
            return ['emisa' => false, 'expira' => null, 'motiv' => 'program vechi, fără licențiere'];
        }

        if (!$forteaza && $this->maiTine($identitate)) {
            return ['emisa' => false, 'expira' => $identitate['licenta']['expira'] ?? null, 'motiv' => null];
        }

        $licenta = $this->licente->emite($certificat, $identitate['masina']);

        $raspuns = $this->cerere($bridge)->post($this->url($bridge, '/licenta'), $licenta);

        if ($raspuns->failed()) {
            $payload = json_decode($raspuns->body(), true);

            return [
                'emisa' => false,
                'expira' => null,
                'motiv' => $payload['detalii'] ?? $payload['eroare'] ?? 'HTTP ' . $raspuns->status(),
            ];
        }

        // În licență data e ISO 8601, ca s-o citească și programul local.
        $certificat->update(['licenta_pana_la' => \Carbon\Carbon::parse($licenta['date']['expira'])]);

        Jurnal::scrie(
            'licenta_bridge',
            'A licențiat programul local al certificatului ' . $certificat->cn
                . ' până la ' . $licenta['date']['expira'],
            ['masina' => $identitate['masina']],
            null,
            true,
            Jurnal::BRIDGE
        );

        return ['emisa' => true, 'expira' => $licenta['date']['expira'], 'motiv' => null];
    }

    /** Amprenta calculatorului și starea licenței de acolo. */
    public function identitatea(array $bridge): array
    {
        $raspuns = $this->cerere($bridge)->get($this->url($bridge, '/identitate'));

        if ($raspuns->status() === 404) {
            return [];
        }

        if ($raspuns->failed()) {
            $payload = json_decode($raspuns->body(), true);

            throw new \RuntimeException(
                'calculatorul nu a răspuns: ' . ($payload['detalii'] ?? $payload['eroare'] ?? 'HTTP ' . $raspuns->status())
            );
        }

        return $raspuns->json() ?: [];
    }

    protected function maiTine(array $identitate): bool
    {
        if (empty($identitate['licentiat']) || empty($identitate['licenta']['expira'])) {
            return false;
        }

        return strtotime($identitate['licenta']['expira']) > strtotime('+' . self::REINNOIRE_CU . ' days');
    }

    /**
     * Cu ce se legitimează cererea de licențiere.
     *
     * La legătură directă, cu codul de instalare: programul de-abia primește
     * licența, iar fără ea n-ar recunoaște un jeton semnat.
     *
     * Prin tunel însă, primul care primește cererea e puntea noastră, iar ea
     * cere jeton semnat. Codul de instalare îl pune puntea mai departe, când
     * trimite comanda către programul de la client.
     */
    protected function cerere(array $bridge)
    {
        $legitimare = $this->esteTunel ? $bridge['token'] : $bridge['cod_instalare'];

        return Http::withToken($legitimare)
            ->timeout($this->config['timeout'] ?? 30);
    }

    protected function url(array $bridge, string $cale): string
    {
        return rtrim($bridge['url'], '/') . $cale;
    }
}
