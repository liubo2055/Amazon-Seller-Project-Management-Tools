<?php

namespace Hui\Xproject\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as BaseTrimmer;

class TrimStrings extends BaseTrimmer{
  protected $except=[
    'password',
    'password_confirmation'
  ];
}
