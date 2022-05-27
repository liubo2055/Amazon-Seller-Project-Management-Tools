<?php

namespace Hui\Xproject\Services\ProjectManager;

use Carbon\Carbon;
use Hui\Xproject\Entities\Project;
use Hui\Xproject\Entities\Todo;
use Hui\Xproject\Entities\User;
use Hui\Xproject\Services\TodoManager\TodoManager;
use Hui\Xproject\Services\TodoManager\TodoPolicy;
use Illuminate\Database\Eloquent\Builder;

class ProjectManager{
  public function __construct(TodoManager $todoManager){
    $this->todoManager=$todoManager;
  }

  public function restrictQueryForUser(Builder $query,User $user):Builder{
    /**
     * @see TodoPolicy::viewProjects()
     */
    $this->fromExistingAndConfirmedTodos($query);

    return $query->whereIn('todo_id',$this->todoManager->restrictQueryForUser(Todo::select('id'),$user));
  }

  /**
   * @param Todo $todo
   * @param int  $count
   * @param bool $populateProjectTitleDescription
   * @return Project[]
   * @throws ProjectManagerException
   */
  public function createProjectsForNewTodo(Todo $todo,int $count,bool $populateProjectTitleDescription):array{
    if(!$todo->exists)
      throw new ProjectManagerException('The to-do must exist');

    $projects=[];

    for($i=0;$i<$count;$i++){
      $project=new Project;
      $project->todo_id=$todo->id;
      $project->number=$i+1;
      $project->product_title=$todo->product_title;
      $project->product_description=$todo->product_description;
      $project->notes=$todo->notes;
      $project->project_price=$todo->product_price;
      $project->status=Project::STATUS_UNASSIGNED;
      $project->original_todo_code=$todo->code;
      $project->original_product_description=$todo->product_description;
      $project->original_notes=$todo->notes;
      $project->original_project_price=$todo->product_price;

      if($populateProjectTitleDescription){
        $project->project_title=_ix('Freelancer\'s responsibility','Project manager');
        $project->project_description=_ix('Freelancer\'s responsibility','Project manager');
      }

      $projects[]=$project;
    }

    return $projects;
  }

  public function accept(Project $project,User $user):void{
    // Precondition: the project can be accepted

    $project->user_id=$user->id;
    $project->status=Project::STATUS_ACCEPTED;
    $project->accepted_at=now();
  }

  public function updateProjectsFromTodo(Todo $todo):void{
    foreach($todo->projects as $project){
      $project->product_title=$todo->product_title;
      $project->product_description=$todo->product_description;
      $project->notes=$todo->notes;
      $project->project_price=$todo->product_price;
    }
  }

  public function unassign(Project $project):void{
    $project->user_id=null;
    $project->status=Project::STATUS_UNASSIGNED;
    $project->accepted_at=null;
  }

  public function submitId(Project $project,string $completionId):void{
    // Precondition: the project can have a completion ID submitted

    $project->status=Project::STATUS_WAITING_DESCRIPTION;
    $project->completion_id=$completionId;
    $project->completion_id_submitted_at=now();

    $this->changeStatusBasedOnDescription($project);
  }

  public function submitUrl(Project $project,string $completionUrl):void{
    // Precondition: the project can have a completion URL submitted

    $project->status=Project::STATUS_WAITING_CONFIRMATION;
    $project->completion_url=$completionUrl;
  }

  public function complete(Project $project):void{
    // Precondition: the project can be completed

    $project->status=Project::STATUS_COMPLETED;
    $project->completed_at=now();
  }

  public function fail(Project $project):void{
    // Precondition: the project can be failed

    $project->status=Project::STATUS_FAILED;
  }

  public function cancel(Project $project):void{
    // Precondition: the project can be canceled

    $this->unassign($project);
  }

  public function filterByFreelancerName(Builder $query,string $name):void{
    if($name==='')
      return;

    $sql=<<<END
user_id IN (
  SELECT id
  FROM users
  WHERE name LIKE ?
)

END;

    $search=sprintf('%%%s%%',mb_strtolower($name));
    $query->whereRaw($sql,[$search]);
  }

  public function filterByTodoAttrs(Builder $query,array $attrs,string $value):void{
    if($value==='')
      return;

    $where=array_map(function(string $attr):string{
      return sprintf('lower(%s) LIKE ?',$attr);
    },$attrs);
    $where=implode(' OR ',$where);

    $sql=<<<END
todo_id IN (
  SELECT id
  FROM todos
  WHERE {$where}
)

END;

    $search=sprintf('%%%s%%',mb_strtolower($value));
    $bindings=array_fill(0,count($attrs),$search);
    $query->whereRaw($sql,$bindings);
  }

  public function changeStatusBasedOnDescription(Project $project):void{
    if($project->statusWithDeleted()===Project::STATUS_WAITING_DESCRIPTION&&(string)$project->project_description!=='')
      $project->status=Project::STATUS_WAITING_URL;
  }

  public function filterByStatuses(Builder $query,array $statuses):void{
    if(!$statuses){
      $query->whereRaw('FALSE');

      return;
    }

    $deleted=in_array(Project::STATUS_DELETED,$statuses);

    if($deleted){
      $statuses=array_filter($statuses,function(string $status):bool{
        return $status!==Project::STATUS_DELETED;
      });

      if(!$statuses){
        $query->onlyTrashed();

        return;
      }
      else
        $query->withTrashed();
    }

    $query->where(function(Builder $query) use ($statuses,$deleted):void{
      $query->whereIn('status',$statuses);
      if($deleted)
        $query->orWhereNotNull('deleted_at');
    });
  }

  public function similarProjectsQuery(Project $project,User $user,?Carbon $minDate,string $attr):Builder{
    $query=Project::query();

    $value=$project->todo->{$attr};
    if((string)$value===''){
      $query->whereRaw('FALSE');

      return $query;
    }

    $sql=<<<END
todo_id IN (
  SELECT id
  FROM todos
  WHERE {$attr}=?
)

END;

    $query
      ->whereRaw($sql,[$value])
      ->whereIn('status',[
        Project::STATUS_ACCEPTED,
        Project::STATUS_WAITING_DESCRIPTION,
        Project::STATUS_WAITING_URL,
        Project::STATUS_WAITING_CONFIRMATION,
        Project::STATUS_COMPLETED
      ])
      ->where('user_id',$user->id);
    if($minDate)
      $query->where('created_at','>=',$minDate);

    return $query;
  }

  private function fromExistingAndConfirmedTodos(Builder $query):void{
    $query->whereIn('todo_id',Todo
      ::select('id')
      ->whereNull('employer_status')
      ->withoutTrashed());
  }

  private $todoManager;
}
