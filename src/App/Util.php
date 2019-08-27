<?php

namespace App;

use App\Interfaces\DataStoreInterface;

class Util
{
    /**
     * @return DataStoreInterface
     */
    public static function getDataStore()
    {
        switch (config('app.data_store')) {
            case 'filesystem':
                $dataProvider = new \App\DataProvider\FilesystemStore();
                break;
            case 'relationaldb':
                $dataProvider = new \App\DataProvider\RelationaldbStore();
                break;
            default:
                throw new \RuntimeException('Invalid data-provider');
        }

        return $dataProvider;
    }
}
