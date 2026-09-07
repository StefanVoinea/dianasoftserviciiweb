// Verifica pe bune ca serverul MCP porneste si raspunde: initialize + tools/list.
import { spawn } from "node:child_process";

const copil = spawn(process.execPath, ["bin.cjs"], {
  env: { ...process.env, DIANASOFT_URL: "http://exemplu.local", DIANASOFT_TOKEN: "test" },
  stdio: ["pipe", "pipe", "pipe"],
});

let iesire = "";
let erori = "";
copil.stdout.on("data", (d) => { iesire += d.toString(); });
copil.stderr.on("data", (d) => { erori += d.toString(); });

const trimite = (mesaj) => copil.stdin.write(JSON.stringify(mesaj) + "\n");

trimite({
  jsonrpc: "2.0", id: 1, method: "initialize",
  params: { protocolVersion: "2024-11-05", capabilities: {}, clientInfo: { name: "test", version: "1" } },
});
setTimeout(() => trimite({ jsonrpc: "2.0", method: "notifications/initialized", params: {} }), 250);
setTimeout(() => trimite({ jsonrpc: "2.0", id: 2, method: "tools/list", params: {} }), 400);

setTimeout(() => {
  copil.kill();
  const raspunsuri = iesire.split("\n").filter(Boolean).map((l) => { try { return JSON.parse(l); } catch { return null; } }).filter(Boolean);

  const init = raspunsuri.find((r) => r.id === 1);
  const unelte = raspunsuri.find((r) => r.id === 2);

  let rau = 0;
  const verdict = (ok, text) => { if (!ok) rau++; console.log("  " + (ok ? "[ok]   " : "[ESUAT]") + text); };

  verdict(!!init && !init.error, "serverul raspunde la initialize" + (init?.error ? ": " + JSON.stringify(init.error) : ""));
  verdict(!!init?.result?.serverInfo, "se prezinta ca " + (init?.result?.serverInfo?.name ?? "?"));
  const lista = unelte?.result?.tools ?? [];
  verdict(lista.length === 7, lista.length + " unelte expuse");
  const nume = lista.map((u) => u.name);
  verdict(nume.every((n) => !/sterg|delete|remove|drop/i.test(n)), "nicio unealta de stergere: " + nume.join(", "));
  verdict(nume.includes("dianasoft_structura"), "unealta de explorat baza e prezenta");
  verdict(erori.trim() === "", "niciun mesaj de eroare pe stderr" + (erori ? ": " + erori.slice(0, 200) : ""));

  console.log("\n" + (rau ? rau + " verificari ESUATE" : "toate verificarile: OK"));
  process.exit(rau ? 1 : 0);
}, 1200);
