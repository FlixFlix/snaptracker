<?php

class TokenStorage {
    protected $tokenPath;

    public function __construct()
    {
        $config = getConfig();

        $this->tokenPath = $config['tokenPath'];

        if (!file_exists($this->tokenPath)) {
            file_put_contents($this->tokenPath, '');
        }

        if (!is_writable($this->tokenPath)) {
            @chmod($this->tokenPath, 0777);
        }
    }

    public function getToken() {
        $tokenData = $this->extractTokenData();

        return $tokenData[0];
    }

    public function getTokenExpiryTime() {
        $tokenData = $this->extractTokenData();

        return $tokenData[1];
    }

    public function getRefreshToken() {
        $tokenData = $this->extractTokenData();

        return $tokenData[2];
    }

    public function setToken($token) {
        $tokenExpiryDate = time() + $token->expires_in;

        if (isset($token->access_token) && isset($token->expires_in) && isset($token->refresh_token)) {
            $tokenInfo = $token->access_token . '|' . $tokenExpiryDate . '|' . $token->refresh_token;

            file_put_contents($this->tokenPath, $tokenInfo);
        } else {
            file_put_contents($this->tokenPath, '');
        }
    }

    public function extractTokenData()
    {
        $token = file_get_contents($this->tokenPath);

        if (!$token) {
            return null;
        }

        $tokenData = explode('|', $token);

        return $tokenData;
    }

    public function isExpired()
    {
        return time() > $this->getTokenExpiryTime();
    }
}
