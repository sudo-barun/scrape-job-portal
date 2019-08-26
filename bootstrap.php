<?php

require_once __DIR__ . '/vendor/autoload.php';

define('APP_ROOT', __DIR__);

try {
    $dotenv = \Dotenv\Dotenv::create(__DIR__);
    $dotenv->load();
} catch (\Dotenv\Exception\InvalidPathException $ex) {
    //
}
