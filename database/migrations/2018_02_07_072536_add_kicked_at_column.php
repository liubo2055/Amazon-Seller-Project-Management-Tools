<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKickedAtColumn extends Migration{
  public function up():void{
    Schema::table('users',function(Blueprint $table):void{
      $table->datetime('kicked_at')
        ->nullable();
    });
  }

  public function down():void{
    Schema::table('users',function(Blueprint $table):void{
      $table->dropColumn('kicked_at');
    });
  }
}
