<?php

require 'vendor/autoload.php'; // lets include common libraries
require 'config/config.php'; // lets include configuration files
require 'includes/autoload.php'; // lets include all the libraries we need

$session = new Session(); // this loads the class from includes/Session.php, that is responsible for session controls
$config = getConfig(); // this loads configuration, for values provided in config/config.php and stores them in a single variable

if (isset($_GET['logout'])) { // this is a function for logging out
    $session->destroySession();

    header('Refresh:0'); // this refreshes a page after logging out
    die;
}

if (!$session->getAccessToken()) { // checks if session has accessToken provided, if not it generates login link for user, or logs in user.
    $connect = new Connect(); // creates a new connection

    if (!$connect->isCodeAvailable()) { // if the response from server is not available, we generate login link
        $authorizationUrl = $connect->getAuthorizationUrl(); // this is where login link is generated

        echo '<a href="' . $authorizationUrl . '">Login Here</a>';
    } else { // if we get a response from Basecamp, we trade code we got for accessToken. After that it is used for later calls.
        $accessToken = $connect->getAccessToken($_GET['code']); // this is where we generate the token

        $session->setAccessToken($accessToken->access_token); // this saves access token to the session

        header('Refresh:0'); // this refreshes a page after logging in
        die;
    }

    die;
}

echo '<a href="?logout">Logout</a><br />';

$communicator = new Communicator($session->getAccessToken()); // after user is logged in, we let him access the communicator class, which communicates with API

$projects = $communicator->getProjects(); // we get a list of projects

$todoLists = [];

foreach ($projects as $project) { // we loop through the projects to get to do lists, and merge them to a single array
    $_todoLists = $communicator->getToDoLists($project);

    $toDoLists = array_merge($_todoLists);
}

// we dump the result

echo '<pre>';
var_dump($toDoLists);
echo '</pre>';

// done
