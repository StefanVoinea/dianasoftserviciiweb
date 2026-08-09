<?php

namespace App\Services\Anaf\Bridge;

use App\Services\Anaf\Spv\SpvException;

/**
 * Innoirea programului de la client, fara ca cineva sa se duca acolo.
 *
 * Programul local si agentul sunt cateva fisiere de text: PHP si PowerShell. Se
 * pot inlocui de la distanta, dar numai daca clientul are cum sa stie ca
 * pachetul vine chiar de la noi — altfel oricine i-ar putea trimite alt program.
 * De aceea pachetul e semnat cu cheia serverului, iar agentul il verifica cu
 * cheia publica pe care o are din kit.
 *
 * Ce NU se innoieste asa: PHP-ul din kit (fisiere in lucru, nu se pot inlocui
 * cat ruleaza), configurare.env (e a clientului) si cheia publica (ea e temelia
 * increderii — se schimba doar cu un kit nou, adus de om).
 */
class ActualizareBridge
{
    /**
     * Fisierele care se innoiesc de la distanta.
     *
     * Numai text: programul, uneltele lui si scripturile. Nimic din ce nu se
     * poate inlocui in siguranta cat timp programul ruleaza.
     */
    public const FISIERE = [
        'server.php',
        'curl-talcuri.php',
        'agent.php',
        'agent-functii.php',
        'agent-lucreaza.php',
        'agent-actualizare.php',
        'cert-info.ps1',
        'pin-test.ps1',
        'sign-pdf.ps1',
        'merge-pdf.ps1',
        'pdf-info.ps1',
        'imprimante.ps1',
        'print-pdf.ps1',
    ];

    /** Scripturile de langa kit, care stau in alt dosar. */
    public const FISIERE_KIT = [
        'diagnoza.bat',
        'diagnoza.ps1',
        'porneste-manual.bat',
        'porneste-agent.bat',
    ];

    protected $licente;
    protected $caleBridge;

    public function __construct(Licente $licente, ?string $caleBridge = null)
    {
        $this->licente = $licente;
        $this->caleBridge = $caleBridge ?: base_path('spv-bridge');
    }

    /**
     * Versiunea programului: amprenta cuprinsului fisierelor lui.
     *
     * Nu se tine minte niciun numar si nu-l creste nimeni de mana — versiunea se
     * schimba exact cand se schimba un fisier, si nici cu o clipa mai devreme.
     * Clientul isi tine versiunea in versiune.txt si o trimite la fiecare panda.
     */
    public function versiunea(): string
    {
        $amprente = [];

        foreach ($this->fisierele() as $nume => $cale) {
            $amprente[] = $nume . ':' . hash_file('sha256', $cale);
        }

        sort($amprente);

        return substr(hash('sha256', implode("\n", $amprente)), 0, 16);
    }

    /**
     * Pachetul de innoire: arhiva si semnatura ei.
     *
     * @return array{arhiva: string, versiune: string, semnatura: string}
     */
    public function pachetul(): array
    {
        $versiune = $this->versiunea();

        $fisiere = [];

        foreach ($this->fisierele() as $nume => $sursa) {
            $fisiere[$nume] = base64_encode((string) file_get_contents($sursa));
        }

        // Versiunea calatoreste in pachet: clientul o scrie langa program dupa
        // ce a pus fisierele, si de acolo o citeste data viitoare.
        $fisiere['versiune.txt'] = base64_encode($versiune);

        /*
         * Pachetul e un document JSON, nu o arhiva.
         *
         * PHP-ul din kit e mic dinadins — are mbstring si openssl, atat cat ii
         * trebuie programului — si nu are extensia zip. O arhiva ar fi cerut-o,
         * iar innoirea ar fi cazut chiar pe calculatoarele pentru care a fost
         * facuta, cu „Class ZipArchive not found". Textul se citeste oriunde,
         * fara nimic instalat, iar fisierele noastre sunt oricum text.
         */
        $cale = tempnam(sys_get_temp_dir(), 'act') . '.json';

        $scris = file_put_contents($cale, json_encode([
            'versiune' => $versiune,
            'fisiere' => $fisiere,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        if ($scris === false) {
            throw new SpvException('Pachetul de actualizare nu a putut fi scris.');
        }

        /*
         * Se semneaza amprenta pachetului, nu pachetul intreg: semnatura ramane
         * scurta, incape intr-un antet, iar clientul verifica intai ca fisierul
         * primit e chiar cel semnat.
         */
        $amprenta = hash_file('sha256', $cale);

        return [
            'arhiva' => $cale,
            'versiune' => $versiune,
            'amprenta' => $amprenta,
            'semnatura' => $this->licente->semneazaPentruBridge($versiune . ':' . $amprenta),
        ];
    }

    /**
     * Fisierele care intra in pachet, cu numele sub care ajung la client.
     *
     * @return array<string, string> nume in arhiva => cale pe server
     */
    public function fisierele(): array
    {
        $gasite = [];

        foreach (self::FISIERE as $nume) {
            $cale = $this->caleBridge . DIRECTORY_SEPARATOR . $nume;

            if (is_file($cale)) {
                $gasite[$nume] = $cale;
            }
        }

        foreach (self::FISIERE_KIT as $nume) {
            $cale = $this->caleBridge . DIRECTORY_SEPARATOR . 'kit' . DIRECTORY_SEPARATOR . $nume;

            if (is_file($cale)) {
                $gasite[$nume] = $cale;
            }
        }

        return $gasite;
    }
}
