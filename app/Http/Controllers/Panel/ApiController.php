<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\G\GlobalCurrencies;
use App\Models\G\GlobalPaymentMethod;
use App\Models\SettingGeneral;
use Illuminate\Http\Request;
use App\PanelAdmin;
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

    public function activePanel(Request $request)
    {
        if (!$request->token == env('PANLE_REQUEST_TOKEN')) {
            return false;
        }

        app('App\Http\Controllers\PanelSeedController')->index('live', $request);
        
        return response()->json(['success' => true]);
    }

    public function canceledPanel(Request $request)
    {
        if (!$request->token == env('PANLE_REQUEST_TOKEN')) {
            return false;
        }

        SettingGeneral::where(['panel_id' => $request->panel_id])->update([
            'updated_by' => $request->id,
            'status' => $request->status,
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

    public function saveMethod(Request $request)
    {
        if (!$request->token == env('PANLE_REQUEST_TOKEN')) {
            return false;
        }
        
        GlobalPaymentMethod::updateOrCreate([
            'uuid' => $request->uuid
        ], [
            'uuid' => $request->uuid,
            'name' => $request->name,
            'fields' => json_encode($request->fields),
        ]);
        return response()->json(['success' => true]);
    }

    public function deleteMethod(Request $request)
    {
        if (!$request->token == env('PANLE_REQUEST_TOKEN')) {
            return false;
        }
        
        $method = GlobalPaymentMethod::where('uuid', $request->uuid)->first();
        if (!empty($method)) {
            $method->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

    public function saveCurrency(Request $request)
    {
        if (!$request->token == env('PANLE_REQUEST_TOKEN')) {
            return false;
        }
        
        GlobalCurrencies::updateOrCreate([
            'code' => $request->code
        ], [
            'code' => $request->code,
            'name' => $request->name,
            'sign' => $request->sign,
        ]);
        return response()->json(['success' => true]);
    }

    public function deleteCurrency(Request $request)
    {
        if (!$request->token == env('PANLE_REQUEST_TOKEN')) {
            return false;
        }
        
        $currency = GlobalCurrencies::where('code', $request->code)->first();
        if (!empty($currency)) {
            $currency->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
}
