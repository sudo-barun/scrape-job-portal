<?php

if (! function_exists('env')) {

    /**
     * Get the value of an environment variable
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        return $value;
    }
}

if (! function_exists('config')) {

    /**
     * Get the configuration value from config file
     * For example:
     * config('database.host') loads config value from database.php using 'host' as the key
     *
     * @param  string $key
     * @return mixed
     */
    function config($key)
    {
        static $config = null;
        if ($config === null) {
            $config = \Northwoods\Config\ConfigFactory::make(
                [
                    'directory' => APP_ROOT . '/config',
                ]
            );
        }

        return $config->get($key);
    }
}
