<?php

namespace Hui\Xproject\Providers;

use Hui\Xproject\Http\ViewComposers\LogsComposer;
use Hui\Xproject\Http\ViewComposers\UsersComposer;
use Hui\Xproject\Http\ViewComposers\ProfileComposer;
use Hui\Xproject\Http\ViewComposers\ProjectsComposer;
use Hui\Xproject\Http\ViewComposers\StatisticsComposer;
use Hui\Xproject\Http\ViewComposers\TodosComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider{
  public function boot():void{
    View::composer('todos',TodosComposer::class);

    View::composer('projects',ProjectsComposer::class);

    View::composer('statistics',StatisticsComposer::class);

    View::composer('users',UsersComposer::class);

    View::composer('logs.index',LogsComposer::class);

    View::composer('profile',ProfileComposer::class);
  }
}
