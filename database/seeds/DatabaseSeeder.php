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
        $this->call(GlobalPagesSeeder::class);
        $this->call(GlobalThemesSeeder::class);
        $this->call(GlobalPaymentSeeder::class);
        $this->call(GlobalNotificationSeeder::class);
        $this->call(GlobalCurrencySeeder::class);
        $this->call(RoleSeeder::class);

        //For sandbox Use...
        $this->call(PanelAdminSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ProviderSeeder::class);
    }
}
