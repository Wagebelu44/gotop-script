<?php

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
        return app('App\Http\Controllers\PanelSeedController')->index('sandbox');
    }
}
