<?php

$tokenStorage = $handler->getTokenStorage();
$entityManager = $handler->getEntityManager();

if ($tokenStorage->getToken()) {
    $communicator = new Communicator($tokenStorage->getToken()); // after user is logged in, we let him access the communicator class, which communicates with API

    $projectsRepository = $entityManager->getRepository('Project');

    $fetchedIds = [];

    $apiProjects = $communicator->getProjects();

    foreach ($projectsRepository->findAll() as $_project) {
        $entityManager->remove($_project);
    }

    $entityManager->flush();

    foreach ($apiProjects as $key => $_project) {
        $toDoSetId = 0;
        $project = new Project();

        $createdAt = new \DateTime($_project->created_at);
        $updatedAt = new \DateTime($_project->updated_at);

        $pattern = '/[a-z0-9_\-\+]+@[a-z0-9\-]+\.([a-z]{2,3})(?:\.[a-z]{2})?/i';

        foreach ($_project->dock as $dockEntry) {
            if ($dockEntry->name == 'todoset') {
                $toDoSetId = $dockEntry->id;
            }
        }

        preg_match_all($pattern, $_project->description, $emails);

        $project->setProjectId($_project->id);
        $project->setName($_project->name);
        $project->setDescription($_project->description);
        $project->setClientEmails($emails[0]);
        $project->setToDoSetId($toDoSetId);
        $project->setCreatedAt($createdAt);
        $project->setUpdatedAt($updatedAt);

        $entityManager->persist($project);
        $entityManager->flush();
    }
}
