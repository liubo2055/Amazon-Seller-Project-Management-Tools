<?php

namespace Hui\Xproject\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated{
  public function handle(Request $request,Closure $next,string $guard=null){
    if(Auth::guard($guard)
      ->check()
    )
      return redirect('/');

    return $next($request);
  }
}
