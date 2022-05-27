<?php

namespace Hui\Xproject\Providers;

use Hui\Xproject\Entities\Project;
use Hui\Xproject\Entities\Todo;
use Hui\Xproject\Entities\User;
use Hui\Xproject\Services\ProjectManager\ProjectPolicy;
use Hui\Xproject\Services\TodoManager\TodoPolicy;
use Hui\Xproject\Services\UserManager\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider{
  public function boot():void{
    $this->registerPolicies();
  }

  protected $policies=[
    User::class=>UserPolicy::class,
    Todo::class=>TodoPolicy::class,
    Project::class=>ProjectPolicy::class
  ];
}
