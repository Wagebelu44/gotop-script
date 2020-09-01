<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\SettingProvider;
use Illuminate\Http\Request;
use Validator;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data = SettingProvider::get();
        return view('panel.settings.providers', compact('data'));
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        try {
            $panelId = 1;
            $data = $request->all();
            $validator = Validator::make($data, [
                'domain' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            SettingProvider::create([
                'panel_id'      => $panelId,
                'domain'        => $data['domain'],
                'api_url'       => $data['url'],
                'api_key'       => $data['api_key'],
                'created_by'    => auth()->guard('panelAdmin')->id(),
            ]);

            return redirect()->back()->with('success', 'Provider save successfully!');
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        try {
            $editProvider = SettingProvider::where('id',$id)->first();
            return response()->json([
                'status' => 'success',
                'data' => $editProvider,
            ], 200);
        }catch (\Exception $exception){
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try{
            $panelId = 1;
            $data = $request->all();
            $validator = Validator::make($data, [
                'domain' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            SettingProvider::find($id)->update([
                'domain'        => $data['domain'],
                'api_url'       => $data['url'],
                'api_key'       => $data['api_key'],
                'updated_by'    => auth()->guard('panelAdmin')->id(),
            ]);

            return redirect()->back()->with('success', 'Provider save successfully!');
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
