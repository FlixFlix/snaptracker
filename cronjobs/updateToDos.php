<?php

require '../bootstrap.php';

$handler = new Initialize();

$entityManager = $handler->getEntityManager();

$tokenData = $handler->getEntityManager()->getRepository('Token')->findOneBy(array(), array('id' => 'DESC'));

if ($tokenData->getToken()) {
    $communicator = new Communicator($tokenData->getToken()); // after user is logged in, we let him access the communicator class, which communicates with API

    $projectsRepository = $entityManager->getRepository('Project');

    foreach ($projectsRepository->findAll() as $_project) {
        $toDos = $communicator->getToDos($_project->getProjectId(), $_project->getToDoSetId());

        // var_dump($toDos);
    }

    var_dump($toDos);
    die;
}
