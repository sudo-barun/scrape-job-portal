<?php

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;

class Kathmandujobs extends AbstractJobPortal
{
    protected $prefix = 'kathmandujobs';
    protected $baseUrl = 'https://kathmandujobs.com/jobs/mobile-web-software-development/';

    public function getName()
    {
        return 'KathmanduJobs';
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getUrl($page)
    {
        return $page === 1 ? $this->baseUrl : $this->baseUrl . '?' . http_build_query([ 'job_page' => $page ]);
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getLogoUrl()
    {
        return 'https://kathmandujobs.com/assets/img/favicon.png';
    }

    public function scrape(Crawler $crawler)
    {
        $jobs = [];
        $filtered = $crawler->filter('.careerfy-wrapper > .careerfy-main-content [id^=post-] .careerfy-entry-content [id^=Job-content-] [id^=jobsearch-data-job-content-] [id^=jobsearch-job-] ul.row > li');
        foreach ($filtered as $row) {
            $itemCrawler = new Crawler($row);
            $jobTitleCrawler = $itemCrawler->filter('h2 a');
            $companyCrawler = $itemCrawler->filter('.careerfy-company-name a');
            $company = trim($companyCrawler->text());
            $company = ltrim(ltrim($company, '@'));
            $jobs[] = [
                'title' => trim($jobTitleCrawler->text()),
                'link' => $jobTitleCrawler->attr('href'),
                'company' => [
                    'title' => $company,
                    'link' => $companyCrawler->attr('href'),
                ],
                'type' => $itemCrawler->filter('h2 span')->text(),
                'posted_on' => $this->getPostedOn($itemCrawler),
                'expires_on' => $this->getExpiresOn($itemCrawler),
                'address' => $this->getAddress($itemCrawler),
            ];
        }
        return $jobs;
    }

    protected function getAddress(Crawler $jobItemCrawler)
    {
        $crawler = $jobItemCrawler->filter('.careerfy-joblisting-text > small .careerfy-map-pin');
        if (! $crawler->count()) {
            return null;
        }
        $address = trim($crawler->getNode(0)->parentNode->textContent);
        return $address;
    }

    protected function getPostedOn(Crawler $jobItemCrawler)
    {
        $crawler = $jobItemCrawler->filter('.careerfy-joblisting-view4-date');
        return $crawler->count() ? $crawler->text() : null;
    }

    protected function getExpiresOn(Crawler $jobItemCrawler)
    {
        $crawler = $jobItemCrawler->filter('.careerfy-joblisting-text > small');
        foreach ($crawler as $spanNode) {
            $cleanInnerText = trim($spanNode->textContent);
            if (strpos($cleanInnerText, 'Deadline') === 0) {
                return trim(substr($cleanInnerText, strlen('Deadline')));
            }
        }
        return null;
    }

    public function hasNext(Crawler $crawler)
    {
        $next = $crawler->filter('.careerfy-wrapper > .careerfy-main-content [id^=post-] .careerfy-entry-content [id^=Job-content-] .jobsearch-pagination-blog a.next.jobsearch-page-numbers');
        $hasNext = !! $next->count();
        return $hasNext;
    }
}
