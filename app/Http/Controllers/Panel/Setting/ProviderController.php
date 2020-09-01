<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\SettingProvider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $providers = SettingProvider::get();
        return view('panel.settings.providers', compact('providers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'domain' => 'required',
        ]);

        try {
            $panelId = 1;
            $provider = new SettingProvider();
            $provider->panel_id = $panelId;
            $provider->domain = $request->domain;
            $provider->api_url = $request->url;
            $provider->api_key = $request->api_key;
            $provider->created_by = auth()->guard('panelAdmin')->id();
            $provider->save();
            return redirect()->back()->with('success', 'Provider Added successfully!');
        }catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $editProvider = SettingProvider::where('id',$id)->first();
        return $editProvider;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'edit_domain' => 'required',
        ]);

        try{
            $panelId = 1;
            SettingProvider::where('id', $request->provider_domain_id)->update([
                'panel_id' => $panelId,
                'domain'=> $request->edit_domain,
                'api_url'=> $request->edit_url,
                'api_key'=> $request->edit_api_key,
                'updated_by' => auth()->guard('panelAdmin')->id()
            ]);
            return redirect()->back()->withSuccess('Provider updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try{
            $pro = SettingProvider::find($request->provider_domain_del_id);
            if ($pro != null) {
                $pro->delete();
            }
            return redirect()->back()->withSuccess('Provider Deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
