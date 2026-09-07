#!/usr/bin/env node
/**
 * Server MCP pentru DianaSoft servicii web (SPV, ANAF, e-Factura, e-Transport).
 *
 * Da unui asistent doua drepturi diferite:
 *   - CITIRE pe toata baza (`structura` si `cauta`), ca sa poata raspunde la
 *     intrebari despre productie, stocuri, comenzi si istoric;
 *   - SCRIERE doar pe nomenclatoare (`adauga`, `modifica`), strict pe lista din
 *     `config/mcp.php`.
 *
 * Nu exista unealta de stergere, iar serverul din Laravel refuza oricum orice
 * scriere in afara listei si orice citire in tabelele de autentificare.
 *
 * Configurare (variabile de mediu):
 *   DIANASOFT_URL    - ex. https://app.dianasoft.ro
 *   DIANASOFT_TOKEN  - token personal, generat de Stefan din aplicatie
 */

import { Server } from "@modelcontextprotocol/sdk/server/index.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import {
  CallToolRequestSchema,
  ListToolsRequestSchema,
} from "@modelcontextprotocol/sdk/types.js";

const BAZA = (process.env.DIANASOFT_URL || "").replace(/\/+$/, "");
const TOKEN = process.env.DIANASOFT_TOKEN || "";

if (!BAZA || !TOKEN) {
  console.error("Lipsesc DIANASOFT_URL si/sau DIANASOFT_TOKEN din configurare.");
  process.exit(1);
}

