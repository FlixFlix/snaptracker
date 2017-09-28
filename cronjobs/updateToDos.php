<?php

$tokenStorage = $handler->getTokenStorage();
$entityManager = $handler->getEntityManager();

if ($tokenStorage->getToken()) {
    $communicator = new Communicator($tokenStorage->getToken()); // after user is logged in, we let him access the communicator class, which communicates with API

    $projectsRepository = $entityManager->getRepository('Project');

    foreach ($projectsRepository->findAll() as $_project) {
        var_dump($_project->getToDoSetId());
    }
}
