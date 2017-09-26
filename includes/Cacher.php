<?php

class Cacher {
    protected $cacheData;
    protected $cacheExpiry;
    protected $correspondenceCacheData;
    protected $correspondenceCacheExpiry;

    public function __construct()
    {
        $config = getConfig();

        $this->cacheData = $config['cachedPath'];
        $this->cacheExpiry = $config['expiryInfoPath'];
        $this->correspondenceCacheExpiry = $config['correspondenceCacheExpiry'];
        $this->correspondenceCacheData = $config['correspondenceCacheData'];

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

        if (!file_exists($this->correspondenceCacheExpiry)) {
            file_put_contents($this->correspondenceCacheExpiry, '');
        }

        if (!is_writable($this->correspondenceCacheExpiry)) {
            chmod($this->correspondenceCacheExpiry, 0777);
        }

        if (!file_exists($this->correspondenceCacheData)) {
            file_put_contents($this->correspondenceCacheData, '');
        }

        if (!is_writable($this->correspondenceCacheData)) {
            chmod($this->correspondenceCacheData, 0777);
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

    public function isCorrespondencesExpired()
    {
        $expiryTime = file_get_contents($this->correspondenceCacheExpiry);

        return time() > $expiryTime;
    }

    public function isExpired()
    {
        $expiryTime = file_get_contents($this->cacheExpiry);

        return time() > $expiryTime;
    }
}
