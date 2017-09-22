<?php

require 'vendor/autoload.php'; // lets include common libraries
require 'config/config.php'; // lets include configuration files
require 'includes/autoload.php'; // lets include all the libraries we need

$config = getConfig(); // this loads configuration, for values provided in config/config.php and stores them in a single variable

$connect = new Connect(); // creates a new connection
$tokenStorage = new TokenStorage();

if (isset($_GET['purge'])) {
    file_put_contents($config['tokenPath']);
}

if (!$tokenStorage->getToken()) {
    if (!$connect->isCodeAvailable()) { // if the response from server is not available, we generate login link
        $authorizationUrl = $connect->getAuthorizationUrl(); // this is where login link is generated

        echo 'Session token expired, please <a href="' . $authorizationUrl . '">Login Here</a>';
    } else {
        $accessToken = $connect->getAccessToken($_GET['code']); // this is where we generate the token

        $tokenStorage->setToken($accessToken);

        // this refreshes a page after logging in
        echo "<script>window.location.href='" . $config['redirectUri'] . "';</script>";
        die;
    }
} else {
    echo 'Token is saved successfully. If the app is not working, try to purge it <a href="activateToken.php?purge">with this link</a>';
    if ($tokenStorage->isExpired()) {
        $refreshToken = $tokenStorage->getRefreshToken();

        $newToken = $connect->renewAccessToken($refreshToken);

        $tokenStorage->setToken($newToken);
    }
}
