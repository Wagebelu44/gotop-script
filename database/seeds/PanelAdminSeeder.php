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
            'panel_id' => 1,
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('12345678'),
            'role' => 'Admin',
            'status' => 'Active'
        ]);
    }
}
