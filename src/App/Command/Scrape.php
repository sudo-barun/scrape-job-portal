<?php

namespace App\Command;

use App\CrawledItem;
use App\Interfaces\JobPortalInterface;
use App\Service\Jobsnepal;
use App\Service\Kathmandujobs;
use App\Service\Merojob;
use App\Util;
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var JobPortalInterface[] $jobPortals */
        $jobPortals = [
            new Jobsnepal(),
            new Merojob(),
            new Kathmandujobs(),
        ];

        foreach ($jobPortals as $jobPortal) {

            $dataStore = Util::getDataStore();
            $lastVersion = $dataStore->getLastVersion($jobPortal);

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
                $crawledItem = (new CrawledItem())
                    ->setContent($content)
                    ->setJobPortal($jobPortal->getPrefix())
                    ->setPage($page)
                    ->setUrl($url)
                    ->setVersion($lastVersion + 1)
                ;
                $dataStore->saveCrawledItem($crawledItem);

                $hasNext = $jobPortal->hasNext(new Crawler($content));
                sleep(config('app.sleep_duration_second'));
            }
            $jobPortal->getBaseUrl();
        }
    }
}
