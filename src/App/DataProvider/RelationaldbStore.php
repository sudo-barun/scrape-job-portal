<?php

namespace App\DataProvider;

use App\Interfaces\DataStoreInterface;
use App\Interfaces\CrawledItemInterface;
use App\Interfaces\JobPortalInterface;
use App\Model\CrawledItem;
use Symfony\Component\DomCrawler\Crawler;

class RelationaldbStore implements DataStoreInterface
{
    public function getLastVersion(JobPortalInterface $jobPortal)
    {
        return CrawledItem::where([
            'job_portal' => $jobPortal->getPrefix(),
        ])->max('version');//select('max(version)','max')->get()->max;
    }

    public function saveCrawledItem(CrawledItemInterface $scrappedItem)
    {
        $crawledItem = (new CrawledItem())->fill([
            'content' => $scrappedItem->getContent(),
            'job_portal' => $scrappedItem->getJobPortal(),
            'page' => $scrappedItem->getPage(),
            'url' => $scrappedItem->getUrl(),
            'version' => $scrappedItem->getVersion(),
        ]);
        $crawledItem->save();
    }

    /**
     * @return CrawledItemInterface[]
     */
    public function getCrawledItems(JobPortalInterface $jobPortal)
    {
        $jobs = [];
        $crawledItems = CrawledItem::where([
            'job_portal' => $jobPortal->getPrefix(),
            'version' => $this->getLastVersion($jobPortal),
        ])->get()->all();

        foreach ($crawledItems as $crawledItem) {
            try {
                $scrappedJobs = $jobPortal->scrape(new Crawler($crawledItem->content));
            } catch (\InvalidArgumentException $ex) {
                error_log(sprintf('Failed to scrape: %s', $ex->getMessage()));
                continue;
            }
            $jobs = array_merge($jobs, $scrappedJobs);
        }

        return $jobs;
    }
}
