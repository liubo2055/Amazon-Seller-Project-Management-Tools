<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByUserIdColumn extends Migration{
  public function up():void{
    Schema::table('users',function(Blueprint $table):void{
      $table->integer('created_by_user_id')
        ->nullable();

      $table->foreign('created_by_user_id')
        ->references('id')
        ->on('users')
        ->onUpdate('CASCADE')
        ->onDelete('RESTRICT');
    });
  }

  public function down():void{
    Schema::table('users',function(Blueprint $table){
      $table->dropColumn('created_by_user_id');
    });
  }
}
