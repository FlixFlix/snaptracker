<?php

// this is just a plain function, to include all the configuration values we need for the application

function getConfig() {
    return [
        'clientId'                => '198b8362c3b60b7b3b53af8e00e652e830c58c2d', // this is the clientId obtainable in integration page
        'clientSecret'            => '33d2cc3d711908cac42c6814fb4881b874f3bf94', // this is the clientSecret obtainable in integration page
        'redirectUri'             => 'https://iredesigned.com/snaptracker', // this is the redirect uri provided in integration page
        'urlAuthorize'            => 'https://launchpad.37signals.com/authorization/new', // this doesn't need to be changed
        'urlAccessToken'          => 'https://launchpad.37signals.com/authorization/token', // this doesn't need to be changed
        'urlResourceOwnerDetails' => '', // this doesn't need to be changed
        'appId'                   => 3897586, // this is the ID of the application, which you obtain by visiting main basecamp page
        'apiUrl'                  => 'https://3.basecampapi.com', // this doesn't need to be changed
        'authorizationUrl'        => 'https://launchpad.37signals.com', // this doesn't need to be changed
        'tokenPath'               => 'tokens2.txt',
        'expiryTime'              => 200, // time in seconds before the data is regenerated
        'dbname'                  => 'snaptracker',
        'dbuser'                  => 'root',
        'dbpass'                  => '',
        'dbhost'                    => 'localhost',
        'dbport'                    => 3306
    ];
}
