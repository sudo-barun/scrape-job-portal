<?php

namespace App\Model;

use App\Interfaces\CrawledItemInterface;
use Illuminate\Database\Eloquent\Model;

class CrawledItem extends Model implements CrawledItemInterface
{
    public $timestamps = false;

    protected $fillable = [
        'job_portal',
        'version',
        'url',
        'page',
        'content',
    ];

    public function getJobPortal()
    {
        return $this->job_portal;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getContent()
    {
        return $this->content;
    }
}
