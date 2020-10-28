<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\DB;
use App\Models\User;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
    	//DB::table('roles')->delete();
        Role::create(['roleName' => 'Admin', 'isAdmin' => 1]);
        Role::create(['roleName' => 'Member', 'isAdmin' => 0]);
        User::create(['fullName' => 'Dang Hai', 'email' => 'dang.it2024@gmail.com', 'password' => bcrypt('123456'), 'role_id' => 1]);


    }
}
