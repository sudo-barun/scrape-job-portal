<?php

namespace App\Service;

use App\Interfaces\JobPortal;
use Symfony\Component\DomCrawler\Crawler;

class Job
{
    public function splitQueryTerms($query)
    {
        return array_filter(explode(' ', $query));
    }

    public function getAll(array $queryParams = [])
    {
        $query = $queryParams['q'] ?? '';
        $terms = $this->splitQueryTerms($query);

        $jobPortalsJobs = [];

        /** @var JobPortal[] $jobPortals */
        $jobPortals = [
            new Merojob(),
            new Jobsnepal(),
            new Kathmandujobs(),
        ];

        foreach ($jobPortals as $jobPortal) {
            $jobs = $this->getJobsOfJobPortal($jobPortal, $terms);
            $jobs = $this->filterByTerms($jobs, $terms);
            $jobPortalsJobs[$jobPortal->getPrefix()] = [
                'jobs' => $jobs,
                'logo' => $jobPortal->getLogoUrl(),
            ];
        }

        return $jobPortalsJobs;
    }

    protected function getJobsOfJobPortal(JobPortal $jobPortal)
    {
        $jobs = [];
        $files = $jobPortal->getContentFiles();
        foreach ($files as $file) {
            $content = file_get_contents($file);
            try {
                $scrappedJobs = $jobPortal->scrape(new Crawler($content));
            } catch (\InvalidArgumentException $ex) {
                error_log(sprintf('Failed to scrape: %s', $ex->getMessage()));
                continue;
            }
            $jobs = array_merge($jobs, $scrappedJobs);
        }
        return $jobs;
    }

    public function filterByTerms($jobs, $terms)
    {
        $jobs = array_values(array_filter($jobs, function ($job) use ($terms) {
            $regexTerms = array_map(function ($term) {
                return preg_quote($term);
            }, $terms);
            $matches = function ($text) use (&$regexTerms) {
                return 1 === preg_match("#(" .join('|', $regexTerms) . ")#i", $text);
            };
            return $matches($job['title']) || $matches($job['company']['title']);
        }));

        return $jobs;
    }
}
