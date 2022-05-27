<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPrivateColumn extends Migration{
  public function up():void{
    Schema::table('todos',function(Blueprint $table):void{
      $table->boolean('private')
        // Upgrade existing to-dos
        ->default(false);
    });

    DB::statement('ALTER TABLE todos ALTER private SET NOT NULL');
  }

  public function down():void{
    Schema::table('todos',function(Blueprint $table){
      $table->dropColumn('private');
    });
  }
}
