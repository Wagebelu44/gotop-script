<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;
use Validator;

class BlogController extends Controller
{

    public function index()
    {
        $data = Blog::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'asc')->get();
        $page = 'index';
        return view('panel.blog.index', compact('data', 'page'));
    }

    public function create()
    {
        $data = null;
        $page = 'create';
        $get_blog_category = BlogCategory::where('panel_id', Auth::user()->panel_id)->get();
        return view('panel.blog.index', compact('data', 'page', 'get_blog_category'));
    }

    public function store(Request $request)
    {
        //return $request->all();
        $this->validate($request, [
            'image'     => 'required',
            'category_id'     => 'required',
            'title'     => 'required',
            'blog_content'     => 'required',
            'type'     => 'required',
            'status'   => 'required'
        ]);

        $checkBlogPostImage = Blog::where('panel_id', Auth::user()->panel_id)->first();
        if ($request->hasFile('image')) {
            if (!empty($checkBlogPostImage->image)) {
                deleteFile('./storage/images/blog-post/', $checkBlogPostImage->image);
            }
            $image = $request->file('image');
            $mime= $image->getClientOriginalExtension();
            $imageName = time()."_post.".$mime;
            $image = Image::make($image)->resize(1880, 1254);
            Storage::disk('public')->put("images/blog-post/".$imageName, (string) $image->encode());
        }

        if (isset($imageName)) {
            $image =  $imageName;
        } else {
            $image = isset($checkBlogPostImage->image) ? $checkBlogPostImage->image:null;
        }

        Blog::create([
            'panel_id'      => Auth::user()->panel_id,
            'title'          => $request->title,
            'slug'=> $this->createSlug(Str::slug(strtolower($request->title))),
            'category_id'          => $request->category_id,
            'content'          => $request->blog_content,
            'image'             => $image,
            'status'        => $request->status,
            'created_by'    => Auth::user()->id,
        ]);
        return redirect()->back()->with('success', 'blog Post save successfully !!');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data = Blog::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
        if (empty($data)) {
            return redirect()->route(' admin.blog.index');
        }
        $get_blog_category = BlogCategory::where('panel_id', Auth::user()->panel_id)->get();
        $page = 'edit';
        return view('panel.blog.index', compact('data', 'page', 'get_blog_category'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'image'     => 'required',
            'category_id'     => 'required',
            'title'     => 'required',
            'blog_content'     => 'required',
            'type'     => 'required',
            'status'   => 'required'
        ]);

        $checkBlogPostImage = Blog::where('panel_id', Auth::user()->panel_id)->first();
        if ($request->hasFile('image')) {
            if (!empty($checkBlogPostImage->image)) {
                deleteFile('./storage/images/blog-post/', $checkBlogPostImage->image);
            }
            $image = $request->file('image');
            $mime= $image->getClientOriginalExtension();
            $imageName = time()."_post.".$mime;
            $image = Image::make($image)->resize(1880, 1254);
            Storage::disk('public')->put("images/blog-post/".$imageName, (string) $image->encode());
        }

        if (isset($imageName)) {
            $image =  $imageName;
        } else {
            $image = isset($checkBlogPostImage->image) ? $checkBlogPostImage->image:null;
        }

        Blog::find($id)->update([
            'panel_id'      => Auth::user()->panel_id,
            'title'          => $request->title,
            'slug'=> $this->createSlug(Str::slug(strtolower($request->title))),
            'category_id'          => $request->category_id,
            'content'          => $request->blog_content,
            'image'             => $image,
            'status'        => $request->status,
            'created_by'    => Auth::user()->id,
        ]);
        return redirect()->back()->with('success', 'blog Post update successfully !!');
    }

    public function destroy($id)
    {
        $data = Blog::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
        if (empty($data)) {
            return redirect()->route('admin.blog.index');
        }
        $data->delete();

        return redirect(route('admin.blog.index'))->with('success', 'blog delete successfully !!');
    }

    //For Generating Unique Slug Our Custom function
    public function createSlug($slug, $id = 0)
    {
        $allSlugs = $this->getRelatedSlugs($slug, $id);

        if (! $allSlugs->contains('slug', $slug)){
            return $slug;
        }

        for ($i = 1; $i <= 10; $i++) {
            $newSlug = $slug.'-'.$i;
            if (! $allSlugs->contains('slug', $newSlug)) {
                return $newSlug;
            }
        }
        throw new \Exception('Can not create a unique slug');
    }

    protected function getRelatedSlugs($slug, $id = 0)
    {
        return Blog::select('slug')->where('slug', 'like', $slug.'%')
            ->where('id', '<>', $id)
            ->get();
    }
}
