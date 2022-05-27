<?php

namespace Hui\Xproject\Services\ProjectManager;

use Hui\Xproject\Entities\Project;
use Hui\Xproject\Entities\User;

class ProjectPolicy{
  public function view(User $user,Project $project):bool{
    return $user->can('view',$project->todo);
  }

  public function accept(User $user,Project $project):bool{
    return $this->view($user,$project)&&$user->isFreelancer()&&$project->statusWithDeleted()===Project::STATUS_UNASSIGNED;
  }

  public function submitCompletionId(User $user,Project $project):bool{
    return $this->view($user,$project)&&$user->isFreelancer()&&$project->user_id===$user->id&&$project->statusWithDeleted()===Project::STATUS_ACCEPTED;
  }

  public function submitCompletionUrl(User $user,Project $project):bool{
    return $this->view($user,$project)&&$user->isFreelancer()&&$project->user_id===$user->id&&$project->statusWithDeleted()===Project::STATUS_WAITING_URL;
  }

  public function editTodoAttrs(User $user,Project $project):bool{
    if(!$this->view($user,$project)||in_array($project->statusWithDeleted(),[
        Project::STATUS_COMPLETED,
        Project::STATUS_FAILED
      ]))
      return false;

    if(!$project->todo->private)
      return $user->isManagerOrAdmin();
    else
      return $user->isEmployer();
  }

  public function editProjectAttrs(User $user,Project $project):bool{
    return $this->view($user,$project)&&($user->isManagerOrAdmin()||$user->isEmployer())&&!in_array($project->statusWithDeleted(),[
        Project::STATUS_COMPLETED,
        Project::STATUS_FAILED
      ]);
  }

  public function editStoreDescription(User $user,Project $project):bool{
    return $this->view($user,$project)&&$user->isManagerOrAdmin()&&$this->editProjectAttrs($user,$project);
  }

  public function complete(User $user,Project $project):bool{
    if(!$this->view($user,$project)||$project->statusWithDeleted()!==Project::STATUS_WAITING_CONFIRMATION)
      return false;

    if(!$project->todo->private)
      return $user->isManagerOrAdmin();
    else
      return $user->isEmployer();
  }

  public function fail(User $user,Project $project):bool{
    if(!$this->view($user,$project))
      return false;

    if($project->user_id===$user->id)
      if($user->isManagerOrAdmin())
        return !in_array($project->statusWithDeleted(),[
          Project::STATUS_UNASSIGNED,
          Project::STATUS_FAILED,
          Project::STATUS_DELETED
        ]);
      else if($user->isFreelancer())
        return !in_array($project->statusWithDeleted(),[
          Project::STATUS_ACCEPTED,
          Project::STATUS_UNASSIGNED,
          Project::STATUS_FAILED,
          Project::STATUS_DELETED
        ]);

    return false;
  }

  public function delete(User $user,Project $project):bool{
    return $this->view($user,$project)&&$user->isManagerOrAdmin()&&$project->statusWithDeleted()===Project::STATUS_UNASSIGNED;
  }

  public function restore(User $user,Project $project):bool{
    return $this->view($user,$project)&&$user->isManagerOrAdmin()&&$project->trashed();
  }

  public function cancel(User $user,Project $project):bool{
    return $this->view($user,$project)&&$user->isFreelancer()&&$project->user_id===$user->id&&$project->statusWithDeleted()===Project::STATUS_ACCEPTED;
  }
}
