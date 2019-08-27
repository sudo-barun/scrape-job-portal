<?php

return [
    'content_dir' => APP_ROOT . '/content',
    'sleep_duration_second' => env('SLEEP_DURATION_SECOND', 2),
    'data_store' => env('DATA_STORE', 'filesystem'), // possible values: filesystem, relationaldb
];
