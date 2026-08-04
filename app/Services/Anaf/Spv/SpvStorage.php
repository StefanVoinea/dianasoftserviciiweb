<?php

namespace App\Services\Anaf\Spv;

use App\Models\AnafDeclaratie;
use App\Models\AnafSocietate;
use App\Models\SpvMesaj;
use App\Models\SpvSolicitare;
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

    /** Legatura cu programul local, pentru descarcarea de-a dreptul in arhiva. */
    protected $client;

    public function __construct(
        ?CertificatService $certificate = null,
        ?ArhivaService $arhiva = null,
        ?AlerteMesaje $alerte = null,
        ?SpvClient $client = null
    ) {
        $this->certificate = $certificate;
        $this->arhiva = $arhiva;
        $this->alerte = $alerte;
        $this->client = $client;
    }

    /**
     * Aduce documentul mesajului pe drumul cel mai scurt cu putinta.
     *
     * Intai se incearca drumul scurt: programul local il ia de la ANAF si il
     * scrie in arhiva lui, iar aici nu ajunge nimic. Instalarile mai vechi nu
     * stiu inca operatia asta, iar pentru ele se face ca inainte — documentul
     * trece prin server, dar se scrie pe disc doar daca arhiva clientului nu l-a
     * primit.
     *
     * @param bool   $vreaText  daca aplicatia are ceva de citit din document
     * @param string $subdosar  unde ramane pe server, cand n-are incotro
     *
     * @return array{cale: ?string, text: ?string, hash: ?string, pe_server: ?string}
     */
    public function aduce(SpvMesaj $mesaj, bool $vreaText = false, string $subdosar = 'downloads'): array
    {
        try {
            $rezultat = $this->aduceInArhiva($mesaj, $vreaText);

            /*
             * Programul local n-a stiut sa citeasca textul. Documentul se cere
             * inapoi din arhiva doar cat sa fie citit, in memorie; pe discul
             * serverului tot nu ajunge.
             */
            if ($vreaText && $rezultat['text'] === null) {
                $rezultat['text'] = $this->textDinArhiva($rezultat['cale']);
            }

            return $rezultat + ['pe_server' => null];
        } catch (ProgramLocalVechiException $e) {
            // Drumul dinainte, mai jos.
        }

        if (!$this->client) {
            throw new SpvException('Nu este configurată legătura cu programul local al clientului.');
        }

        $fisier = $this->client->descarcare($mesaj->mesaj_id);

        $mesaj->update([
            'descarcat_la' => now(),
            'hash_fisier' => $fisier->hash(),
            'ultima_eroare' => null,
        ]);

        $cale = $this->arhiveazaMesaj($mesaj, $fisier);
        $peServer = null;

        // Fara arhiva la client — sau cu ea neizbutita — documentul ramane aici,
        // ca pana acum: altfel s-ar pierde cu totul.
        if ($cale === null) {
            $salvat = $this->saveFile($fisier, $subdosar);
            $mesaj->update(['cale_fisier' => $salvat['path']]);
            $peServer = $salvat['path'];
        }

        return [
            'cale' => $cale,
            'text' => $vreaText ? $this->textul($fisier) : null,
            'hash' => $fisier->hash(),
            'pe_server' => $peServer,
        ];
    }

    /** Textul unui document adus in memorie; null daca nu se poate citi. */
    protected function textul(SpvFisier $fisier): ?string
    {
        if (ltrim($fisier->extensie, '.') !== 'pdf') {
            return null;
        }

        try {
            return TextPdf::dinContinut($fisier->continut);
        } catch (SpvException $e) {
            return null;
        }
    }

    /** Textul unui document care se afla deja in arhiva clientului. */
    protected function textDinArhiva(?string $cale): ?string
    {
        if (!$cale || !$this->arhiva || strtolower(pathinfo($cale, PATHINFO_EXTENSION)) !== 'pdf') {
            return null;
        }

        try {
            return TextPdf::dinContinut($this->arhiva->ia($cale));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Aduce documentul din SPV de-a dreptul in arhiva de pe calculatorul
     * clientului, fara sa treaca prin server.
     *
     * Programul local il ia de la ANAF si il scrie in dosarul firmei; inapoi vin
     * doar calea sub care l-a pus si, cand e nevoie, textul din el — atat cat sa
     * se stie ce scrie ANAF in recipisa sau in vectorul fiscal.
     *
     * @param bool $vreaText daca aplicatia are ceva de citit din document
     *
     * @return array{cale: string, text: ?string, hash: string}
     *
     * @throws ProgramLocalVechiException programul de la client nu stie inca
     * @throws SpvException               orice alta piedica
     */
    public function aduceInArhiva(SpvMesaj $mesaj, bool $vreaText = false): array
    {
        if (!$this->client) {
            throw new ProgramLocalVechiException('Nu este configurată legătura cu programul local.');
        }

        if (!$this->arhiva || !$this->arhiva->activa()) {
            throw new ProgramLocalVechiException('Arhiva de pe calculatorul clientului nu este pornită.');
        }

        $destinatie = $this->destinatiaMesajului($mesaj);
        $destinatie['text'] = $vreaText;

        $rezultat = $this->client->descarcaInArhiva($mesaj->mesaj_id, $destinatie);

        $mesaj->update([
            'arhiva_cale' => $rezultat['cale'],
            'hash_fisier' => $rezultat['hash'] ?: $mesaj->hash_fisier,
            'descarcat_la' => now(),
            'ultima_eroare' => null,
        ]);

        $this->copiazaLangaDeclaratie($mesaj, $rezultat['cale']);

        return [
            'cale' => $rezultat['cale'],
            'text' => $rezultat['text'] ?? null,
            'hash' => (string) $rezultat['hash'],
        ];
    }

    /**
     * Recipisa primeste o copie si langa declaratia la care raspunde.
     *
     * Copia se face intre doua dosare de pe calculatorul clientului: documentul
     * e deja acolo, deci nu are de ce sa mai faca drumul pana la server.
     */
    protected function copiazaLangaDeclaratie(SpvMesaj $mesaj, string $cale): void
    {
        $declaratie = $this->declaratiaMesajului($mesaj);

        if (!$declaratie || $declaratie->arhiva_recipisa) {
            return;
        }

        $extensie = '.' . (pathinfo($cale, PATHINFO_EXTENSION) ?: 'pdf');

        try {
            $copie = $this->arhiva->copiaza(
                $cale,
                $this->dosarulFirmei($mesaj, $declaratie),
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

        $declaratie->update(['arhiva_recipisa' => $copie]);
    }

    /**
     * Unde ii sta documentului acestui mesaj: dosarul firmei, subdosarul SPV pe
     * tipuri si numele lui, fara extensie — abia raspunsul ANAF spune daca e pdf
     * sau zip.
     *
     * @return array{firma: string, dosar: string, nume: string, inlocuieste: ?string}
     */
    protected function destinatiaMesajului(SpvMesaj $mesaj): array
    {
        $tip = $this->tipulDocumentului($mesaj);

        /*
         * Numele poarta doua date, in ordinea asta:
         *
         *   <Tip>_<CIF>_<data ANAF>_<data descarcarii>_<numarul mesajului>
         *
         * Intai cea de la ANAF, ziua in care documentul a fost intocmit: dupa
         * ea se cauta, si tot dupa ea se insira dosarul cand e sortat pe nume.
         * A doua spune cand a intrat documentul in arhiva — nu e acelasi lucru,
         * fiindca un document poate fi adus si dupa saptamani.
         *
         * Mesajele mai vechi, aduse inainte de a fi tinuta minte data de la
         * ANAF, poarta mai departe o singura data: a descarcarii.
         */
        $creat = $mesaj->data_creare ? Carbon::parse($mesaj->data_creare) : null;
        $descarcat = $mesaj->descarcat_la ? Carbon::parse($mesaj->descarcat_la) : now();

        return [
            'firma' => $this->dosarulFirmei($mesaj, $this->declaratiaMesajului($mesaj)),
            'dosar' => config('anaf.arhiva.dosar_spv', 'SPV') . '/' . $tip,
            'nume' => ArhivaService::curata(implode('_', array_filter([
                $tip,
                $mesaj->cif,
                $creat ? $creat->format('Y-m-d') : null,
                $descarcat->format('Y-m-d'),
                $mesaj->mesaj_id,
            ]))),
            'inlocuieste' => $mesaj->arhiva_cale,
        ];
    }

    /**
     * Felul documentului, asa cum se aseaza el in arhiva.
     *
     * Raspunsurile la solicitari poarta in SPV un tip care nu spune nimic despre
     * ce e inauntru — toate se cheama „RASPUNS SOLICITARE", fie ca e vector
     * fiscal, fisa rol sau bilant. In dosarul clientului ele stau dupa ce s-a
     * cerut, ca sa se vada din nume si din dosar ce document e.
     */
    protected function tipulDocumentului(SpvMesaj $mesaj): string
    {
        $cerut = optional($this->solicitareaMesajului($mesaj))->tip_document;

        return ArhivaService::curata($cerut ?: $mesaj->tip) ?: 'Diverse';
    }

    /**
     * Solicitarea la care raspunde mesajul.
     *
     * ANAF intoarce numarul solicitarii chiar pe mesaj, acelasi pe care l-a dat
     * la cerere; dupa el se recunosc una pe alta. Fara el — sau cand cererea a
     * fost facuta din alta parte — se ramane la tipul spus de SPV.
     */
    protected function solicitareaMesajului(SpvMesaj $mesaj): ?SpvSolicitare
    {
        if (!$mesaj->id_solicitare) {
            return null;
        }

        return SpvSolicitare::query()->totiUtilizatorii()
            ->where('id_solicitare', (string) $mesaj->id_solicitare)
            ->first();
    }

    /**
     * Dosarul firmei: denumirea din Entitati inrolate; daca CUI-ul nu e inrolat,
     * cea de pe declaratia la care raspunde documentul.
     *
     * Entitatea poate fi si o persoana, inrolata cu CNP-ul ei: si ea are spatiu
     * privat virtual, si ea cere documente de acolo.
     */
    protected function dosarulFirmei(SpvMesaj $mesaj, ?AnafDeclaratie $declaratie): string
    {
        $societate = $mesaj->cif ? AnafSocietate::where('cif', $mesaj->cif)->first() : null;

        return ArhivaService::dosarFirma(
            optional($societate)->denumire ?: optional($declaratie)->den_firma,
            $mesaj->cif
        );
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

        $declaratie = $this->declaratiaMesajului($mesaj);
        $extensie = '.' . ltrim($fisier->extensie, '.');
        $destinatie = $this->destinatiaMesajului($mesaj);

        try {
            $cale = $this->arhiva->pune(
                $fisier->continut,
                $destinatie['firma'],
                $destinatie['dosar'],
                $destinatie['nume'] . $extensie,
                $destinatie['inlocuieste']
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
            $this->punaLangaDeclaratie($declaratie, $fisier, $destinatie['firma'], $extensie);
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
