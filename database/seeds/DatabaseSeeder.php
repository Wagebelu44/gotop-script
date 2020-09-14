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
        $this->call(UserSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(GlobalPagesSeeder::class);
        $this->call(GlobalThemesSeeder::class);
        $this->call(GlobalPaymentSeeder::class);
        $this->call(PanelAdminSeeder::class);
    }
}
