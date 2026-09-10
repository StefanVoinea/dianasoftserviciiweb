<?php

namespace App\Services\Anaf\Etransport;

use App\Models\EtransportDeclaratie;
use App\Models\EtransportNotificare;

/**
 * [2026-09-10] Starea adevărată a unei declarații depuse.
 *
 * La încărcare ANAF întoarce întotdeauna un cod UIT, chiar și pentru o
 * declarație pe care o va respinge. Scrie asta chiar în răspunsul de atunci:
 *
 *   „Verificati starea XML-ului transmis. Codul UIT este valabil din momentul
 *    in care apare ca valid dupa apelul de stare"
 *
 * Prelucrarea se face după aceea, iar verdictul vine pe două căi care spun
 * același lucru:
 *
 *   - interogarea de stare (`stareMesaj`): `ok`, `in prelucrare` sau `nok`,
 *     acesta din urmă cu motivul în `Errors`;
 *   - notificarea din lista e-Transport: `OK` sau `ERR`, cu motivul în `mesaje`.
 *
 * Amândouă trec pe aici, ca fila „Declarații UIT" și fila „Notificări" să nu se
 * poată contrazice: o declarație respinsă arată respinsă și fără să apese
 * cineva „Preia".
 */
class StareDeclaratie
{
    protected $client;

    public function __construct(EtransportClient $client)
    {
        $this->client = $client;
    }

    /**
     * Întreabă ANAF ce s-a ales de declarație și îi pune starea întoarsă.
     *
     * @return array{schimbata: bool, stare: string, erori: array<int, string>, raspuns: array}
     */
    public function verifica(EtransportDeclaratie $declaratie): array
    {
        $raspuns = $this->client->stareMesaj($declaratie->index_incarcare, $declaratie->cif_declarant);

        return $this->dupaStareMesaj($declaratie, $raspuns);
    }

    /**
     * Aceeași socoteală, pe un răspuns deja primit.
     *
     * @return array{schimbata: bool, stare: string, erori: array<int, string>, raspuns: array}
     */
    public function dupaStareMesaj(EtransportDeclaratie $declaratie, array $raspuns): array
    {
        $stare = mb_strtolower(trim((string) ($raspuns['stare'] ?? '')));
        $uit = $raspuns['UIT'] ?? ($raspuns['uit'] ?? null);

        $campuri = ['raspuns_anaf' => $raspuns];

        if ($uit) {
            $campuri['uit'] = $uit;
        }

        $noua = $this->stareDupaRaspuns($stare);
        $veche = $declaratie->stare;

        if ($noua !== null) {
            $campuri['stare'] = $noua;
        }

        $declaratie->update($campuri);

        return [
            'schimbata' => $noua !== null && $noua !== $veche,
            'stare' => $declaratie->stare,
            'erori' => $declaratie->fresh()->erori_anaf,
            'raspuns' => $raspuns,
        ];
    }

    /**
     * Verdictul din notificarea preluată în fila Notificări.
     *
     * E același verdict, venit pe cealaltă cale. O declarație depusă mai demult,
     * pe care nimeni n-a apucat s-o verifice, se îndreaptă și de aici.
     */
    public function dupaNotificare(EtransportNotificare $notificare): ?EtransportDeclaratie
    {
        $noua = [
            'OK' => 'validata',
            'ERR' => 'respinsa',
        ][$notificare->stare] ?? null;

        if ($noua === null) {
            return null;
        }

        $declaratie = $this->declaratiaNotificarii($notificare);

        // O ciornă nedepusă din aplicație nu se atinge: notificarea e a altei depuneri.
        if ($declaratie === null || $declaratie->stare === 'ciorna' || $declaratie->stare === $noua) {
            return null;
        }

        $declaratie->update(['stare' => $noua]);

        return $declaratie;
    }

    /**
     * Declarația din care a plecat notificarea.
     *
     * Întâi după indexul de încărcare, care e al depunerii și deci sigur; dacă
     * notificarea nu-l poartă (unele vechi n-au), după codul UIT.
     */
    protected function declaratiaNotificarii(EtransportNotificare $notificare): ?EtransportDeclaratie
    {
        if ($notificare->id_incarcare) {
            $declaratie = EtransportDeclaratie::where('index_incarcare', $notificare->id_incarcare)->first();

            if ($declaratie) {
                return $declaratie;
            }
        }

        if ($notificare->uit) {
            return EtransportDeclaratie::where('uit', $notificare->uit)->first();
        }

        return null;
    }

    /**
     * Ce înseamnă starea întoarsă de ANAF.
     *
     * `null` = nu se știe încă, declarația rămâne cum era. Orice altceva decât
     * „ok" și „în prelucrare" e o respingere: ANAF scrie „nok" la o declarație
     * căzută la validare și „XML cu erori nepreluat de sistem" la una pe care
     * nici n-a citit-o.
     */
    protected function stareDupaRaspuns(string $stare): ?string
    {
        if ($stare === 'ok') {
            return 'validata';
        }

        if ($stare === '' || mb_strpos($stare, 'prelucrare') !== false) {
            return null;
        }

        return 'respinsa';
    }
}
