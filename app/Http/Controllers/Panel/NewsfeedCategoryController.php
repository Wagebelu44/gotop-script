<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\NewsfeedCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsfeedCategoryController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();
        if (Auth::user()->can('newsfeed')) {
            $sql_data = NewsfeedCategory::where('panel_id', Auth::user()->panel_id);
            if (isset($input['search_text']) && !empty($request['search_text'])) {
                $sql_data->where(function($q) use($input) {
                    $q->where('name', 'LIKE', '%'.$input['search_text'].'%');
                    $q->orWhere('color', 'LIKE', '%'.$input['search_text'].'%');
                    $q->orWhere('status', 'LIKE', '%'.$input['search_text'].'%');
                });
            }
            $data= $sql_data->orderBy('id', 'asc')->get();
            $page = 'index';
            return view('panel.newsfeed.category', compact('data', 'page'));
        } else {
            return view('panel.permission');
        }

    }

    public function create()
    {
        if (Auth::user()->can('create newsfeed category')) {
            $data = null;
            $page = 'create';
            return view('panel.newsfeed.category', compact('data', 'page'));
        } else {
            return view('panel.permission');
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('create newsfeed category')) {
            $this->validate($request, [
                'name'     => 'required',
                'color'    => 'required',
                'status'   => 'required'
            ]);

            NewsfeedCategory::create([
                'panel_id'      => Auth::user()->panel_id,
                'name'          => $request->name,
                'status'        => $request->status,
                'color'         => $request->color,
                'created_by'    => Auth::user()->id,
            ]);

            return redirect()->back()->with('success', 'Category save successfully !!');
        } else {
            return view('panel.permission');
        }
    }

    public function edit($id)
    {
        if (Auth::user()->can('edit newsfeed category')) {
            $data = NewsfeedCategory::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route(' admin.newsfeed-category.index');
            }

            $page = 'edit';
            return view('panel.newsfeed.category', compact('data', 'page'));
        } else {
            return view('panel.permission');
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('edit newsfeed category')) {
            $this->validate($request, [
                'name'     => 'required',
                'status'   => 'required'
            ]);

            NewsfeedCategory::find($id)->update([
                'panel_id'      => Auth::user()->panel_id,
                'name'          => $request->name,
                'color'         => $request->color,
                'status'        => $request->status,
                'updated_by'    => Auth::user()->id,
            ]);

            return redirect()->back()->with('success', 'Category update successfully !!');
        } else {
            return view('panel.permission');
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('delete newsfeed category')) {
            $data = NewsfeedCategory::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route('admin.newsfeed-category.index');
            }
            $data->delete();

            return redirect(route('admin.newsfeed-category.index'))->with('success', 'Newsfeed category delete successfully !!');
        } else {
            return view('panel.permission');
        }
    }
}
