<?php

namespace Hui\Xproject\Providers;

use Hui\Xproject\Entities\Project;
use Hui\Xproject\Entities\Todo;
use Hui\Xproject\Entities\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider{
  public function boot():void{
    parent::boot();

    Route::model('todo',Todo::class);
    Route::model('project',Project::class);
    Route::model('user',User::class);

    Route::bind('projectWithTrashed',function(string $id):?Project{
      return Project
        ::where(compact('id'))
        ->withTrashed()
        ->first();
    });
    Route::bind('userWithTrashed',function(string $id):?User{
      return User
        ::where(compact('id'))
        ->withTrashed()
        ->first();
    });
  }

  public function map():void{
    $this->mapApiRoutes();
    $this->mapWebRoutes();
  }

  protected function mapWebRoutes():void{
    Route::middleware('web')
      ->namespace($this->namespace)
      ->group(base_path('routes/web.php'));
  }

  protected function mapApiRoutes():void{
    Route::middleware('api')
      ->namespace($this->namespace)
      ->group(base_path('routes/api.php'));
  }

  protected $namespace='Hui\Xproject\Http\Controllers';
}
