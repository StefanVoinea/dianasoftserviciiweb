<?php

namespace App\Services\Anaf\Spv;

use App\Services\Anaf\Spv\Contracts\SpvTransport;
use App\Support\Aplicatia;
use App\Support\ContextUtilizator;
use Illuminate\Http\Client\Response;

class SpvClient
{
    protected $transport;
    protected $config;
    protected $certificate;

    /**
     * Cand a plecat spre ANAF apelul dinainte, pe fiecare certificat (microtime).
     *
     * @var array<string, float>
     */
    protected $ultimulApel = [];

    public function __construct(SpvTransport $transport, array $config, ?CertificatService $certificate = null)
    {
        $this->transport = $transport;
        $this->config = $config;
        $this->certificate = $certificate;
    }

    /**
     * Certificatul pe care se tine socoteala pauzei.
     *
     * ANAF numara apelurile pe certificatul care le face, deci si pauza e a
     * lui. Cand nu se stie cu care se lucreaza — configuratie fara evidenta de
     * certificate —, toate apelurile impart aceeasi socoteala, ca pana acum.
     */
    protected function cheiaRandului(): string
    {
        if (!($this->config['throttle_pe_certificat'] ?? true)) {
            return 'toate';
        }

        $id = $this->certificate ? $this->certificate->idCurent() : null;

        return $id === null ? 'toate' : (string) $id;
    }

    /**
     * Pauza ceruta de ANAF intre doua apeluri, tinuta pe fiecare certificat.
     *
     * Se socoteste de la plecarea apelului dinainte, nu de la intoarcerea lui.
     * Asa ANAF primeste tot cel mult un apel la ragazul cerut, dar noi nu mai
     * asteptam de doua ori: un lot de mesaje aduse prin tunel are fiecare apel
     * de cateva secunde, iar pauza intreaga se adauga peste ele degeaba. La o
     * suta de mesaje, asta insemna doua minute de asteptare fara rost.
     *
     * Socoteala e pe certificat fiindca si a ANAF-ului e tot asa: doua tokene
     * ale aceluiasi client n-au nicio treaba unul cu altul. Tinuta pe toate
     * laolalta, ea incetinea de doua ori un client cu doua tokene, degeaba.
     */
    protected function asteaptaRandul(): void
    {
        $ragaz = (int) $this->config['throttle_ms'] * 1000;

        if ($ragaz <= 0) {
            return;
        }

        $cheia = $this->cheiaRandului();
        $atunci = $this->ultimulApel[$cheia] ?? 0.0;
        $trecut = (int) ((microtime(true) - $atunci) * 1000000);

        if ($atunci > 0 && $trecut < $ragaz) {
            usleep($ragaz - $trecut);
        }

        $this->ultimulApel[$cheia] = microtime(true);
    }

    public function listaMesaje(int $zile = 60, ?string $cif = null): array
    {
        $query = ['zile' => min($zile, $this->config['zile_max'])];

        if ($cif !== null) {
            $query['cif'] = $cif;
        }

        return $this->json('/listaMesaje', $query);
    }

    /**
     * Raspunsul complet la listaMesaje, fara a trata "nu exista mesaje" ca eroare.
     * Contine si campurile cnp, cui (CIF-urile accesibile certificatului) si serial,
     * utile chiar cand nu exista niciun mesaj in intervalul cerut.
     */
    public function listaMesajeBrut(int $zile = 60): array
    {
        $response = $this->call('/listaMesaje', ['zile' => min($zile, $this->config['zile_max'])]);

        $payload = json_decode($response->body(), true);

        if (!is_array($payload)) {
            throw new SpvException(
                'Răspuns non-JSON de la SPV: ' . mb_substr($response->body(), 0, 300)
            );
        }

        return $payload;
    }

    /**
     * Solicita un document din SPV (vector fiscal, fisa rol, situatie sintetica etc.).
     * Parametrii suplimentari acceptati de ANAF, in functie de tip:
     * an, luna, motiv (Adeverinte Venit), numar_inregistrare (Duplicat Recipisa),
     * cui_pui (Fisa Rol pentru un punct de lucru).
     */
    public function cerere(string $tip, string $cui, array $optiuni = []): array
    {
        $query = ['tip' => $tip, 'cui' => $cui];

        foreach (['an', 'luna', 'motiv', 'numar_inregistrare', 'cui_pui'] as $parametru) {
            if (isset($optiuni[$parametru]) && $optiuni[$parametru] !== '' && $optiuni[$parametru] !== null) {
                $query[$parametru] = $optiuni[$parametru];
            }
        }

        return $this->json('/cerere', $query);
    }

