<?php

namespace App\Service;

use App\Interfaces\JobPortalInterface;
use Symfony\Component\DomCrawler\Crawler;

class Jobsnepal extends AbstractJobPortal implements JobPortalInterface
{
    protected $prefix = 'jobsnepal';
    protected $baseUrl = 'https://www.jobsnepal.com/category/information-technology-jobs';

    public function getName()
    {
        return 'JobsNepal';
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getUrl($page)
    {
        return $page === 1 ? $this->baseUrl : $this->baseUrl . '?' . http_build_query([ 'page' => $page ]);
    }

    public function getLogoUrl()
    {
        return 'https://www.jobsnepal.com/assets/front/images/favicons/android-icon-192x192.png';
    }

    public function hasNext(Crawler $crawler)
    {
        $next = $crawler->filter('.pagination > .page-item:last-child a');
        $hasNext = !! $next->count();
        return $hasNext;
    }

    public function scrape(Crawler $crawler)
    {
        $jobs = [];
        $filtered = $crawler->filter('#app .top-content .card');
        foreach ($filtered as $row) {
            $itemCrawler = new Crawler($row);
            $jobTitle = $itemCrawler->filter('.job-title > a');
            $addressCrawler = $itemCrawler->filter('.job-title + ul > li:nth-child(2) > div > div');
            $companyCrawler = $itemCrawler->filter('.job-title + ul > li:nth-child(1) > div > div');
            $jobs[] = [
                'title' => trim($jobTitle->text()),
                'link' => $jobTitle->attr('href'),
                'company' => [
                    'title' => trim($companyCrawler->text()),
                    'link' => $companyCrawler->attr('href'),
                ],
                'type' => null,
                'posted_on' => null,
                'expires_on' => null,
                'address' => $addressCrawler->count() ? $addressCrawler->text() : null,
            ];
        }
        return $jobs;
    }
}
