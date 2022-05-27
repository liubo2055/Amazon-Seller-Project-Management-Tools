<?php

use Hui\Xproject\Entities\Project;
use Hui\Xproject\Entities\Todo;
use Hui\Xproject\Entities\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectsSeeder extends Seeder{
  public function run():void{
    if(Project::count())
      return;

    $now=now();

    $userIds=User::findByRole(User::ROLE_FREELANCER)
      ->pluck('id');
    $statuses=Project::STATUSES;

    foreach(Todo::all() as $todo)
      for($i=mt_rand(0,30),$number=1;$i>0;$i--,$number++){
        $status=array_random($statuses);

        DB::table('projects')
          ->insert([
            'todo_id'=>$todo->id,
            'user_id'=>$status===Project::STATUS_UNASSIGNED?null:$userIds->random(),
            'number'=>$number,
            'project_title'=>sprintf('Project %u/%u title',
              $todo->id,
              $number),
            'product_title'=>mt_rand(0,5)?$todo->product_title:null,
            'project_description'=>sprintf('Project %u/%u description',
              $todo->id,
              $number),
            'store_description'=>sprintf('Project %u/%u store description',
              $todo->id,
              $number),
            'product_description'=>mt_rand(0,5)?$todo->product_description:null,
            'notes'=>mt_rand(0,5)?$todo->notes:null,
            'project_price'=>mt_rand(1,100000)/100,
            'status'=>$status,
            'completion_id'=>$status===Project::STATUS_COMPLETED?strtoupper(str_random(8)):null,
            'completion_url'=>$status===Project::STATUS_COMPLETED?sprintf('http://%s.com',strtolower(str_random(8))):null,
            'original_todo_code'=>$todo->code,
            'original_product_description'=>$todo->product_description,
            'original_notes'=>$todo->notes,
            'original_project_price'=>mt_rand(1,100000)/100,
            'accepted_at'=>$status!==Project::STATUS_UNASSIGNED?$now->copy()
              ->addDays(mt_rand(0,30)):null,
            'completed_at'=>$status===Project::STATUS_COMPLETED?$now->copy()
              ->addDays(mt_rand(0,30)):null,
            'created_at'=>$now->copy()
              ->subDays($i),
            'updated_at'=>$now
          ]);
      }
  }
}
