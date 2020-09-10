<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Theme;
use Auth;

class ThemeController extends Controller
{
    public function index()
    {
        $themes = Theme::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'ASC')->get();
        return view('panel.theme.index', compact('themes'));
    }

    public function show($id)
    {
        $theme = Theme::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
        if (!empty($theme)) {
            dd('a');
        }
        return redirect()->back()->with('error', 'Theme not found!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'menu_name' => 'required|max:255',
        ]);

        $external_url = '';
        if ($request->menu_link == 0) :
            $external_url = $request->external_url;
        endif;

        Menu::create([
            'panel_id'          => Auth::user()->panel_id,
            'menu_name'         => $request->menu_name,
            'external_link'     => $external_url,
            'menu_link_id'      => $request->menu_link,
            'menu_link_type'    => $request->menu_type == 1 ? 'YES' : 'NO',
            'created_at'        => Auth::user()->id
        ]);

        return redirect()->back()->with('success', 'Menu save successfully!');
    }

    public function edit($id)
    {
        $editMenu = Menu::where('panel_id', Auth::user()->panel_id)->where('id',$id)->first();
        return response()->json([
            'status' => 'success',
            'data' => $editMenu,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'menu_name_edit' => 'required|max:255',
        ]);

        $external_url = '';
        if ($request->menu_link == 0){
            $external_url = $request->external_url;
        }

        Menu::find($id)->update([
            'panel_id'       => Auth::user()->panel_id,
            'menu_name'      => $request->menu_name_edit,
            'external_link'  => $external_url,
            'menu_link_id'   => $request->menu_link_edit,
            'updated_at'     => Auth::user()->id
        ]);

        return redirect()->back()->with('success', 'Menu update successfully!');
    }

    public function destroy($id)
    {
        $data = Menu::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
        if (empty($data)) {
            return redirect()->route('admin.menu.index');
        }
        $data->delete();

        return redirect()->back()->with('success', 'Menu delete successfully !!');
    }

    public function active(Request $request, $id)
    {
        $theme = Theme::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
        if (!empty($theme)) {
            Theme::where('panel_id', Auth::user()->panel_id)->update(['status' => 'Deactivated']);
            $theme->update(['status' => 'Active']);
            return redirect()->back()->with('success', 'Theme active successfully!');
        }
        return redirect()->back()->with('error', 'Theme not found!');
    }
}
