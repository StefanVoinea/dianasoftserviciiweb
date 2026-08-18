<?php

namespace App\Jobs;

use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Models\AnafSocietate;
use App\Services\Anaf\Arhiva\ArhivaService;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Spv\CertificatService;
use App\Support\ContextCompanie;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Arhiveaza declaratiile si recipisele importate din programul vechi.
 *
 * Fisierele stau pe calculatorul clientului, in dosarele programului vechi;
 * copierea in arhiva o face programul local, dintr-o cale in alta, la cererea
 * de aici. Merge prin coada: sute de depuneri inseamna sute de drumuri pana la
 * calculatorul clientului, iar cererea din administrare nu are de ce sa le
 * astepte.
 */
class ArhiveazaDepunerileImportate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Un lot mic de copii prin programul local; mai mic decat retry_after-ul
     * cozii, ca lucrarea sa nu fie data inca o data cat timp prima inca merge.
     */
    public $timeout = 3600;

    public $tries = 1;

    /** Dupa atatea esecuri la rand se lasa restul: programul local nu raspunde. */
    protected const ESECURI_LA_RAND = 5;

    /** @var int */
    public $companie;

    /** @var array<int, array{id: int, fisier: ?string, recipisa: ?string}> */
    public $lucrari;

    public function __construct(int $companie, array $lucrari)
    {
        $this->companie = $companie;
        $this->lucrari = $lucrari;
    }

    public function handle(ArhivaService $arhiva, CertificatService $certificate): void
    {
        ContextCompanie::pentru($this->companie, function () use ($arhiva, $certificate) {
            if (!$arhiva->activa()) {
                Jurnal::esec(
                    'import_depuneri',
                    'Declarațiile importate nu au fost arhivate: arhivarea locală nu este pornită.'
                );

                return;
            }

            $reusite = 0;
            $esuate = 0;
            $laRand = 0;

            foreach ($this->lucrari as $pozitie => $lucrare) {
                try {
                    $reusite += $this->arhiveaza($arhiva, $certificate, $lucrare);
                    $laRand = 0;
                } catch (\Exception $e) {
                    $esuate++;
                    $laRand++;

                    Jurnal::esec(
                        'import_depuneri',
                        'Depunerea #' . $lucrare['id'] . ' nu a putut fi arhivată: ' . $e->getMessage()
                    );

                    /*
                     * Cazute mai multe la rand, pricina e una singura — programul
                     * local oprit sau fara comanda de copiere. Restul ar cadea la
                     * fel, dupa cate un timp de asteptare fiecare: se spune si se
                     * lasa, in loc sa se astepte degeaba ore intregi.
                     */
                    if ($laRand >= self::ESECURI_LA_RAND) {
                        Jurnal::esec(
                            'import_depuneri',
                            sprintf(
                                'Arhivarea s-a oprit după %d eșecuri la rând; %d depuneri rămân nearhivate.'
                                    . ' Porniți/actualizați programul local și importați din nou fișierul.',
                                $laRand,
                                count($this->lucrari) - $pozitie - 1
                            )
                        );

                        break;
                    }
                }
            }

            Jurnal::scrie(
                'import_depuneri',
                sprintf(
                    'A arhivat documentele depunerilor importate: %d fișiere aduse, %d eșuate din %d depuneri.',
                    $reusite,
                    $esuate,
                    count($this->lucrari)
                )
            );
        });
    }

    /** @return int cate fisiere a adus pentru aceasta depunere */
    protected function arhiveaza(ArhivaService $arhiva, CertificatService $certificate, array $lucrare): int
    {
        $declaratie = AnafDeclaratie::find($lucrare['id']);

        if (!$declaratie) {
            return 0;
        }

        /*
         * Copierea o face calculatorul care raspunde de firma: cel al
         * certificatului cu care e inrolata — acolo a lucrat programul vechi si
         * acolo e si arhiva. Fara inrolare, certificatul implicit al clientului.
         */
        $societate = AnafSocietate::where('cif', $declaratie->cui)->first();
        $certificat = $societate && $societate->certificat
            ? $societate->certificat
            : AnafCertificat::where('activ', true)->orderByDesc('implicit')->first();

        if (!$certificat) {
            throw new \RuntimeException('Clientul nu are niciun certificat activ prin care să se copieze.');
        }

        $certificate->foloseste($certificat);

        $denumire = $declaratie->den_firma ?: optional($societate)->denumire;
        $dosarFirma = ArhivaService::dosarFirma($denumire, $declaratie->cui);
        $dosarTip = ArhivaService::curata($declaratie->tip) ?: 'Diverse';

        $arhiva->uneste($declaratie->cui, $dosarFirma);

        $aduse = 0;

        if (!empty($lucrare['fisier']) && !$declaratie->arhiva_semnat) {
            $nume = ArhivaService::numeDeclaratie($declaratie, 'depusa', 'pdf');

            $cale = $arhiva->dinLocal($lucrare['fisier'], $dosarFirma, $dosarTip, $nume);
            $declaratie->update(['arhiva_semnat' => $cale]);

            // Declaratiile depuse stau si in dosarul comun „Toate".
            $arhiva->copiazaInToate($cale, $nume);
            $aduse++;
        }

        if (!empty($lucrare['recipisa']) && !$declaratie->arhiva_recipisa) {
            $nume = ArhivaService::numeDeclaratie($declaratie, 'recipisa', 'pdf');

            $cale = $arhiva->dinLocal($lucrare['recipisa'], $dosarFirma, $dosarTip, $nume);
            $declaratie->update(['arhiva_recipisa' => $cale]);

            $arhiva->copiazaInToate($cale, $nume);
            $aduse++;
        }

        return $aduse;
    }
}
