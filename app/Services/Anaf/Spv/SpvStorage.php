<?php

namespace App\Services\Anaf\Spv;

use App\Models\AnafDeclaratie;
use App\Models\AnafSocietate;
use App\Models\SpvMesaj;
use App\Services\Anaf\Arhiva\ArhivaException;
use App\Services\Anaf\Arhiva\ArhivaService;
use App\Services\Anaf\Jurnal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SpvStorage
{
    protected $certificate;

    /** Arhiva de pe calculatorul clientului; lipseste doar in teste. */
    protected $arhiva;

    /** Instiintarile pe email pentru mesajele nou intrate. */
    protected $alerte;

    public function __construct(
        ?CertificatService $certificate = null,
        ?ArhivaService $arhiva = null,
        ?AlerteMesaje $alerte = null
    ) {
        $this->certificate = $certificate;
        $this->arhiva = $arhiva;
        $this->alerte = $alerte;
    }

    /**
     * Duce documentul adus din SPV in arhiva de pe calculatorul clientului.
     *
     * Locul lui de drept e dosarul SPV al firmei, pe tipuri de document:
     *
     *     <Firma (CUI)>\SPV\<Tip document>\<Tip>_<CIF>_<data descarcarii>_<id>.pdf
     *
     * Recipisele primesc pe deasupra si o copie langa declaratia la care
     * raspund, ca sa fie gasite acolo unde le cauta omul — dar raman si in SPV,
     * unde stau toate documentele aduse de acolo.
     *
     * Denumirea dosarului firmei vine din Entitati inrolate; daca CUI-ul nu e
     * inrolat, dosarul poarta doar codul fiscal.
     */
    public function arhiveazaMesaj(SpvMesaj $mesaj, SpvFisier $fisier): ?string
    {
        if (!$this->arhiva || !$this->arhiva->activa()) {
            return null;
        }

        $societate = $mesaj->cif ? AnafSocietate::where('cif', $mesaj->cif)->first() : null;
        $declaratie = $this->declaratiaMesajului($mesaj);
        $extensie = '.' . ltrim($fisier->extensie, '.');

        $firma = ArhivaService::dosarFirma(
            optional($societate)->denumire ?: optional($declaratie)->den_firma,
            $mesaj->cif
        );

        $tip = ArhivaService::curata($mesaj->tip) ?: 'Diverse';

        // Data descarcarii, nu cea a mesajului: asa se vede cand a intrat
        // documentul in arhiva.
        $descarcat = $mesaj->descarcat_la ? Carbon::parse($mesaj->descarcat_la) : now();

        $nume = ArhivaService::curata(implode('_', array_filter([
            $tip,
            $mesaj->cif,
            $descarcat->format('Y-m-d'),
            $mesaj->mesaj_id,
        ]))) . $extensie;

        try {
            $cale = $this->arhiva->pune(
                $fisier->continut,
                $firma,
                config('anaf.arhiva.dosar_spv', 'SPV') . '/' . $tip,
                $nume,
                $mesaj->arhiva_cale
            );
        } catch (ArhivaException $e) {
            Jurnal::esec(
                'mesaj_arhivare',
                'Documentul ' . $mesaj->mesaj_id . ' nu a putut fi pus în arhiva locală: ' . $e->getMessage(),
                [],
                $mesaj->cif
            );

            return null;
        }

        $mesaj->update(['arhiva_cale' => $cale]);

        // Copia de langa declaratie se face o singura data: daca recipisa a fost
        // deja adusa acolo, nu se mai adauga inca un fisier.
        if ($declaratie && !$declaratie->arhiva_recipisa) {
            $this->punaLangaDeclaratie($declaratie, $fisier, $firma, $extensie);
        }

        if ($this->arhiva->stergeDePeServer() && $mesaj->cale_fisier) {
            Storage::delete($mesaj->cale_fisier);
            $mesaj->update(['cale_fisier' => null]);
        }

        return $cale;
    }

    /** Copia recipisei din dosarul declaratiei la care raspunde. */
    protected function punaLangaDeclaratie(
        AnafDeclaratie $declaratie,
        SpvFisier $fisier,
        string $firma,
        string $extensie
    ): void {
        try {
            $cale = $this->arhiva->pune(
                $fisier->continut,
                $firma,
                ArhivaService::curata($declaratie->tip) ?: 'Diverse',
                ArhivaService::numeDeclaratie($declaratie, 'recipisa', $extensie)
            );
        } catch (ArhivaException $e) {
            Jurnal::esec(
                'mesaj_arhivare',
                'Recipisa nu a putut fi pusă lângă declarația ' . $declaratie->tip
                    . ' pentru ' . $declaratie->cui . ': ' . $e->getMessage(),
                [],
                $declaratie->cui
            );

            return;
        }

        $declaratie->update(['arhiva_recipisa' => $cale]);
    }

    /**
     * Declaratia la care raspunde un mesaj de tip RECIPISA.
     *
     * ANAF pune indicele de incarcare in textul mesajului, acelasi indice pe
     * care l-a intors la depunere; dupa el se recunosc una pe alta.
     */
    protected function declaratiaMesajului(SpvMesaj $mesaj): ?AnafDeclaratie
    {
        if (strtoupper((string) $mesaj->tip) !== 'RECIPISA' || !$mesaj->detalii || !$mesaj->cif) {
            return null;
        }

        // Recipisa se aseaza langa declaratie indiferent cine a depus-o: altfel
        // documentul adus de un coleg n-ar mai gasi declaratia.
        return AnafDeclaratie::query()->totiUtilizatorii()
            ->where('cui', $mesaj->cif)
            ->whereNotNull('index_recipisa')
            ->get()
            ->first(function (AnafDeclaratie $declaratie) use ($mesaj) {
                return strpos($mesaj->detalii, $declaratie->index_recipisa) !== false;
            });
    }

    public function saveMessage(array $payload, ?string $cif = null): SpvMesaj
    {
        // Fara filtrarea pe certificatele utilizatorului: altfel un mesaj deja
        // inregistrat n-ar fi gasit si s-ar incerca scrierea lui a doua oara.
        $mesaj = SpvMesaj::query()->toateCertificatele()
            ->firstOrNew(['mesaj_id' => $payload['id'] ?? '']);

        // Instiintarile pleaca doar pentru mesajele abia intrate: lista ANAF le
        // intoarce si pe cele stiute, iar acelea au fost deja anuntate.
        $nou = !$mesaj->exists;

        $mesaj->fill([
            'mesaj_id' => $payload['id'] ?? '',
            'cif' => $cif ?? ($payload['cif'] ?? ''),
            'tip' => $payload['tip'] ?? 'SPV',
            'detalii' => $payload['detalii'] ?? null,
            'id_solicitare' => $payload['id_solicitare'] ?? null,
            'data_creare' => $this->parseazaData($payload['data_creare'] ?? null),
            // Certificatul cu care a fost obtinut mesajul
            'certificat_id' => $mesaj->certificat_id ?: optional($this->certificate)->idCurent(),
        ]);

        $mesaj->save();

        if ($nou && $this->alerte) {
            $this->alerte->pentruMesajNou($mesaj);
        }

        return $mesaj;
    }

    protected function parseazaData($valoare): ?string
    {
        if (empty($valoare)) {
            return null;
        }

        // ANAF trimite datele în format românesc (ex. "02.07.2026 12:30:34")
        foreach (['d.m.Y H:i:s', 'd.m.Y H:i', 'd.m.Y', 'Y-m-d H:i:s', 'Y-m-d'] as $format) {
            try {
                // "!" resetează câmpurile absente (ora) la zero în loc de ora curentă
                $data = Carbon::createFromFormat('!' . $format, trim($valoare));
            } catch (\Exception $e) {
                continue;
            }

            if ($data->format($format) === trim($valoare)) {
                return $data->format('Y-m-d H:i:s');
            }
        }

        return null;
    }

    public function saveFile(SpvFisier $fisier, ?string $subdir = null): array
    {
        $path = $this->buildPath($fisier->id, $fisier->extensie, $subdir);
        Storage::put($path, $fisier->continut);

        return ['path' => $path, 'hash' => $fisier->hash()];
    }

    protected function buildPath(string $id, string $extensie, ?string $subdir = null): string
    {
        $base = config('anaf.spv.storage_path', 'spv');
        $folder = $subdir ? $base . '/' . $subdir : $base;

        return rtrim($folder, '/') . '/' . $id . '.' . $extensie;
    }
}
