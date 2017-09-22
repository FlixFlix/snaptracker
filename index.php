<?php
// require($_SERVER['DOCUMENT_ROOT'].'/wp/wp-load.php');
// get_header();
require 'vendor/autoload.php'; // lets include common libraries
require 'config/config.php'; // lets include configuration files
require 'includes/autoload.php'; // lets include all the libraries we need

$tokenStorage = new TokenStorage();

echo '<pre>';

if ($tokenStorage->getToken()) {
    $communicator = new Communicator($tokenStorage->getToken()); // after user is logged in, we let him access the communicator class, which communicates with API

    $toDosList = $communicator->getAllToDos();

    highlight_string("<?php\n\$toDosList =\n" . var_export($toDosList, true) . ";\n?>");
} else {
    echo 'Can not connect to API';
}

// get_footer();
// done
