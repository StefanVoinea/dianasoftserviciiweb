<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Anaf\Jurnal;
use App\Services\Mobil\ProgrameleDeTelefon;
use App\Support\ContextUtilizator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/**
 * Programele de telefon: ce versiune e pusa pe server si de unde se ia.
 *
 * Aplicatiile de Android nu trec prin niciun magazin, deci innoirea lor cade in
 * sarcina noastra: telefonul intreaba aici daca a aparut ceva mai nou decat ce
 * are el, iar cand e, primeste si o legatura pe care o poate deschide.
 *
 * Legatura e semnata si tine putin. Arhiva insasi nu poarta niciun secret — e
 * chiar programul —, dar nici nu are de ce sa stea la vederea internetului
 * intreg: cine o ia, o ia pentru ca i-a cerut-o aplicatia lui.
 */
class ProgrameMobilController extends Controller
{
    /** Cat tine legatura de descarcare. Destul cat sa apuce sa se descarce. */
    protected const LEGATURA_MINUTE = 30;

    protected $programele;

    public function __construct(ProgrameleDeTelefon $programele)
    {
        $this->programele = $programele;
    }

    /**
     * Ce versiune e pe server pentru o aplicatie.
     *
     * Intreaba si telefonul (ca sa stie daca are de ce se innoi), si fila din
     * browser (ca sa arate ce anume da mai departe clientului).
     */
    public function versiunea(string $aplicatia)
    {
        $noua = $this->programele->ceaMaiNoua($aplicatia);

        if ($noua === null) {
            return response()->json([
                'success' => true,
                'exista' => false,
                'poate_incarca' => $this->poatePublica(),
                'mesaj' => 'Pentru „' . ($this->numele($aplicatia) ?: $aplicatia)
                    . '" nu este încărcată încă nicio versiune pe server.',
            ]);
        }

        return response()->json([
            'success' => true,
            'exista' => true,
            'aplicatia' => $aplicatia,
            'nume' => $this->numele($aplicatia),
            'versiune' => $noua['versiune'],
            'cod' => $noua['cod'],
            'marime' => $noua['marime'],
            'pusa_la' => date('d.m.Y H:i', $noua['pusa_la']),
            // Dupa asta isi arata fila si unealta de pus o versiune noua
            'poate_incarca' => $this->poatePublica(),
            'url' => URL::temporarySignedRoute(
                'mobil.descarca',
                now()->addMinutes(self::LEGATURA_MINUTE),
                ['aplicatia' => $aplicatia, 'cod' => $noua['cod']]
            ),
        ]);
    }

    /**
     * Arhiva insasi.
     *
     * Ruta e semnata, nu pazita de jetonul obisnuit: ea se deschide in browserul
     * telefonului, care nu stie nimic despre jetonul aplicatiei. Semnatura tine
     * loc de legitimatie, si tine putin.
     */
    public function descarca(Request $request, string $aplicatia)
    {
        $noua = $this->programele->ceaMaiNoua($aplicatia);

        if ($noua === null) {
            return response()->json(['success' => false, 'message' => 'Nu există nicio versiune.'], 404);
        }

        /*
         * Codul cerut se cantareste fata de ce e acum pe server. Daca intre
         * timp s-a pus una mai noua, se da tot cea noua: legatura veche nu are
         * de ce sa aduca inapoi un program depasit.
         */
        return response()->download(
            $noua['cale'],
            $this->programele->numeDeDescarcare($aplicatia, $noua['versiune']),
            ['Content-Type' => 'application/vnd.android.package-archive']
        );
    }

    /**
     * Aceeasi arhiva, dar pentru fila din browser: cu jetonul obisnuit.
     *
     * Aici omul apasa un buton intr-o pagina in care e deja legitimat, deci nu
     * are rost sa mai treaca printr-o legatura semnata.
     */
    public function descarcaDinAplicatie(string $aplicatia)
    {
        $noua = $this->programele->ceaMaiNoua($aplicatia);

        if ($noua === null) {
            return response()->json([
                'success' => false,
                'message' => 'Nu este încărcată nicio versiune a acestei aplicații.',
            ], 404);
        }

        Jurnal::scrie(
            'mobil_descarcare',
            'A descărcat aplicația de telefon „' . $this->numele($aplicatia) . '" ' . $noua['versiune'],
            ['aplicatia' => $aplicatia, 'versiune' => $noua['versiune']]
        );

        return response()->download(
            $noua['cale'],
            $this->programele->numeDeDescarcare($aplicatia, $noua['versiune']),
            ['Content-Type' => 'application/vnd.android.package-archive']
        );
    }

