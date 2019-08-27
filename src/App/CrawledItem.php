<?php

namespace App;

use App\Interfaces\CrawledItemInterface;

class CrawledItem implements CrawledItemInterface
{
    protected $jobPortal;
    protected $version;
    protected $url;
    protected $page;
    protected $content;

    public function setJobPortal($jobPortal)
    {
        $this->jobPortal = $jobPortal;
        return $this;
    }

    public function getJobPortal()
    {
        return $this->jobPortal;
    }

    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }
}
