<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i <100 ; $i++) {
            DB::table('users')->insert([
                'panel_id' => 1,
                'name' => 'User '.$i,
                'username' => 'user_'.$i,
                'skype_name' => 'User_'.$i,
                'email' => 'user'.$i.'@gmail.com',
                'password' => bcrypt('12345678'),
                'status' => 'Active',
                'last_login_at' => now(),
            ]);
        }
    }
}
