<?php

use Hui\Xproject\Services\AppsIoHandler;

return [
  'base_url'=>env('HUIAPPS_BASE_URL','https://huistore.com'),
  'client_id'=>env('HUIAPPS_CLIENT_ID',0),
  'client_secret'=>env('HUIAPPS_CLIENT_SECRET',''),
  'api_token'=>env('HUIAPPS_API_TOKEN',''),
  'handler'=>AppsIoHandler::class
];
