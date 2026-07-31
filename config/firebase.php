<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging
    |--------------------------------------------------------------------------
    |
    | Folosit pentru alertele instantanee pe telefon. Fara aceste date,
    | trimiterea este pur si simplu oprita: aplicatia mobila se intoarce la
    | verificarea periodica, deci nimeni nu ramane neanuntat.
    |
    | Datele se iau din consola Firebase:
    |   - id-ul proiectului: Project settings > General
    |   - cheia contului de serviciu: Project settings > Service accounts >
    |     "Generate new private key" (fisier JSON, pastrat in afara depozitului)
    |
    */

    'proiect' => env('FIREBASE_PROJECT_ID'),

    // Calea catre fisierul JSON al contului de serviciu.
    'cont_serviciu' => env('FIREBASE_CREDENTIALS'),

    'timeout' => (int) env('FIREBASE_TIMEOUT', 20),

    // Adresa de trimitere (HTTP v1). Se schimba doar la testare.
    'url' => env('FIREBASE_FCM_URL', 'https://fcm.googleapis.com/v1/projects/{proiect}/messages:send'),
];
