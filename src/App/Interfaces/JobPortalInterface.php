<?php

namespace App\Interfaces;

use Symfony\Component\DomCrawler\Crawler;

interface JobPortalInterface
{
    public function getName();

    public function getLogoUrl();

    public function getBaseUrl();

    public function getPrefix();

    public function getUrl($page);

    public function hasNext(Crawler $crawler);

    public function scrape(Crawler $crawler);
}
