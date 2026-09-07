#!/usr/bin/env node
/**
 * Lansatorul serverului MCP.
 *
 * Exista separat, in CommonJS, dintr-un motiv practic: pe unele calculatoare
 * traieste si un Node 12, folosit pentru build-ul de frontend. Daca serverul
 * porneste din greseala cu el, `server.js` nici nu apuca sa fie evaluat — ESM-ul
 * isi rezolva importurile inainte de orice linie din corpul modulului, asa ca o
 * verificare pusa acolo n-ar rula niciodata si eroarea ar fi criptica.
 *
 * Fisierul asta ruleaza pe orice Node, verifica versiunea si abia apoi incarca
 * serverul propriu-zis.
 */
"use strict";

var major = Number(process.versions.node.split(".")[0]);

if (major < 18) {
  process.stderr.write(
    "\n  Serverul MCP DianaSoft are nevoie de Node 18 sau mai nou.\n" +
      "  Acum ruleaza pe Node " + process.versions.node + ".\n\n" +
      "  Pe calculatorul asta exista probabil mai multe versiuni de Node\n" +
      "  (una veche, pentru build-ul de frontend). In configurarea Claude,\n" +
      "  pune calea completa catre executabilul nou, in loc de \"node\":\n\n" +
      "    \"command\": \"C:/Program Files/nodejs/node.exe\"\n\n" +
      "  Verifici ce versiune e cu:  node -v\n\n"
  );
  process.exit(1);
}

import("./server.js").catch(function (e) {
  process.stderr.write("\n  Serverul MCP nu a putut porni: " + (e && e.message ? e.message : e) + "\n\n");
  process.exit(1);
});
