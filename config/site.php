<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Content locales
    |--------------------------------------------------------------------------
    |
    | Every translatable field on every content model is edited once per locale
    | listed here. Sprint 4 wires the front end routing and language switcher
    | against the same list, so adding a third language means changing this
    | array and nothing else in the admin.
    |
    */

    'locales' => [
        'en' => 'English',
        'fr' => 'Français',
    ],

    'default_locale' => 'en',

];
