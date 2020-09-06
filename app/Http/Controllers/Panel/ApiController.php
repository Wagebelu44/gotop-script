<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\PanelAdmin;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;

class ApiController extends Controller
{
    public function logName(Request $request)
    {
        if (!$request->token == env('PANLE_REQUEST_TOKEN')) {
            return false;
        }
        $logNames = Activity::select('log_name')->where('panel_id', $request->panel_id)->orderBy('log_name', 'ASC')->groupBy('log_name')->get();
        return response()->json($logNames);
    }

    public function postActiveLog(Request $request)
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
        
        $user = PanelAdmin::insert($request->user);
        $user->assignRole('Super Admin');
        return response()->json($user);
    }
}
