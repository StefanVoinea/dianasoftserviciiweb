<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * [2026-09-01] Endpoint MCP REMOTE (`POST /mcp`).
 *
 * Varianta stdio (folderul `mcp-dianasoft`) cere ca utilizatorul sa instaleze un
 * proces Node la el pe calculator. Asta e varianta in care nu instaleaza nimic:
 * lipeste un URL in "Add custom connector" si gata.
 *
 * Transport: Streamable HTTP, fara stare. Fiecare cerere e un mesaj JSON-RPC 2.0
 * si primeste raspunsul in acelasi HTTP; nu se tine sesiune intre cereri, deci nu
 * conteaza pe ce proces web nimereste cererea urmatoare.
 *
 * Uneltele sunt EXACT aceleasi ca la varianta stdio, pentru ca executia se
 * deleaga catre `McpController`. Asa nu pot exista doua comportamente diferite:
 * lista alba din `config/mcp.php`, cele doua faze la modificarea in masa si
 * lipsa oricarei stergeri sunt valabile identic pe ambele drumuri.
 */
class McpRpcController extends Controller
{
    /** Versiunea de protocol pe care o anuntam. */
    const PROTOCOL = "2024-11-05";

    public function rpc(Request $request)
    {
        $mesaj = $request->json()->all();

        // notificarile (fara `id`) nu primesc raspuns
        if (!isset($mesaj["id"])) {
            return response()->noContent(202);
        }

        $id = $mesaj["id"];
        $metoda = $mesaj["method"] ?? "";
        $parametri = $mesaj["params"] ?? [];

        try {
            switch ($metoda) {
                case "initialize":
                    return $this->rezultat($id, [
                        "protocolVersion" => self::PROTOCOL,
                        "capabilities"    => ["tools" => new \stdClass()],
                        "serverInfo"      => ["name" => "dianasoft", "version" => "1.0.0"],
                    ]);

                case "ping":
                    return $this->rezultat($id, new \stdClass());

                case "tools/list":
                    return $this->rezultat($id, ["tools" => $this->unelte()]);

                case "tools/call":
                    return $this->rezultat($id, $this->cheamaUnealta(
                        $parametri["name"] ?? "",
                        $parametri["arguments"] ?? []
                    ));

                default:
                    return $this->eroare($id, -32601, "Metoda necunoscuta: " . $metoda);
            }
        } catch (\Exception $e) {
            return $this->eroare($id, -32603, $e->getMessage());
        }
    }

    /** Executia trece prin acelasi controller ca varianta stdio. */
    private function cheamaUnealta($nume, $argumente)
    {
        $harta = [
            "dianasoft_tabele"    => ["tabele", "GET"],
            "dianasoft_structura" => ["structura", "POST"],
            "dianasoft_cauta"     => ["cauta", "POST"],
            "dianasoft_modifica"  => ["modifica", "POST"],
            "dianasoft_adauga"    => ["adauga", "POST"],
            "dianasoft_operatii"  => ["operatii", "GET"],
            "dianasoft_anuleaza"  => ["anuleaza", "POST"],
        ];
        if (!isset($harta[$nume])) {
            return $this->continut(["eroare" => "Unealta necunoscuta: " . $nume], true);
        }

        [$metoda, $verb] = $harta[$nume];
        $cerere = new Request();
        $cerere->setUserResolver(request()->getUserResolver());
        $cerere->merge((array) $argumente);

        $controller = app(McpController::class);
        $raspuns = $metoda === "tabele"
            ? $controller->tabele()
            : $controller->{$metoda}($cerere);

        $date = json_decode($raspuns->getContent(), true);
        return $this->continut($date, $raspuns->getStatusCode() >= 400 || isset($date["eroare"]));
    }

    private function continut($date, $eEroare = false)
    {
        return [
            "content" => [["type" => "text", "text" => json_encode($date, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)]],
            "isError" => (bool) $eEroare,
        ];
    }

    private function rezultat($id, $rezultat)
    {
        return response()->json(["jsonrpc" => "2.0", "id" => $id, "result" => $rezultat]);
    }

    private function eroare($id, $cod, $mesaj)
    {
        return response()->json(["jsonrpc" => "2.0", "id" => $id, "error" => ["code" => $cod, "message" => $mesaj]]);
    }

