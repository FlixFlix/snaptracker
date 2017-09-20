<?php

use GuzzleHttp\Client;

class Communicator {
    protected $client;
    protected $token;
    protected $appId;

    public function __construct($token)
    {
        $config = getConfig();

        $this->client = new Client();
        $this->token = $token;
        $this->appId = $config['appId'];
    }

    public function authorize()
    {
        $url = $this->generateAuthorizationUrl('authorization.json');

        return json_decode((string) $this->makeGetRequest($url)->getBody());
    }

    public function getTodoLists($project) {
        $id = $project->id;

        foreach ($project->dock as $singleDock) {
            if ($singleDock->name == 'todoset') {
                break;
            }
        }

        $toDoSet = json_decode((string) $this->makeGetRequest($singleDock->url)->getBody());

        $toDoLists = json_decode((string) $this->makeGetRequest($toDoSet->todolists_url)->getBody());

        return $toDoLists;
    }

    public function generateAuthorizationUrl($endPoint)
    {
        $config = getConfig();

        return $config['authorizationUrl'] . '/' . $endPoint;
    }

    public function generateUrl($endPoint)
    {
        $config = getConfig();

        return $config['apiUrl'] . '/' . $this->appId . '/' . $endPoint;
    }

    public function getProjects()
    {
        $url = $this->generateUrl('projects.json');

        return json_decode((string) $this->makeGetRequest($url)->getBody());
    }

    public function makeGetRequest($url)
    {
        return $this->client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);
    }

    public function setAppId($id)
    {
        $this->appId = $id;
    }
}
