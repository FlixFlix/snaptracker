<?php

use GuzzleHttp\Client;

class Communicator {
    protected $client;
    protected $token;
    protected $appId;

    /**
     * This function initializes the class, required for communicating with API.
     *
     * $token string Token you get from OAuth, which is required for every single request
     */
    public function __construct($token)
    {
        $config = getConfig(); // loads configuration from config/config.php

        $this->client = new Client(); // initializes API Request Client, which will provide us with the functions we need
        $this->token = $token; // token we got from autorizing with Basecamp
        $this->appId = $config['appId']; // application ID from configuration file
    }

    /**
     * This function gets ToDo list for a single project
     */
    public function getTodoLists($projectId) {
        $url = $this->generateUrl('projects/' . $projectId . '.json');

        $projectRequest = $this->makeGetRequest($url);

        $project = json_decode((string) $projectRequest->getBody());

        foreach ($project->dock as $singleDock) { // we loop through dock values, till we get to todoset. After that we break the loop, and keep variable in memory, to use it later
            if ($singleDock->name == 'todoset') {
                break;
            }
        }

        $toDoSet = json_decode((string) $this->makeGetRequest($singleDock->url)->getBody()); // gets information about the To-Do Set and To-Do lists it contains

        return json_decode((string) $this->makeGetRequest($toDoSet->todolists_url)->getBody());
    }

    /**
     * This function fetches all projects from the Basecamp API
     */
    public function getProjects()
    {
        $url = $this->generateUrl('projects.json');

        return json_decode((string) $this->makeGetRequest($url)->getBody());
    }


    /**
     * This function fetches all project correspondences, for a specific project.
     *
     * $projectId integer ID of the project.
     */
    public function getProjectCorrespondences($projectId)
    {
        $url = $this->generateUrl('buckets/' . $projectId . '/client/correspondences.json');

        $correspondences = $this->makeGetRequest($url);

        return json_decode((string) $correspondences->getBody());
    }

    /**
     * This function fetches all correspondences from all the projects
     */
    public function getAllCorrespondences()
    {
        $projects = $this->getProjects();

        $correspondences = [];

        foreach ($projects as $project) {
            $url = $this->generateUrl('buckets/' . $project->id . '/client/correspondences.json');

            $_correspondences = json_decode((string) $this->makeGetRequest($url)->getBody());

            $correspondences = array_merge($correspondences, $_correspondences);
        }

        return $correspondences;
    }

    /**
     * This function gets all todos from a single list
     */
    // public function getToDos($url)
    // {
    //     return json_decode((string) $this->makeGetRequest($url)->getBody());
    // }

    /**
     * Generates url for a single request, to Basecamp API.
     *
     * $endPoint string A path which needs to be called, for example `projects.json`, `people.json/2`, etc.
     */
    public function generateUrl($endPoint)
    {
        $config = getConfig();

        return $config['apiUrl'] . '/' . $this->appId . '/' . $endPoint; // just a simple string concatenation, with required parameters for API url
    }

    /**
     * Extracts the list, which contains the specific title.
     */
    public function extractList($toDoLists, $title)
    {
        $toDoList = null;

        foreach ($toDoLists as $_toDoList) {
            if ($_toDoList->name == $title) {
                $toDoList = $_toDoList;
                break;
            }
        }

        if (!$toDoList) {
            return null;
        }

        $toDoList->toDos = json_decode((string) $this->makeGetRequest($toDoList->todos_url)->getBody());

        return $toDoList;
    }

    /**
     * Makes a request to a specified API url, and attaches authorization token to it, so API would know who is logging in to website.
     *
     * $url string Url to be requested.
     */
    public function makeGetRequest($url)
    {
        return $this->client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);
    }

    /**
     * Wrapper function that puts everything into a big json.
     * $url array Array of project objects
     */
    public function getAllToDos()
    {
        $projects = $this->getProjects(); // we get a list of projects

        foreach ($projects as $key => $project) { // we loop through the projects to get to do lists, and merge them to a single array
            $_todoLists = $this->getToDoLists($project);

            $projects[$key]->toDoSets = $_todoLists;

            foreach ($projects[$key]->toDoSets as $toDoKey => $singleList) {
                $toDos = $this->getToDos($singleList->todos_url);

                $projects[$key]->toDoSets[$toDoKey]->toDos = $toDos;
            }
        }

        return $projects;
    }
}
