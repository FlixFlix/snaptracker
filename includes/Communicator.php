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
    public function getTodoLists($project) {
        foreach ($project->dock as $singleDock) { // we loop through dock values, till we get to todoset. After that we break the loop, and keep variable in memory, to use it later
            if ($singleDock->name == 'todoset') {
                break;
            }
        }

        $toDoSet = json_decode((string) $this->makeGetRequest($singleDock->url)->getBody()); // gets information about the To-Do Set and To-Do lists it contains

        return json_decode((string) $this->makeGetRequest($toDoSet->todolists_url)->getBody()); // returns array with all the To-Do lists
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
}
