<?php
return [
    'google' => ['client_id' => env('GOOGLE_CLIENT_ID')],
    'google_drive' => [
        'service_account_json' => env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON'),
        'folder_id' => env('GOOGLE_DRIVE_FOLDER_ID'),
        'shared_drive_id' => env('GOOGLE_DRIVE_SHARED_DRIVE_ID'),
    ],
    'facebook' => [
        'app_id' => env('FACEBOOK_APP_ID'),
        'app_secret' => env('FACEBOOK_APP_SECRET'),
        'graph_version' => env('FACEBOOK_GRAPH_VERSION', 'v23.0'),
    ],
];
