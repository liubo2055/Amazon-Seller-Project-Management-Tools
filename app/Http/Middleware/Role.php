<?php

namespace Hui\Xproject\Http\Middleware;

use Closure;
use Hui\Xproject\Entities\User;
use Hui\Xproject\Exceptions\XprojectException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class Role{
  public function handle(Request $request,Closure $next,...$roles){
    foreach($roles as $role)
      if(!in_array($role,User::ROLES))
        throw new XprojectException('Wrong role');

    $user=user();
    if(!$user)
      throw new AuthenticationException('Unauthenticated');
    else if(!$user->isAdmin()&&!in_array($user->role,$roles))
      throw new AuthenticationException('Unauthenticated');

    return $next($request);
  }
}
