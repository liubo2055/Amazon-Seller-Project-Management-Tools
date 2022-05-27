<?php

namespace Hui\Xproject\Listeners;

use Illuminate\Auth\Events\Login;

class LoginListener{
  public function handle(Login $login):void{
    /** @noinspection PhpUndefinedFieldInspection */
    logger(sprintf('User %s logged in',$login->user->email));

    session([
      LOGIN_TIME_SESSION_KEY=>time()
    ]);
  }
}