    public function descarcare(string $id): SpvFisier
    {
        $response = $this->call('/descarcare', ['id' => $id]);

        $contentType = strtolower($response->header('Content-Type') ?? '');

        if (str_contains($contentType, 'json') || $this->looksLikeJson($response->body())) {
            $payload = json_decode($response->body(), true) ?: [];
            throw new SpvException($payload['eroare'] ?? 'Descărcare eșuată pentru mesajul ' . $id);
        }

        $extensie = str_contains($contentType, 'zip') ? 'zip' : 'pdf';

        return new SpvFisier($id, $response->body(), $extensie, $contentType);
    }

    /**
     * Descarca documentul de-a dreptul in arhiva de pe calculatorul clientului.
     *
     * Cererea o duce programul local pana la ANAF si tot el scrie documentul in
     * dosarul firmei; aici se intoarce doar calea sub care l-a pus.
     *
     * @param array $destinatie firma, dosar, nume (fara extensie), inlocuieste, text
     *
     * @return array{cale: string, extensie: string, marime: int, hash: string, text: ?string}
     */
    public function descarcaInArhiva(string $id, array $destinatie): array
    {
        // Pauza ceruta de ANAF intre apeluri se tine si aici: apelul lui e tot
        // unul catre ei, doar ca facut de la celalalt capat.
        $this->asteaptaRandul();

        return $this->transport->descarcaInArhiva($id, $destinatie);
    }

    /**
     * Aceleasi documente, cerute programului local toate deodata.
     *
     * Pauza dintre apeluri o tine acum el, unde e si apelul; aici se tine minte
     * doar ca s-a lucrat, ca urmatorul apel obisnuit sa nu plece prea devreme.
     *
     * @param  array<int, array>  $documente
     * @return array<string, array>
     */
    public function descarcaLotInArhiva(array $documente): array
    {
        $this->asteaptaRandul();

        $iesite = $this->transport->descarcaLotInArhiva($documente, (int) $this->config['throttle_ms']);

        // Ultimul apel al lotului a plecat de la programul local, nu de aici:
        // socoteala se pune la zi ca sa nu se calce peste pauza lui.
        $this->ultimulApel[$this->cheiaRandului()] = microtime(true);

        return $iesite;
    }

    /**
     * Ce i se spune omului cand tokenul isi asteapta PIN-ul.
     *
     * Se numeste tokenul, fiindca un contabil are des doua si trebuie sa stie
     * la care sa se duca; si se spune unde e fereastra — pe calculatorul
     * clientului, nu aici.
     */
    protected function vorbaPinului(array $payload): string
    {
        $tokenul = $this->certificate ? optional($this->certificate->activ())->cn : null;

        $vorba = $tokenul
            ? 'Tokenul „' . $tokenul . '" își așteaptă PIN-ul'
            : 'Tokenul își așteaptă PIN-ul';

        $vorba .= ' pe calculatorul clientului';

        $fereastra = trim((string) ($payload['pin_fereastra'] ?? ''));

        if ($fereastra !== '') {
            $vorba .= ' („' . $fereastra . '")';
        }

        return $vorba . '. Scrieți-l acolo, apoi încercați din nou —'
            . ' codul nu se poate trimite de aici.';
    }

    private function json(string $path, array $query): array
    {
        $response = $this->call($path, $query);

        $payload = json_decode($response->body(), true);

        if (!is_array($payload)) {
            throw new SpvException(
                'Răspuns non-JSON de la SPV (' . $path . '): ' . mb_substr($response->body(), 0, 300)
            );
        }

        if (isset($payload['eroare'])) {
            throw new SpvException($payload['eroare']);
        }

        return $payload;
    }

    /**
     * A scris cineva codul tokenului cat a tinut apelul acesta?
     *
     * Se cunoaste dupa clipa insemnata la scriere: daca ea cade in rastimpul
     * apelului, atunci fereastra s-a deschis in mijlocul lui si abia apoi a fost
     * dezlegata. Alta pricina n-are cum sa nimereasca tocmai acolo.
     */
    private function codulTocmaiSAScris($deCand): bool
    {
        $tokenul = $this->certificate ? $this->certificate->activ() : null;

        // Proaspat din baza de date: scrierea s-a facut in alta cerere decat asta.
        $tokenul = $tokenul ? $tokenul->fresh() : null;

        return $tokenul
            && $tokenul->pin_stare === 'gata'
            && $tokenul->pin_verificat_la
            && $tokenul->pin_verificat_la->gte($deCand);
    }

