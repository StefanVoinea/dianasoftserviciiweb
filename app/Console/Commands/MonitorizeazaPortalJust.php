<?php

namespace App\Console\Commands;

use App\Mail\ModificariPortalJustEmail;
use App\Models\DispozitivNotificare;
use App\Models\PortalJustModificare;
use App\Models\PortalJustMonitorizare;
use App\Services\Just\MonitorizarePortalJust;
use App\Services\Just\PortalJustException;
use App\Services\Notificari\Fcm;
use App\Support\ContextCompanie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Verifica dosarele urmarite in Portal Just si instiinteaza pe email
 * utilizatorii pentru care au aparut modificari.
 *
 * Se ruleaza zilnic. Fiecare destinatar primeste un singur email, cu toate
 * modificarile lui, grupate pe dosar.
 */
class MonitorizeazaPortalJust extends Command
{
    protected $signature = 'portaljust:monitorizeaza
                            {--monitorizare= : Verifică doar monitorizarea cu acest id}
                            {--fara-email : Doar verifică și înregistrează, fără să trimită emailuri}';

    protected $description = 'Verifică dosarele urmărite în Portal Just și trimite modificările pe email';

    public function handle(MonitorizarePortalJust $serviciu): int
    {
        // Rularea din consolă nu are client selectat: se trece prin fiecare,
        // ca fiecare client să vadă doar dosarele lui.
        $companii = PortalJustMonitorizare::query()->toateCompaniile()
            ->active()
            ->distinct()
            ->pluck('company_id');

        // Se adaugă și clienții cu înștiințări netrimise: o monitorizare oprită
        // între timp nu trebuie să lase modificările deja găsite neanunțate.
        $restante = PortalJustModificare::query()->toateCompaniile()
            ->where(function ($query) {
                $query->whereNull('notificat_la')->orWhereNull('push_la');
            })
            ->distinct()
            ->pluck('company_id');

        $companii = $companii->merge($restante)->unique()->values();

        if ($companii->isEmpty()) {
            $this->info('Nicio monitorizare activă.');

            return 0;
        }

        $totalModificari = 0;

        foreach ($companii as $companie) {
            $totalModificari += ContextCompanie::pentru($companie, function () use ($serviciu, $companie) {
                return $this->pentruClient($serviciu, $companie);
            });
        }

        $this->info('Gata: ' . $totalModificari . ' modificări sesizate.');

        return 0;
    }

    /** @return int numărul de modificări găsite pentru clientul curent */
    protected function pentruClient(MonitorizarePortalJust $serviciu, $companie): int
    {
        $query = PortalJustMonitorizare::active();

        if ($this->option('monitorizare')) {
            $query->where('id', (int) $this->option('monitorizare'));
        }

        $monitorizari = $query->get();

        if ($monitorizari->isNotEmpty()) {
            $this->line('Client ' . $companie . ': ' . $monitorizari->count() . ' monitorizări');
        }

        $pauza = (int) config('portaljust.monitorizare.pauza_ms');
        $gasite = 0;

        foreach ($monitorizari as $monitorizare) {
            try {
                $modificari = $serviciu->verifica($monitorizare);
            } catch (PortalJustException $e) {
                Log::warning('Portal Just: verificare eșuată pentru ' . $monitorizare->valoare . ': ' . $e->getMessage());
                $this->warn('  ' . $monitorizare->valoare . ' — ' . $e->getMessage());

                continue;
            }

            $gasite += count($modificari);

            $this->line(sprintf(
                '  %s „%s”: %d modificări',
                $monitorizare->tip_etichete,
                $monitorizare->valoare,
                count($modificari)
            ));

            if ($pauza > 0) {
                usleep($pauza * 1000);
            }
        }

        if (!$this->option('fara-email')) {
            $this->instiinteaza();
            $this->alerteazaPeTelefon();
        }

        return $gasite;
    }

