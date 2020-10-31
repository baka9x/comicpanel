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
        Role::create(['roleName' => 'Admin', 'isAdmin' => 1, 'permission' => '[{"resourceName":"Home","read":true,"write":true,"update":true,"delete":true,"name":"/"},{"resourceName":"Tags","read":true,"write":true,"update":true,"delete":true,"name":"tags"},{"resourceName":"Category","read":true,"write":true,"update":true,"delete":true,"name":"category"},{"resourceName":"Create blogs","read":true,"write":true,"update":true,"delete":true,"name":"createBlog"},{"resourceName":"Blogs","read":true,"write":true,"update":true,"delete":true,"name":"blogs"},{"resourceName":"Admin users","read":true,"write":true,"update":true,"delete":true,"name":"adminusers"},{"resourceName":"Role","read":true,"write":true,"update":true,"delete":true,"name":"role"},{"resourceName":"Assign Role","read":true,"write":true,"update":true,"delete":true,"name":"assignRole"}]']);
        Role::create(['roleName' => 'Member', 'isAdmin' => 0]);
        User::create(['fullName' => 'Dang Hai', 'email' => 'dang.it2024@gmail.com', 'password' => bcrypt('123456'), 'role_id' => 1]);


    }
}
