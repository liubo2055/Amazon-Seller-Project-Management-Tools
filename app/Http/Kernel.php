<?php

namespace Hui\Xproject\Http;

use Hui\Xproject\Http\Middleware\Admin;
use Hui\Xproject\Http\Middleware\AuthenticateAndSetTimezone;
use Hui\Xproject\Http\Middleware\CompleteProfile;
use Hui\Xproject\Http\Middleware\RedirectIfAuthenticated;
use Hui\Xproject\Http\Middleware\Role;
use Hui\Xproject\Http\Middleware\TrimStrings;
use Hui\Xproject\Http\Middleware\TrustProxies;
use Hui\Xproject\Http\Middleware\UnblockedUser;
use Hui\Xproject\Http\Middleware\Visible;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Kernel extends HttpKernel{
  protected $middleware=[
    CheckForMaintenanceMode::class,
    ValidatePostSize::class,
    TrimStrings::class,
    ConvertEmptyStringsToNull::class,
    TrustProxies::class
  ];

  protected $middlewareGroups=[
    'web'=>[
      EncryptCookies::class,
      AddQueuedCookiesToResponse::class,
      StartSession::class,
      ShareErrorsFromSession::class,
      VerifyCsrfToken::class,
      SubstituteBindings::class
    ],
    'api'=>[
      'throttle:60,1',
      'bindings'
    ]
  ];

  protected $routeMiddleware=[
    'auth'=>AuthenticateAndSetTimezone::class,
    'auth.basic'=>AuthenticateWithBasicAuth::class,
    'bindings'=>SubstituteBindings::class,
    'can'=>Authorize::class,
    'guest'=>RedirectIfAuthenticated::class,
    'throttle'=>ThrottleRequests::class,
    'admin'=>Admin::class,
    'role'=>Role::class,
    'complete'=>CompleteProfile::class,
    'unblocked'=>UnblockedUser::class,
    'visible'=>Visible::class
  ];
}
