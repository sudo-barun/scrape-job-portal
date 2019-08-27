<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../bootstrap.php';

$dataStore = \App\Util::getDataStore();

$jobService = new \App\Service\Job($dataStore);
$portalJobs = $jobService->getAll($_GET);
$q = $_GET['q'] ?? '';

include __DIR__ . '/content.php';
