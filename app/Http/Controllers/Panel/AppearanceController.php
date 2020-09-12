<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Theme;
use App\Models\ThemePage;
use Illuminate\Http\Request;
use Auth;

class AppearanceController extends Controller
{

    public function index()
    {
        if(Auth::user()->can('pages')) {
            $data = Page::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'asc')->get();
            $page = 'index';
            return view('panel.appearance.index', compact('data', 'page'));
        }else{
            return view('panel.permission');
        }
    }

    public function create()
    {
        if(Auth::user()->can('pages')) {
            $data = null;
            $page = 'create';
            return view('panel.appearance.index', compact('data', 'page'));
        }else{
            return view('panel.permission');
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('pages')) {
            $this->validate($request, [
                'name' => 'required|max:255',
                'url' => 'required'
            ]);

            $page = Page::create([
                'panel_id'          => Auth::user()->panel_id,
                'name'              => $request->name,
                'url'               => $request->url,
                'content'           => $request->page_content,
                'is_public'         => $request->is_public,
                'meta_title'        => $request->meta_title,
                'meta_keyword'      => $request->meta_keywords,
                'meta_description'  => $request->meta_description,
                'created_by'        => Auth::user()->id,
            ]);

            if (!empty($page)){
                $themes = Theme::where('panel_id', Auth::user()->panel_id)->get();
                if (!empty($themes)){
                    $themePages = [];
                    foreach ($themes as $key => $theme){
                        $themePages[] = [
                            'panel_id' => Auth::user()->panel_id,
                            'page_id'  => $page->id,
                            'theme_id' => $theme->id,
                            'content'  => defaultThemePageContent()
                        ];
                    }
                    ThemePage::insert($themePages);
                }
            }
            return redirect()->back()->with('success', 'Appearance Post save successfully !!');
        }else{
            return view('panel.permission');
        }
    }

    public function edit($id)
    {
        if(Auth::user()->can('pages')) {
            $data = Page::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route(' admin.appearance.index');
            }
            $page = 'edit';
            return view('panel.appearance.index', compact('data', 'page'));
        }else{
            return view('panel.permission');
        }
    }

    public function update(Request $request, $id)
    {
        if(Auth::user()->can('pages')) {
            $this->validate($request, [
                'name' => 'required|max:255',
                'url' => 'required'
            ]);

            Page::find($id)->update([
                'panel_id'          => Auth::user()->panel_id,
                'name'              => $request->name,
                'content'           => $request->page_content,
                'url'               => $request->url,
                'is_public'         => $request->is_public,
                'meta_title'        => $request->meta_title,
                'meta_keyword'      => $request->meta_keywords,
                'meta_description'  => $request->meta_description,
                'updated_by'        => Auth::user()->id,
            ]);
            return redirect()->back()->with('success', 'Appearance Post update successfully !!');
        }else{
            return view('panel.permission');
        }
    }

    public function updateStatus(Request $request)
    {
        if(Auth::user()->can('pages')) {
            $request->validate([
                'id' => 'required|numeric',
                'status' => 'required',
            ]);
            $status = '';
            if ($request->status == 'Active'){
                $status = 'Deactivated';
            } elseif ($request->status == 'Deactivated'){
                $status = 'Active';
            }

            Page::find($request->id)->update([
                'status'      => $status,
                'updated_by'  => Auth::user()->id,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Status change successfully !!'
            ]);
        }else{
            return view('panel.permission');
        }
    }

}
