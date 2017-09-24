<?php

class Cacher {
    protected $cacheData;
    protected $cacheExpiry;

    public function __construct()
    {
        $config = getConfig();

        $this->cacheData = $config['cachedPath'];
        $this->cacheExpiry = $config['expiryInfoPath'];

        if (!file_exists($this->cacheData)) {
            file_put_contents($this->cacheData, '');
        }

        if (!is_writable($this->cacheData)) {
            chmod($this->cacheData, 0777);
        }

        if (!file_exists($this->cacheExpiry)) {
            file_put_contents($this->cacheExpiry, '');
        }

        if (!is_writable($this->cacheExpiry)) {
            chmod($this->cacheExpiry, 0777);
        }
    }

    public function setResults($results)
    {
        file_put_contents($this->cacheData, json_encode($results));
    }

    public function getResults()
    {
        return json_decode(file_get_contents($this->cacheData));
    }

    public function extendExpiry()
    {
        $config = getConfig();

        file_put_contents($this->cacheExpiry, time() + $config['expiryTime']);
    }

    public function isExpired()
    {
        $expiryTime = file_get_contents($this->cacheExpiry);

        return time() > $expiryTime;
    }
}
