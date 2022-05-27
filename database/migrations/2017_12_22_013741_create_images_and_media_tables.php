<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesAndMediaTables extends Migration{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up(){
    Schema::create('images',function(Blueprint $table):void{
      $table->increments('id');
      $table->integer('project_id');
      $table->string('path');
      $table->string('url');
      $table->timestamp('created_at');
      $table->timestamp('updated_at');

      $table->foreign('project_id')
        ->references('id')
        ->on('projects')
        ->onUpdate('CASCADE')
        ->onDelete('CASCADE');
    });

    Schema::create('medias',function(Blueprint $table):void{
      $table->increments('id');
      $table->integer('project_id');
      $table->string('content');
      $table->timestamp('created_at');
      $table->timestamp('updated_at');

      $table->foreign('project_id')
        ->references('id')
        ->on('projects')
        ->onUpdate('CASCADE')
        ->onDelete('CASCADE');
    });
  }

  public function down(){
    Schema::drop('medias');
    Schema::drop('images');
  }
}
