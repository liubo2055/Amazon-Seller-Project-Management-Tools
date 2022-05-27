<?php

namespace Hui\Xproject\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;

class Handler extends ExceptionHandler{
  protected function unauthenticated($request,AuthenticationException $exception){
    /**
     * @todo remove
     */
    if(!$request->expectsJson())
      logger(sprintf('*** Redirecting to %s from %s (exception %s, user %s, IP %s)',
        route('login'),
        redirect()
          ->getUrlGenerator()
          ->full(),
        $exception->getMessage(),
        user()?user()->id:'N/A',
        app(Request::class)->ip()));

    return parent::unauthenticated($request,$exception);
  }

  protected $dontFlash=[
    'password',
    'password_confirmation'
  ];
}
