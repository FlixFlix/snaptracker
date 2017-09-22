<?php namespace App\Services;

use App\BasecampTokens;
use App\BcProject;
use App\BcResponse;
use App\BcTodolist;
use League\OAuth2\Client\Provider\GenericProvider;
use GuzzleHttp\Client;
use League\OAuth2\Client\Token\AccessToken;
use Redirect;
use Session;
use DB;

class BasecampService
{
    var $provider = null;
    var $response = null;
    var $uri = null;
    var $responseHeaders = null;
    var $allObjects;
    var $httpCache = [];
    var $firstTime = true;
    var $baseUri = null;
    var $userAgent;

    public function __construct()
    {
        $this->provider = new GenericProvider([
            'clientId' => env('basecamp_client_id'),    // The client ID assigned to you by the provider
            'clientSecret' => env('basecamp_client_secret'),   // The client password assigned to you by the provider
            'redirectUri' => url('basecamp/getToken'),
            'urlAuthorize' => 'https://launchpad.37signals.com/authorization/new',
            'urlAccessToken' => 'https://launchpad.37signals.com/authorization/token',
            'urlResourceOwnerDetails' => 'https://github.com/basecamp/api/blob/master/sections/authentication.md'
        ]);
        $this->baseUri = 'https://3.basecampapi.com/XXXXXX/';
        $this->userAgent = 'j.com (joel@XXXXX.com)';
    }

    public function parseResponse()
    {
        $this->responseBody = json_decode($this->response->getBody());
        $this->responseHeaders = $this->response->getHeaders();

        if (isset($this->responseHeaders['X-Total-Count'])) {
            $this->responseTotalCount = $this->responseHeaders['X-Total-Count'][0];
        }

        if (isset($this->responseHeaders['Link'])) {
            $this->responseLink = $this->responseHeaders['Link'][0];
        } else {
            $this->responseLink = null;
        }

        if (isset($this->responseHeaders['X-Ratelimit'])) {
            $this->responseRateLimit = $this->responseHeaders['X-Ratelimit'][0];
        }

        if (isset($this->responseHeaders['ETag'])) {
            $this->responseEtag = $this->responseHeaders['ETag'][0];
        }
    }

    public function initiateBasecampConnection()
    {
        $this->_client = new Client([
            'base_uri' => $this->baseUri
        ]);

        $headers = ['headers' => [
            'User-Agent' => $this->userAgent,
            "Authorization" => "Bearer " . $this->getAccessToken(),
        ]];
        if ($this->firstTime == true) {
            $bcResponse = BcResponse::where('link', $this->uri)->first();
        } else {
            $bcResponse = BcResponse::where('link', $this->responseLink)->first();
        }
        if ($bcResponse instanceof BcResponse) {
            $eTag = $bcResponse->etag;
        } else {
            $eTag = null;
        }

        $LastModified = null;

        $httpCache = [
            'If-None-Match' => $eTag, //ETag
            'If-Modified-Since' => $LastModified //Last-Modified
        ];

        if ($httpCache['If-None-Match'] != null) {
            $headers['headers']['If-None-Match'] = $httpCache['If-None-Match'];
        }
        if ($httpCache['If-Modified-Since'] != null) {
            $headers['headers']['If-Modified-Since'] = $httpCache['If-Modified-Since'];
        }

        try {
            $this->response = $this->_client->request(
                'GET',
                $this->uri,
                $headers
            );

        } catch (ClientException $e) {
            dd($e);
        }
    }

    public function getAllRecords()
    {
        $this->allObjects = [];
        do {
            $this->initiateBasecampConnection();
            $this->parseResponse();
            $this->saveResponse();
            if ($this->response->getStatusCode() == 304) {
                //This is for caching
            } else {
                if ($this->response->getStatusCode() == 429) {
                    sleep(10);
                    $this->initiateBasecampConnection();
                    $this->parseResponse();
                }
                $this->allObjects = array_merge($this->allObjects, $this->responseBody);
            }
            if (isset($this->responseLink)) {
                $regex = '#<(.*?)>#';
                preg_match($regex, $this->responseLink, $matches);
                $this->uri = $matches[1];
            }
        } while (isset($this->responseLink));
    }

    public function saveResponse()
    {
        $bcResponse = BcResponse::where('link', $this->uri)->first();
        if (!$bcResponse instanceof BcResponse) {
            $bcResponse = new BcResponse();
        }
        $bcResponse->link = $this->uri;
        $bcResponse->etag = $this->responseEtag;
        $bcResponse->save();
    }

