<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegisterDateColumn extends Migration{
  public function up():void{
    Schema::table('users',function(Blueprint $table):void{
      $table->string('register_date')
        ->nullable();
    });
  }

  public function down():void{
    Schema::table('users',function(Blueprint $table):void{
      $table->dropColumn('register_date');
    });
  }
}
