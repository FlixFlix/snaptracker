<?php

// this is just a plain function, to include all the configuration values we need for the application

function getConfig() {
    return [
        'clientId'                => 'a497d1d3fc989abd4f9b756ee29a81c815dbae88', // this is the clientId obtainable in integration page
        'clientSecret'            => '95143a2d776d5d0e0fcc9bc3c5c331c7737e4799', // this is the clientSecret obtainable in integration page
        'redirectUri'             => 'https://iredesigned.com/wp/snaptracker/', // this is the redirect uri provided in integration page
        'urlAuthorize'            => 'https://launchpad.37signals.com/authorization/new', // this doesn't need to be changed
        'urlAccessToken'          => 'https://launchpad.37signals.com/authorization/token', // this doesn't need to be changed
        'urlResourceOwnerDetails' => '', // this doesn't need to be changed
        'appId'                   => 3847617, // this is the ID of the application, which you obtain by visiting main basecamp page
        'apiUrl'                  => 'https://3.basecampapi.com', // this doesn't need to be changed
        'authorizationUrl'        => 'https://launchpad.37signals.com' // this doesn't need to be changed
    ];
}