    public function getTodolists()
    {
        $allBasecampProjects = BcProject::all();
        foreach($allBasecampProjects as $bcProject){
            $todoSetId = $bcProject->todoset_id;
            $bucketId = $bcProject->bc_id;
            $this->uri = 'https://3.basecampapi.com/3140673/buckets/'.$bucketId.'/todosets/'.$todoSetId.'/todolists.json';
            $this->getAllRecords();
            foreach($this->allObjects as $todolist){
                $bcTodolist = BcTodolist::where('bc_todolist_id', $todolist->id)->first();
                if (!$bcTodolist instanceof BcTodolist) {
                    $bcTodolist = new BcTodolist();
                }
                $bcTodolist->bc_todolist_id = $todolist->id;
                $bcTodolist->bc_status = $todolist->status;
                $bcTodolist->bc_name = $todolist->name;
                $bcTodolist->bc_description = $todolist->description;
                $bcTodolist->bc_todoset_id = $todolist->parent->id;
                $bcTodolist->bc_project_id = $todolist->bucket->id;
                $bcTodolist->bc_app_url = $todolist->app_url;
                $bcTodolist->bc_url = $todolist->url;
                $bcTodolist->bc_completed_ratio = $todolist->completed_ratio;
                $bcTodolist->bc_completed = $todolist->completed;
                $bcTodolist->save();
            }
        }
        dd($this->allObjects);
    }

    public function getBasecamps()
    {

//        dd($eTag);
//        $eTag = 'W/"3fc3c26a37d4f474bb120d6164dcadbd"';
        $this->uri = 'https://3.basecampapi.com/3140673/projects.json';
        $this->getAllRecords();

        foreach ($this->allObjects as $bcProject) {
            $dbBcproject = BcProject::where('bc_id', $bcProject->id)->first();
            if (!$dbBcproject instanceof BcProject) {
                $dbBcproject = new BcProject();
            }
            $dbBcproject->bc_id = $bcProject->id;
            $dbBcproject->bc_status = $bcProject->status;
            $dbBcproject->bc_name = $bcProject->name;
            $dbBcproject->bc_description = $bcProject->description;
            $dbBcproject->bc_purpose = $bcProject->purpose;
            $dbBcproject->bc_bookmark_url = $bcProject->bookmark_url;
            $dbBcproject->bc_url = $bcProject->url;
            $dbBcproject->bc_app_url = $bcProject->app_url;
            $dbBcproject->todoset_id = $bcProject->dock[2]->id;
            $dbBcproject->save();
        }
        dd($this->allObjects);

    }

    public function getAccessToken()
    {
        $basecamp_token = DB::table('basecamp_tokens')
            ->orderBy('id', 'desc')->first();

        $basecamp_token = json_decode(json_encode($basecamp_token), true);

        $existingAccessToken = new AccessToken($basecamp_token);

        if ($existingAccessToken->hasExpired()) {
            $newAccessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $existingAccessToken->getRefreshToken()
            ]);
            // Purge old access token and store new access token to your data store.
            $basecampToken = new BasecampTokens();
            $basecampToken->access_token = $newAccessToken->getToken();
            $basecampToken->refresh_token = $newAccessToken->getRefreshToken();
            $basecampToken->expires = $newAccessToken->getExpires();
            $basecampToken->save();

            $existingAccessToken = $newAccessToken;

            Session::flash('message', 'Got token from basecamp');
        }


        return $existingAccessToken;
    }

    public
    function getToken()
    {
// If we don't have an authorization code then get one
        if (!isset($_GET['code'])) {

            // Fetch the authorization URL from the provider; this returns the
            // urlAuthorize option and generates and applies any necessary parameters
            // (e.g. state).
            $options = ['type' => 'web_server'];

            $authorizationUrl = $this->provider->getAuthorizationUrl($options);

            // Get the state generated for you and store it to the session.
            $_SESSION['oauth2state'] = $this->provider->getState();

            // Redirect the user to the authorization URL.
            header('Location: ' . $authorizationUrl);
            exit;

// Check given state against previously stored one to mitigate CSRF attack
//        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
//
//            unset($_SESSION['oauth2state']);
//            exit('Invalid state');

        } else {

            try {

                // Try to get an access token using the authorization code grant.
                $accessToken = $this->provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code'],
                    'type' => 'web_server'
                ]);

                // We have an access token, which we may use in authenticated
                // requests against the service provider's API.
                $basecampToken = new BasecampTokens();
//                echo $accessToken->getToken() . "\n";
                $basecampToken->access_token = $accessToken->getToken();
//                echo $accessToken->getRefreshToken() . "\n";
                $basecampToken->refresh_token = $accessToken->getRefreshToken();
//                echo $accessToken->getExpires() . "\n";
                $basecampToken->expires = $accessToken->getExpires();
//                echo ($accessToken->hasExpired() ? 'expired' : 'not expired') . "\n";
                $basecampToken->save();

                Session::flash('message', 'Got token from basecamp');

                // Using the access token, we may look up details about the
                // resource owner.
//                $resourceOwner = $this->provider->getResourceOwner($accessToken);
//
//                var_export($resourceOwner->toArray());
//
//                // The provider provides a way to get an authenticated API request for
//                // the service, using the access token; it returns an object conforming
//                // to Psr\Http\Message\RequestInterface.
//                $request = $this->provider->getAuthenticatedRequest(
//                    'GET',
//                    'http://brentertainment.com/oauth2/lockdin/resource',
//                    $accessToken
//                );

            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

                // Failed to get the access token or user details.
                exit($e->getMessage());
                Session::flash('message', $e->getMessage());

            }
        }
    }


}
