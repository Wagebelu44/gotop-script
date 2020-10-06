<?php

namespace App\Http\Controllers;

use App\Models\SettingProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\PanelAdmin;

class PanelSeedController
{
    public function index($type, $request = null)
    {
        //Create panel admin...
        if ($type == 'live') {
            $user = PanelAdmin::create($request->user);

            //Setting table create or update...
            DB::table('setting_generals')->updateOrInsert(['panel_id' => $user->panel_id], [
                'updated_by' => $user->id,
                'status' => $request->status,
                'currency' => $request->currency,
                'currency_sign' => $request->currency_sign,
                'currency_name' => $request->currency_name,
                'timezone' => $request->timezone,
                'panel_type' => $request->panel_type,
                'main_panel_id' => $request->main_panel_id,
                'main_panel_domain' => $request->main_panel_domain,
            ]);

            if ($request->main_panel_id > 0) {
                SettingProvider::create([
                    'panel_id' => $user->panel_id,
                    'domain' => $request->main_panel_domain,
                    'api_url' => $request->main_panel_domain.'/api/v2',
                    'api_key' => null,
                ]);
            }
        } else {
            $user = PanelAdmin::create([
                'uuid' => Str::uuid(),
                'panel_id' => 1,
                'name' => 'Test Admin',
                'email' => 'admin@test.com',
                'password' => bcrypt('12345678'),
                'role' => 'Admin',
                'status' => 'Active'
            ]);
        
            //Setting table create or update...
            DB::table('setting_generals')->updateOrInsert(['panel_id' => $user->panel_id], [
                'updated_by' => 1,
                'status' => 'Active',
                'currency' => 'USD',
                'currency_sign' => '$',
                'currency_name' => 'Us Dollar',
                'timezone' => '-11',
                'panel_type' => 'Main',
                'main_panel_id' => null,
                'main_panel_domain' => null,
            ]);
        }

        //Assign role to panel admin...
        $user->assignRole('Super Admin');
        
        
        //Added page to panel...
        $pageData = [];
        $pages = DB::table('global_pages')->get();
        foreach ($pages as $page) {
            $pageData[] = [
                'panel_id' => $user->panel_id,
                'global_page_id' => $page->id,
                'name' => $page->name,
                'default_url' => $page->url,
                'url' => $page->url,
                'content' => $page->content,
                'meta_title' => $page->meta_title,
                'meta_keyword' => $page->meta_keyword,
                'meta_description' => $page->meta_description,
                'is_public' => $page->is_public,
                'is_editable' => $page->is_editable,
                'page_in_menu' => $page->page_in_menu,
                'sorting' => $page->sorting,
                'status' => $page->status,
            ];
        }
        DB::table('pages')->insert($pageData);

        //Added menu to panel...        
        $menuData = [];
        $pages = DB::table('pages')->where('panel_id', $user->panel_id)->get();
        foreach ($pages as $page) {
            $menuData[] = [
                'panel_id' => $user->panel_id,
                'menu_name' => $page->name,
                'menu_link_id' => $page->id,
                'menu_link_type' => $page->is_public,
                'page_in_menu' => $page->page_in_menu,
                'sort' => $page->sorting,
            ];
        }
        DB::table('menus')->insert($menuData);
        
        //Added theme to panel...
        $themeData = [];
        $themes = DB::table('global_themes')->get();
        foreach ($themes as $theme) {
            $themeData[] = [
                'panel_id' => $user->panel_id,
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
        $themes = DB::table('themes')->where('panel_id', $user->panel_id)->get();
        foreach ($themes as $theme) {
            $themePageData[] = [
                'panel_id' => $user->panel_id,
                'theme_id' => $theme->id,
                'page_id' => 0,
                'group' => 'twig',
                'name' => 'layout.twig',
                'content' => (file_exists(public_path($theme->location.'/layout.twig')))?file_get_contents(public_path($theme->location.'/layout.twig')):'',
                'sort' => 1,
            ];

            $pages = DB::table('pages')->where('panel_id', $user->panel_id)->get();
            foreach ($pages as $page) {
                $pageName = Str::slug(strtolower($page->name), '-').'.twig';
                $themePageData[] = [
                    'panel_id' => $user->panel_id,
                    'theme_id' => $theme->id,
                    'page_id' => $page->id,
                    'group' => 'twig',
                    'name' => $pageName,
                    'content' => (file_exists(public_path($theme->location.'/'.$pageName)))?file_get_contents(public_path($theme->location.'/'.$pageName)):'',
                    'sort' => 2,
                ];
            }
            $themePageData[] = [
                'panel_id' => $user->panel_id,
                'theme_id' => $theme->id,
                'page_id' => 0,
                'group' => 'css',
                'name' => 'style.css',
                'content' => (file_exists(public_path($theme->location.'/style.css')))?file_get_contents(public_path($theme->location.'/style.css')):'',
                'sort' => 3,
            ];
            $themePageData[] = [
                'panel_id' => $user->panel_id,
                'theme_id' => $theme->id,
                'page_id' => 0,
                'group' => 'js',
                'name' => 'custom.js',
                'content' => (file_exists(public_path($theme->location.'/custom.js')))?file_get_contents(public_path($theme->location.'/custom.js')):'',
                'sort' => 4,
            ];
        }
        DB::table('theme_pages')->insert($themePageData);


        //Added notification to panel...
        $notifications = DB::table('global_notifications')->where('status', 'Active')->get();
        foreach ($notifications as $notification) {
            $notificationData[] = [
                'panel_id' => $user->panel_id,
                'type' => $notification->type,
                'title' => $notification->title,
                'description' => $notification->description,
                'subject' => $notification->subject,
                'body' =>  $notification->body,
                'status' => 'Deactivated',
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('setting_notifications')->insert($notificationData);
    }
}
