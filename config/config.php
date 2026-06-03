<?php
// Centrale configuratie. Waarden kunnen via omgevingsvariabelen worden overschreven,
// zodat geheimen niet in de code hoeven te staan.

return [
    'app' => [
        'name'  => 'Luminosity OS',
        'debug' => filter_var(getenv('LUM_DEBUG') ?: 'false', FILTER_VALIDATE_BOOL),
        // Publieke basis-URL (voor Mollie redirect/webhook).
        'url'   => getenv('LUM_APP_URL') ?: 'http://46.224.187.198',
    ],

    // Mollie-betalingen. Vul de test-API-key in via de omgevingsvariabele
    // LUM_MOLLIE_KEY (test_... uit het Mollie-dashboard). Zonder geldige key
    // valt het portaal terug op een GESIMULEERDE betaling (test/demo).
    'mollie' => [
        'key' => getenv('LUM_MOLLIE_KEY') ?: '',
    ],

    // Database (MariaDB) voor het logboek van geprobeerde opdrachten (US8 / ERD).
    'db' => [
        'host'     => getenv('LUM_DB_HOST') ?: '127.0.0.1',
        'name'     => getenv('LUM_DB_NAME') ?: 'luminosity',
        'user'     => getenv('LUM_DB_USER') ?: 'luminosity',
        'pass'     => getenv('LUM_DB_PASS') ?: 'luminosity',
        'charset'  => 'utf8mb4',
    ],

    // De Python-taal-engine (hergebruikt uit de codebase).
    'engine' => [
        'python' => getenv('LUM_PYTHON') ?: 'python3',
        'path'   => getenv('LUM_ENGINE_PATH') ?: '/opt/luminosity-engine',
    ],

    // Sandbox waarin een opdracht wordt uitgevoerd: een wegwerp-Docker-container
    // zonder netwerk, met strikte limieten. Voert NOOIT iets op de host uit.
    'sandbox' => [
        'image'   => getenv('LUM_SANDBOX_IMAGE') ?: 'archlinux:latest',
        'timeout' => (int)(getenv('LUM_SANDBOX_TIMEOUT') ?: 15),
    ],
];
