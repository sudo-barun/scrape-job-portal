<?php

namespace App\Interfaces;

interface CrawledItemInterface
{
    public function getJobPortal();

    public function getVersion();

    public function getUrl();

    public function getPage();

    public function getContent();
}
