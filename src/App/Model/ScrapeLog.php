<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ScrapeLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'job_portal',
        'version',
        'url',
        'page',
        'content',
    ];

    public function scrapeAttempt()
    {
        return $this->belongsTo(ScrapeAttempt::class, 'attempt_id');
    }
}
