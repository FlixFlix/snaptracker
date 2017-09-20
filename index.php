<?php

require 'vendor/autoload.php'; // lets include common libraries
require 'config/config.php'; // lets include configuration files
require 'includes/autoload.php'; // lets include all the libraries we need

$session = new Session(); // this loads the class from
$config = getConfig();

if (isset($_GET['logout'])) {
    $session->destroySession();

    header('Location: /');
    die;
}

if (!$session->getAccessToken()) {
    $connect = new Connect();

    if (!$connect->isCodeAvailable()) {
        $authorizationUrl = $connect->getAuthorizationUrl();

        echo '<a href="' . $authorizationUrl . '">Login Here</a>';
    } else {
        $accessToken = $connect->getAccessToken($_GET['code']);

        $session->setAccessToken($accessToken->access_token);

        header('Location: /');
        die;
    }

    exit;
}

echo '<a href="/?logout">Logout</a><br />';

$communicator = new Communicator($session->getAccessToken());

$authorizationData = $communicator->authorize();

$projects = $communicator->getProjects();

$todoLists = [];

foreach ($projects as $project) {
    $_todoLists = $communicator->getToDoLists($project);

    $toDoLists = array_merge($_todoLists);
}
echo '<pre>';
var_dump($toDoLists);
echo '</pre>';
die;
