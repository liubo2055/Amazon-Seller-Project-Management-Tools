<?php

use Hui\Xproject\Services\MarketplaceInformation\MarketplaceInformation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTodosTable extends Migration{
  public function up():void{
    Schema::create('todos',function(Blueprint $table):void{
      /**
       * @var MarketplaceInformation $marketplaceInformation
       */
      $marketplaceInformation=app(MarketplaceInformation::class);

      $table->increments('id');
      $table->string('code',20)
        ->unique();
      $table->boolean('fba');
      $table->string('product_url');
      $table->string('product_asin',10);
      $table->enum('marketplace',$marketplaceInformation->marketplaceCodes());
      $table->decimal('product_price',7,2);
      $table->string('product_title')
        ->nullable();
      $table->string('product_description')
        ->nullable();
      $table->string('seller_name')
        ->nullable();
      $table->string('storefront_url')
        ->nullable();
      $table->string('seller_id',20)
        ->nullable();
      $table->string('notes')
        ->nullable();
      $table->integer('daily_limit');
      $table->timestamp('created_at');
      $table->timestamp('updated_at');
      $table->softDeletes();
    });
  }

  public function down():void{
    Schema::drop('todos');
  }
}
