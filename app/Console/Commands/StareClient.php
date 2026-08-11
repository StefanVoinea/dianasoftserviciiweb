<?php

namespace App\Console\Commands;

use App\Models\AnafCertificat;
use App\Models\AnafSocietate;
use App\Models\Company;
use App\Services\Anaf\Bridge\Punte;
use App\Support\ContextCompanie;
use Illuminate\Console\Command;

/**
 * Ce stie aplicatia despre calculatorul unui client.
 *
 * S-a nascut dintr-un "0 licente emise, 0 sarite": comanda de licentiere nu
 * gasise niciun certificat pentru clientul acela, iar din raspunsul ei nu se
 * putea sti daca vina e a clientului gresit, a certificatului scos din uz, sau
 * a unei inrolari care nu s-a facut niciodata.
 *
 * De fiecare data cand ceva nu merge la un client, primele intrebari sunt
 * aceleasi: are certificate? sunt in lucru? agentul a mai dat semne? are
 * licenta? ce versiune de program? Aici se raspunde la toate deodata.
 */
class StareClient extends Command
{
    protected $signature = 'anaf:stare-client
                            {client : id-ul clientului (company_id)}';

    protected $description = 'Arată starea certificatelor și a programelor locale ale unui client';

    public function handle(Punte $punte): int
    {
        $id = (int) $this->argument('client');
        $firma = Company::find($id);

        $this->line('Client: ' . $id . ($firma ? ' — ' . $firma->denumire : ' (nu există în „companies")'));

        /*
         * Certificatele se cauta peste toate companiile, apoi se filtreaza: asa
         * se vede si cazul in care ele exista, dar sunt trecute la alt client —
         * pricina cea mai greu de banuit si cea mai usor de indreptat.
         */
        $certificate = AnafCertificat::query()->toateCompaniile()
            ->where('company_id', $id)
            ->orderByDesc('implicit')
            ->orderBy('cn')
            ->get();

        if ($certificate->isEmpty()) {
            $this->warn('Niciun certificat pentru acest client.');

            $altele = AnafCertificat::query()->toateCompaniile()
                ->select('company_id')
                ->selectRaw('count(*) as cate')
                ->groupBy('company_id')
                ->orderByDesc('cate')
                ->limit(5)
                ->get();

            if ($altele->isNotEmpty()) {
                $this->line('');
                $this->line('Clienți care au certificate (primii 5, după număr):');

                foreach ($altele as $rand) {
                    $numeAltuia = optional(Company::find($rand->company_id))->denumire;
                    $this->line('  ' . $rand->company_id . ': ' . $rand->cate
                        . ($numeAltuia ? ' — ' . $numeAltuia : ''));
                }
            }

            $this->line('');
            $this->line('De verificat: id-ul clientului, sau dacă agentul a apucat să se înroleze');
            $this->line('(în jurnalul lui scrie „Certificatele de pe acest calculator au fost anunțate").');

            return 1;
        }

        foreach ($certificate as $certificat) {
            $this->line('');
            $this->line('  ' . ($certificat->cn ?: '(fără nume)') . '  [id ' . $certificat->id . ']');
            $this->line('    amprentă:    ' . $certificat->thumbprint);
            $this->line('    stare:       ' . ($certificat->activ ? 'în lucru' : 'SCOS DIN UZ')
                . ($certificat->implicit ? ', implicit' : ''));
            $this->line('    legătură:    ' . ($certificat->mod_legatura ?: 'directă')
                . ($certificat->bridge_url ? ' (' . $certificat->bridge_url . ')' : ''));
            $this->line('    agent văzut: ' . $this->cand($certificat->agent_vazut_la)
                . ($punte->agentulEsteTreaz($certificat) ? ' — treaz' : ' — NU răspunde'));
            $this->line('    licență:     ' . $this->cand($certificat->licenta_pana_la, 'nu are'));
            $this->line('    program:     ' . ($certificat->versiune_bridge ?: 'nu s-a anunțat'));
            $this->line('    PIN:         ' . ($certificat->pin_stare ?: 'nu s-a probat')
                . ($certificat->pin_motiv ? ' (' . $certificat->pin_motiv . ')' : ''));

            $inrolate = ContextCompanie::pentru($id, function () use ($certificat) {
                return AnafSocietate::where('certificat_id', $certificat->id)->count();
            });

            $this->line('    entități:    ' . $inrolate);
        }

        $this->line('');

        $faraLicenta = $certificate->where('activ', true)->whereNull('licenta_pana_la');

        if ($faraLicenta->isNotEmpty()) {
            $this->warn($faraLicenta->count() . ' certificat(e) în lucru fără licență.');
            $this->line('Se emite cu: php artisan anaf:licente-bridge --client=' . $id);
        }

        return 0;
    }

    /** O dată, spusă pe scurt, sau de ce lipsește. */
    protected function cand($data, string $candLipseste = 'niciodată'): string
    {
        if (!$data) {
            return $candLipseste;
        }

        return $data->format('d.m.Y H:i') . ' (' . $data->diffForHumans() . ')';
    }
}
