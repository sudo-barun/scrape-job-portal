<?php

namespace App\Service;

use App\Interfaces\DataStoreInterface;
use App\Interfaces\JobPortalInterface;

class Job
{
    protected $dataProvider;

    public function __construct(DataStoreInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
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
            new Merojob(),
            new Jobsnepal(),
            new Kathmandujobs(),
        ];

        foreach ($jobPortals as $jobPortal) {
            $jobs = $this->getJobsOfJobPortal($jobPortal);
            $jobs = $this->filterByTerms($jobs, $terms);
            $jobPortalsJobs[$jobPortal->getPrefix()] = [
                'jobs' => $jobs,
                'logo' => $jobPortal->getLogoUrl(),
            ];
        }

        return $jobPortalsJobs;
    }

    protected function getJobsOfJobPortal(JobPortalInterface $jobPortal)
    {
        $crawledItems = $this->dataProvider->getCrawledItems($jobPortal);
        return $crawledItems;
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
