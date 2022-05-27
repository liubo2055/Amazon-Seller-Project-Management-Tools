<?php

namespace Hui\Xproject\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider{
  const LOG_QUERIES=false;

  public function boot():void{
    if(static::LOG_QUERIES)
      DB::listen(function(QueryExecuted $query):void{
        logger(sprintf('[%s] %s',
          Route::currentRouteName(),
          $query->sql));
      });

    /** @noinspection PhpIncludeInspection */
    require_once app_path('constants.php');
    /** @noinspection PhpIncludeInspection */
    require_once app_path('functions.php');
  }
}
