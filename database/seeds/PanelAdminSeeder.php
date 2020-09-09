<?php

use App\PanelAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            'uuid' => Str::uuid(),
            'panel_id' => 1,
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('12345678'),
            'role' => 'Admin',
            'status' => 'Active'
        ]);

        DB::table('roles_model')->insert([
            'role_id' => 1, 
            'model_type' => '\App\PanelAdmin', 
            'model_id' => 1
        ]);
        
        $pageData = [];
        $pages = DB::table('global_pages')->get();
        foreach ($pages as $page) {
            $pageData[] = [
                'panel_id' => 1,
                'global_page_id' => $page->id,
                'name' => $page->name,
                'url' => $page->url,
                'content' => $page->content,
                'meta_title' => $page->meta_title,
                'meta_keyword' => $page->meta_keyword,
                'meta_description' => $page->meta_description,
                'is_public' => $page->is_public,
                'is_editable' => $page->is_editable,
                'status' => $page->status,
            ];
        }
        DB::table('pages')->insert($pageData);
        
        $themeData = [];
        $themes = DB::table('global_themes')->get();
        foreach ($themes as $theme) {
            $themeData[] = [
                'panel_id' => 1,
                'global_theme_id' => $theme->id,
                'name' => $theme->name,
                'location' => $theme->location,
                'snapshot' => $theme->snapshot,
                'status' => $theme->status,
                'activated_at' => date('Y-m-d H:i:s'),
            ];
        }
        DB::table('themes')->insert($themeData);
    }
}
