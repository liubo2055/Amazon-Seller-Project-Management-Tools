<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStorefrontsColumn extends Migration{
  public function up():void{
    Schema::table('users',function(Blueprint $table):void{
      $table->json('storefronts')
        ->nullable();
    });
  }

  public function down():void{
    Schema::table('users',function(Blueprint $table):void{
      $table->dropColumn('storefronts');
    });
  }
}
