<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * [2026-09-04] Accesul MCP: da unui asistent (Claude, legat prin „Connectors")
 * voie sa citeasca date din tot programul, si sa adauge sau sa modifice
 * nomenclatoare — niciodata sa stearga.
 *
 * Adus din DianaSoft ALPROF. Deosebirea de fond: acolo era un ERP al unei singure
 * fabrici, aici e un serviciu cu multi clienti. De aceea citirea sta in perimetrul
 * clientului: tabelele cu `company_id` se filtreaza pe firma curenta, iar cele
 * fara `company_id` se impart in nomenclatoare comune (le citeste oricine) si
 * restul (doar administratorul serviciului). Vezi `config/mcp.php`.
 *
 * Cele doua drepturi sunt asimetrice si asa trebuie sa ramana:
 *
 *   CITIRE  — pe toata baza, in perimetrul de mai sus. `structura` si `cauta` merg
 *             pe orice tabela, in afara celor care tin material de autentificare,
 *             si intorc orice coloana, in afara celor cu nume de secret. Ca sa fie
 *             de folos pe tabele de sute de mii de randuri, `cauta` stie si sa
 *             grupeze si sa insumeze.
 *
 *   SCRIERE — doar pe lista din `config/mcp.php`, coloana cu coloana. Faptul ca o
 *             tabela se poate citi nu inseamna deloc ca se poate scrie in ea.
 *
 * Ce nu se poate face de aici, prin constructie:
 *   - STERGERE. Nu exista nicio metoda de stergere in acest controller si nicio
 *     cale prin care sa se ajunga la `delete()`. Nici anularea unei operatii nu
 *     sterge: readuce valorile dinainte pe randurile modificate, iar randurile
 *     adaugate raman (se pot dezactiva, daca tabela are `activ`).
 *   - MODIFICARE in afara listei din `config/mcp.php`: declaratiile, mesajele SPV,
 *     certificatele, utilizatorii se pot citi, dar nu se pot scrie de aici.
 *   - SQL. Nu se trimite text de interogare; se trimit conditii structurate, iar
 *     fiecare nume de tabela si de coloana e verificat fata de schema reala
 *     inainte sa ajunga in interogare.
 *
 * Modificarea in masa are doua faze, ca sa nu se intample nimic din greseala:
 *   1. `modifica` fara token  -> nu scrie nimic; spune cate randuri ar fi atinse,
 *      arata exemple si intoarce un token.
 *   2. `modifica` cu token    -> aplica exact ce s-a aratat la pasul 1.
 * Peste `prag_confirmare` randuri, pasul 1 e obligatoriu.
 *
 * `mcp_operatii` tine fiecare operatie cu valorile dinainte, rand cu rand, ca sa
 * poata fi vazuta si anulata ca un tot. Modelele care au trait-ul RecordsActivity
 * ajung in plus si in `activities`.
 */
class McpController extends Controller
{
    /** Ce se poate scrie, si de unde incepe cautarea. */
    public function tabele()
    {
        $rezultat = [];
        foreach (config("mcp.tabele") as $nume => $cfg) {
            $rezultat[] = [
                "tabela"    => $nume,
                "eticheta"  => $cfg["eticheta"],
                "cheie"     => $cfg["cheie"],
                "cautabile" => $cfg["cautabile"],
                "editabile" => $cfg["editabile"],
            ] + (!empty($cfg["doar_administrator"])
                ? ["doar_administrator" => true, "disponibila_acum" => $this->esteAdministrator()]
                : []);
        }
        return response()->json([
            "de_modificat"     => $rezultat,
            "de_citit"         => count($this->tabeleCitibilePentruMine()) . " tabele — practic toata baza, "
                . "in perimetrul firmei curente. "
                . "Cheama `structura` fara argumente pentru lista, sau cu `tabela` pentru coloanele ei.",
            "firma_curenta"    => (int) session("company_id"),
            "administrator"    => $this->esteAdministrator(),
            "atentie"          => "Citirea si scrierea nu merg mana in mana: se pot citi si tabele care nu apar "
                . "in `de_modificat`, dar in ele NU se poate scrie.",
            "prag_confirmare"  => config("mcp.prag_confirmare"),
            "maxim_randuri"    => config("mcp.maxim_randuri"),
            "stergere"         => "nu este posibila prin acest acces",
        ]);
    }

    /**
     * Fara `tabela`: lista tabelelor citibile. Cu `tabela`: coloanele ei.
     * Doar citire, in ambele cazuri.
     */
    public function structura(Request $request)
    {
        try {
            $editabile = $this->tabeleEditabile();

            if (!$request->tabela) {
                $contine = trim((string) $request->contine);
                $randuri = $this->randuriAproximative();
                $lista = [];
                foreach ($this->tabeleCitibilePentruMine() as $t) {
                    if ($contine !== "" && stripos($t, $contine) === false) {
                        continue;
                    }
                    $linie = ["tabela" => $t, "randuri_aprox" => (int) ($randuri[$t] ?? 0)];
                    if (!$this->areCompanyId($t)) {
                        $linie["comuna"] = true;   // fara company_id: nomenclator comun sau tabela de administrare
                    }
                    if (isset($editabile[$t])) {
                        $linie["se_poate_si_modifica_prin"] = $editabile[$t];
                    }
                    $lista[] = $linie;
                }
                return response()->json([
                    "tabele"    => $lista,
                    "returnate" => count($lista),
                    "nota"      => "`randuri_aprox` e estimarea MySQL, nu o numaratoare exacta. "
                        . "Pentru coloanele unei tabele, cheama `structura` cu `tabela`.",
                ]);
            }

            $tabela  = $this->tabelaCitibila($request->tabela);
            $coloane = $this->coloanePermise($tabela);

            $rezultat = [
                "tabela"  => $tabela,
                "coloane" => array_values(array_map(function ($c) {
                    return [
                        "nume"  => $c->Field,
                        "tip"   => $c->Type,
                        "null"  => $c->Null === "YES",
                        "cheie" => $c->Key ?: null,
                    ];
                }, $coloane)),
                "randuri" => $this->interogareCitire($tabela, $coloane, null, false)->count(),
            ];
            if (isset($coloane["company_id"])) {
                $rezultat["nota_company"] = "Are `company_id`; cautarea si numaratoarea de mai sus se limiteaza "
                    . "automat la firma curenta (" . (int) session("company_id") . ")."
                    . ($this->esteAdministrator()
                        ? " Ca administrator, poti cere `toate_firmele: true` la `cauta`."
                        : "");
            }
            if (isset($editabile[$tabela])) {
                $cfg = config("mcp.tabele." . $editabile[$tabela]);
                $rezultat["se_poate_modifica"] = [
                    "ca_tabela" => $editabile[$tabela],
                    "coloane"   => $cfg["editabile"],
                ];
            } else {
                $rezultat["se_poate_modifica"] = false;
            }

            $ascunse = $this->coloaneAscunse($tabela);
            if ($ascunse) {
                $rezultat["coloane_ascunse"] = $ascunse;
            }

            return response()->json($rezultat);
        } catch (\Exception $e) {
            return response()->json(["eroare" => $e->getMessage()], 422);
        }
    }

    /**
     * Cauta randuri in orice tabela citibila. Doar citire.
     *
     * Cu `grupare` si/sau `agregate` intoarce sinteze in loc de randuri — altfel
     * pe tabelele mari (consumuri, istoric de comenzi) o limita de 500 de randuri
     * nu raspunde la nicio intrebare reala.
     */
    public function cauta(Request $request)
    {
        try {
            $tabela  = $this->tabelaCitibila($request->tabela);
            $coloane = $this->coloanePermise($tabela);
            $limita  = min(
                max((int) $request->limita ?: (int) config("mcp.citire.limita_implicita"), 1),
                (int) config("mcp.citire.limita_maxima")
            );

            $q = $this->interogareCitire($tabela, $coloane, $request->filtru, $request->boolean("toate_firmele"));

            $grupare  = array_values(array_filter((array) $request->grupare));
            $agregate = array_values(array_filter((array) $request->agregate));

            if ($grupare || $agregate) {
                return response()->json(
                    $this->sinteza($tabela, $coloane, $q, $grupare, $agregate, $request->ordonare, $limita)
                );
            }

            $cerute = array_values(array_filter((array) $request->coloane));
            $selectate = [];
            foreach (($cerute ?: array_keys($coloane)) as $c) {
                $selectate[] = $this->coloanaValida($tabela, $c, $coloane);
            }

            $total = (clone $q)->count();
            $this->aplicaOrdonarea($q, $tabela, $coloane, $request->ordonare, []);
            $randuri = $q->limit($limita)->get($selectate);

            return response()->json([
                "tabela"    => $tabela,
                "total"     => $total,
                "returnate" => $randuri->count(),
                "randuri"   => $randuri,
            ] + ($total > $randuri->count()
                ? ["nota" => "S-au intors primele " . $randuri->count() . " din " . $total
                    . ". Restrangeti filtrul, cereti `limita` mai mare (maxim "
                    . config("mcp.citire.limita_maxima") . ") sau folositi `grupare`/`agregate`."]
                : []));
        } catch (\Exception $e) {
            return response()->json(["eroare" => $e->getMessage()], 422);
        }
    }

    /**
     * Modificare in masa. Fara `token` = simulare; cu `token` = aplicare.
     */
    public function modifica(Request $request)
    {
        try {
            $cfg = $this->config($request->tabela);

            if ($request->token) {
                return $this->aplica($request->token);
            }

            $valori = $this->valoriPermise($cfg, $request->valori, "editabile");
            $q = $this->interogare($cfg, $request->filtru);
            $total = (clone $q)->count();

            if ($total === 0) {
                return response()->json(["eroare" => "Filtrul nu se potriveste cu niciun rand."], 422);
            }
            if ($total > config("mcp.maxim_randuri")) {
                return response()->json(["eroare" => "Operatia ar atinge " . $total . " randuri, peste limita de "
                    . config("mcp.maxim_randuri") . ". Restrangeti filtrul."], 422);
            }

            // se retin valorile dinainte, ca sa se poata anula
            $randuri = [];
            $exemple = [];
            foreach ($q->cursor() as $model) {
                $inainte = [];
                foreach (array_keys($valori) as $camp) {
                    $inainte[$camp] = $model->{$camp};
                }
                $randuri[] = ["id" => $model->getKey(), "inainte" => $inainte];
                if (count($exemple) < 10) {
                    $exemple[] = [
                        "cheie"   => $model->{$cfg["cheie"]},
                        "inainte" => $inainte,
                        "dupa"    => $valori,
                    ];
                }
            }

            $operatie = DB::table("mcp_operatii")->insertGetId([
                "company_id" => session("company_id"),
                "user_id"    => Auth::id(),
                "token"      => (string) Str::uuid(),
                "operatie"   => "modifica",
                "tabela"     => $request->tabela,
                "descriere"  => $request->descriere,
                "filtru"     => json_encode($request->filtru),
                "valori"     => json_encode($valori),
                "nr_randuri" => count($randuri),
                "randuri"    => json_encode($randuri),
                "stare"      => "pregatita",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
            $token = DB::table("mcp_operatii")->where("id", $operatie)->value("token");

            return response()->json([
                "simulare"       => true,
                "tabela"         => $request->tabela,
                "randuri_atinse" => count($randuri),
                "valori"         => $valori,
                "exemple"        => $exemple,
                "token"          => $token,
                "cum_se_aplica"  => "Trimiteti aceeasi cerere cu `token`. Nimic nu s-a schimbat inca.",
            ]);
        } catch (\Exception $e) {
            return response()->json(["eroare" => $e->getMessage()], 422);
        }
    }

    /** Adauga un rand nou intr-o tabela permisa. */
    public function adauga(Request $request)
    {
        DB::beginTransaction();
        try {
            $cfg = $this->config($request->tabela);
            $valori = $this->valoriPermise($cfg, $request->valori, "editabile", true);

            $cheie = $cfg["cheie"];
            if (empty($valori[$cheie]) && $request->valori && isset($request->valori[$cheie])) {
                $valori[$cheie] = $request->valori[$cheie];
            }
            if (empty($valori[$cheie])) {
                throw new \Exception("Lipseste `" . $cheie . "`, care identifica randul.");
            }

            $model = $cfg["model"];
            $existent = $this->interogareScriere($cfg)->where($cheie, $valori[$cheie])->first();
            if ($existent) {
                throw new \Exception("Exista deja un rand cu " . $cheie . " = '" . $valori[$cheie]
                    . "'. Folositi `modifica` daca vreti sa-l schimbati.");
            }

            if ($this->areCompanyId((new $model)->getTable())) {
                $valori["company_id"] = session("company_id");
            }
            $nou = $model::create($valori);

            DB::table("mcp_operatii")->insert([
                "company_id" => session("company_id"),
                "user_id"    => Auth::id(),
                "token"      => (string) Str::uuid(),
                "operatie"   => "adauga",
                "tabela"     => $request->tabela,
                "descriere"  => $request->descriere,
                "valori"     => json_encode($valori),
                "nr_randuri" => 1,
                "randuri"    => json_encode([["id" => $nou->getKey(), "inainte" => null]]),
                "stare"      => "aplicata",
                "aplicata_la" => now(),
                "created_at" => now(),
                "updated_at" => now(),
            ]);

            DB::commit();
            return response()->json(["adaugat" => $this->randVizibil($nou->fresh(), $cfg)]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["eroare" => $e->getMessage()], 422);
        }
    }

    /** Istoricul operatiilor facute prin acest acces. */
    public function operatii(Request $request)
    {
        $randuri = DB::table("mcp_operatii")
            ->where("company_id", session("company_id"))
            ->when($request->tabela, function ($q) use ($request) { return $q->where("tabela", $request->tabela); })
            ->orderByDesc("id")
            ->limit(min(max((int) $request->limita ?: 20, 1), 100))
            ->get(["id", "operatie", "tabela", "descriere", "valori", "nr_randuri", "stare",
                   "aplicata_la", "anulata_la", "created_at"]);

        return response()->json(["operatii" => $randuri->map(function ($o) {
            $o->valori = json_decode($o->valori, true);
            return $o;
        })]);
    }

    /**
     * Anuleaza o modificare aplicata: readuce valorile dinainte.
     * NU sterge nimic — o operatie de tip `adauga` nu se poate anula asa.
     */
    public function anuleaza(Request $request)
    {
        DB::beginTransaction();
        try {
            $op = DB::table("mcp_operatii")
                ->where("company_id", session("company_id"))
                ->where("id", (int) $request->operatie_id)->first();

            if (!$op) {
                throw new \Exception("Operatia nu a fost gasita.");
            }
            if ($op->operatie !== "modifica") {
                throw new \Exception("Se pot anula doar modificarile. Un rand adaugat nu se sterge prin acest acces; "
                    . "daca tabela are campul `activ`, puneti-l pe 0 cu o modificare.");
            }
            if ($op->stare !== "aplicata") {
                throw new \Exception("Operatia are starea '" . $op->stare . "'; se pot anula doar cele aplicate.");
            }

            $cfg = $this->config($op->tabela);
            $refacute = 0;
            foreach (json_decode($op->randuri, true) as $rand) {
                $m = $this->interogareScriere($cfg)->find($rand["id"]);
                if ($m) {
                    $m->update($rand["inainte"]);
                    $refacute++;
                }
            }

            DB::table("mcp_operatii")->where("id", $op->id)
                ->update(["stare" => "anulata", "anulata_la" => now(), "updated_at" => now()]);

            DB::commit();
            return response()->json([
                "anulata"  => $op->id,
                "randuri"  => $refacute,
                "mesaj"    => "Valorile dinainte au fost puse la loc.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["eroare" => $e->getMessage()], 422);
        }
    }

    // ─────────────────────────── intern ───────────────────────────

    /** Aplica o operatie pregatita, dupa token. */
    private function aplica($token)
    {
        DB::beginTransaction();
        try {
            $op = DB::table("mcp_operatii")->where("token", $token)->first();
            if (!$op) {
                throw new \Exception("Token necunoscut. Rulati intai simularea.");
            }
            if ($op->stare !== "pregatita") {
                throw new \Exception("Operatia a fost deja " . $op->stare . ".");
            }
            if ($op->user_id && Auth::id() && $op->user_id != Auth::id()) {
                throw new \Exception("Operatia a fost pregatita de alt utilizator.");
            }

            $cfg = $this->config($op->tabela);
            $valori = json_decode($op->valori, true);
            $modificate = 0;
            foreach (json_decode($op->randuri, true) as $rand) {
                $m = $this->interogareScriere($cfg)->find($rand["id"]);
                if ($m) {
                    $m->update($valori);
                    $modificate++;
                }
            }

            DB::table("mcp_operatii")->where("id", $op->id)
                ->update(["stare" => "aplicata", "aplicata_la" => now(), "updated_at" => now()]);

            DB::commit();
            return response()->json([
                "aplicata"         => true,
                "operatie_id"      => $op->id,
                "tabela"           => $op->tabela,
                "randuri_modificate" => $modificate,
                "cum_se_anuleaza"  => "anuleaza cu operatie_id = " . $op->id,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["eroare" => $e->getMessage()], 422);
        }
    }

    private function config($tabela)
    {
        $tabele = config("mcp.tabele");
        if (!$tabela || !isset($tabele[$tabela])) {
            throw new \Exception("Tabela '" . $tabela . "' nu e disponibila prin acest acces. "
                . "Cele permise: " . implode(", ", array_keys($tabele)) . ".");
        }
        if (!empty($tabele[$tabela]["doar_administrator"]) && !$this->esteAdministrator()) {
            throw new \Exception("Tabela '" . $tabela . "' (" . $tabele[$tabela]["eticheta"]
                . ") se poate modifica doar de administratorul serviciului.");
        }
        return $tabele[$tabela];
    }

    // ─────────────────── cine intreaba ───────────────────

    /** Administratorul serviciului: conventia aplicatiei (vezi ContextCompanie). */
    private function esteAdministrator()
    {
        return \App\Support\ContextCompanie::esteAdministrator();
    }

    /** Are tabela coloana `company_id`, adica e a unui client anume? */
    private function areCompanyId($tabela)
    {
        static $cache = [];
        if (!isset($cache[$tabela])) {
            $cache[$tabela] = false;
            foreach (DB::select("SHOW COLUMNS FROM `" . $tabela . "` LIKE 'company_id'") as $c) {
                $cache[$tabela] = true;
            }
        }
        return $cache[$tabela];
    }

    /**
     * O tabela fara `company_id` care nu e nomenclator comun tine date de
     * administrare a serviciului (utilizatori, clienti, banci, marketing).
     * O citeste doar administratorul.
     */
    private function tabelaDoarAdministrator($tabela)
    {
        return !$this->areCompanyId($tabela)
            && !in_array($tabela, (array) config("mcp.citire.tabele_comune"), true);
    }

    /** Tabelele citibile, minus cele de administrare cand nu intreaba administratorul. */
    private function tabeleCitibilePentruMine()
    {
        if ($this->esteAdministrator()) {
            return $this->tabeleCitibile();
        }
        return array_values(array_filter($this->tabeleCitibile(), function ($t) {
            return !$this->tabelaDoarAdministrator($t);
        }));
    }

    // ─────────────────── ce se poate citi ───────────────────

    /**
     * Tabelele pe care accesul le poate citi: tot ce e in baza, minus lista de
     * excludere. Se citeste schema reala, nu o lista scrisa de mana, ca sa nu
     * ramana pe dinafara tabele adaugate ulterior.
     */
    private function tabeleCitibile()
    {
        static $lista = null;
        if ($lista !== null) {
            return $lista;
        }
        $interzise = (array) config("mcp.citire.tabele_interzise");
        $lista = [];
        foreach (DB::select("SHOW TABLES") as $rand) {
            $campuri = (array) $rand;             // cheia e `Tables_in_<baza>`
            $nume = reset($campuri);
            if (!in_array($nume, $interzise, true)) {
                $lista[] = $nume;
            }
        }
        sort($lista);
        return $lista;
    }

    /** Estimarile de marime, ca sa se vada dintr-o privire ce e mare si ce e gol. */
    private function randuriAproximative()
    {
        $randuri = [];
        foreach (DB::select("SELECT TABLE_NAME t, TABLE_ROWS r FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()") as $x) {
            $randuri[$x->t] = $x->r;
        }
        return $randuri;
    }

    /** Numele de tabela din config, dupa numele real al tabelei din baza. */
    private function tabeleEditabile()
    {
        static $harta = null;
        if ($harta !== null) {
            return $harta;
        }
        $harta = [];
        foreach (config("mcp.tabele") as $nume => $cfg) {
            $model = $cfg["model"];
            $harta[(new $model)->getTable()] = $nume;
        }
        return $harta;
    }

    /**
     * Verifica numele tabelei fata de schema reala. Dupa asta poate intra intre
     * backtick-uri fara grija: e una din valorile intoarse de `SHOW TABLES`.
     */
    private function tabelaCitibila($tabela)
    {
        $tabela = (string) $tabela;
        if ($tabela === "") {
            throw new \Exception("Lipseste `tabela`. Cheama `structura` fara argumente ca sa vezi ce exista.");
        }
        if (in_array($tabela, (array) config("mcp.citire.tabele_interzise"), true)) {
            throw new \Exception("Tabela '" . $tabela . "' nu se citeste prin acest acces: "
                . "tine material de autentificare (tokenuri, resetari de parola), nu date de business.");
        }
        if (!in_array($tabela, $this->tabeleCitibile(), true)) {
            throw new \Exception("Nu exista tabela '" . $tabela . "'. "
                . "Cheama `structura` (optional cu `contine`) ca sa gasesti numele corect.");
        }
        if (!$this->esteAdministrator() && $this->tabelaDoarAdministrator($tabela)) {
            throw new \Exception("Tabela '" . $tabela . "' tine date de administrare a serviciului, "
                . "nu ale unui client; o poate citi doar administratorul.");
        }
        return $tabela;
    }

    /** Coloanele vizibile ale unei tabele: toate, minus cele cu nume de secret. */
    private function coloanePermise($tabela)
    {
        static $cache = [];
        if (isset($cache[$tabela])) {
            return $cache[$tabela];
        }
        $coloane = [];
        foreach (DB::select("SHOW COLUMNS FROM `" . $tabela . "`") as $c) {
            if (!$this->coloanaInterzisa($c->Field)) {
                $coloane[$c->Field] = $c;
            }
        }
        return $cache[$tabela] = $coloane;
    }

    private function coloanaInterzisa($nume)
    {
        foreach ((array) config("mcp.citire.coloane_interzise") as $interzis) {
            if (strcasecmp((string) $nume, $interzis) === 0) {
                return true;
            }
        }
        return false;
    }

    /** Ce s-a scos din tabela asta, ca sa nu para ca lipseste din greseala. */
    private function coloaneAscunse($tabela)
    {
        $ascunse = [];
        foreach (DB::select("SHOW COLUMNS FROM `" . $tabela . "`") as $c) {
            if ($this->coloanaInterzisa($c->Field)) {
                $ascunse[] = $c->Field;
            }
        }
        return $ascunse;
    }

    /**
     * Verifica un nume de coloana. Singura cale prin care un nume ajunge intr-o
     * interogare — inclusiv in `selectRaw` — trece pe aici, deci nu poate fi
     * altceva decat o coloana reala si permisa.
     */
    private function coloanaValida($tabela, $camp, $coloane)
    {
        if (isset($coloane[$camp])) {
            return $camp;
        }
        if ($this->coloanaInterzisa($camp)) {
            throw new \Exception("Coloana '" . $camp . "' nu e accesibila prin acest acces, "
                . "nici la citire si nici la filtrare.");
        }
        throw new \Exception("Tabela '" . $tabela . "' nu are coloana '" . $camp . "'. "
            . "Cheama `structura` cu `tabela` = '" . $tabela . "' ca sa vezi coloanele.");
    }

    // ─────────────────── interogari ───────────────────

    /**
     * Interogarea pentru MODIFICARE: pe model, limitata la firma curenta.
     * Filtrarea merge pe orice coloana citibila — a putea filtra dupa o coloana
     * nu inseamna a o putea scrie; ce se scrie e verificat separat.
     */
    private function interogare($cfg, $filtru)
    {
        $tabela = (new $cfg["model"])->getTable();
        return $this->aplicaConditii($this->interogareScriere($cfg), $filtru, $tabela, $this->coloanePermise($tabela));
    }

    /**
     * Punctul de plecare al oricarei SCRIERI: pe model, limitat la firma curenta
     * cand tabela are `company_id`. Tabelele fara (evidenta de marketing) sunt
     * oricum doar ale administratorului, prin `config()`.
     */
    private function interogareScriere($cfg)
    {
        $model = $cfg["model"];
        $q = $model::query();
        if ($this->areCompanyId((new $model)->getTable())) {
            $q->where("company_id", session("company_id"));
        }
        return $q;
    }

    /**
     * Interogarea pentru CITIRE: direct pe tabela, fara model, limitata la firma
     * curenta. Doar administratorul poate cere `toate_firmele`; pentru oricine
     * altcineva cererea e ignorata, nu refuzata — nu-i o greseala, e perimetrul lui.
     */
    private function interogareCitire($tabela, $coloane, $filtru, $toateFirmele = false)
    {
        $q = DB::table($tabela);
        if (isset($coloane["company_id"]) && !($toateFirmele && $this->esteAdministrator())) {
            $q->where($tabela . ".company_id", session("company_id"));
        }
        return $this->aplicaConditii($q, $filtru, $tabela, $coloane);
    }

    /**
     * Fiecare conditie e [camp, operator, valoare]; operatorii sunt o lista
     * inchisa, iar campul e verificat fata de schema.
     */
    private function aplicaConditii($q, $filtru, $tabela, $coloane)
    {
        $operatori = ["=", "!=", ">", ">=", "<", "<=", "contine", "incepe_cu", "gol", "necompletat", "in", "intre"];
        foreach ((array) $filtru as $conditie) {
            $camp = $this->coloanaValida($tabela, $conditie["camp"] ?? null, $coloane);
            $op   = $conditie["operator"] ?? "=";
            $val  = $conditie["valoare"] ?? null;

            if (!in_array($op, $operatori, true)) {
                throw new \Exception("Operator necunoscut: '" . $op . "'. Permisi: " . implode(", ", $operatori) . ".");
            }

            if ($op === "contine")           { $q->where($camp, "like", "%" . $val . "%"); }
            elseif ($op === "incepe_cu")     { $q->where($camp, "like", $val . "%"); }
            elseif ($op === "gol" || $op === "necompletat") {
                $q->where(function ($x) use ($camp) { $x->whereNull($camp)->orWhere($camp, ""); });
            }
            elseif ($op === "in")            { $q->whereIn($camp, (array) $val); }
            elseif ($op === "intre") {
                $capete = array_values((array) $val);
                if (count($capete) !== 2) {
                    throw new \Exception("Operatorul `intre` cere doua valori: [de_la, pana_la].");
                }
                $q->whereBetween($camp, $capete);
            }
            else                             { $q->where($camp, $op, $val); }
        }
        return $q;
    }

    /** Ordonare dupa coloane reale sau dupa numele unui agregat calculat. */
    private function aplicaOrdonarea($q, $tabela, $coloane, $ordonare, $alias)
    {
        foreach ((array) $ordonare as $o) {
            $camp = is_array($o) ? ($o["camp"] ?? null) : $o;
            $dir  = strtolower((string) (is_array($o) ? ($o["directie"] ?? "asc") : "asc"));
            if (!in_array($dir, ["asc", "desc"], true)) {
                throw new \Exception("Directie de ordonare necunoscuta: '" . $dir . "'. Permise: asc, desc.");
            }
            if (in_array($camp, $alias, true)) {
                $q->orderBy(DB::raw("`" . $camp . "`"), $dir);
                continue;
            }
            $q->orderBy($this->coloanaValida($tabela, $camp, $coloane), $dir);
        }
        return $q;
    }

    /**
     * Sinteza: grupare si functii de agregare. Numele coloanelor calculate sunt
     * fie generate de noi, fie validate cu o expresie stricta — nimic din ce vine
     * de la client nu ajunge nefiltrat in SQL.
     */
    private function sinteza($tabela, $coloane, $q, $grupare, $agregate, $ordonare, $limita)
    {
        $functii = ["numar" => "COUNT", "suma" => "SUM", "medie" => "AVG", "minim" => "MIN", "maxim" => "MAX"];

        $select = [];
        foreach ($grupare as $g) {
            $g = $this->coloanaValida($tabela, $g, $coloane);
            $select[] = "`" . $g . "`";
            $q->groupBy($g);
        }

        if (empty($agregate)) {
            $agregate = [["functie" => "numar"]];
        }
        $alias = [];
        foreach ($agregate as $a) {
            $f = $a["functie"] ?? "numar";
            if (!isset($functii[$f])) {
                throw new \Exception("Functie de agregare necunoscuta: '" . $f . "'. "
                    . "Permise: " . implode(", ", array_keys($functii)) . ".");
            }
            $camp = $a["camp"] ?? null;
            if ($f === "numar" && !$camp) {
                $expresie = "COUNT(*)";
                $nume = "numar";
            } else {
                $camp = $this->coloanaValida($tabela, $camp, $coloane);
                $expresie = $functii[$f] . "(`" . $camp . "`)";
                $nume = $f . "_" . $camp;
            }
            if (isset($a["ca"])) {
                $nume = (string) $a["ca"];
                if (!preg_match("/^[A-Za-z_][A-Za-z0-9_]{0,39}$/", $nume)) {
                    throw new \Exception("Numele '" . $nume . "' nu e bun pentru o coloana calculata: "
                        . "doar litere, cifre si liniuta de subliniere, maxim 40 de caractere.");
                }
            }
            $select[] = $expresie . " AS `" . $nume . "`";
            $alias[] = $nume;
        }

        $this->aplicaOrdonarea($q, $tabela, $coloane, $ordonare, $alias);
        $randuri = $q->selectRaw(implode(", ", $select))->limit($limita)->get();

        $rezultat = [
            "tabela"    => $tabela,
            "grupare"   => $grupare,
            "returnate" => $randuri->count(),
            "randuri"   => $randuri,
        ];
        if ($randuri->count() >= $limita) {
            $rezultat["nota"] = "S-a atins limita de " . $limita . " grupuri; pot exista si altele. "
                . "Ordonati descrescator dupa ce va intereseaza, sau restrangeti filtrul.";
        }
        return $rezultat;
    }

    /** Pastreaza doar campurile permise si refuza explicit restul. */
    private function valoriPermise($cfg, $valori, $lista, $permiteCheia = false)
    {
        $valori = (array) $valori;
        if (empty($valori)) {
            throw new \Exception("Nu s-a trimis nicio valoare.");
        }
        $permise = $cfg[$lista];
        if ($permiteCheia) {
            $permise[] = $cfg["cheie"];
        }
        $refuzate = array_diff(array_keys($valori), $permise);
        if (!empty($refuzate)) {
            throw new \Exception("Campurile [" . implode(", ", $refuzate) . "] nu pot fi modificate prin acest acces. "
                . "Permise pe '" . $cfg["eticheta"] . "': " . implode(", ", $permise) . ".");
        }
        return $valori;
    }

    /** Randul asa cum se intoarce: cheia, campurile cautabile si cele editabile. */
    private function randVizibil($model, $cfg)
    {
        $campuri = array_unique(array_merge([$cfg["cheie"]], $cfg["cautabile"], $cfg["editabile"]));
        $rand = ["id" => $model->getKey()];
        foreach ($campuri as $c) {
            $rand[$c] = $model->{$c};
        }
        return $rand;
    }
}
