<?php

namespace Hui\Xproject\Http\Controllers\Traits;

use Hui\Xproject\Entities\User;

trait UserInfoTrait{
  protected function userInfo(User $user):array{
    return [
      'name'=>$user->name,
      'qq'=>$user->qq,
      'phone'=>$user->phone,
      'email'=>$user->email
    ];
  }
}
