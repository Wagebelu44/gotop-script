<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\PanelAdmin;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;

class ApiController extends Controller
{
    public function index(Request $request)
    {
        
        return response()->json('API worked!');
    }

    public function sentLogName(Request $request)
    {
        if (!$request->token == env('PANLE_REQUEST_TOKEN')) {
            return false;
        }
        $logNames = Activity::select('log_name')->where('panel_id', $request->panel_id)->orderBy('log_name', 'ASC')->groupBy('log_name')->get();
        return response()->json($logNames);
    }

    public function sentActiveLog(Request $request)
    {
        if (!$request->token == env('PANLE_REQUEST_TOKEN')) {
            return false;
        }
        $sql = Activity::select('activity_log.id', 'activity_log.created_at', 'activity_log.description', 'activity_log.subject_id', 'activity_log.ip',  'activity_log.log_name', 'panel_admins.name')->orderBy('created_at', 'DESC');
        $sql->join('panel_admins', 'panel_admins.id', '=', 'activity_log.causer_id');
        $sql->where('activity_log.panel_id', $request->panel_id);

        if ($request->account) {
            $sql->where('activity_log.causer_id', $request->account);
        }
        if ($request->event) {
            $sql->where('activity_log.log_name', $request->event);
        }
        if ($request->from) {
            $sql->whereDate('activity_log.created_at', '>=', $request->from);
        }
        if ($request->to) {
            $sql->whereDate('activity_log.created_at', '<=', $request->to);
        }
        if ($request->details) {
            $sql->where(function($q) use($request) {
                $q->where('activity_log.description', '<=', $request->details)
                    ->orWhere('activity_log.id', '<=', $request->details)
                    ->orWhere('activity_log.subject_id', '<=', $request->details)
                    ->orWhere('activity_log.ip', '<=', $request->details);
            });
        }

        
        $activities = $sql->paginate($request->paginate);
        return response()->json($activities);
    }

    public function postPermissions(Request $request)
    {
        if (!$request->token == env('PANLE_REQUEST_TOKEN')) {
            return false;
        }
        
        $data = Permission::insert($request->permissions);
        return response()->json($data);
    }

    public function saveAdminUser(Request $request)
    {
        if (!$request->token == env('PANLE_REQUEST_TOKEN')) {
            return false;
        }
        
        //Create panel admin...
        $user = PanelAdmin::create($request->user);

        //Assign role to panel admin...
        $user->assignRole('Super Admin');

        //Setting table create or update...
        activity()->disableLogging();
        DB::table('setting_generals')->updateOrInsert(['panel_id' => $user->panel_id], [
            'updated_by' => $user->id,
            'status' => $request->status,
            'currency' => $request->currency,
            'timezone' => $request->timezone,
        ]);
        
        
        //Added page to panel...
        $pageData = [];
        $pages = DB::table('global_pages')->get();
        foreach ($pages as $page) {
            $pageData[] = [
                'panel_id' => $user->panel_id,
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
                'content' => '',
                'sort' => 1,
            ];

            $pages = DB::table('pages')->where('panel_id', $user->panel_id)->where('is_editable', 'Yes')->get();
            foreach ($pages as $page) {
                $themePageData[] = [
                    'panel_id' => $user->panel_id,
                    'theme_id' => $theme->id,
                    'page_id' => $page->id,
                    'group' => 'twig',
                    'name' => strtolower($page->name).'.twig',
                    'content' => defaultThemePageContent(),
                    'sort' => 2,
                ];
            }
            $themePageData[] = [
                'panel_id' => $user->panel_id,
                'theme_id' => $theme->id,
                'page_id' => 0,
                'group' => 'css',
                'name' => 'style.css',
                'content' => '',
                'sort' => 3,
            ];
            $themePageData[] = [
                'panel_id' => $user->panel_id,
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
                'panel_id' => $user->panel_id,
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
                'panel_id' => $user->panel_id,
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
                'panel_id' => $user->panel_id,
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
                'panel_id' => $user->panel_id,
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
                'panel_id' => $user->panel_id,
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
                'panel_id' => $user->panel_id,
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
                'panel_id' => $user->panel_id,
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
                'panel_id' => $user->panel_id,
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
        
        return response()->json(['success' => true]);
    }

    public function saveUser(Request $request)
    {
        if (!$request->token == env('PANLE_REQUEST_TOKEN')) {
            return false;
        }
        
        $user = PanelAdmin::updateOrCreate(['uuid' => $request->user['uuid'], 'panel_id' => $request->user['panel_id']], $request->user);
        $user->syncPermissions($request->permissions);

        return response()->json(['success' => true]);
    }

    public function userPasswordUpdate(Request $request)
    {
        if (!$request->token == env('PANLE_REQUEST_TOKEN')) {
            return false;
        }
        
        $user = PanelAdmin::where('panel_id', $request->user['panel_id'])->where('uuid', $request->user['uuid'])->first();
        if (!empty($user)) {
            $user->update(['password' => $request->user['password']]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => $request->user]);
    }
}
