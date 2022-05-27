<?php

namespace Hui\Xproject\Http\Controllers\Auth;

use Hui\Xproject\Http\Controllers\Controller;
use Hui\Xproject\Services\PasswordBroker\PasswordBrokerManager;
use Illuminate\Contracts\Auth\PasswordBroker as BasePasswordBroker;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller{
  use SendsPasswordResetEmails;

  public function broker():BasePasswordBroker{
    return (new PasswordBrokerManager(app()))->broker();
  }
}
