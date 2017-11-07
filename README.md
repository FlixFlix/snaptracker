# snaptracker
Snap Tracker

## Installation

1. First you have to create integration details for app to be able to use the tracker. Instructions on how to do it are [here](https://github.com/basecamp/api/blob/master/sections/authentication.md#oauth-2).
2. After creating app integration details, provide the `clientId`, `clientSecret` and `redirectUri` in the configuration. You also need to provide `appId`, which can be found by visiting main basecamp page, in https://3.basecamp.com/*******/ part.
3. Create the database (it can not be created automatically due to usual server limitations) for integration, and provide its name and database credentials in `dbuser`, `dbname`, `dbpass`, `dbhost` parts of the configuration file.

## Authentication

To login for the first time, visit `activateToken.php` page. It is responsible for logging in and saving token for basecamp application. After logging in you can always logout by pressing `Purge token`.

Token is regenerated automatically after it expires (after 2 weeks from logging in). If something changes in basecamp side, you can renew it by `Purging token` and logging in again.
