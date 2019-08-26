<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../bootstrap.php';

$jobService = new \App\Service\Job();
$portalJobs = $jobService->getAll($_GET);
$q = $_GET['q'] ?? '';

include __DIR__ . '/content.php';
