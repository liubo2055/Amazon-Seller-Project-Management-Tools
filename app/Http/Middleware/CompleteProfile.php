<?php

namespace Hui\Xproject\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CompleteProfile{
  public function handle(Request $request,Closure $next){
    $user=user();

    if($user&&!$user->isComplete())
      return redirect()->route('profile');

    return $next($request);
  }
}
