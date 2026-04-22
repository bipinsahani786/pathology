<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Software Feature Toggles
    |--------------------------------------------------------------------------
    |
    | These settings control high-level accessibility of features in the software.
    | Most can be overridden via the .env file.
    |
    */

    // Ability to log in as another user (for debugging or admin support)
    'impersonation' => env('FEATURE_IMPERSONATION', true),

    // Ability for Agents and Collection Centers to have their own login accounts
    'partner_logins' => env('FEATURE_PARTNER_LOGINS', true),

    // Inventory Management Module
    'inventory' => env('FEATURE_INVENTORY', true),
];
