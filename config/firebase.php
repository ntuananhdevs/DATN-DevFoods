<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Firebase services including
    | authentication, cloud firestore, and other Firebase services.
    |
    */

    'project_id' => env('FIREBASE_PROJECT_ID'),
    'api_key' => env('FIREBASE_API_KEY'),
    'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
    'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
    'app_id' => env('FIREBASE_APP_ID'),
    'measurement_id' => env('FIREBASE_MEASUREMENT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Authentication Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration specific to Firebase Authentication
    |
    */
    'auth' => [
        'enabled' => env('FIREBASE_AUTH_ENABLED', true),
        'providers' => [
            'google' => [
                'enabled' => env('FIREBASE_GOOGLE_AUTH_ENABLED', true),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Web Configuration
    |--------------------------------------------------------------------------
    |
    | These configurations will be passed to the frontend
    |
    */
    'web_config' => [
        'apiKey' => env('FIREBASE_API_KEY'),
        'authDomain' => env('FIREBASE_AUTH_DOMAIN'),
        'projectId' => env('FIREBASE_PROJECT_ID'),
        'storageBucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'appId' => env('FIREBASE_APP_ID'),
        'measurementId' => env('FIREBASE_MEASUREMENT_ID'),
    ],
]; 