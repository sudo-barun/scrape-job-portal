<?php

namespace App\Command;

use App\Interfaces\JobPortal;
use App\Service\Jobsnepal;
use App\Service\Kathmandujobs;
use App\Service\Merojob;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class Scrape extends Command
{
    protected $mainUrl = 'https://www.jobsnepal.com/category/it-jobs';
    protected $client;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->client = new Client([
            'cookies' => true,
        ]);
    }

    protected function configure()
    {
        $this->setName('scrape');
        $this->setDescription('Scrape from job portals');
    }

    protected function fetchContent($url)
    {
        $response = $this->client->request('GET', $url);
        return $response->getBody()->getContents();
    }

    protected function storeContent($content, $filename)
    {
        $contentDir = config('app.content_dir');
        $segments = explode('/', $filename);
        $dir = $contentDir . '/' . join('/', array_slice($segments, 0, count($segments) - 1));
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents("$contentDir/$filename", $content);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var JobPortal[] $jobPortals */
        $jobPortals = [
            new Jobsnepal(),
            new Merojob(),
            new Kathmandujobs(),
        ];

        foreach ($jobPortals as $jobPortal) {
            echo "\n";
            echo sprintf('Scrapping %s (%s):', strtoupper($jobPortal->getPrefix()), $jobPortal->getBaseUrl());
            echo "\n";
            $hasNext = true;
            for ($i = 0; $hasNext; $i++) {
                $page = $i + 1;
                $url = $jobPortal->getUrl($page);

                echo "\n";
                echo sprintf('Fetching %s', $url);
                echo "\n";
                try {
                    $content = $this->fetchContent($url);
                } catch (ConnectException $ex) {
                    echo sprintf('Connection error occurred: %s', $ex->getMessage());
                    continue 2;
                }
                $filename = $jobPortal->getPrefix() . '/' . $page . '.html';
                $this->storeContent($content, $filename);

                $hasNext = $jobPortal->hasNext(new Crawler($content));
                sleep(config('app.sleep_duration_second'));
            }
            $jobPortal->getBaseUrl();
        }
    }
}
