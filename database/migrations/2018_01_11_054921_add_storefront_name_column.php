<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStorefrontNameColumn extends Migration{
  public function up():void{
    Schema::table('todos',function(Blueprint $table):void{
      $table->string('storefront_name')
        // Update existing to-dos
        ->default('');
    });


    DB::statement('ALTER TABLE todos ALTER storefront_name DROP DEFAULT');
  }

  public function down():void{
    Schema::table('todos',function(Blueprint $table):void{
      $table->dropColumn('storefront_name');
    });
  }
}
