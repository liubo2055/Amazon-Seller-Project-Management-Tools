<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppsIoUserIdColumn extends Migration{
  public function up():void{
    Schema::table('users',function(Blueprint $table):void{
      $table->integer('apps_io_user_id')
        ->nullable();
    });
  }

  public function down():void{
    Schema::table('users',function(Blueprint $table):void{
      $table->dropColumn('apps_io_user_id');
    });
  }
}
