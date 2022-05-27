<?php

namespace Hui\Xproject\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate;

class AuthenticateAndSetTimezone extends Authenticate{
  protected function authenticate(array $guards):void{
    parent::authenticate($guards);

    if(user())
      date_default_timezone_set(user()->timezone);
  }
}
