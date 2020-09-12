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
        //Panel admin create...
        PanelAdmin::create([
            'uuid' => Str::uuid(),
            'panel_id' => 1,
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('12345678'),
            'role' => 'Admin',
            'status' => 'Active'
        ]);

        //Assign role to panel admin...
        DB::table('roles_model')->insert([
            'role_id' => 1, 
            'model_type' => '\App\PanelAdmin', 
            'model_id' => 1
        ]);
        
        //Added page to panel...
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
        
        //Added theme to panel...
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

        
        //Added theme page to panel...
        $themes = DB::table('themes')->where('panel_id', 1)->get();
        foreach ($themes as $theme) {
            $themePageData[] = [
                'panel_id' => 1,
                'theme_id' => $theme->id,
                'page_id' => 0,
                'group' => 'twig',
                'name' => 'layout.twig',
                'content' => '',
                'sort' => 1,
            ];

            $pages = DB::table('pages')->where('panel_id', 1)->where('is_editable', 'Yes')->get();
            foreach ($pages as $page) {
                $themePageData[] = [
                    'panel_id' => 1,
                    'theme_id' => $theme->id,
                    'page_id' => $page->id,
                    'group' => 'twig',
                    'name' => strtolower($page->name).'.twig',
                    'content' => defaultThemePageContent(),
                    'sort' => 2,
                ];
            }
            $themePageData[] = [
                'panel_id' => 1,
                'theme_id' => $theme->id,
                'page_id' => 0,
                'group' => 'css',
                'name' => 'style.css',
                'content' => '',
                'sort' => 3,
            ];
            $themePageData[] = [
                'panel_id' => 1,
                'theme_id' => $theme->id,
                'page_id' => 0,
                'group' => 'js',
                'name' => 'custom.js',
                'content' => '',
                'sort' => 4,
            ];
        }
        DB::table('theme_pages')->insert($themePageData);
    }
}
