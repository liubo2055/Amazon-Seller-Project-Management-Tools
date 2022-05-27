<?php

use Hui\Xproject\Entities\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ExtendUserRoleEnumeration extends Migration{
  public function up():void{
    DB::statement('ALTER TABLE users DROP CONSTRAINT users_role_check');

    $roles=array_map(function(string $role):string{
      return sprintf('\'%s\'',$role);
    },User::ROLES);

    // Cannot be done with enum()->role()
    DB::statement(sprintf('ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role=ANY(ARRAY[%s]))',implode(',',$roles)));
  }
}
