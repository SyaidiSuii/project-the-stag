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

    'toyyibpay' => [
        'secret_key' => env('TOYYIBPAY_SECRET_KEY'),
        'category_code' => env('TOYYIBPAY_CATEGORY_CODE'),
        'base_url' => env('TOYYIBPAY_BASE_URL', 'https://dev-toyyibpay.com/index.php/api'),
    ],

     /*
    |--------------------------------------------------------------------------
    | AI Recommendation Service Configuration
    |--------------------------------------------------------------------------
    */

    'ai_recommender' => [
        'base_url' => env('AI_RECOMMENDER_BASE_URL', 'http://localhost:8000'),
        'timeout' => env('AI_RECOMMENDER_TIMEOUT', 30),
        'enabled' => env('AI_RECOMMENDER_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Groq AI Chatbot Configuration
    |--------------------------------------------------------------------------
    */

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
        'model' => env('GROQ_MODEL', 'llama3-8b-8192'),
        'timeout' => env('GROQ_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging (FCM) Configuration
    |--------------------------------------------------------------------------
    */

    'fcm' => [
        'enabled' => env('NOTIFICATIONS_ENABLED', true),
        'api_key' => env('FIREBASE_API_KEY'),
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'database_url' => env('FIREBASE_DATABASE_URL'),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'app_id' => env('FIREBASE_APP_ID'),
        'vapid_key' => env('FIREBASE_VAPID_KEY'),
        'service_account_path' => env('FIREBASE_SERVICE_ACCOUNT_PATH'),
    ],

];
