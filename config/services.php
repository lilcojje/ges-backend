<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'facebook' => [    
        'client_id' => '3289482551264596',  
        'client_secret' => 'd0eb8912a85e9cb171b3333c735aedab',  
        'redirect' => ''
    ],

    'twitter' => [    
        'client_id' => 'uoadBi3EVv0qe7u0qvaFL3tyL',  
        'client_secret' => 'CTHsW8esGgCeWst7cbLyglKnZWQ0naqitoHKaDyXhv4yGcMlRY',  
        'redirect' => ''
    ],

    'instagram' => [    
        'client_id' => '419250520421209',  
        'client_secret' => '6e4f6659ce87c21fd404b1f5dd85547e',  
        'redirect' => ''
    ],

    'linkedin' => [    
        'client_id' => '78qijqm0e7rkn7',  
        'client_secret' => 'bWu2MWhaqlW1KU49',  
        'redirect' => ''
    ],

    'google' => [    
        'client_id' => '847999625855-diptthq6nopqf6v1j4f9bqcdv9oi18tk.apps.googleusercontent.com',  
        'client_secret' => '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',  
        'redirect' => ''
    ],

];
