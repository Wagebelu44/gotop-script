<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Auth;

class AppearanceController extends Controller
{

    public function index()
    {
        $data = Page::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'asc')->get();
        $page = 'index';
        return view('panel.appearance.index', compact('data', 'page'));
    }

    public function create()
    {
        $data = null;
        $page = 'create';
        return view('panel.appearance.index', compact('data', 'page'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'url' => 'required'
        ]);

        Page::create([
            'panel_id'          => Auth::user()->panel_id,
            'name'              => $request->name,
            'url'               => $request->url,
            'content'           => $request->content,
            'is_public'         => $request->is_public,
            'meta_title'        => $request->meta_title,
            'meta_keyword'      => $request->meta_keywords,
            'meta_description'  => $request->meta_description,
            'created_by'        => Auth::user()->id,
        ]);
        return redirect()->back()->with('success', 'Appearance Post save successfully !!');
    }

    public function edit($id)
    {
        $data = Page::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
        if (empty($data)) {
            return redirect()->route(' admin.appearance.index');
        }
        $page = 'edit';
        return view('panel.appearance.index', compact('data', 'page'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'url' => 'required'
        ]);

        Page::find($id)->update([
            'panel_id'          => Auth::user()->panel_id,
            'name'              => $request->name,
            'content'           => $request->content,
            'url'               => $request->url,
            'is_public'         => $request->is_public,
            'meta_title'        => $request->meta_title,
            'meta_keyword'      => $request->meta_keywords,
            'meta_description'  => $request->meta_description,
            'updated_by'        => Auth::user()->id,
        ]);
        return redirect()->back()->with('success', 'Appearance Post update successfully !!');
    }

}
