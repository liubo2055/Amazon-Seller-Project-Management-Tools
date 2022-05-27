<?php

use Hui\Xproject\Entities\Todo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployerStatusColumn extends Migration{
  public function up():void{
    Schema::table('todos',function(Blueprint $table):void{
      $table->enum('employer_status',Todo::EMPLOYER_STATUSES)
        ->nullable();
    });
  }

  public function down():void{
    Schema::table('todos',function(Blueprint $table):void{
      $table->dropColumn('employer_status');
    });
  }
}
