<?php

return [

    /*
    |--------------------------------------------------------------------------
    | True Footage Custom Configurations
    |--------------------------------------------------------------------------
    |
    | Add settings to manage custom configurations for TF application code.
    |
    */

    'default' => [
        'cli' => [
            'mls' => [
                'incoming_file_base_path' => env('MLS_CLI_INCOMING_FILE_DEFAULT_BASE_PATH', '/tmp'),
            ],
        ],
    ],

];
