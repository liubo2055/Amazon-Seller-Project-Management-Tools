<?php

namespace Hui\Xproject\Services\UserManager;

use Hui\Xproject\Entities\User;
use Illuminate\Database\Eloquent\Builder;

class UserManager{
  public function restrictQueryForUser(Builder $query,?User $user):Builder{
    $query->whereIn('role',$this->editableRolesFor($user));
    if($this->canOnlyCreatePrivateFreelancers($user))
      $query->where('created_by_user_id',$user->id);

    return $query;
  }

  public function canImportUsers(User $user):bool{
    return $user->isAdmin();
  }

  public function editableRolesFor(?User $user):array{
    switch(true){
      case !$user:
        return [];
        break;
      case $user->isAdmin():
        return User::ROLES;
      case $user->isManager():
        return [
          User::ROLE_EMPLOYER,
          User::ROLE_FREELANCER
        ];
        break;
      case $user->isEmployer():
        return [User::ROLE_FREELANCER];
        break;
      default:
        return [];
    }
  }

  public function canOnlyCreatePrivateFreelancers(User $user):bool{
    return $user->isEmployer();
  }

  public function rolesForCreatorUsers():array{
    return [User::ROLE_EMPLOYER];
  }
}
