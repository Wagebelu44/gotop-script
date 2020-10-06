<?php

namespace App\Http\Controllers\Panel\Setting;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\SettingGeneral;
use App\Models\SettingProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ProviderController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('provider setting')) {
            $data = SettingProvider::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'DESC')->get();
            $setting = SettingGeneral::select('panel_type')->where('panel_id', Auth::user()->panel_id)->first();
            return view('panel.settings.providers', compact('setting', 'data'));
        } else {
            return view('panel.permission');
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('provider setting')) {
            $this->validate($request, [
                'domain' => 'required',
                'status' => 'required',
            ]);

            $setting = SettingGeneral::select('panel_type')->where('panel_id', Auth::user()->panel_id)->first();
            if ($setting->panel_type == 'Child') {
                return redirect()->back()->with('error', "You can Add only your mother provider domain.");
            }
            
            $storePermission = false;
            if (env('PROJECT') == 'live') {
                try {
                    $response = Http::post(env('PROJECT_LIVE_URL').'/api/check-provider', [
                        'name' => $request->domain,
                        'token' => env('PANLE_REQUEST_TOKEN'),
                    ]);

                    if ($response->ok()) {
                        if ($response->successful()) {
                            $data = json_decode($response->body());
                            if ($data->success) {
                                $storePermission = true;
                            } else {
                                return redirect()->back()->with('error', "Provider not found!");
                            }
                        } else {
                            return redirect()->back()->with('error', "Provider adding failed for server error!");
                        }
                    } else {
                        return redirect()->back()->with('error', "Provider adding failed for server error!");
                    }
                } catch(Exception $e) {
                    return redirect()->back()->with('error', "Provider adding failed for server error!");
                }
            } else {
                $storePermission = true;
            }

            if ($storePermission) {
                SettingProvider::create([
                    'panel_id'      => Auth::user()->panel_id,
                    'domain'        => $request->domain,
                    'api_url'       => $request->url,
                    'api_key'       => $request->api_key,
                    'status'       => $request->status,
                    'created_by'    => Auth::user()->id
                ]);
                return redirect()->back()->with('success', 'Provider added successfully!');
            } else {
                return redirect()->back()->with('success', 'Provider adding failed!');
            }

        } else {
            return view('panel.permission');
        }
    }

    public function edit($id)
    {
        if (Auth::user()->can('provider setting')) {
            $editProvider = SettingProvider::where('panel_id', Auth::user()->panel_id)->where('id',$id)->first();
            return response()->json([
                'status' => 'success',
                'data' => $editProvider,
            ], 200);
        } else {
            return view('panel.permission');
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('provider setting')) {
            $this->validate($request, [
                'domain' => 'required',
                'status' => 'required',
            ]);

            $setting = SettingGeneral::select('panel_type', 'main_panel_domain')->where('panel_id', Auth::user()->panel_id)->first();
            if ($setting->panel_type == 'Child' && $setting->main_panel_domain != $request->domain) {
                return redirect()->back()->with('error', "You can Edit only your mother provider domain.");
            }

            SettingProvider::find($id)->update([
                'panel_id'      => Auth::user()->panel_id,
                'domain'        => $request->domain,
                'api_url'       => $request->url,
                'api_key'       => $request->api_key,
                'status'       => $request->status,
                'updated_by'    => Auth::user()->panel_id
            ]);

            return redirect()->back()->with('success', 'Provider update successfully!');
        } else {
            return view('panel.permission');
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('provider setting')) {
            $setting = SettingGeneral::select('panel_type')->where('panel_id', Auth::user()->panel_id)->first();
            if ($setting->panel_type == 'Child') {
                return redirect()->back()->with('error', "You can not delete your mother provider domain.");
            }

            $data = SettingProvider::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route('admin.setting.provider.index');
            }
            $data->delete();
            return redirect()->back()->with('success', 'Provider delete successfully !!');
        } else {
            return view('panel.permission');
        }
    }
}
