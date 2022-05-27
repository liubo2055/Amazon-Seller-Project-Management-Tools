<?php

namespace Hui\Xproject\Console\Commands;

use Hui\Xproject\Entities\Project;
use Hui\Xproject\Services\ProjectManager\ProjectManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckCompletionIds extends Command{
  public function handle(ProjectManager $projectManager):void{
    $limit=config('limits.accepted_projects_without_completion_id');

    $this->info(sprintf('Checking accepted projects at %s',now()->format('Y-m-d H:i:s')),'vv');

    DB::beginTransaction();

    foreach(Project::findByStatusAndLock(Project::STATUS_ACCEPTED) as $project){
      if(!$project->accepted_at){
        $this->error(sprintf('Accepted at for project %u not available',$project->id));
        continue;
      }

      $hours=$project->accepted_at->diffInHours();

      if($hours>=$limit){
        $this->info(sprintf('Project %u accepted by user %u at %s (%u>=%u hours ago), removing assignment',
          $project->id,
          $project->user_id,
          $project->accepted_at->format('Y-m-d H:i:s'),
          $hours,
          $limit),'v');

        $projectManager->unassign($project);
        $project->save();
      }
    }

    DB::commit();
  }

  protected $signature='xp:check-completion-ids';

  protected $description='Removes assignment from projects with no completion ID set after a deadline';
}
