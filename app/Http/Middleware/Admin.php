<?php

namespace Hui\Xproject\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class Admin{
  public function handle(Request $request,Closure $next){
    $user=user();

    if(!$user||!$user->isAdmin())
      throw new AuthenticationException('Unauthenticated');

    return $next($request);
  }
}
