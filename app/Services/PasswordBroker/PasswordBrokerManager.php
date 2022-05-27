<?php

namespace Hui\Xproject\Services\PasswordBroker;

use Illuminate\Auth\Passwords\PasswordBrokerManager as BasePasswordBrokerManager;
use InvalidArgumentException;

class PasswordBrokerManager extends BasePasswordBrokerManager{
  /**
   * @param string $name
   * @return PasswordBroker|\Illuminate\Contracts\Auth\PasswordBroker
   */
  protected function resolve($name){
    $config=$this->getConfig($name);
    if($config===null)
      throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");

    return new PasswordBroker($this->createTokenRepository($config),$this->app['auth']->createUserProvider($config['provider']??null));
  }
}
