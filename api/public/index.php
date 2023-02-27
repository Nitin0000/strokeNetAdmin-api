<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/sendgrid/sendgrid-php.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../config/settings.php';
require __DIR__ . '/../config/constants.php';


$app = new \Slim\App($settings);

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, userId, userToken')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});


// Set up dependencies
require __DIR__ . '/../config/dependencies.php';

// Register middleware
require __DIR__ . '/../config/middleware.php';

// Register routes
require __DIR__ . '/../config/routes.php';

// Run app
$app->run();
