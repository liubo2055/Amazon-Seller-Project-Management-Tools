<?php

namespace Hui\Xproject\Http\Middleware;

use Closure;
use Hui\Xproject\Services\MenuManager\MenuManager;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class Visible{
  public function __construct(MenuManager $menuManager){
    $this->menuManager=$menuManager;
  }

  public function handle(Request $request,Closure $next,string $option){
    /** @noinspection PhpParamsInspection */
    if(!$this->menuManager->visible(user(),$option))
      throw new AuthenticationException('Unauthenticated');

    return $next($request);
  }

  private $menuManager;
}
