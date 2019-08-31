<?php

namespace App;

use App\Interfaces\JobPortalInterface;
use App\Model\ScrapeAttempt;
use App\Model\ScrapeLog;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\DomCrawler\Crawler;

class Store
{
    public function getLastScrapeAttempt(JobPortalInterface $jobPortal)
    {
        return ScrapeAttempt::whereHas('scrapeLogs', function (Builder $query) use ($jobPortal) {
            $query->where('job_portal', $jobPortal->getPrefix());
        })->whereNotNull('completed_at')->orderBy('started_at', 'desc')->first();
    }

    public function getJobsOfJobPortal(JobPortalInterface $jobPortal)
    {
        $jobs = [];
        $lastAttempt = $this->getLastScrapeAttempt($jobPortal);
        $scrapeLogs = ScrapeLog::where([
            'job_portal' => $jobPortal->getPrefix(),
            'attempt_id' => $lastAttempt->id,
        ])->get()->all();

        foreach ($scrapeLogs as $scrapeLog) {
            try {
                $response = \GuzzleHttp\Psr7\parse_response($scrapeLog->response);
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
