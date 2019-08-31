<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../bootstrap.php';

$jobService = new \App\Service\Job();
$portalsData = $jobService->getAll($_GET);
$lastAttempts = array_reduce($portalsData, function ($acc, $portalData) {
    $lastAttempt = $portalData['lastAttempt'];
    foreach ($acc as $attempt) {
        if ($attempt->id === $lastAttempt->id) {
            return $acc;
        }
    }
    $acc[] = $lastAttempt;
    return $acc;
}, []);
$lastAttemptMap = array_reduce($lastAttempts, function ($acc, $lastAttempt) use (&$portalsData) {
    $filteredPortalsData = array_filter($portalsData, function ($portalData) use ($lastAttempt) {
        return $portalData['lastAttempt']->id === $lastAttempt->id;
    });
    $dateTime = (new \DateTime($lastAttempt->started_at, new DateTimeZone('UTC')))
        ->setTimezone(new DateTimeZone('Asia/Kathmandu'));
    $dateTimeStr = $dateTime->format(DATE_ATOM/*'Y-m-d H:i:s'*/);
    $acc[$dateTimeStr] = array_merge(
        $acc[$dateTimeStr] ?? [],
        array_keys($filteredPortalsData)
    );
    return $acc;
}, []);
$q = $_GET['q'] ?? '';

include __DIR__ . '/content.php';
