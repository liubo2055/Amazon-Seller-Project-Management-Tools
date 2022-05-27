<?php

use Hui\Xproject\Entities\User;
use Hui\Xproject\Exceptions\XprojectException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUserIdColumn extends Migration{
  public function up():void{
    if(!DB::pretending()){
      $row=DB::selectOne('SELECT id FROM users WHERE role IN (?,?)',[
        User::ROLE_MANAGER,
        User::ROLE_ADMIN
      ]);
      if(!$row)
        throw new XprojectException('No admin or manager user available');
      $id=$row->id;
    }
    else
      $id=1;

    Schema::table('todos',function(Blueprint $table) use ($id):void{
      $table->integer('user_id')
        // Update existing to-dos
        ->default($id);

      $table->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onUpdate('CASCADE')
        ->onDelete('RESTRICT');
    });

    DB::statement('ALTER TABLE todos ALTER user_id DROP DEFAULT');
  }

  public function down():void{
    Schema::table('todos',function(Blueprint $table):void{
      $table->dropColumn('user_id');
    });
  }
}
