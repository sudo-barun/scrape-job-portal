<?php

namespace App\Service;

use App\Interfaces\JobPortal;

abstract class AbstractJobPortal implements JobPortal
{
    public function getContentFiles()
    {
        $files = [];
        for ($i = 0; ; $i++) {
            $page = $i + 1;
            $fileToScrape = config('app.content_dir') . '/' . $this->getPrefix() .  "/$page.html";
            if (!file_exists($fileToScrape)) {
                break;
            }
            $files[] = $fileToScrape;
        }

        return $files;
    }
}
