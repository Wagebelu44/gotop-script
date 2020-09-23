<?php

namespace App\Http\Controllers\Panel\Appearance;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('menu')) {
            $pages = Page::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'asc')->get();
            $menus = Menu::with('page')->where('panel_id', Auth::user()->panel_id)->orderBy('sort', 'asc')->get();
            return view('panel.appearance.menu', compact('pages', 'menus'));
        } else {
            return view('panel.permission');
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('menu')) {
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
                'menu_link_type'    => $request->menu_type == 1 ? 'Yes' : 'No',
                'created_at'        => Auth::user()->id
            ]);

            return redirect()->back()->with('success', 'Menu save successfully!');
        } else {
            return view('panel.permission');
        }
    }

    public function edit($id)
    {
        if (Auth::user()->can('menu')) {
            $editMenu = Menu::where('panel_id', Auth::user()->panel_id)->where('id',$id)->first();
            return response()->json([
                'status' => 'success',
                'data' => $editMenu,
            ], 200);
        } else {
            return view('panel.permission');
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('menu')) {
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
        } else {
            return view('panel.permission');
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('menu')) {
            $data = Menu::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route('admin.appearance.menu.index');
            }
            $data->delete();
            return redirect()->back()->with('success', 'Menu delete successfully !!');
        } else {
            return view('panel.permission');
        }
    }

    public function sortableMenu(Request $request)
    {
        if (Auth::user()->can('menu')) {
            $dataSorting = Menu::where('panel_id', Auth::user()->panel_id)->get();
            foreach ($dataSorting as $menu) {
                $menu->timestamps = false; // To disable update_at field updation
                $id = $menu->id;
                foreach ($request->order as $orderSort) {
                    if ($orderSort['id'] == $id) {
                        $menu->update(['sort' => $orderSort['position']]);
                    }

                }
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Update successfully',
            ], 200);
        } else {
            return view('panel.permission');
        }
    }
}