async function cere(cale, metoda, corp) {
  const raspuns = await fetch(`${BAZA}/api/mcp/${cale}`, {
    method: metoda,
    headers: {
      Authorization: `Bearer ${TOKEN}`,
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    body: corp ? JSON.stringify(corp) : undefined,
  });
  const text = await raspuns.text();
  let date;
  try {
    date = JSON.parse(text);
  } catch {
    return { eroare: `Raspuns neasteptat de la server (HTTP ${raspuns.status}): ${text.slice(0, 300)}` };
  }
  if (raspuns.status === 401) {
    return { eroare: "Token invalid sau expirat. Cere-i lui Stefan unul nou." };
  }
  return date;
}

const UNELTE = [
  {
    name: "dianasoft_tabele",
    description:
      "Punctul de plecare. Spune ce se poate MODIFICA — o lista scurta de nomenclatoare, coloana cu coloana. " +
      "De CITIT se poate citi aproape toata baza: pentru asta foloseste `dianasoft_structura`. Nu exista stergere.",
    inputSchema: { type: "object", properties: {} },
  },
  {
    name: "dianasoft_structura",
    description:
      "Exploreaza baza de date. Fara argumente: toate tabelele care se pot citi, cu marimea lor aproximativa; " +
      "da `contine` ca sa filtrezi numele (ex. 'declaratii', 'spv', 'certificat'). Cu `tabela`: coloanele ei cu tipuri, cate " +
      "randuri are si daca se poate si scrie in ea. Cheam-o inainte sa cauti intr-o tabela pe care n-o cunosti, " +
      "ca sa nu ghicesti numele coloanelor.",
    inputSchema: {
      type: "object",
      properties: {
        tabela: { type: "string", description: "Numele tabelei. Lipseste = da lista." },
        contine: { type: "string", description: "Filtreaza lista dupa o bucata din nume." },
      },
    },
  },
  {
    name: "dianasoft_cauta",
    description:
      "Citeste randuri din ORICE tabela citibila. Doar citire, nu schimba nimic. Filtrul e o lista de conditii " +
      "[{camp, operator, valoare}]; operatori: =, !=, >, >=, <, <=, contine, incepe_cu, gol, in, intre. " +
      "`coloane` limiteaza ce se intoarce, `ordonare` e [{camp, directie}]. Pe tabelele mari (mesaje SPV, jurnal, registrul comertului) " +
      "nu cere randuri, cere sinteze: `grupare` cu coloanele dupa care se grupeaza si `agregate` cu " +
      "[{functie: numar|suma|medie|minim|maxim, camp, ca}].",
    inputSchema: {
      type: "object",
      properties: {
        tabela: { type: "string", description: "ex. anaf_declaratii, spv_mesaje, anaf_societati" },
        coloane: {
          type: "array",
          description: "Ce coloane sa se intoarca. Gol = toate.",
          items: { type: "string" },
        },
        grupare: {
          type: "array",
          description: "Coloanele dupa care se grupeaza.",
          items: { type: "string" },
        },
        agregate: {
          type: "array",
          description: "Ce se calculeaza pe fiecare grup.",
          items: {
            type: "object",
            properties: {
              functie: { type: "string", description: "numar, suma, medie, minim, maxim" },
              camp: { type: "string" },
              ca: { type: "string", description: "Numele coloanei rezultat." },
            },
          },
        },
        ordonare: {
          type: "array",
          items: {
            type: "object",
            properties: {
              camp: { type: "string" },
              directie: { type: "string", description: "asc sau desc" },
            },
            required: ["camp"],
          },
        },
        filtru: {
          type: "array",
          description: "Conditii de filtrare. Gol = toate randurile.",
          items: {
            type: "object",
            properties: {
              camp: { type: "string" },
              operator: { type: "string" },
              valoare: {},
            },
            required: ["camp"],
          },
        },
        limita: { type: "number", description: "Cate randuri sa intoarca (implicit 50, maxim 500)" },
        toate_firmele: {
          type: "boolean",
          description: "Doar pentru administratorul serviciului: citeste peste toti clientii, nu doar firma curenta.",
        },
      },
      required: ["tabela"],
    },
  },
  {
    name: "dianasoft_modifica",
    description:
      "Modifica in masa randurile care se potrivesc filtrului. Merge DOAR pe tabelele din `dianasoft_tabele`: " +
      "faptul ca o tabela se poate citi nu inseamna ca se poate si scrie in ea. Are DOUA faze: " +
      "fara `token` nu schimba nimic — spune cate randuri ar fi atinse, arata exemple si intoarce un token; " +
      "cu `token` aplica exact ce s-a aratat. Arata utilizatorului rezultatul simularii si cere-i acordul " +
      "inainte sa aplici. Modificarea se poate anula dupa aceea.",
    inputSchema: {
      type: "object",
      properties: {
        tabela: { type: "string" },
        filtru: {
          type: "array",
          items: {
            type: "object",
            properties: { camp: { type: "string" }, operator: { type: "string" }, valoare: {} },
            required: ["camp"],
          },
        },
        valori: { type: "object", description: "Campurile de schimbat si valorile noi." },
        descriere: { type: "string", description: "In cuvinte, ce se schimba si de ce. Ramane in istoric." },
        token: { type: "string", description: "Doar la aplicare — token-ul primit de la simulare." },
      },
      required: ["tabela"],
    },
  },
  {
    name: "dianasoft_adauga",
    description: "Adauga un rand nou intr-o tabela permisa. Refuza daca exista deja unul cu aceeasi cheie.",
    inputSchema: {
      type: "object",
      properties: {
        tabela: { type: "string" },
        valori: { type: "object" },
        descriere: { type: "string" },
      },
      required: ["tabela", "valori"],
    },
  },
  {
    name: "dianasoft_operatii",
    description: "Istoricul modificarilor facute prin acest acces, cu starea fiecareia.",
    inputSchema: {
      type: "object",
      properties: {
        tabela: { type: "string" },
        limita: { type: "number" },
      },
    },
  },
  {
    name: "dianasoft_anuleaza",
    description:
      "Anuleaza o modificare aplicata: readuce valorile dinainte pe randurile atinse. " +
      "Nu sterge niciodata randuri; un rand adaugat ramane.",
    inputSchema: {
      type: "object",
      properties: { operatie_id: { type: "number" } },
      required: ["operatie_id"],
    },
  },
];

const server = new Server(
  { name: "dianasoft", version: "1.0.0" },
  { capabilities: { tools: {} } }
);

server.setRequestHandler(ListToolsRequestSchema, async () => ({ tools: UNELTE }));

server.setRequestHandler(CallToolRequestSchema, async (cerere) => {
  const { name, arguments: argumente = {} } = cerere.params;

  let rezultat;
  switch (name) {
    case "dianasoft_tabele":
      rezultat = await cere("tabele", "GET");
      break;
    case "dianasoft_structura":
      rezultat = await cere("structura", "POST", argumente);
      break;
    case "dianasoft_cauta":
      rezultat = await cere("cauta", "POST", argumente);
      break;
    case "dianasoft_modifica":
      rezultat = await cere("modifica", "POST", argumente);
      break;
    case "dianasoft_adauga":
      rezultat = await cere("adauga", "POST", argumente);
      break;
    case "dianasoft_operatii": {
      const p = new URLSearchParams();
      if (argumente.tabela) p.set("tabela", argumente.tabela);
      if (argumente.limita) p.set("limita", String(argumente.limita));
      rezultat = await cere(`operatii?${p.toString()}`, "GET");
      break;
    }
    case "dianasoft_anuleaza":
      rezultat = await cere("anuleaza", "POST", argumente);
      break;
    default:
      rezultat = { eroare: `Unealta necunoscuta: ${name}` };
  }

  return {
    content: [{ type: "text", text: JSON.stringify(rezultat, null, 2) }],
    isError: Boolean(rezultat && rezultat.eroare),
  };
});

const transport = new StdioServerTransport();
await server.connect(transport);
