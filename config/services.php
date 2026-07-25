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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI (AI Motivation / Chat)
    |--------------------------------------------------------------------------
    |
    | Optional for the MVP: when 'key' is empty, App\Services\Ai\OpenAiClient
    | never calls out to OpenAI and callers fall back to static templates.
    |
    */

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Addis AI (Amharic / Afan Oromo voice)
    |--------------------------------------------------------------------------
    |
    | Text-to-speech (/api/v1/voice/generations) and speech-to-text
    | (/api/v2/stt). Optional: when 'key' is empty, voice features are
    | disabled without breaking the rest of the app.
    |
    */

    'addis_ai' => [
        'key' => env('ADDIS_AI_API_KEY'),
        'base_url' => env('ADDIS_AI_BASE_URL', 'https://api.addisassistant.com'),
        'voice_id' => env('ADDIS_AI_VOICE_ID', 'am-hamen'),
        'language' => env('ADDIS_AI_LANGUAGE', 'am'),
    ],

];
