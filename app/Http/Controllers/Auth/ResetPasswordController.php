<?php

namespace Hui\Xproject\Http\Controllers\Auth;

use Hui\Xproject\Http\Controllers\Controller;
use Hui\Xproject\Services\PasswordBroker\PasswordBrokerManager;
use Illuminate\Contracts\Auth\PasswordBroker as BasePasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller{
  use ResetsPasswords;

  public function broker():BasePasswordBroker{
    return (new PasswordBrokerManager(app()))->broker();
  }

  protected function rules(){
    return [
      'token'=>'required',
      'email'=>'required|email',
      'password'=>'required|confirmed|min:6|max:16',
    ];
  }

  protected function sendResetFailedResponse(/** @noinspection PhpUnusedParameterInspection */
    Request $request,$response){
    // There's no email field
    return redirect()
      ->back()
      ->withErrors([
        'password'=>trans($response)
      ]);
  }

  protected $redirectTo='/';
}
