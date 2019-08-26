<?php

namespace App\Interfaces;

use Symfony\Component\DomCrawler\Crawler;

interface JobPortal
{
    public function getLogoUrl();

    public function getBaseUrl();

    public function getPrefix();

    public function getContentFiles();

    public function getUrl($page);

    public function hasNext(Crawler $crawler);

    public function scrape(Crawler $crawler);
}
