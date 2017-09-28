<?php
// require($_SERVER['DOCUMENT_ROOT'].'/wp/wp-load.php');
// get_header();
require 'bootstrap.php';

$handler = new Initialize();

if (isset($_GET['updateProjects'])) {
    include('cronjobs/updateProjects.php');
    die;
}

if (isset($_GET['updateToDos'])) {
    include('cronjobs/updateToDos.php');
    die;
}

echo '<pre>';

$projects = $handler->getEntityManager()->getRepository('Project')->findAll();

$communicator = new Communicator($handler->getTokenStorage()->getToken());

foreach ($projects as $project) {
    var_dump($project->getProjectId());
    die;
}

echo '</pre>';
/*

// var_dump($handler->getTokenStorage());
// die;

// echo '<pre>';

// if ($tokenStorage->getToken()) {
//     $entityManager = $this->getEntityManager();

//     // $communicator = new Communicator($tokenStorage->getToken()); // after user is logged in, we let him access the communicator class, which communicates with API

//     // if ($cacher->isExpired()) {
//     //     $toDosList = $communicator->getAllToDos();

//     //     $cacher->setResults($toDosList);
//     //     $cacher->extendExpiry();
//     // }

//     // if ($cacher->isCorrespondencesExpired()) {
//     //     var_dump($communicator->getAllCorrespondences());
//     // }

//     // $toDosList = $cacher->getResults();

//     /*
//     highlight_string("<?php\n\$toDosList =\n" . var_export($toDosList, true) . ";\n?>");
<!-- //
// } else {
//     echo 'Can not connect to API';
// }
 -->
// echo '</pre>';

// get_footer();
// done
*/
