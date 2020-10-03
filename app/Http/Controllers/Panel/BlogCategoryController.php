<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogCategoryController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('blog')) {
            $data = BlogCategory::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'asc')->get();
            $page = 'index';
            return view('panel.blog.category-index', compact('data', 'page'));
        } else {
            return view('panel.permission');
        }

    }

    public function create()
    {
        if (Auth::user()->can('blog')) {
            $data = null;
            $page = 'create';
            return view('panel.blog.category-index', compact('data', 'page'));
        } else {
            return view('panel.permission');
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('blog')) {
            $this->validate($request, [
                'name'     => 'required|max:191',
                'status'   => 'required'
            ]);

            BlogCategory::create([
                'panel_id'      => Auth::user()->panel_id,
                'name'          => $request->name,
                'status'        => $request->status,
                'created_by'    => Auth::user()->id,
            ]);

            return redirect()->back()->with('success', 'blog Category save successfully !!');
        } else {
            return view('panel.permission');
        }
    }

    public function edit($id)
    {
        if (Auth::user()->can('blog')) {
            $data = BlogCategory::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route(' admin.blog-category.index');
            }

            $page = 'edit';
            return view('panel.blog.category-index', compact('data', 'page'));
        } else {
            return view('panel.permission');
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('blog')) {
            $this->validate($request, [
                'name'     => 'required|max:191',
                'status'   => 'required'
            ]);

            BlogCategory::find($id)->update([
                'panel_id'      => Auth::user()->panel_id,
                'name'          => $request->name,
                'status'        => $request->status,
                'updated_by'    => Auth::user()->id,
            ]);

            return redirect()->back()->with('success', 'blog Category update successfully !!');
        } else {
            return view('panel.permission');
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('blog')) {
            $data = BlogCategory::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route('admin.blog-category.index');
            }
            $data->delete();

            return redirect(route('admin.blog-category.index'))->with('success', 'blog delete successfully !!');
        } else {
            return view('panel.permission');
        }
    }
}
