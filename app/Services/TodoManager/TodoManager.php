<?php

namespace Hui\Xproject\Services\TodoManager;

use Hui\Xproject\Entities\Project;
use Hui\Xproject\Entities\Todo;
use Hui\Xproject\Entities\User;
use Illuminate\Database\Eloquent\Builder;

class TodoManager{
  public function restrictQueryForUser(Builder $query,User $user):Builder{
    switch($user->role){
      case User::ROLE_ADMIN:
      case User::ROLE_MANAGER:
        break;
      case User::ROLE_FREELANCER:
        $query
          ->withoutTrashed()
          ->whereNull('employer_status')
          ->whereIn('id',Project
            ::select('todo_id')
            ->where('status',Project::STATUS_UNASSIGNED)
            ->orWhere('user_id',$user->id));
        if(!$user->isPrivateFreelancer())
          $query->where('private',false);
        else
          $query
            ->where('private',true)
            ->where('user_id',$user->created_by_user_id);
        break;
      case User::ROLE_EMPLOYER:
        $query
          ->withoutTrashed()
          ->where('user_id',$user->id);
        break;
      default:
        throw new TodoManagerException('Wrong role');
    }

    return $query;
  }

  public function progress(Todo $todo):array{
    $statuses=Project::statusesWithDeleted();
    $statuses=array_combine($statuses,array_fill(0,count($statuses),0));

    foreach($todo->projectsWithTrashed as $project)
      $statuses[$project->statusWithDeleted()]++;

    return $statuses;
  }

  public function filterByStatuses(Builder $query,array $statuses):void{
    if(!$statuses){
      $query->whereRaw('FALSE');

      return;
    }

    $unassigned=false;
    $accepted=false;
    $completed=false;
    $deleted=false;
    $employerUnpaid=false;
    $employerUnconfirmed=false;

    foreach($statuses as $status)
      switch($status){
        case Todo::STATUS_UNASSIGNED:
          $unassigned=true;
          break;
        case Todo::STATUS_ACCEPTED:
          $accepted=true;
          break;
        case Todo::STATUS_COMPLETED:
          $completed=true;
          break;
        case Todo::STATUS_DELETED:
          $deleted=true;
          break;
        case Todo::STATUS_EMPLOYER_UNPAID:
          $employerUnpaid=true;
          break;
        case Todo::STATUS_EMPLOYER_UNCONFIRMED:
          $employerUnconfirmed=true;
          break;
      }

    if($deleted)
      if(!$unassigned&&!$accepted&&!$completed&&!$employerUnpaid&&!$employerUnconfirmed){
        $query->onlyTrashed();

        return;
      }
      else
        $query->withTrashed();

    if($unassigned&&$accepted&&$completed&&$employerUnpaid&&$employerUnconfirmed)
      return;

    /**
     * @see TodoManager::todoStatus()
     */

    $sql=[];
    $bindings=[];

    if($unassigned){
      // Exclude to-dos with at least one non-unassigned project; include only to-dos with at least one project
      $sql[]=<<<END
(
  employer_status IS NULL
  AND id NOT IN (
    SELECT todo_id
    FROM projects
    WHERE status!=?
    OR deleted_at IS NOT NULL
  )
  AND id IN (
    SELECT todo_id
    FROM projects
  )
)

END;
      $bindings[]=Project::STATUS_UNASSIGNED;
    }

    if($accepted){
      // Include to-dos with non-unassigned and non-completed projects (both kinds in the same to-do); include only to-dos with at least one project
      $sql[]=<<<END
(
  employer_status IS NULL
  AND id IN (
    SELECT todo_id
    FROM projects non_unassigned_projects
    JOIN projects non_completed_projects
    USING (todo_id)
    WHERE (
      non_unassigned_projects.status!=?
      OR non_unassigned_projects.deleted_at IS NOT NULL
    )
    AND non_completed_projects.status NOT IN (?,?)
    AND non_completed_projects.deleted_at IS NULL
  )
  AND id IN (
    SELECT todo_id
    FROM projects
  )
)

END;

      $bindings[]=Project::STATUS_UNASSIGNED;
      $bindings[]=Project::STATUS_COMPLETED;
      $bindings[]=Project::STATUS_FAILED;
    }

    if($completed){
      // Exclude to-dos with at least one non-completed project
      $sql[]=<<<END
(
  employer_status IS NULL
  AND id NOT IN (
    SELECT todo_id
    FROM projects
    WHERE status NOT IN (?,?)
    AND deleted_at IS NULL
  )
)

END;
      $bindings[]=Project::STATUS_COMPLETED;
      $bindings[]=Project::STATUS_FAILED;
    }

    // This is equivalent to $query->orOnlyTrashed(), which does not exist
    if($deleted)
      $sql[]='deleted_at IS NOT NULL';

    $sql=implode(' OR ',$sql);
    // Avoid "WHERE (OR..." when $sql is empty
    if(!$sql)
      $sql='FALSE';

    $query->where(function(Builder $query) use ($sql,$bindings,$employerUnpaid,$employerUnconfirmed):void{
      $query->whereRaw($sql,$bindings);

      if($employerUnpaid)
        $query->orWhere('employer_status',Todo::STATUS_EMPLOYER_UNPAID);
      if($employerUnconfirmed)
        $query->orWhere('employer_status',Todo::STATUS_EMPLOYER_UNCONFIRMED);
    });
  }

