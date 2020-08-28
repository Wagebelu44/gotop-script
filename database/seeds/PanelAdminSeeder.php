<?php

use App\PanelAdmin;
use Illuminate\Database\Seeder;

class PanelAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PanelAdmin::create([
            'panel_id' => '1',
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role' => 'Admin',
            'password' => bcrypt('12345678'),
        ]);
    }
}