    /**
     * Pune pe server o versiune noua, venita din fila.
     *
     * Fara asta, arhiva ajungea la clienti numai daca se urca cineva pe server
     * si o copia de mana: prin git nu poate merge — e mare si se schimba la
     * fiecare compilare —, iar la desfasurare nu se atinge nimeni de dosarul
     * acesta. Asa, cine face aplicatia o si da mai departe, din aceeasi pagina
     * din care clientii o iau.
     *
     * Numele arhivei nu se alege aici: el vine gata facut de la compilare si
     * poarta versiunea. Un nume scris de mana s-ar greși tocmai unde doare —
     * un cod mai mic decat cel de acum, si telefoanele nu s-ar mai innoi
     * niciodata, fara sa spuna nimeni de ce.
     */
    public function incarca(Request $request, string $aplicatia)
    {
        if (!$this->poatePublica()) {
            return response()->json([
                'success' => false,
                'message' => 'Nu aveți dreptul de a pune pe server o versiune a aplicației de telefon.',
            ], 403);
        }

        if (!isset(ProgrameleDeTelefon::APLICATII[$aplicatia])) {
            return response()->json(['success' => false, 'message' => 'Aplicație necunoscută.'], 404);
        }

        $request->validate([
            // 200 MB: o arhiva de telefon nu trece de 40, dar nici nu vrem sa
            // taiem una legata doar fiindca a crescut.
            'arhiva' => 'required|file|max:204800',
        ]);

        $fisier = $request->file('arhiva');
        $numele = $fisier->getClientOriginalName();

        $desfacut = $this->programele->desfaNumele($numele);

        if ($desfacut === null || $desfacut['aplicatia'] !== $aplicatia) {
            return response()->json([
                'success' => false,
                'message' => 'Numele arhivei trebuie să fie „' . $aplicatia . '-1.2.3+4.apk", '
                    . 'cu versiunea din pubspec.yaml. Folosiți „publica.ps1", care îl scrie singur.',
            ], 422);
        }

        $acum = $this->programele->ceaMaiNoua($aplicatia);

        if ($acum !== null && $desfacut['cod'] <= $acum['cod']) {
            return response()->json([
                'success' => false,
                'message' => 'Pe server este deja versiunea ' . $acum['versiune'] . ' (cod ' . $acum['cod']
                    . '). O arhivă cu cod mai mic sau la fel nu ar ajunge niciodată pe telefoane;'
                    . ' creșteți codul în pubspec.yaml.',
            ], 422);
        }

        $fisier->storeAs(ProgrameleDeTelefon::DOSAR, $numele);

        Jurnal::scrie(
            'mobil_incarcare',
            'A pus pe server aplicația de telefon „' . $this->numele($aplicatia) . '" '
                . $desfacut['versiune'] . ' (cod ' . $desfacut['cod'] . ')',
            ['aplicatia' => $aplicatia, 'fisier' => $numele]
        );

        return response()->json([
            'success' => true,
            'message' => 'Versiunea ' . $desfacut['versiune'] . ' a fost pusă pe server.'
                . ' Telefoanele o vor găsi la următoarea pornire.',
        ]);
    }

    /**
     * Are omul de acum dreptul de a publica o aplicatie de telefon?
     *
     * Nu e un drept de administrator de firma: arhiva pusa pe server ajunge
     * singura pe telefoanele tuturor clientilor, deci el priveste pe toti
     * deodata, nu firma celui care apasa. Se tine deci intr-o lista de adrese
     * din configurare, goala din start — adica nimeni, pana cand se scrie
     * cineva acolo anume.
     */
    protected function poatePublica(): bool
    {
        $omul = ContextUtilizator::curent();

        if (!$omul || !$omul->email) {
            return false;
        }

        $ingaduiti = array_filter(array_map(
            function ($adresa) {
                return mb_strtolower(trim($adresa));
            },
            explode(',', (string) config('mobil.publica'))
        ));

        return in_array(mb_strtolower($omul->email), $ingaduiti, true);
    }

    protected function numele(string $aplicatia): string
    {
        return ProgrameleDeTelefon::APLICATII[$aplicatia] ?? '';
    }
}
