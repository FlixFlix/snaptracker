<?php

class Session {
    public function __construct()
    {
        session_start();
    }

    public function getAccessToken()
    {
        return isset($_SESSION['accessToken']) ? $_SESSION['accessToken'] : false;
    }

    public function setAccessToken($token)
    {
        $_SESSION['accessToken'] = $token;
    }

    public function destroySession()
    {
        session_destroy();
    }
}
