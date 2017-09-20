<?php

use League\OAuth2\Client\Provider\GenericProvider;
use GuzzleHttp\Client;

class Connect {
    public $provider; // Oauth provider variable which we will use later

    /**
     * Provides a client with parameters to connect a user with.
     */
    public function __construct() {
        $config = getConfig();

        $this->provider = new GenericProvider([
            'clientId'                => $config['clientId'],
            'clientSecret'            => $config['clientSecret'],
            'redirectUri'             => $config['redirectUri'],
            'urlAuthorize'            => $config['urlAuthorize'],
            'urlAccessToken'          => $config['urlAccessToken'],
            'urlResourceOwnerDetails' => $config['urlResourceOwnerDetails']
        ]);
    }

    /**
     * This function generates working authorization url, which GenericProvider fails to generate.
     */
     public function getAccessToken($code)
     {
        $config = getConfig();

        $accessTokenUrl = $this->provider->getBaseAccessTokenUrl($config);

        $accessTokenUrl = $accessTokenUrl . "?type=web_server&client_id=" . $config['clientId'] . "&redirect_uri=" . $config['redirectUri'] . "&client_secret=" . $config['clientSecret'] . "&code=" . $code;

        $client = new Client();

        $response = $client->post($accessTokenUrl);

        if ($response->getStatusCode() == 200) {
            $body = (string) $response->getBody();

            return json_decode($body);
        }

        return [];
     }

    /**
     * This function generates working authorization url, which GenericProvider fails to generate, so we have to implement some string functions, that helps with it.
     */
    public function getAuthorizationUrl()
    {
        $config = getConfig();

        $authorizationUrlParams = explode("?", $this->provider->getAuthorizationUrl());

        $urlParams = explode("&", $authorizationUrlParams[1]);

        $requiredParams = ['client_id', 'redirect_uri'];

        $newParams = [];

        foreach ($urlParams as $singleParam) {
            $paramData = explode("=", $singleParam);

            if (in_array($paramData[0], $requiredParams)) {
                $newParams[] = [
                    'param' => $paramData[0],
                    'data' => $paramData[1]
                ];
            }
        }

        $url = $config['urlAuthorize'].'?type=web_server&';

        foreach ($newParams as $param) {
            $url .= $param['param'] . '=' .$param['data'] . '&';
        }

        return substr($url, 0, -1);
    }

    /**
     * With this function we check whether we have response from the server
     */
    public function isCodeAvailable() {
        return isset($_GET['code']);
    }
}
