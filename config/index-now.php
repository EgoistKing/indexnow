<?php

declare(strict_types=1);
/*
    |--------------------------------------------------------------------------
    | IndexNow Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your IndexNow settings. You can specify the
    | default key and key location for submitting URLs to IndexNow.
    |
*/
return [
	'app_url' => env('INDEXNOW_APP_URL', 'localhost'),
	'key' => env('INDEXNOW_KEY', ''),
	'key_location' => env('INDEXNOW_KEY_LOCATION', ''),
	'api_host' => env('INDEXNOW_SERVICE', 'api.indexnow.org'),
];
