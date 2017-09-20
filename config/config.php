<?php

// this is just a plain function, to include all the configuration values we need for the application

function getConfig() {
    return [
        'clientId'                => 'b0e1b53ec0a2d223b44c20ddca20aec88bf9882d',
        'clientSecret'            => '0472285e711337bbe79947e8282194a1bb84686d',
        'redirectUri'             => 'http://scotchbox/',
        'urlAuthorize'            => 'https://launchpad.37signals.com/authorization/new',
        'urlAccessToken'          => 'https://launchpad.37signals.com/authorization/token',
        'urlResourceOwnerDetails' => '',
        'appId'                   => 3847617,
        'apiUrl'                  => 'https://3.basecampapi.com',
        'authorizationUrl'        => 'https://launchpad.37signals.com'
    ];
}
