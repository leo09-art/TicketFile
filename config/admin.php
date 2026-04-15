<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Compte administrateur initial
    |--------------------------------------------------------------------------
    |
    | Ces valeurs sont utilisées par le seeder pour créer le premier admin
    | du projet après un clonage.
    |
    */

    'name' => env('ADMIN_NAME', 'Administrateur'),
    'email' => env('ADMIN_EMAIL', 'admin@ticketfile.test'),
    'password' => env('ADMIN_PASSWORD', 'admin12345'),
];

