<?php

namespace App\Http\Controllers\Panel\Appearance;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Theme;
use App\Models\ThemePage;
use Illuminate\Http\Request;
use Auth;

class PageController extends Controller
{
    public function index()
    {
        $data = Page::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'asc')->get();
        $page = 'index';
        return view('panel.appearance.pages', compact('data', 'page'));
    }

    public function create()
    {
        $data = null;
        $page = 'create';
        return view('panel.appearance.pages', compact('data', 'page'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'url' => 'required|alpha_dash|string|regex:/[a-z]/'
        ]);

        $page = Page::create([
            'panel_id'          => Auth::user()->panel_id,
            'name'              => $request->name,
            'url'               => strtolower($request->url),
            'content'           => $request->page_content,
            'is_public'         => $request->is_public,
            'meta_title'        => $request->meta_title,
            'meta_keyword'      => $request->meta_keywords,
            'meta_description'  => $request->meta_description,
            'created_by'        => Auth::user()->id,
            'created_at'        => now(),
            'updated_at'        => now(),
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
                        'name' => strtolower($request->name),
                        'sort' => 2,
                        'content'  => defaultThemePageContent()
                    ];
                }
                ThemePage::insert($themePages);
            }
        }

        return redirect()->back()->with('success', 'Appearance Post save successfully !!');
    }

    public function edit($id)
    {
        $data = Page::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
        if (empty($data)) {
            return redirect()->route('admin.appearance.page.index');
        }
        $page = 'edit';
        return view('panel.appearance.pages', compact('data', 'page'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'url' => 'required|alpha_dash|string|regex:/[a-z]/'
        ]);

        Page::find($id)->update([
            'panel_id'          => Auth::user()->panel_id,
            'name'              => $request->name,
            'content'           => $request->page_content,
            'url'               => strtolower($request->url),
            'is_public'         => $request->is_public,
            'meta_title'        => $request->meta_title,
            'meta_keyword'      => $request->meta_keywords,
            'meta_description'  => $request->meta_description,
            'updated_by'        => Auth::user()->id,
            'updated_at'        => now(),
        ]);
        return redirect()->back()->with('success', 'Appearance Post update successfully !!');
    }

    public function updateStatus(Request $request)
    {
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
            'updated_at'  => now(),
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Status change successfully !!'
        ]);
    }
}
