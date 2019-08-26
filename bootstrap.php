<?php

require_once __DIR__ . '/vendor/autoload.php';

define('APP_ROOT', __DIR__);

$dotenv = \Dotenv\Dotenv::create(__DIR__);
$dotenv->load();
