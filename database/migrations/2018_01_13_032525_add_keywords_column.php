<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeywordsColumn extends Migration{
  public function up():void{
    Schema::table('todos',function(Blueprint $table):void{
      $table->json('keywords')
        ->nullable();
    });
  }

  public function down():void{
    Schema::table('todos',function(Blueprint $table):void{
      $table->dropColumn('keywords');
    });
  }
}
