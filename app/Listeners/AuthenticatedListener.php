<?php

namespace Hui\Xproject\Listeners;

use Hui\Xproject\Entities\User;
use Illuminate\Auth\Events\Authenticated;

class AuthenticatedListener{
  public function handle(Authenticated $authenticated):void{
    /**
     * @var User $user
     */
    $user=$authenticated->user;
    if($user&&$user->locale)
      $locale=$user->locale;
    else
      $locale=LOCALE_CN;

    changeLocale($locale);
  }
}