    /** Aceleasi descrieri ca in serverul stdio, ca asistentul sa se poarte la fel. */
    private function unelte()
    {
        $conditie = [
            "type" => "array",
            "items" => [
                "type" => "object",
                "properties" => [
                    "camp" => ["type" => "string"],
                    "operator" => ["type" => "string"],
                    "valoare" => new \stdClass(),
                ],
                "required" => ["camp"],
            ],
        ];

        return [
            [
                "name" => "dianasoft_tabele",
                "description" => "Punctul de plecare. Spune ce se poate MODIFICA — o lista scurta de nomenclatoare, "
                    . "coloana cu coloana. De CITIT se poate citi aproape toata baza: pentru asta foloseste "
                    . "`dianasoft_structura`. Nu exista stergere.",
                "inputSchema" => ["type" => "object", "properties" => new \stdClass()],
            ],
            [
                "name" => "dianasoft_structura",
                "description" => "Exploreaza baza de date. Fara argumente: toate tabelele care se pot citi, cu marimea "
                    . "lor aproximativa; da `contine` ca sa filtrezi numele (ex. 'declaratii', 'spv', 'certificat'). Cu `tabela`: "
                    . "coloanele ei cu tipuri, cate randuri are si daca se poate si scrie in ea. Cheam-o inainte sa "
                    . "cauti intr-o tabela pe care n-o cunosti, ca sa nu ghicesti numele coloanelor.",
                "inputSchema" => [
                    "type" => "object",
                    "properties" => [
                        "tabela"  => ["type" => "string", "description" => "Numele tabelei. Lipseste = da lista."],
                        "contine" => ["type" => "string", "description" => "Filtreaza lista dupa o bucata din nume."],
                    ],
                ],
            ],
            [
                "name" => "dianasoft_cauta",
                "description" => "Citeste randuri din ORICE tabela citibila. Doar citire, nu schimba nimic. "
                    . "Filtrul e o lista de conditii [{camp, operator, valoare}]; operatori: =, !=, >, >=, <, <=, "
                    . "contine, incepe_cu, gol, in, intre. `coloane` limiteaza ce se intoarce, `ordonare` e "
                    . "[{camp, directie}]. Pe tabelele mari (mesaje SPV, jurnal, registrul comertului) nu cere randuri, cere sinteze: "
                    . "`grupare` cu coloanele dupa care se grupeaza si `agregate` cu "
                    . "[{functie: numar|suma|medie|minim|maxim, camp, ca}].",
                "inputSchema" => [
                    "type" => "object",
                    "properties" => [
                        "tabela" => ["type" => "string", "description" => "ex. anaf_declaratii, spv_mesaje, anaf_societati"],
                        "filtru" => $conditie,
                        "coloane" => ["type" => "array", "items" => ["type" => "string"],
                            "description" => "Ce coloane sa se intoarca. Gol = toate."],
                        "grupare" => ["type" => "array", "items" => ["type" => "string"],
                            "description" => "Coloanele dupa care se grupeaza."],
                        "agregate" => [
                            "type" => "array",
                            "description" => "Ce se calculeaza pe fiecare grup.",
                            "items" => [
                                "type" => "object",
                                "properties" => [
                                    "functie" => ["type" => "string", "description" => "numar, suma, medie, minim, maxim"],
                                    "camp" => ["type" => "string"],
                                    "ca" => ["type" => "string", "description" => "Numele coloanei rezultat."],
                                ],
                            ],
                        ],
                        "ordonare" => [
                            "type" => "array",
                            "items" => [
                                "type" => "object",
                                "properties" => [
                                    "camp" => ["type" => "string"],
                                    "directie" => ["type" => "string", "description" => "asc sau desc"],
                                ],
                                "required" => ["camp"],
                            ],
                        ],
                        "limita" => ["type" => "number", "description" => "implicit 50, maxim 500"],
                        "toate_firmele" => ["type" => "boolean", "description" => "Doar pentru administratorul "
                            . "serviciului: citeste peste toti clientii, nu doar firma curenta."],
                    ],
                    "required" => ["tabela"],
                ],
            ],
            [
                "name" => "dianasoft_modifica",
                "description" => "Modifica in masa randurile care se potrivesc filtrului. Merge DOAR pe tabelele din "
                    . "`dianasoft_tabele`: faptul ca o tabela se poate citi nu inseamna ca se poate si scrie in ea. "
                    . "Are DOUA faze: fara `token` "
                    . "nu schimba nimic — spune cate randuri ar fi atinse, arata exemple si intoarce un token; cu "
                    . "`token` aplica exact ce s-a aratat. Arata utilizatorului rezultatul simularii si cere-i acordul "
                    . "inainte sa aplici. Modificarea se poate anula dupa aceea.",
                "inputSchema" => [
                    "type" => "object",
                    "properties" => [
                        "tabela" => ["type" => "string"],
                        "filtru" => $conditie,
                        "valori" => ["type" => "object", "description" => "Campurile de schimbat si valorile noi."],
                        "descriere" => ["type" => "string", "description" => "In cuvinte, ce se schimba si de ce."],
                        "token" => ["type" => "string", "description" => "Doar la aplicare."],
                    ],
                    "required" => ["tabela"],
                ],
            ],
            [
                "name" => "dianasoft_adauga",
                "description" => "Adauga un rand nou intr-o tabela permisa. Refuza daca exista deja unul cu aceeasi cheie.",
                "inputSchema" => [
                    "type" => "object",
                    "properties" => [
                        "tabela" => ["type" => "string"],
                        "valori" => ["type" => "object"],
                        "descriere" => ["type" => "string"],
                    ],
                    "required" => ["tabela", "valori"],
                ],
            ],
            [
                "name" => "dianasoft_operatii",
                "description" => "Istoricul modificarilor facute prin acest acces, cu starea fiecareia.",
                "inputSchema" => [
                    "type" => "object",
                    "properties" => ["tabela" => ["type" => "string"], "limita" => ["type" => "number"]],
                ],
            ],
            [
                "name" => "dianasoft_anuleaza",
                "description" => "Anuleaza o modificare aplicata: readuce valorile dinainte pe randurile atinse. "
                    . "Nu sterge niciodata randuri; un rand adaugat ramane.",
                "inputSchema" => [
                    "type" => "object",
                    "properties" => ["operatie_id" => ["type" => "number"]],
                    "required" => ["operatie_id"],
                ],
            ],
        ];
    }
}
