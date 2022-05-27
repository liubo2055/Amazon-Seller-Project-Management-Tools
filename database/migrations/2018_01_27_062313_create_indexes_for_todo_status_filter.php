<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndexesForTodoStatusFilter extends Migration{
  public function up():void{
    Schema::table('todos',function(Blueprint $table):void{
      $table->index('employer_status');
    });

    Schema::table('projects',function(Blueprint $table):void{
      $table->index([
        'todo_id',
        'status',
        'deleted_at'
      ]);
    });
  }

  public function down():void{
    Schema::table('todos',function(Blueprint $table):void{
      $table->dropIndex(['employer_status']);
    });

    Schema::table('projects',function(Blueprint $table):void{
      $table->dropIndex([
        'todo_id',
        'status',
        'deleted_at'
      ]);
    });
  }
}
