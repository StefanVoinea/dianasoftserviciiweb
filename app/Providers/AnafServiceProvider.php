<?php

namespace App\Providers;

use App\Services\Anaf\Arhiva\ArhivaService;
use App\Services\Anaf\Bridge\Licente;
use App\Services\Anaf\Bridge\LicentiereBridge;
use App\Services\Anaf\Bridge\Punte;
use App\Services\Anaf\Declaratii\DepunereService;
use App\Services\Anaf\Declaratii\ConcatenareService;
use App\Services\Anaf\Declaratii\DeclaratieXml;
use App\Services\Anaf\Declaratii\DukIntegrator;
use App\Services\Anaf\Declaratii\MonitorizareFolder;
use App\Services\Anaf\Declaratii\PdfDeclaratie;
use App\Services\Anaf\Declaratii\RecipisaService;
use App\Services\Anaf\Declaratii\SemnareService;
use App\Services\Anaf\Etransport\EtransportClient;
use App\Services\Anaf\Oauth\OauthAnaf;
use App\Services\Anaf\Spv\AlerteMesaje;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\Contracts\SpvTransport;
use App\Services\Anaf\Spv\SpvClient;
use App\Services\Anaf\Spv\SpvStorage;
use App\Services\Anaf\Spv\Transport\BridgeTransport;
use App\Services\Anaf\Spv\Transport\CertificateTransport;
use Illuminate\Support\ServiceProvider;

class AnafServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SpvTransport::class, function ($app) {
            $config = config('anaf.spv');

            if ($config['driver'] === 'cert') {
                return new CertificateTransport($config);
            }

            if ($config['driver'] === 'bridge') {
                // Transportul afla de la CertificatService pe ce bridge sa trimita.
                return new BridgeTransport($config, $app->make(CertificatService::class));
            }

            throw new \InvalidArgumentException('Driver SPV necunoscut: ' . $config['driver']);
        });

        $this->app->singleton(SpvClient::class, function ($app) {
            return new SpvClient(
                $app->make(SpvTransport::class),
                config('anaf.spv')
            );
        });

        $this->app->singleton(CertificatService::class, function () {
            $config = config('anaf.spv');
            $config['thumbprint'] = env('SPV_CERT_THUMBPRINT');

            return new CertificatService($config);
        });

        $this->app->singleton(SpvStorage::class, function ($app) {
            return new SpvStorage(
                $app->make(CertificatService::class),
                $app->make(ArhivaService::class),
                $app->make(AlerteMesaje::class)
            );
        });

        $this->app->singleton(EtransportClient::class, function ($app) {
            return new EtransportClient(
                config('anaf.etransport'),
                $app->make(CertificatService::class),
                $app->make(OauthAnaf::class)
            );
        });

        $this->app->singleton(DukIntegrator::class, function () {
            return new DukIntegrator(config('anaf.declaratii.duk'));
        });

        // Semnarea si depunerea merg pe bridge-ul certificatului ales.
        $this->app->singleton(SemnareService::class, function ($app) {
            return new SemnareService(
                config('anaf.spv') + config('anaf.declaratii'),
                $app->make(CertificatService::class)
            );
        });

        // Unirea PDF-urilor pentru tiparire foloseste tot programul local.
        $this->app->singleton(ConcatenareService::class, function ($app) {
            return new ConcatenareService(
                config('anaf.spv') + config('anaf.declaratii'),
                $app->make(CertificatService::class)
            );
        });

        $this->app->singleton(PdfDeclaratie::class, function ($app) {
            return new PdfDeclaratie(
                config('anaf.spv') + config('anaf.declaratii'),
                $app->make(CertificatService::class)
            );
        });

        $this->app->singleton(DepunereService::class, function ($app) {
            return new DepunereService(
                config('anaf.spv') + config('anaf.declaratii'),
                $app->make(CertificatService::class)
            );
        });

        // Arhiva de documente de pe calculatorul clientului, tot prin bridge.
        $this->app->singleton(ArhivaService::class, function ($app) {
            return new ArhivaService(
                config('anaf.arhiva'),
                $app->make(CertificatService::class)
            );
        });

        // Licentierea programului local de pe calculatorul clientului
        $this->app->singleton(LicentiereBridge::class, function ($app) {
            return new LicentiereBridge(
                $app->make(Licente::class),
                $app->make(CertificatService::class),
                $app->make(Punte::class),
                config('anaf.spv')
            );
        });

        // Dosarul urmarit de pe calculatorul clientului
        $this->app->singleton(MonitorizareFolder::class, function ($app) {
            return new MonitorizareFolder(
                config('anaf.declaratii'),
                $app->make(CertificatService::class),
                $app->make(DeclaratieXml::class),
                $app->make(DukIntegrator::class),
                $app->make(SemnareService::class),
                $app->make(ArhivaService::class),
                $app->make(PdfDeclaratie::class)
            );
        });

        $this->app->singleton(RecipisaService::class, function ($app) {
            return new RecipisaService(
                config('anaf.declaratii'),
                $app->make(SpvClient::class),
                $app->make(SpvStorage::class),
                $app->make(ArhivaService::class)
            );
        });
    }
}
