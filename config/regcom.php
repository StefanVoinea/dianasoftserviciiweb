<?php

/*
 * API-ul de consultare a datelor firmelor din Registrul Comertului (tabela datefirmeregcom, ~1,7 milioane de randuri).
 * Tabela sta doar aici; celelalte aplicatii DianaSoft (alprof, blumenfield, easyifn, cerbererp, dsgarage, fortuna) o
 * consulta prin /api/regcom/... cu o cheie de aplicatie (Authorization: Bearer <cheie>), cerut 10.11.2026.
 *
 * REGCOM_API_CHEI = cheile acceptate, despartite prin virgula (cate una pe aplicatie, ca sa se poata retrage separat):
 *   REGCOM_API_CHEI=dsgarage:xxxxxxxx,alprof:yyyyyyyy
 * Partea dinaintea ":" e doar o eticheta pentru jurnal; cheia e sirul intreg, asa cum e scris aici.
 */
return [
    'chei' => array_values(array_filter(array_map('trim', explode(',', (string) env('REGCOM_API_CHEI', ''))))),

    // cate randuri intoarce cel mult o cautare (nu lista paginata)
    'limita_cautare' => 200,
];
