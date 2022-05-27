<?php

namespace Hui\Xproject\Services;

use Hui\AppsIoLaravel\Models\User as AppsIoUser;
use Hui\AppsIoLaravel\Services\AppsIoHandler as AppsIoHandlerInterface;
use Hui\AppsIoLaravel\Services\AppsIoHandlerTrait;
use Hui\Xproject\Entities\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;

class AppsIoHandler implements AppsIoHandlerInterface{
  use AppsIoHandlerTrait;

  public function redirect(?string $redirectTo):RedirectResponse{
    if($redirectTo&&starts_with($redirectTo,url('')))
      return redirect($redirectTo);
    else
      return redirect()->route('todos');
  }

  public function findUser(int $appsIoId):?Authenticatable{
    return User
      ::where('apps_io_user_id',$appsIoId)
      ->first();
  }

  public function newUser():Authenticatable{
    return new User;
  }

  public function updateUserAttrs(Authenticatable $user,AppsIoUser $appsIoUser):void{
    /**
     * @var User $user
     */
    $this->defaultUpdateUserAttrs($user,$appsIoUser);

    $user->role=$appsIoUser->freeMember?User::ROLE_FREE_EMPLOYER:User::ROLE_EMPLOYER;
  }

  public function logoutUser(Authenticatable $user):void{
    /**
     * @var User $user
     */
    $user->kicked_at=now();
    $user->save();

    logger(sprintf('User %s kicked',$user->email));
  }

  public function blockUser(Authenticatable $user):void{
    /**
     * @var User $user
     */
    $user->blocked_at=now();
    $user->save();

    logger(sprintf('User %s blocked',$user->email));
  }

  public function isCurrentUserRemote():bool{
    return user()&&user()->apps_io_user_id;
  }
}
