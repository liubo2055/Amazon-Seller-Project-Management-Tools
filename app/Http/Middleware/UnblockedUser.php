<?php

namespace Hui\Xproject\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UnblockedUser{
  public function handle(Request $request,Closure $next){
    $user=user();

    if($user){
      $loginTime=session(LOGIN_TIME_SESSION_KEY);

      if($user->isKicked($loginTime)||$user->isBlocked()){
        if(!$user->isBlocked())
          logger(sprintf('User %s was kicked at %s (login time: %u), logging out',
            $user->email,
            $user->kicked_at->format('Y-m-d H:i:s'),
            $loginTime));
        else
          logger(sprintf('User %s was blocked at %s, logging out',
            $user->email,
            $user->blocked_at->format('Y-m-d H:i:s')));

        auth()->logout();

        return redirect('/');
      }
    }

    return $next($request);
  }
}
