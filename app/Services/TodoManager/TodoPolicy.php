<?php

namespace Hui\Xproject\Services\TodoManager;

use Hui\Xproject\Entities\Project;
use Hui\Xproject\Entities\Todo;
use Hui\Xproject\Entities\User;

class TodoPolicy{
  public function __construct(TodoManager $todoManager){
    $this->todoManager=$todoManager;
  }

  public function view(User $user,Todo $todo):bool{
    if(!$todo->exists)
      return true;

    /**
     * @see TodoManager::restrictQueryForUser()
     */
    switch($user->role){
      case User::ROLE_ADMIN:
      case User::ROLE_MANAGER:
        return true;
      case User::ROLE_FREELANCER:
        if($todo->trashed())
          return false;
        if($user->isPrivateFreelancer()&&(!$todo->private||$todo->user_id!==$user->created_by_user_id))
          return false;
        foreach($todo->projects as $project)
          if($project->statusWithDeleted()===Project::STATUS_UNASSIGNED||$project->user_id===$user->id)
            return true;

        return false;
      case User::ROLE_EMPLOYER:
        return !$todo->trashed()&&$todo->user_id===$user->id;
      default:
        throw new TodoManagerException('Wrong role');
    }
  }

  public function create(User $user):bool{
    return $user->isManagerOrAdmin();
  }

  public function createPrivate(User $user):bool{
    return $user->isEmployer();
  }

  public function edit(User $user,Todo $todo):bool{
    if(!$this->view($user,$todo))
      return false;

    if(!$todo->private)
      return $this->create($user);
    else
      return $this->createPrivate($user);
  }

  public function clone(User $user,Todo $todo):bool{
    return $this->edit($user,$todo);
  }

  public function delete(User $user,Todo $todo):bool{
    return $this->view($user,$todo)&&$user->isManagerOrAdmin()&&in_array($todo->status(),[
        Todo::STATUS_UNASSIGNED,
        Todo::STATUS_EMPLOYER_UNPAID,
        Todo::STATUS_EMPLOYER_UNCONFIRMED
      ]);
  }

  public function pay(User $user,Todo $todo):bool{
    return $this->view($user,$todo)&&$this->todoManager->canBePaid($todo)&&$todo->user_id===$user->id;
  }

  public function confirm(User $user,Todo $todo):bool{
    return $this->view($user,$todo)&&$user->isManagerOrAdmin()&&$todo->status()===Todo::STATUS_EMPLOYER_UNCONFIRMED;
  }

  public function viewProjects(User $user,Todo $todo):bool{
    return $this->view($user,$todo)&&$todo->employer_status===null&&$this->view($user,$todo);
  }

  public function viewPayments(User $user,Todo $todo):bool{
    return $this->view($user,$todo)&&$user->isManagerOrAdmin();
  }

  private $todoManager;
}
