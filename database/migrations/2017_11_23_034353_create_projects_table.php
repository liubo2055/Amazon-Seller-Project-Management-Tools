<?php

use Hui\Xproject\Entities\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration{
  public function up():void{
    Schema::create('projects',function(Blueprint $table):void{
      $table->increments('id');
      $table->integer('todo_id');
      $table->integer('user_id')
        ->nullable();
      $table->integer('number');
      $table->string('project_title')
        ->nullable();
      $table->string('product_title')
        ->nullable();
      $table->string('project_description')
        ->nullable();
      $table->string('store_description')
        ->nullable();
      $table->string('product_description')
        ->nullable();
      $table->string('notes')
        ->nullable();
      $table->decimal('project_price',7,2);
      $table->enum('status',Project::STATUSES);
      $table->string('completion_id',20)
        ->nullable();
      $table->string('completion_url')
        ->nullable();
      $table->string('original_todo_code',20);
      $table->string('original_product_description')
        ->nullable();
      $table->string('original_notes')
        ->nullable();
      $table->decimal('original_project_price',7,2);
      $table->timestamp('accepted_at')
        ->nullable();
      $table->timestamp('completion_id_submitted_at')
        ->nullable();
      $table->timestamp('completed_at')
        ->nullable();
      $table->timestamp('created_at');
      $table->timestamp('updated_at');
      $table->softDeletes();

      $table->foreign('todo_id')
        ->references('id')
        ->on('todos')
        ->onUpdate('CASCADE')
        ->onDelete('CASCADE');
      $table->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onUpdate('CASCADE')
        ->onDelete('RESTRICT');
    });
  }

  public function down():void{
    Schema::drop('projects');
  }
}
