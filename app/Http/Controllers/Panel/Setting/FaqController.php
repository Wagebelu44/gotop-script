<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\SettingFaq;
use Illuminate\Http\Request;
use Auth;

class FaqController extends Controller
{
    public function index()
    {
        $data = SettingFaq::where('panel_id', Auth::user()->panel_id)->orderBy('sort', 'asc')->get();
        $page = 'index';
        return view('panel.settings.faq', compact('data', 'page'));
    }

    public function create()
    {
        $data = null;
        $page = 'create';
        return view('panel.settings.faq', compact('data', 'page'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'question' => 'required',
            'answer'   => 'required',
            'status'   => 'required'
        ]);

        SettingFaq::create([
            'panel_id'      => Auth::user()->panel_id,
            'question'      => $request->question,
            'answer'        => $request->answer,
            'status'        => $request->status,
            'created_by'    => Auth::user()->id,
        ]);

        return redirect()->back()->with('success', 'Faq save successfully !!');
    }

    public function edit($id)
    {
        $data = SettingFaq::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
        if (empty($data)) {
            return redirect()->route('admin.setting.faq.index');
        }

        $page = 'edit';
        return view('panel.settings.faq', compact('data', 'page'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'question' => 'required',
            'answer'   => 'required',
            'status'   => 'required'
        ]);

        SettingFaq::find($id)->update([
            'panel_id'      => Auth::user()->panel_id,
            'question'      => $request->question,
            'answer'        => $request->answer,
            'status'        => $request->status,
            'updated_by'    => Auth::user()->id,
        ]);

        return redirect()->back()->with('success', 'Faq update successfully !!');
    }

    public function destroy($id)
    {
        $data = SettingFaq::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
        if (empty($data)) {
            return redirect()->route('admin.setting.faq.index');
        }
        $data->delete();

        return redirect(route('admin.setting.faq.index'))->with('success', 'Faq delete successfully !!');
    }

    public function sortable(Request $request)
    {
        $data = SettingFaq::where('panel_id', Auth::user()->panel_id)->get();
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
    }
}
