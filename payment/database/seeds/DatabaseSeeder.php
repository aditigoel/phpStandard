<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleTableSeeder::class);
        $this->command->info('->>> User Role created -<<<');

      


        $this->call(UserTableSeeder::class);
        $this->command->info('->>> Users Created -<<<');


 }
}
