<?php

namespace App\Interfaces;

interface DataStoreInterface
{
    public function saveCrawledItem(CrawledItemInterface $scrappedItem);

    /**
     * @return CrawledItemInterface[]
     */
    public function getCrawledItems(JobPortalInterface $jobPortal);

    public function getLastVersion(JobPortalInterface $jobPortal);
}
