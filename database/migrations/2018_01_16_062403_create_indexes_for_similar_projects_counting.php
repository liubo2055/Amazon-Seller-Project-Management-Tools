<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndexesForSimilarProjectsCounting extends Migration{
  public function up():void{
    Schema::table('todos',function(Blueprint $table):void{
      $table->index('product_asin');
      $table->index('storefront_name');
      $table->index('seller_id');
    });
  }

  public function down():void{
    Schema::table('todos',function(Blueprint $table):void{
      $table->dropIndex([
        'product_asin',
        'storefront_name',
        'seller_id'
      ]);
    });
  }
}
