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


        //Added notification to panel...
        DB::table('setting_notifications')->insert([
            [
                'panel_id' => 1,
                'subject' => 'Welcome',
                'body' =>  'Hello,
Thank you for signing up.
Your username is: {{ user.username }}
Use it to sign in to {{ panel.url }}',
                'title' => 'Welcome',
                'description' => 'Sent to new users when their account is created.',
                'type' => '1',
                'status' => 'inactive',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'panel_id' => 1,
                'subject' => 'Welcome',
                'body' =>  'Hello,
You requested a password change. To change your password follow the link below: {{ resetpassword.url }}',
                'title' => 'Forgot password',
                'description' => 'Sent to users when they request a password reset.',
                'type' => '1',
                'status' => 'inactive',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'panel_id' => 1,
                'subject' => 'New message',
                'body' =>   'Hello,
You have a new message in the ticket.
Follow the link below to see the message: {{ ticket.url }}',
                'title' => 'New message',
                'description' => 'Sent to users when they receive a new message',
                'type' => '1',
                'status' => 'inactive',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'panel_id' => 1,
                'subject' => 'Payment received',
                'body' => 'New payment #{{ payment.id }} received.
View payment in admin panel: {{ payment.admin_url }}',
                'title' => 'Payment received',
                'description' => 'Sent to staff when a user adds funds automatically.',
                'type' => '2',
                'status' => 'inactive',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'panel_id' => 1,
                'subject' => 'New manual orders',
                'body' =>   'New manual order(s) received. Total pending manual orders: {{ orders.manual.pending_number }}
View all manual orders in admin panel: {{ orders.manual.url }}',
                'title' => 'New manual orders',
                'description' => 'Periodically sent to staff if new manual orders received.',
                'type' => '2',
                'status' => 'inactive',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'panel_id' => 1,
                'subject' => 'Fail orders',
                'body' =>   'Order(s) got Fail status. Total orders with Fail status: {{ orders.fail_number }}
View Fail orders in admin panel: {{ orders.fail_url }}',
                'title' => 'Fail orders',
                'description' => 'Periodically sent to staff if some orders got Fail status.',
                'type' => '2',
                'status' => 'inactive',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'panel_id' => 1,
                'subject' => 'New messages',
                'body' =>   'New message(s) received. Total unread tickets: {{ tickets.unread_number }}
View tickets in admin panel: {{ tickets.url }}',
                'title' => 'New messages',
                'description' => 'Periodically sent to staff if new messages received.',
                'type' => '2',
                'status' => 'inactive',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'panel_id' => 1,
                'subject' => 'New manual payout',
                'body' =>   'New manual payout request received.
View Payouts in admin panel: {{ affiliates.payouts }}',
                'title' => 'New manual payout',
                'description' => 'Sent to staff when a user create manual payout.',
                'type' => '2',
                'status' => 'inactive',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
