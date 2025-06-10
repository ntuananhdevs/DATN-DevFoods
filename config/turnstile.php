<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site Key
    |--------------------------------------------------------------------------
    |
    | This is the site key provided by Cloudflare for your domain.
    |
    */
    'site_key' => env('TURNSTILE_SITE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Secret Key
    |--------------------------------------------------------------------------
    |
    | This is the secret key provided by Cloudflare for your domain.
    |
    */
    'secret_key' => env('TURNSTILE_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Verify URL
    |--------------------------------------------------------------------------
    |
    | This is the URL used to verify the Turnstile token.
    |
    */
    'verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | The theme to use for the Turnstile widget. Can be 'light' or 'dark'.
    |
    */
    'theme' => env('TURNSTILE_THEME', 'light'),

    /*
    |--------------------------------------------------------------------------
    | Size
    |--------------------------------------------------------------------------
    |
    | The size of the Turnstile widget. Can be 'normal', 'compact'.
    |
    */
    'size' => env('TURNSTILE_SIZE', 'normal'),
]; 