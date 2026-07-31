<?php

namespace App\Console\Commands;

use App\Models\AnafCertificat;
use App\Models\CertificatAbonat;
use App\Support\ContextCompanie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Avertizeaza pe email inainte de expirarea certificatelor digitale.
 * Se ruleaza zilnic; avertizarea se repeta periodic cat timp certificatul
 * este in fereastra de expirare, ca sa nu fie ratata.
 */
class AvertizeazaExpirareCertificate extends Command
{
    protected $signature = 'anaf:certificate-expira
                            {--zile= : Cu câte zile înainte se avertizează (implicit din config)}
                            {--forteaza : Trimite chiar dacă s-a avertizat recent}';

    protected $description = 'Trimite avertizări email pentru certificatele digitale care expiră';

    public function handle(): int
    {
        $zile = (int) ($this->option('zile') ?: config('anaf.certificate.zile_avertizare'));

        // Rularea din consola nu are client selectat: se trece prin fiecare, ca
        // avertizarile sa ajunga doar la abonatii clientului respectiv.
        $companii = AnafCertificat::query()->toateCompaniile()
            ->whereNotNull('company_id')
            ->distinct()
            ->pluck('company_id');

        if ($companii->isEmpty()) {
            $this->info('Niciun certificat înregistrat.');

            return 0;
        }

        $total = 0;

        foreach ($companii as $companie) {
            $total += ContextCompanie::pentru($companie, function () use ($zile, $companie) {
                return $this->pentruClient($companie, $zile);
            });
        }

        return 0;
    }

    /** @return int numărul de avertizări trimise pentru clientul curent */
    protected function pentruClient($companie, int $zile): int
    {
        $reamintire = (int) config('anaf.certificate.reamintire_zile');

        $certificate = AnafCertificat::deAvertizat($zile)->get();

        if ($certificate->isEmpty()) {
            return 0;
        }

        $this->line('Client ' . $companie . ':');

        $trimise = 0;

        foreach ($certificate as $certificat) {
            $avertizatRecent = $certificat->avertizat_la
                && $certificat->avertizat_la->greaterThan(now()->subDays($reamintire));

            if ($avertizatRecent && !$this->option('forteaza')) {
                $this->line('Sărit (avertizat recent): ' . $certificat->cn);
                continue;
            }

            $destinatari = CertificatAbonat::pentruCertificat($certificat->id);

            if ($destinatari === []) {
                $this->warn('Nu există adrese abonate pentru ' . $certificat->cn . ' — avertizarea nu poate fi trimisă.');
                continue;
            }

            try {
                Mail::raw($this->mesaj($certificat), function ($mail) use ($destinatari, $certificat) {
                    $mail->to($destinatari)
                        ->subject('Certificat digital: expiră în ' . $certificat->zile_ramase . ' zile');
                });

                $certificat->update(['avertizat_la' => now()]);
                $trimise++;

                $this->info(sprintf(
                    'Avertizare trimisă pentru %s (%d zile) către: %s',
                    $certificat->cn,
                    $certificat->zile_ramase,
                    implode(', ', $destinatari)
                ));
            } catch (\Exception $e) {
                Log::error('Avertizare certificat eșuată: ' . $e->getMessage());
                $this->error('Trimitere eșuată pentru ' . $certificat->cn . ': ' . $e->getMessage());
            }
        }

        return $trimise;
    }

    protected function mesaj(AnafCertificat $certificat): string
    {
        $entitati = $certificat->societati()->pluck('denumire', 'cif')
            ->map(function ($denumire, $cif) {
                return '  - ' . $cif . ($denumire ? ' (' . $denumire . ')' : '');
            })->implode(PHP_EOL);

        return implode(PHP_EOL, array_filter([
            'Certificatul digital folosit pentru SPV și semnarea declarațiilor expiră în curând.',
            '',
            'Titular:   ' . $certificat->cn,
            'Emitent:   ' . $certificat->emitent,
            'Serie:     ' . $certificat->serie,
            'Expiră la: ' . optional($certificat->valabil_pana_la)->format('d.m.Y H:i')
                . ' (peste ' . $certificat->zile_ramase . ' zile)',
            '',
            $entitati ? 'Entități afectate:' : null,
            $entitati ?: null,
            '',
            'După expirare nu veți mai putea citi mesajele SPV, solicita documente sau semna și depune declarații,',
            'până la înlocuirea certificatului și actualizarea amprentei (SPV_CERT_THUMBPRINT) în configurație.',
        ], function ($linie) {
            return $linie !== null;
        }));
    }
}
