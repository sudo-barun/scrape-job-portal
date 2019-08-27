<?php

namespace App\Command;

use App\Util;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Job extends Command
{
    protected $job;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->job = new \App\Service\Job(Util::getDataStore());
    }

    protected function configure()
    {
        $this->setName('job');
        $this->setDescription('List job entries from the scrapped data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobPortalsData = $this->job->getAll();

        foreach ($jobPortalsData as $jobPortal => $jobPortalData) {
            echo "\n\n" . strtoupper($jobPortal) . "\n";
            foreach ($jobPortalData['jobs'] as $i => $job) {
                echo sprintf("\n%s. %s | %s | %s \n", $i + 1, $job['title'], $job['company']['title'] ?? '', $job['link']);
            }
        }
    }
}
