<?php

namespace App;

use App\Interfaces\JobPortalInterface;
use App\Model\ScrapeLog;
use Symfony\Component\DomCrawler\Crawler;

class Store
{
    public function getLastVersion(JobPortalInterface $jobPortal)
    {
        return ScrapeLog::where([
            'job_portal' => $jobPortal->getPrefix(),
        ])->max('version');//select('max(version)','max')->get()->max;
    }

    public function getJobsOfJobPortal(JobPortalInterface $jobPortal)
    {
        $jobs = [];
        $crawledItems = ScrapeLog::where([
            'job_portal' => $jobPortal->getPrefix(),
            'version' => $this->getLastVersion($jobPortal),
        ])->get()->all();

        foreach ($crawledItems as $crawledItem) {
            try {
                $response = \GuzzleHttp\Psr7\parse_response($crawledItem->response);
                $scrappedJobs = $jobPortal->scrape(new Crawler((string) $response->getBody()));
            } catch (\InvalidArgumentException $ex) {
                error_log(sprintf('Failed to scrape: %s', $ex->getMessage()));
                continue;
            }
            $jobs = array_merge($jobs, $scrappedJobs);
        }

        return $jobs;
    }
}
