<?php
// require($_SERVER['DOCUMENT_ROOT'].'/wp/wp-load.php');
// get_header();
// echo '<pre>';

require 'bootstrap.php';

$handler = new Initialize();

$token = $handler->getEntityManager()->getRepository('Token')->findOneBy(array(), array('id' => 'DESC'));

if ($token) {
    $communicator = new Communicator($token->getToken());

    $toDoLists = $communicator->getToDoLists(5234542); // this gets the lists for a project with provided ID

    $toDoList = $communicator->extractList($toDoLists, 'Tracker'); // this extracts the list with specific title, in this case 'Tracker'

    var_dump($toDoList);
    die;
} else {
    echo '<pre>Unauthorized request</pre>';
}

// echo '</pre>';

// get_footer();
// done
