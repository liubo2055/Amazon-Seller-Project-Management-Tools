<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjectTitleDescriptionColumn extends Migration{
  public function up():void{
    Schema::table('todos',function(Blueprint $table):void{
      $table->boolean('project_title_description')
        // Update existing to-dos
        ->default(true);
    });

    DB::statement('ALTER TABLE todos ALTER project_title_description DROP DEFAULT');
  }

  public function down():void{
    Schema::table('todos',function(Blueprint $table):void{
      $table->dropColumn('project_title_description');
    });
  }
}
