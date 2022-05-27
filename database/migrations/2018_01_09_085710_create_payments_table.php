<?php

use Hui\Xproject\Entities\Payment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration{
  public function up():void{
    Schema::create('payments',function(Blueprint $table):void{
      $table->increments('id');
      $table->integer('todo_id');
      $table->string('code');
      $table->decimal('amount',7,2);
      $table->decimal('amount_due',7,2)
        ->nullable();
      $table->decimal('amount_paid',7,2)
        ->nullable();
      $table->enum('status',Payment::STATUSES);
      $table->string('buyer_email')
        ->nullable();
      $table->string('buyer_id')
        ->nullable();
      $table->string('notify_id')
        ->nullable();
      $table->json('response')
        ->nullable();
      $table->text('error')
        ->nullable();
      $table->timestamp('created_at');
      $table->timestamp('updated_at');
      $table->timestamp('deleted_at')
        ->nullable();

      $table->unique('code');

      $table->foreign('todo_id')
        ->references('id')
        ->on('todos')
        ->onUpdate('CASCADE')
        ->onDelete('RESTRICT');
    });
  }

  public function down():void{
    Schema::drop('payments');
  }
}
