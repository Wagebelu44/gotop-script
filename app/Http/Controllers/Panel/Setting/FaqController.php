<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\SettingFaq;
use Illuminate\Http\Request;
use Validator;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $panelId = 1;
        $data = SettingFaq::where('panel_id', $panelId)->orderBy('sort', 'asc')->get();
        $page = 'index';
        return view('panel.settings.faq', compact('data', 'page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = null;
        $page = 'create';
        return view('panel.settings.faq', compact('data', 'page'));
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
                'question' => 'required',
                'answer'   => 'required',
                'status'   => 'required'
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            SettingFaq::create([
                'panel_id'      => $panelId,
                'question'      => $data['question'],
                'answer'        => $data['answer'],
                'status'        => $data['status'],
                'created_by'    =>  auth()->guard('panelAdmin')->id(),
            ]);

            return redirect()->back()->with('success', 'Faq save successfully !!');

        }catch (\Exception $exception){
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {

        $data = SettingFaq::find($id);
        $page = 'edit';
        return view('panel.settings.faq', compact('data', 'page'));
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
        try {
            $panelId = 1;
            $data = $request->all();
            $validator = Validator::make($data, [
                'question' => 'required',
                'answer'   => 'required',
                'status'   => 'required'
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            SettingFaq::find($id)->update([
                'panel_id'      => $panelId,
                'question'      => $data['question'],
                'answer'        => $data['answer'],
                'status'        => $data['status'],
                'updated_by'    =>  auth()->guard('panelAdmin')->id(),
            ]);

            return redirect()->back()->with('success', 'Faq update successfully !!');

        }catch (\Exception $exception){
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        try {
            SettingFaq::destroy($id);
            return redirect(route('admin.setting.faq.index'))->with('success', 'Faq delete successfully !!');
        }catch (\Exception $exception){
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function sortable(Request $request){
        try {
            $panelId = 1;
            $data = SettingFaq::where('panel_id', $panelId)->get();
            foreach ($data as $faq) {
                $faq->timestamps = false; // To disable update_at field updation
                $id = $faq->id;
                foreach ($request->order as $order) {
                    if ($order['id'] == $id) {
                        $faq->update(['sort' => $order['position']]);
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Update successfully',
            ], 200);
        }catch (\Exception $exception){
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
