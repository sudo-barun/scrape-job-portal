<?php

namespace App\DataProvider;

use App\Interfaces\DataStoreInterface;
use App\Interfaces\JobPortalInterface;
use App\Interfaces\CrawledItemInterface;
use App\Util;
use Symfony\Component\DomCrawler\Crawler;

class FilesystemStore implements DataStoreInterface
{
    public function getLastVersion(JobPortalInterface $jobPortal)
    {
        $jobPortalDir = APP_ROOT . '/content/' . $jobPortal->getPrefix();
        $dirs = array_values(array_filter(glob("$jobPortalDir/*"), 'is_dir'));
        $versions = array_map('basename', $dirs);
        $versions = array_map('intval', $versions);
        return $versions ? max($versions) : 0;
    }

    public function saveCrawledItem(CrawledItemInterface $scrappedItem)
    {
        $filename = $scrappedItem->getJobPortal()
            . '/' . $scrappedItem->getVersion()
            . '/' . $scrappedItem->getPage() . '.html';
        $this->storeContent($scrappedItem->getContent(), $filename);
    }

    public function getCrawledItems(JobPortalInterface $jobPortal)
    {
        $jobs = [];
        $files = $this->getContentFiles($jobPortal);
        foreach ($files as $file) {
            $content = file_get_contents($file);
            try {
                $scrappedJobs = $jobPortal->scrape(new Crawler($content));
            } catch (\InvalidArgumentException $ex) {
                error_log(sprintf('Failed to scrape: %s', $ex->getMessage()));
                continue;
            }
            $jobs = array_merge($jobs, $scrappedJobs);
        }
        return $jobs;
    }

    protected function storeContent($content, $filename)
    {
        $contentDir = config('app.content_dir');
        $segments = explode('/', $filename);
        $dir = $contentDir . '/' . join('/', array_slice($segments, 0, count($segments) - 1));
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents("$contentDir/$filename", $content);
    }

    protected function getContentFiles(JobPortalInterface $jobPortal)
    {
        $files = [];
        for ($i = 0; ; $i++) {
            $page = $i + 1;
            $fileToScrape = config('app.content_dir')
                . '/' . $jobPortal->getPrefix()
                . '/' . Util::getDataStore()->getLastVersion($jobPortal)
                . '/' . "$page.html";
            if (!file_exists($fileToScrape)) {
                break;
            }
            $files[] = $fileToScrape;
        }

        return $files;
    }
}
