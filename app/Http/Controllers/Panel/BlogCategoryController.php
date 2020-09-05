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
        $data = BlogCategory::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'asc')->get();
        $page = 'index';
        return view('panel.blog.category-index', compact('data', 'page'));
    }

    public function create()
    {
        $data = null;
        $page = 'create';
        return view('panel.blog.category-index', compact('data', 'page'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required',
            'status'   => 'required'
        ]);

        BlogCategory::create([
            'panel_id'      => Auth::user()->panel_id,
            'name'          => $request->name,
            'status'        => $request->status,
            'created_by'    => Auth::user()->id,
        ]);

        return redirect()->back()->with('success', 'blog Category save successfully !!');
    }

    public function edit($id)
    {
        $data = BlogCategory::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
        if (empty($data)) {
            return redirect()->route(' admin.blog-category.index');
        }

        $page = 'edit';
        return view('panel.blog.category-index', compact('data', 'page'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'     => 'required',
            'status'   => 'required'
        ]);

        BlogCategory::find($id)->update([
            'panel_id'      => Auth::user()->panel_id,
            'name'          => $request->name,
            'status'        => $request->status,
            'updated_by'    => Auth::user()->id,
        ]);

        return redirect()->back()->with('success', 'blog Category update successfully !!');
    }

    public function destroy($id)
    {
        $data = BlogCategory::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
        if (empty($data)) {
            return redirect()->route('admin.blog-category.index');
        }
        $data->delete();

        return redirect(route('admin.blog-category.index'))->with('success', 'blog delete successfully !!');
    }
}
