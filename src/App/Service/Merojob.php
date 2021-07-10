<?php

namespace App\Service;

use App\Interfaces\JobPortalInterface;
use Symfony\Component\DomCrawler\Crawler;

class Merojob extends AbstractJobPortal implements JobPortalInterface
{
    protected $prefix = 'merojob';
    protected $baseUrl = 'https://merojob.com/category/it-telecommunication/';

    public function getName()
    {
        return 'Merojob';
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
        return $page === 1 ? $this->baseUrl : $this->baseUrl . '?' . http_build_query([ 'page' => $page]);
    }

    public function getLogoUrl()
    {
        return 'https://c.aayulogic.com.np/images/mj-logo.png';
    }

    public function hasNext(Crawler $crawler)
    {
        $next = $crawler->filter('#search_bar > .search-results > #search_job .pagination .pagination-next');
        $hasNext = !! $next->count();
        return $hasNext;
    }

    public function scrape(Crawler $crawler)
    {
        $jobs = [];
        $itemsCrawler = $crawler->filter('#search_bar > .search-results > #search_job .card');
        foreach ($itemsCrawler as $itemNode) {
            $itemCrawler = new Crawler($itemNode);
            if (! $itemCrawler->filter('.card-body')->count()) {
                // skip ad
                continue;
            }
            $titleCrawler = $itemCrawler->filter('.card-body .job-card h1 a');
            $link = $titleCrawler->count() ? $this->buildAbsoluteUrl($titleCrawler->attr('href')) : null;
            $skillsCrawler = $itemCrawler->filter('[itemprop=skills] > span');
            $jobs[] = [
                'title' => $titleCrawler->count() ? trim($titleCrawler->attr('title')) : null,
                'link' => $link,
                'company' => $this->getCompany($itemCrawler),
                'type' => null,
                'posted_on' => $itemCrawler->filter('.card-footer meta[itemprop="datePosted"]')->attr('content'),
                'expires_on' => $itemCrawler->filter('.card-footer meta[itemprop="validThrough"]')->attr('content'),
                'address' => $this->getAddress($itemCrawler),
                'skills' => $skillsCrawler->each(function (Crawler $crawler) {
                    return $crawler->text();
                }),
            ];
        }
        return $jobs;
    }

    protected function getCompany(Crawler $jobItemCrawler)
    {
        $companyCrawler = $jobItemCrawler->filter('.card-body .job-card h3');
        if (! $companyCrawler->count()) {
            return [
                'title' => null,
                'link' => null,
            ];
        }
        $companyLinkCrawler = $companyCrawler->filter('a');
        $title = trim($companyCrawler->text());
        $link = $companyLinkCrawler->count() ? $this->buildAbsoluteUrl($companyLinkCrawler->attr('href')) : null;
        return compact('title', 'link');
    }

    protected function getAddress(Crawler $jobItemCrawler)
    {
        $crawler = $jobItemCrawler->filter('.card-body .job-card .location meta[itemprop="addressRegion"]');
        return $crawler->count()
            ? $crawler->attr('content')
            : null;
    }

    protected function buildAbsoluteUrl($url)
    {
        if (1 === preg_match('/^(http(s))/', $url)) {
            return $url;
        }
        $parsed = parse_url($this->baseUrl);
        $link = "{$parsed['scheme']}://{$parsed['host']}";
        $link = join('/', [ $link, ltrim($url, '/') ]);
        return $link;
    }
}
