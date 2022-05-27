<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder{
  public function run():void{
    $this->call(UsersSeeder::class);
    $this->call(TodosSeeder::class);
    $this->call(ProjectsSeeder::class);
  }
}