    private function call(string $path, array $query, bool $reluat = false): Response
    {
        $this->asteaptaRandul();

        /*
         * Taiata la secunda: baza de date tine clipa scrierii fara fractiuni,
         * iar clipa asta le are. Fara taiere, un cod scris in chiar secunda in
         * care a pornit apelul parea scris inaintea lui, si reluarea nu se mai
         * facea — tocmai in cazul cel mai des, cand apelul pica repede.
         */
        $inceputul = now()->startOfSecond();

        $response = $this->transport->get($path, $query);

        if ($response->failed()) {
            /*
             * Pe drumul până la ANAF sunt acum mai multe verigi: puntea,
             * agentul, programul local. Fiecare știe să spună ce a pățit, iar
             * vorba lui e mai de folos decât un cod de stare — altfel omul își
             * caută zadarnic vinovăția în certificat.
             */
            $payload = json_decode($response->body(), true);

            /*
             * Tokenul isi asteapta PIN-ul, iar omul n-a apucat sa-l scrie.
             *
             * Se spune deosebit fiindca se dezleaga cu totul altfel decat o
             * pana de retea: nu are ce mai incerca serverul, ci trebuie ca
             * cineva sa se duca la calculatorul acela — sau sa intre pe el de
             * la distanta — si sa scrie codul. Spus ca „apelul a esuat", omul
             * cauta zadarnic vina in legatura.
             */
            if (!empty($payload['pin_asteapta'])) {
                /*
                 * Se insemneaza pe token ca isi asteapta codul, si de unde a
                 * plecat lucrarea: cine a apasat butonul pe telefon trebuie
                 * intrebat pe telefon, nu intr-o fila din browser pe care poate
                 * n-o are nimeni in fata.
                 *
                 * Lucrarile pornite de la sine — dosarul urmarit, sarcina de
                 * noapte — n-au pe nimeni in spate: acelea se arata oriunde,
                 * fiindca oricine e prin preajma le poate dezlega.
                 */
                $tokenul = $this->certificate ? $this->certificate->activ() : null;

                if ($tokenul) {
                    $tokenul->update([
                        'pin_stare' => 'asteapta',
                        'pin_motiv' => trim((string) ($payload['pin_fereastra'] ?? '')),
                        'pin_verificat_la' => now(),
                        'pin_cerut_de' => optional(ContextUtilizator::curent())->id,
                        'pin_cerut_din' => Aplicatia::curenta(),
                    ]);
                }

                throw new PinAsteaptaException(
                    $this->vorbaPinului($payload),
                    (string) ($payload['pin_fereastra'] ?? ''),
                    (string) ($payload['pin_proces'] ?? ''),
                    $this->certificate ? $this->certificate->activ() : null
                );
            }

            /*
             * Apelul a picat, dar intre timp cineva a scris codul tokenului.
             *
             * Asa se intampla la cea dintai lucrare de dupa pornirea
             * calculatorului: cererea pleaca, driverul cere codul in mijlocul
             * stangerii de mana cu ANAF, iar cat omul il scrie sesiunea
             * securizata se stinge (SEC_E_CONTEXT_EXPIRED). Apelul pica, desi
             * tokenul e acum dezlegat si a doua incercare merge.
             *
             * Se reia o singura data, si numai cand codul a fost scris chiar in
             * rastimpul apelului: altfel n-am face decat sa batem de doua ori la
             * o usa inchisa.
             */
            if (!$reluat && $this->codulTocmaiSAScris($inceputul)) {
                return $this->call($path, $query, true);
            }

            if (!empty($payload['eroare'])) {
                throw new SpvException(trim($payload['eroare'] . ' ' . ($payload['detalii'] ?? '')));
            }

            if ($response->status() === 401 || $response->status() === 403) {
                throw new SpvException('Autentificare SPV respinsă (certificat expirat sau fără drepturi).');
            }

            throw new SpvException('SPV HTTP ' . $response->status() . ' pe ' . $path);
        }

        return $response;
    }

    private function looksLikeJson(string $body): bool
    {
        return str_starts_with(ltrim($body), '{');
    }
}
