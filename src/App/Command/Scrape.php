<?php

namespace App\Command;

use App\Model\ScrapeLog;
use App\Store;
use App\Interfaces\JobPortalInterface;
use App\Model\ScrapeAttempt;
use App\Service\Jobsnepal;
use App\Service\Kathmandujobs;
use App\Service\Merojob;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class Scrape extends Command
{
    protected $mainUrl = 'https://www.jobsnepal.com/category/it-jobs';
    protected $client;
    protected $store;
    protected $container;
    protected $history;
    protected $stack;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->store = new Store();
        $this->container = [];
        $this->history = \GuzzleHttp\Middleware::history($this->container);
        $this->stack = \GuzzleHttp\HandlerStack::create();
        // Add the history middleware to the handler stack.
        $this->stack->push($this->history);
        $this->client = new Client([
            'cookies' => true,
            'handler' => $this->stack,
        ]);
    }

    protected function configure()
    {
        $this->setName('scrape');
        $this->setDescription('Scrape from job portals');
    }

    protected function fetchContent($url)
    {
        $request = new Request('GET', $url);
        $request = $request->withRequestTarget((string) $request->getUri());
        $response = $this->client->send($request);
        return $response;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var JobPortalInterface[] $jobPortals */
        $jobPortals = [
            new Jobsnepal(),
            new Merojob(),
            new Kathmandujobs(),
        ];

        $scrapeAttempt = new ScrapeAttempt();
        $scrapeAttempt->started_at = new DateTime();
        $scrapeAttempt->completed_at = null;
        $scrapeAttempt->save();

        foreach ($jobPortals as $jobPortal) {

            echo "\n";
            echo sprintf('Scrapping %s (%s):', strtoupper($jobPortal->getPrefix()), $jobPortal->getBaseUrl());
            echo "\n";
            $hasNext = true;
            for ($i = 0; $hasNext; $i++) {
                $page = $i + 1;
                $url = $jobPortal->getUrl($page);

                $scrapeLog = new ScrapeLog();
                $scrapeLog->attempt_id = $scrapeAttempt->id;
                $scrapeLog->job_portal = $jobPortal->getPrefix();
                $scrapeLog->url = $url;
                $scrapeLog->started_at = new DateTime();
                $scrapeLog->completed_at = null;
                $scrapeLog->success = null;
                $scrapeLog->request = null;
                $scrapeLog->response = null;
                $scrapeLog->error = null;

                echo "\n";
                echo sprintf('Fetching %s', $url);
                echo "\n";
                try {
                    $response = $this->fetchContent($url);
                    $request = array_values(array_slice($this->container, -1))[0]['request'];
                    $scrapeLog->completed_at = new DateTime();
                    $scrapeLog->request = \GuzzleHttp\Psr7\str($request);
                    $scrapeLog->response = \GuzzleHttp\Psr7\str($response);
                    $scrapeLog->success = true;
                    $scrapeLog->error = null;
                    $scrapeLog->save();
                } catch (RequestException $ex) {
                    echo sprintf('Error occurred: %s', $ex->getMessage());
                    $request = array_values(array_slice($this->container, -1))[0]['request'];
                    $response = $ex->getResponse();
                    $scrapeLog->completed_at = new DateTime();
                    $scrapeLog->request = \GuzzleHttp\Psr7\str($request);
                    $scrapeLog->response = \GuzzleHttp\Psr7\str($response);
                    $scrapeLog->success = false;
                    $scrapeLog->error = $ex->getTraceAsString();
                    $scrapeLog->save();
                    continue 2;
                }

                echo "\n";
                echo sprintf('Fetch successful', $url);
                echo "\n";

                $hasNext = $jobPortal->hasNext(new Crawler((string) $response->getBody()));
                sleep(config('app.sleep_duration_second'));
            }
            $jobPortal->getBaseUrl();
        }

        $scrapeAttempt->completed_at = new \DateTime();
        $scrapeAttempt->save();
    }
}
