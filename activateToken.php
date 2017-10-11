<?php

require 'bootstrap.php';

$handler = new Initialize();

$tokenRepository = $handler->getEntityManager()->getRepository('Token');

$tokenData = $tokenRepository->findOneBy(array(), array('id' => 'DESC'));

$connect = new Connect();

if (isset($_GET['purge']) && $tokenData) {
    $handler->getEntityManager()->remove($tokenData);
    $handler->getEntityManager()->flush();

    echo "<script>window.location.href='" . $config['redirectUri'] . "';</script>";
    die;
}

if (!$tokenData) {
    if (!$connect->isCodeAvailable()) {
        $authorizationUrl = $connect->getAuthorizationUrl(); // this is where login link is generated

        echo 'Session token expired, please <a href="' . $authorizationUrl . '">Login Here</a>';
    } else {
        $accessToken = $connect->getAccessToken($_GET['code']); // this is where we generate the token

        $token = new Token();

        $token->setToken($accessToken->access_token);
        $token->setExpiryTime(time() + $accessToken->expires_in);
        $token->setRefreshToken($accessToken->refresh_token);

        $handler->getEntityManager()->persist($token);
        $handler->getEntityManager()->flush();

        // this refreshes a page after logging in
        echo "<script>window.location.href='" . $config['redirectUri'] . "';</script>";
        die;
    }
} else {
    echo 'Token is saved successfully. If the app is not working, try to purge it <a href="activateToken.php?purge">with this link</a>';

    if ($tokenData->getExpiryTime() <= time()) {
        $newToken = $connect->renewAccessToken($tokenData->getRefreshToken());

        $tokenData->setToken($newToken);

        $handler->getEntityManager()->persist($tokenData);
        $handler->getEntityManager()->flush();
    }
}
