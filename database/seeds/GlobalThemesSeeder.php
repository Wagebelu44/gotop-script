<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalThemesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $themes = [
            [
                'name' => 'Default',
                'location' => 'themes/default',
                'snapshot' => '',
                'status' => 'Active',
            ],
            [
                'name' => 'Premium',
                'location' => 'themes/premium',
                'snapshot' => '',
                'status' => 'Deactivated',
            ]
        ];
        
        DB::table('global_themes')->insert($themes);
    }
}
