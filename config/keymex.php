<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration Keymex
    |--------------------------------------------------------------------------
    |
    | Configuration spécifique au réseau Keymex
    |
    */

    'agence' => [
        'slug' => env('KEYMEX_AGENCY_SLUG', 'keymex-synergie'),
        'name' => env('KEYMEX_AGENCY_NAME', 'KEYMEX Synergie'),
    ],
];
