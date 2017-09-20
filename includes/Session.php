<?php

class Session {
    /**
     * Starts a session, so we can save AccessToken
     */
    public function __construct()
    {
        session_start();
    }

    /**
     * Gets access token if its set, otherwise returns false and directs user to login.
     */
    public function getAccessToken()
    {
        return isset($_SESSION['accessToken']) ? $_SESSION['accessToken'] : false;
    }

    /**
     * Sets access token, for a current session. Only used after logging in and storing access token to it.
     */
    public function setAccessToken($token)
    {
        $_SESSION['accessToken'] = $token;
    }

    /**
     * Destroys session. Used for logging out.
     */
    public function destroySession()
    {
        session_destroy();
    }
}
