<?php
include $_SERVER['DOCUMENT_ROOT']."/config/db_settings.php";
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production

        // Database
        'db' => [
            'database_type' => 'mysql',
            'database_name' => db_name,
            'server' => db_host,
            'username' => db_user,
            'password' => db_password,
            'charset' => 'utf8'
        ],

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],
        
        // Sendgrid Settings
        'sendgrid' => [
            'apikey' => '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',
        ],     
        
//        // SMS Settings
       'sms' => [
           'authId' => 'MAZGI5YJZHOTQ5ZDRLOD',
           'authToken' => 'OGY5MDQzZmY1MGJhY2VlZGQ1NmVlY2JjNDViNmU0'
       ],
//        
//        // Google Maps API
//        'googlemaps' => [
//            'apiKey' => 'AIzaSyCjb95hDgrrez5cDy-mfqKk-wg_4LgaYHY'
//        ],
        
        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],
    ],
];