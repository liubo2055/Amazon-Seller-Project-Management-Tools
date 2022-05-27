<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ExtendProjectDescriptionColumn extends Migration{
  public function up():void{
    // The statistics view must be dropped before updating the column type
    DB::statement('DROP VIEW statistics');
    DB::statement('ALTER TABLE projects ALTER project_description TYPE VARCHAR(5000)');

    $updateStatisticsView=new UpdateStatisticsView;
    $updateStatisticsView->up();
  }
}