    /**
     * Alertele instantanee pe telefon, prin Firebase.
     *
     * Se urmăresc separat de emailuri: dacă una dintre căi eșuează, doar aceea
     * se reia. Fără Firebase configurat nu se întâmplă nimic aici — aplicația
     * mobilă are oricum verificarea ei periodică.
     */
    protected function alerteazaPeTelefon(): void
    {
        $fcm = app(Fcm::class);

        if (!$fcm->activ()) {
            return;
        }

        $modificari = PortalJustModificare::faraPush()
            ->with('monitorizare')
            ->orderBy('id')
            ->get()
            ->filter(function (PortalJustModificare $modificare) {
                return $modificare->monitorizare && $modificare->monitorizare->user_id;
            });

        if ($modificari->isEmpty()) {
            return;
        }

        foreach ($modificari->groupBy(function ($modificare) {
            return $modificare->monitorizare->user_id;
        }) as $userId => $aleUtilizatorului) {
            $dispozitive = DispozitivNotificare::where('user_id', $userId)->get();

            if ($dispozitive->isEmpty()) {
                // Utilizatorul nu are aplicația instalată: rămâne cu emailul.
                PortalJustModificare::whereIn('id', $aleUtilizatorului->pluck('id'))
                    ->update(['push_la' => now()]);

                continue;
            }

            [$titlu, $corp] = $this->textAlertei($aleUtilizatorului);

            $trimise = 0;

            foreach ($dispozitive as $dispozitiv) {
                $rezultat = $fcm->trimite($dispozitiv->token, $titlu, $corp, [
                    'tip' => 'modificari_dosare',
                    'modificare_id' => $aleUtilizatorului->max('id'),
                    'dosar' => $aleUtilizatorului->first()->dosar_numar,
                ]);

                if ($rezultat === Fcm::TRIMIS) {
                    $dispozitiv->update(['ultima_folosire' => now(), 'esecuri' => 0]);
                    $trimise++;

                    continue;
                }

                if ($rezultat === Fcm::TOKEN_INVALID) {
                    // Aplicația a fost dezinstalată sau tokenul înlocuit.
                    $dispozitiv->delete();

                    continue;
                }

                $dispozitiv->increment('esecuri');

                if ($dispozitiv->esecuri >= DispozitivNotificare::ESECURI_MAXIME) {
                    $dispozitiv->delete();
                }
            }

            // Se marchează chiar dacă niciun dispozitiv nu a răspuns: altfel
            // aceleași alerte s-ar reîncerca la nesfârșit, din oră în oră.
            PortalJustModificare::whereIn('id', $aleUtilizatorului->pluck('id'))
                ->update(['push_la' => now()]);

            $this->line(sprintf(
                '  Alertă trimisă utilizatorului %d către %d din %d dispozitive',
                $userId,
                $trimise,
                $dispozitive->count()
            ));
        }
    }

    /** @return array{0:string, 1:string} titlul și textul alertei */
    protected function textAlertei($modificari): array
    {
        if ($modificari->count() === 1) {
            $modificare = $modificari->first();

            return ['Dosar ' . $modificare->dosar_numar, $modificare->descriere];
        }

        $dosare = $modificari->pluck('dosar_numar')->unique();

        return [
            $modificari->count() . ' modificări la dosarele urmărite',
            $dosare->count() === 1
                ? 'Dosar ' . $dosare->first()
                : 'Dosarele: ' . $dosare->take(4)->implode(', ') . ($dosare->count() > 4 ? ' și altele' : ''),
        ];
    }

    /** Trimite fiecărui destinatar modificările lui nenotificate. */
    protected function instiinteaza(): void
    {
        $modificari = PortalJustModificare::nenotificate()
            ->with('monitorizare')
            ->orderBy('dosar_numar')
            ->get()
            ->filter(function (PortalJustModificare $modificare) {
                return $modificare->monitorizare && $modificare->monitorizare->email;
            });

        if ($modificari->isEmpty()) {
            return;
        }

        foreach ($modificari->groupBy(function ($modificare) {
            return $modificare->monitorizare->email;
        }) as $email => $aleDestinatarului) {
            $dosare = $this->grupeazaPeDosar($aleDestinatarului);

            try {
                Mail::to($email)->send(new ModificariPortalJustEmail($dosare, $aleDestinatarului->count()));

                PortalJustModificare::whereIn('id', $aleDestinatarului->pluck('id'))
                    ->update(['notificat_la' => now()]);

                $this->info(sprintf(
                    '  Email trimis către %s: %d modificări, %d dosare',
                    $email,
                    $aleDestinatarului->count(),
                    count($dosare)
                ));
            } catch (\Exception $e) {
                // Modificările rămân nenotificate: se reîncearcă la rularea următoare.
                Log::error('Portal Just: email eșuat către ' . $email . ': ' . $e->getMessage());
                $this->error('  Trimitere eșuată către ' . $email . ': ' . $e->getMessage());
            }
        }
    }

    /** @return array<int, array{numar:string, institutie:?string, urmarit_pentru:?string, modificari:array}> */
    protected function grupeazaPeDosar($modificari): array
    {
        $dosare = [];

        foreach ($modificari->groupBy('dosar_numar') as $numar => $aleDosarului) {
            $prima = $aleDosarului->first();

            $dosare[] = [
                'numar' => $numar,
                'institutie' => $prima->institutie,
                'urmarit_pentru' => $prima->monitorizare
                    ? $prima->monitorizare->tip_etichete . ' „' . $prima->monitorizare->valoare . '”'
                    : null,
                'modificari' => $aleDosarului->pluck('descriere')->all(),
            ];
        }

        return $dosare;
    }
}
