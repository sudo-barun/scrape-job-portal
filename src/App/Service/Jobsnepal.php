<?php

namespace App\Service;

use App\Interfaces\JobPortalInterface;
use Symfony\Component\DomCrawler\Crawler;

class Jobsnepal extends AbstractJobPortal implements JobPortalInterface
{
    protected $prefix = 'jobsnepal';
    protected $baseUrl = 'https://www.jobsnepal.com/category/it-jobs';

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
        return $page === 1 ? $this->baseUrl : $this->baseUrl . '/page-' . $page;
    }

    public function getLogoUrl()
    {
        return 'https://www.jobsnepal.com/assets/front/images/favicons/android-icon-192x192.png';
    }

    public function hasNext(Crawler $crawler)
    {
        $next = $crawler->filter('#main-content #pagination-ctrl-block .pagination-next');
        $hasNext = !! $next->count();
        return $hasNext;
    }

    public function scrape(Crawler $crawler)
    {
        $jobs = [];
        $filtered = $crawler->filter('#main-content .job-listing table > tr.row');
        foreach ($filtered as $row) {
            $itemCrawler = new Crawler($row);
            $jobTitle = $itemCrawler->filter('.job-item');
            $jobs[] = [
                'title' => trim($jobTitle->text()),
                'link' => $jobTitle->attr('href'),
                'company' => [
                    'title' => trim($itemCrawler->filter('td:nth-child(2)')->text()),
                    'link' => $itemCrawler->filter('td:nth-child(2) a')->attr('href'),
                ],
                'type' => $itemCrawler->filter('td:nth-child(3)')->text(),
                'posted_on' => null,
                'expires_on' => $itemCrawler->filter('td:nth-child(4)')->text(),
                'address' => null,
            ];
        }
        return $jobs;
    }
}
