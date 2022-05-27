<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddHuistoreGatewayColumn extends Migration{
  public function up():void{
    Schema::table('payments',function(Blueprint $table):void{
      $table->boolean('huistore_gateway')
        // Upgrade existing payments
        ->default(false);
    });

    DB::statement('ALTER TABLE payments ALTER huistore_gateway SET NOT NULL');
  }

  public function down():void{
    Schema::table('payments',function(Blueprint $table){
      $table->dropColumn('huistore_gateway');
    });
  }
}
