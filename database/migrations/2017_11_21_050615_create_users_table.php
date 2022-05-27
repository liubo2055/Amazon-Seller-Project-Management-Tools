<?php

use Hui\Xproject\Entities\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration{
  public function up():void{
    Schema::create('users',function(Blueprint $table):void{
      $table->increments('id');
      $table->string('email')
        ->unique();
      $table->string('password');
      $table->enum('role',User::ROLES);
      $table->string('name');
      $table->string('qq')
        ->nullable();
      $table->string('wechat_id')
        ->nullable();
      $table->enum('locale',LOCALES);
      $table->string('timezone');
      $table->string('phone')
        ->nullable();
      $table->string('company_name')
        ->nullable();
      $table->string('company_url')
        ->nullable();
      $table->string('alipay_id')
        ->nullable();
      $table->string('image_path')
        ->nullable();
      $table->string('image_url')
        ->nullable();
      $table->rememberToken();
      $table->timestamp('created_at');
      $table->timestamp('updated_at');
      $table->softDeletes();
    });
  }

  public function down():void{
    Schema::drop('users');
  }
}
