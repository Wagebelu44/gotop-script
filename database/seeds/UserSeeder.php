<?php

use App\User;
use Illuminate\Database\Seeder;

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
            User::create([
                'panel_id' => '1',
                'username' => 'User_'.$i,
                'skype_name' => 'User_'.$i,
                'email' => 'user'.$i.'@gmail.com',
                'password' => bcrypt('12345678'),
                'status' => 'active',
                'last_login_at' => now(),
            ]);
        }
    }
}
