<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\SettingProvider;
use Illuminate\Http\Request;
use Auth;

class ProviderController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('provider setting')) {
            $data = SettingProvider::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'DESC')->get();
            return view('panel.settings.providers', compact('data'));
        }else{
            return view('panel.permission');
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('provider setting')) {
            $this->validate($request, [
                'domain' => 'required',
            ]);

            SettingProvider::create([
                'panel_id'      => Auth::user()->panel_id,
                'domain'        => $request->domain,
                'api_url'       => $request->url,
                'api_key'       => $request->api_key,
                'created_by'    => Auth::user()->id
            ]);

            return redirect()->back()->with('success', 'Provider save successfully!');
        }else{
            return view('panel.permission');
        }
    }

    public function edit($id)
    {
        if(Auth::user()->can('provider setting')) {
            $editProvider = SettingProvider::where('panel_id', Auth::user()->panel_id)->where('id',$id)->first();
            return response()->json([
                'status' => 'success',
                'data' => $editProvider,
            ], 200);
        }else{
            return view('panel.permission');
        }
    }

    public function update(Request $request, $id)
    {
        if(Auth::user()->can('provider setting')) {
            $this->validate($request, [
                'domain' => 'required',
            ]);

            SettingProvider::find($id)->update([
                'panel_id'      => Auth::user()->panel_id,
                'domain'        => $request->domain,
                'api_url'       => $request->url,
                'api_key'       => $request->api_key,
                'updated_by'    => Auth::user()->panel_id
            ]);

            return redirect()->back()->with('success', 'Provider update successfully!');
        }else{
            return view('panel.permission');
        }

    }

    public function destroy($id)
    {
        if(Auth::user()->can('provider setting')) {
            $data = SettingProvider::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route('admin.setting.provider.index');
            }
            $data->delete();
            return redirect()->back()->with('success', 'Provider delete successfully !!');
        }else{
            return view('panel.permission');
        }
    }
}
