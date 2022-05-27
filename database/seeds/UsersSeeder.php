<?php

use Hui\Xproject\Entities\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder{
  public function run():void{
    if(User::count())
      return;

    $password='xFTBDapH';
    $now=now();

    DB::table('users')
      ->insert([
        'email'=>'liubo2055@gmail.com',
        'password'=>bcrypt($password),
        'role'=>User::ROLE_ADMIN,
        'name'=>'Bo Liu',
        'timezone'=>'Asia/Shanghai',
        'locale'=>LOCALE_CN,
        'created_at'=>$now,
        'updated_at'=>$now
      ]);
    DB::table('users')
      ->insert([
        'email'=>'hammurabi@gmail.com',
        'password'=>bcrypt($password),
        'role'=>User::ROLE_ADMIN,
        'name'=>'Juan Miguel García',
        'timezone'=>'Europe/Madrid',
        'locale'=>LOCALE_EN,
        'created_at'=>$now,
        'updated_at'=>$now
      ]);

    DB::table('users')
      ->insert([
        'email'=>'kiki@huistore.com',
        'password'=>bcrypt('zvVU9vGn'),
        'role'=>User::ROLE_MANAGER,
        'name'=>'Kiki',
        'timezone'=>'Asia/Shanghai',
        'locale'=>LOCALE_CN,
        'created_at'=>$now,
        'updated_at'=>$now
      ]);

    DB::table('users')
      ->insert([
        'email'=>'mary@huistore.com',
        'password'=>bcrypt('Sz82kwX5'),
        'role'=>User::ROLE_MANAGER,
        'name'=>'Mary',
        'timezone'=>'Asia/Shanghai',
        'locale'=>LOCALE_CN,
        'created_at'=>$now,
        'updated_at'=>$now
      ]);

    DB::table('users')
      ->insert([
        'email'=>'joan@huistore.com',
        'password'=>bcrypt('Gjzpa9kt'),
        'role'=>User::ROLE_MANAGER,
        'name'=>'Joan',
        'timezone'=>'Asia/Shanghai',
        'locale'=>LOCALE_CN,
        'created_at'=>$now,
        'updated_at'=>$now
      ]);

    foreach([
      'manager1'=>User::ROLE_MANAGER,
      'freelancer1'=>User::ROLE_FREELANCER,
      'employer1'=>User::ROLE_EMPLOYER
    ] as $name=>$role)
      DB::table('users')
        ->insert([
          'email'=>sprintf('%s@hui1dollar.com',$name),
          'password'=>bcrypt($password),
          'role'=>$role,
          'name'=>$name,
          'timezone'=>'Asia/Shanghai',
          'locale'=>LOCALE_CN,
          'created_at'=>$now,
          'updated_at'=>$now
        ]);
  }
}
