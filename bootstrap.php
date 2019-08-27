<?php

require_once __DIR__ . '/vendor/autoload.php';

define('APP_ROOT', __DIR__);


// Load environment variables
try {
    $dotenv = \Dotenv\Dotenv::create(__DIR__);
    $dotenv->load();
} catch (\Dotenv\Exception\InvalidPathException $ex) {
    //
}


/**
 * Setup database connection
 */
$capsuleManager = new Illuminate\Database\Capsule\Manager();

$capsuleManager->addConnection([
    'driver' => 'pgsql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'charset' => 'utf8',
    'prefix' => '',
    'schema' => 'public',
    'sslmode' => 'prefer',
]);

// Make this Capsule instance available globally via static methods
$capsuleManager->setAsGlobal();

// Setup the Eloquent ORM
$capsuleManager->bootEloquent();
