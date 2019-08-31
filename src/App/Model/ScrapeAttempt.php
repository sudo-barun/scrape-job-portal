<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ScrapeAttempt extends Model
{
    public $timestamps = false;

    public function scrapeLogs()
    {
        return $this->hasMany(ScrapeLog::class, 'attempt_id');
    }
}