  public function filterByFreelancerName(Builder $query,string $name):void{
    if($name==='')
      return;

    $sql=<<<END
id IN (
  SELECT todo_id
  FROM projects
  JOIN users
  ON user_id=users.id
  WHERE name LIKE ?
)

END;

    $search=sprintf('%%%s%%',mb_strtolower($name));
    $query->whereRaw($sql,[$search]);
  }

  public function mustBePaidBy(Todo $todo,User $user):bool{
    return !$todo->exists&&!$todo->private&&$user->isEmployer();
  }

  public function limitReachedForTodo(Todo $todo):bool{
    $count=0;
    foreach($todo->projects as $project)
      if($project->accepted_at&&$project->accepted_at->diffInHours()<24)
        if(++$count>=$todo->daily_limit)
          return true;

    return false;
  }

  public function initialize(Todo $todo,User $user):void{
    // Precondition: the to-do is new

    $todo->fba=true;
    $todo->project_title_description=false;

    if($user->isEmployer())
      $todo->seller_name=$user->name;
  }

  public function clone(Todo $todo,User $user):Todo{
    $clonedTodo=new Todo;
    $this->initialize($clonedTodo,$user);

    /**
     * @see Todo::$id
     * @see Todo::$code
     */
    foreach(array_keys($todo->getAttributes()) as $attribute)
      if($attribute!=='id'&&$attribute!=='code')
        // Don't use $value iterating attributes, as those values don't apply casting to attributes like keywords
        $clonedTodo->{$attribute}=$todo->{$attribute};

    return $clonedTodo;
  }

  public function disabledFields(Todo $todo,User $user):array{
    if($todo->exists)
      return [
        'code',
        'product_url',
        'product_asin',
        'marketplace',
        'storefront_url',
        'seller_id',
        'project_title_description'
      ];
    else if($user->isEmployer())
      return [
        'code',
        'product_asin',
        'marketplace',
        'seller_name',
        'seller_id'
      ];
    else
      return [];
  }

  public function ignoredFields(Todo $todo,User $user):array{
    if($todo->exists)
      return $this->disabledFields($todo,$user);
    /**
     * @see TodoManager::mustGenerateCode()
     */
    else if($user->isEmployer())
      return ['code'];
    else
      return [];
  }

  public function canBePaid(Todo $todo):bool{
    return $todo->status()===Todo::STATUS_EMPLOYER_UNPAID;
  }

  public function markAsPaid(Todo $todo):void{
    // Precondition: the to-do can be marked as paid

    $todo->employer_status=Todo::STATUS_EMPLOYER_UNCONFIRMED;
    $todo->created_at=now();
  }

  public function confirm(Todo $todo):void{
    // Precondition: the to-do can be confirmed

    $todo->employer_status=null;
  }

  public function mustProvideStorefrontUrl(User $user):bool{
    return $user->isEmployer();
  }

  public function codeRegexp():string{
    // To allow any alphanumeric code, return |^[a-zA-Z0-9-]+$|

    /**
     * @see TodoManager::generateCode()
     */
    // Allow any alphanumeric code except "E" or "e" and one or more digits
    return '/^[a-df-zA-DF-Z0-9-].*$|^E\d*[a-zA-Z-]\d*$|^E$|^e$/';
  }

  public function mustGenerateCode(Todo $todo,User $user):bool{
    return !$todo->exists&&$user->isEmployer();
  }

  public function generateCode():string{
    // Preconditions: the code must be generated; concurrent access to the to-dos table is disabled

    $lastTodo=Todo
      ::whereRaw('code ~ \'^E\\d{7}$\'')
      ->withTrashed()
      ->orderBy('code','DESC')
      ->first();

    if(!$lastTodo)
      $number=1;
    else
      $number=(int)substr($lastTodo->code,1)+1;

    return sprintf('E%07u',$number);
  }

  public function todoStatus(Todo $todo):string{
    // This must be the first check
    if($todo->trashed())
      return Todo::STATUS_DELETED;
    else if($todo->employer_status)
      return $todo->employer_status;
    else if($todo->projects->isEmpty())
      return Todo::STATUS_COMPLETED;

    $allUnassigned=true;
    $allCompleted=true;

    $unassignedStatuses=[Project::STATUS_UNASSIGNED];
    $completedStatuses=[
      Project::STATUS_COMPLETED,
      Project::STATUS_FAILED,
      Project::STATUS_DELETED
    ];

    foreach($todo->projects as $project){
      $projectStatus=$project->statusWithDeleted();

      if(!in_array($projectStatus,$unassignedStatuses))
        $allUnassigned=false;
      if(!in_array($projectStatus,$completedStatuses))
        $allCompleted=false;
    }

    if($allUnassigned)
      return Todo::STATUS_UNASSIGNED;
    else if($allCompleted)
      return Todo::STATUS_COMPLETED;
    else
      return Todo::STATUS_ACCEPTED;
  }
}
