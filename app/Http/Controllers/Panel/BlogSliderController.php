<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\BlogSlider;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Storage;
use Image;
use Validator;

class BlogSliderController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('blog')) {
            $data = BlogSlider::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'asc')->get();
            $page = 'index';
            return view('panel.blog.slider-index', compact('data', 'page'));
        }else{
            return view('panel.permission');
        }
    }

    public function create()
    {
        if (Auth::user()->can('blog')) {
            $data = null;
            $page = 'create';
            return view('panel.blog.slider-index', compact('data', 'page'));
        }else{
            return view('panel.permission');
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('blog')) {
            $this->validate($request, [
                'title'     => 'required',
                'read_more' => 'required',
                'image'     => 'required',
                'status'    => 'required'
            ]);
            $checkBlogImage = BlogSlider::where('panel_id', Auth::user()->panel_id)->first();
            if ($request->hasFile('image')) {
                if (!empty($checkBlogImage->image)) {
                    deleteFile('./storage/images/blog/', $checkBlogImage->image);
                }
                $image = $request->file('image');
                $mime= $image->getClientOriginalExtension();
                $imageName = time()."_blog.".$mime;
                $image = Image::make($image)->resize(1880, 1254);
                Storage::disk('public')->put("images/blog/".$imageName, (string) $image->encode());
            }

            if (isset($imageName)) {
                $image =  $imageName;
            } else {
                $image = isset($checkBlogImage->image) ? $checkBlogImage->image:null;
            }

            BlogSlider::create([
                'panel_id'      => Auth::user()->panel_id,
                'title'         => $request->title,
                'read_more'     => $request->read_more,
                'image'         => $image,
                'status'        => $request->status,
                'created_by'    => Auth::user()->id,
            ]);

            return redirect()->back()->with('success', 'blog Slider save successfully !!');
        }else{
            return view('panel.permission');
        }
    }

    public function edit($id)
    {
        if (Auth::user()->can('blog')) {
            $data = BlogSlider::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route(' admin.blog-slider.index');
            }
            $page = 'edit';
            return view('panel.blog.slider-index', compact('data', 'page'));
        }else{
            return view('panel.permission');
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('blog')) {
            $this->validate($request, [
                'title'     => 'required',
                'read_more' => 'required',
                'image'     => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:1024',
                'status'    => 'required'
            ]);
            $checkBlogImage = BlogSlider::select('image')->where('panel_id', Auth::user()->panel_id)->first();
            if ($request->hasFile('image')) {
                if (!empty($checkBlogImage->image)) {
                    deleteFile('./storage/images/setting/', $checkBlogImage->image);
                }
                $image = $request->file('image');
                $mime= $image->getClientOriginalExtension();
                $imageName = time()."_blog.".$mime;
                $image = Image::make($image)->resize(1880, 1254);
                Storage::disk('public')->put("images/blog/".$imageName, (string) $image->encode());
            }

            BlogSlider::find($id)->update([
                'panel_id'      => Auth::user()->panel_id,
                'title'         => $request->title,
                'read_more'     => $request->read_more,
                'image'         => $imageName,
                'status'        => $request->status,
                'updated_by'    => Auth::user()->id,
            ]);

            return redirect()->back()->with('success', 'blog Slider update successfully !!');
        }else{
            return view('panel.permission');
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('blog')) {
            $data = BlogSlider::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route('admin.blog-slider.index');
            }
            $data->delete();

            return redirect(route('admin.blog-slider.index'))->with('success', 'blog slider delete successfully !!');
        }else{
            return view('panel.permission');
        }
    }
}
