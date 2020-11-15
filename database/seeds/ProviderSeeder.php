<?php

use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
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
                'panel_id' => '1',
                'domain' => 'smmworldpanel.com',
                'api_url' => 'https://smmworldpanel.com/api/v2',
                'api_key' => '9bfce3a9c83f3363078eeaf0b1c73923',
                'status' => 'Active',
                'created_at' => now(),
            ],
            [
                'panel_id' => '1',
                'domain' => 'justanotherpanel.com',
                'api_url' => 'https://justanotherpanel.com/api/v2',
                'api_key' => 'de1b6b7113aa690927b09e1a69c83398',
                'status' => 'Active',
                'created_at' => now(),
            ],
            [
                'panel_id' => '1',
                'domain' => 'medyasosyal.com',
                'api_url' => 'https://medyasosyal.com/api/v2',
                'api_key' => '0271b7f4009ca7948bdf0d37a4cb9c3b',
                'status' => 'Active',
                'created_at' => now(),
            ],
            [
                'panel_id' => '1',
                'domain' => 'go2toppanel.com',
                'api_url' => 'https://go2toppanel.com/api/v2',
                'api_key' => '1d6fa0f316cad2ea11477885a6c95d40',
                'status' => 'Active',
                'created_at' => now(),
            ],
        ];
        
        DB::table('setting_providers')->insert($themes);
    }
}
