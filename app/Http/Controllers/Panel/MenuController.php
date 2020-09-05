<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Http\Request;
use Auth;

class MenuController extends Controller
{
    public function index()
    {
        $pages = Page::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'asc')->get();
        $menus = Menu::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'asc')->get();
        return view('panel.appearance.menu.index', compact('pages', 'menus'));
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

    public function sortable(Request $request)
    {
        $data = Menu::where('panel_id', Auth::user()->panel_id)->get();
        foreach ($data as $menu) {
            $menu->timestamps = false; // To disable update_at field updation
            $id = $menu->id;
            foreach ($request->order as $order) {
                if ($order['id'] == $id) {
                    $menu->update(['sort' => $order['position']]);
                }
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Update successfully',
        ], 200);
    }
}
