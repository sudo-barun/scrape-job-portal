<?php

require_once __DIR__ . '/bootstrap.php';

$application = new \Symfony\Component\Console\Application();
$application->add(new \App\Command\Scrape());
$application->add(new \App\Command\Job());
$application->run();
