<?php

namespace Hui\Xproject\Services\UserManager;

use Hui\Xproject\Entities\User;

class UserPolicy{
  public function edit(User $user,User $editedUser):bool{
    switch(true){
      case $user->isAdmin():
        return true;
      case $user->isManager():
        return $editedUser->isFreelancer();
      case $user->isEmployer():
        return $editedUser->isFreelancer()&&$editedUser->isCreatedBy($user);
      default:
        return false;
    }
  }
}
