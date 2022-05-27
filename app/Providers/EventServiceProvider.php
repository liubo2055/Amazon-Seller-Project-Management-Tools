<?php

namespace Hui\Xproject\Providers;

use Hui\Xproject\Events\ImageDeleted;
use Hui\Xproject\Events\ImageUpdated;
use Hui\Xproject\Events\PaymentSaved;
use Hui\Xproject\Listeners\AuthenticatedListener;
use Hui\Xproject\Listeners\ImageDeletedListener;
use Hui\Xproject\Listeners\ImageUpdatedListener;
use Hui\Xproject\Listeners\LoginListener;
use Hui\Xproject\Listeners\PaymentSavedListener;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider{
  protected $listen=[
    ImageUpdated::class=>[ImageUpdatedListener::class],
    ImageDeleted::class=>[ImageDeletedListener::class],
    Authenticated::class=>[AuthenticatedListener::class],
    PaymentSaved::class=>[PaymentSavedListener::class],
    Login::class=>[LoginListener::class]
  ];
}
