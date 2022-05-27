<?php

namespace Hui\Xproject\Services\PasswordBroker;

use Hui\Xproject\Entities\User;
use Illuminate\Auth\Passwords\PasswordBroker as BasePasswordBroker;

class PasswordBroker extends BasePasswordBroker{
  const BLOCKED_USER='passwords.blocked';

  /**
   * @param mixed[] $credentials
   * @return string
   */
  public function sendResetLink(array $credentials){
    $user=$this->getUser($credentials);
    /**
     * @var User $user
     */
    if($user&&$user->isBlocked())
      return static::BLOCKED_USER;
    else
      return parent::sendResetLink($credentials);
  }

  /**
   * @param mixed[] $credentials
   * @return User|string
   */
  protected function validateReset(array $credentials){
    $user=parent::validateReset($credentials);
    if($user instanceof User&&$user->isBlocked())
      return static::BLOCKED_USER;
    else
      return $user;
  }
}
