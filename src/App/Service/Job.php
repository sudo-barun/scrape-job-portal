<?php

namespace App\Service;

use App\Store;
use App\Interfaces\JobPortalInterface;

class Job
{
    protected $store;

    public function __construct()
    {
        $this->store = new Store();
    }

    public function splitQueryTerms($query)
    {
        return array_filter(explode(' ', $query));
    }

    public function getAll(array $queryParams = [])
    {
        $query = $queryParams['q'] ?? '';
        $terms = $this->splitQueryTerms($query);

        $jobPortalsJobs = [];

        /** @var JobPortalInterface[] $jobPortals */
        $jobPortals = [
            new Jobsnepal(),
            new Merojob(),
            new Kathmandujobs(),
        ];

        foreach ($jobPortals as $jobPortal) {
            $lastAttempt = $this->store->getLastScrapeAttempt($jobPortal);
            $jobs = $this->store->getJobsOfJobPortal($jobPortal);
            $jobs = $this->filterByTerms($jobs, $terms);
            $jobPortalsJobs[$jobPortal->getPrefix()] = [
                'name' => $jobPortal->getName(),
                'lastAttempt' => $lastAttempt,
                'jobs' => $jobs,
                'logo' => $jobPortal->getLogoUrl(),
            ];
        }

        return $jobPortalsJobs;
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
            return (
                $matches($job['title'])
                ||
                $matches($job['type'])
                ||
                $matches($job['address'])
            );
        }));

        return $jobs;
    }
}
