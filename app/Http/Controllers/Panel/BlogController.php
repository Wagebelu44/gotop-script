<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MediaController;
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
        if(Auth::user()->can('blog')) {
            $data = Blog::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'asc')->get();
            $page = 'index';
            return view('panel.blog.index', compact('data', 'page'));
        }else{
            return view('panel.permission');
        }
    }

    public function create()
    {
        if(Auth::user()->can('blog')) {
            $data = null;
            $page = 'create';
            $categories = BlogCategory::where('panel_id', Auth::user()->panel_id)->get();
            return view('panel.blog.index', compact('data', 'page', 'categories'));
        }else{
            return view('panel.permission');
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('blog')) {
            $this->validate($request, [
                'image'         => 'required|image',
                'title'         => 'required',
                'blog_content'  => 'required',
                'type'          => 'required',
                'status'        => 'required'
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image = (new MediaController())->imageUpload($file, 'images/blog', 1);
            }
            Blog::create([
                'panel_id'          => Auth::user()->panel_id,
                'title'             => $request->title,
                'slug'              => $this->createSlug(Str::slug(strtolower($request->title))),
                'category_id'       => $request->category_id,
                'content'           => $request->blog_content,
                'meta_title'        => $request->meta_title,
                'meta_keyword'      => $request->meta_keyword,
                'meta_description'  => $request->meta_description,
                'image'             => isset($image) ? $image['name']:'',
                'status'            => $request->status,
                'type'              => $request->type,
                'created_by'        => Auth::user()->id,
            ]);
            return redirect()->back()->with('success', 'blog Post save successfully !!');
        }else{
            return view('panel.permission');
        }
    }

    public function edit($id)
    {
        if(Auth::user()->can('blog')) {
            $data = Blog::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route(' admin.blog.index');
            }
            $categories = BlogCategory::where('panel_id', Auth::user()->panel_id)->get();
            $page = 'edit';
            return view('panel.blog.index', compact('data', 'page', 'categories'));
        }else{
            return view('panel.permission');
        }
    }

    public function update(Request $request, $id)
    {
        if(Auth::user()->can('blog')) {
            $this->validate($request, [
                'image'        => 'sometimes|image',
                'title'        => 'required',
                'blog_content' => 'required',
                'type'         => 'required',
                'status'       => 'required'
            ]);

            if ($request->hasFile('image')) {
                $checkBlogPostImage = Blog::where('panel_id', Auth::user()->panel_id)->first();
                if (!empty($checkBlogPostImage->image)) {
                    (new MediaController())->delete('images/blog', $checkBlogPostImage->image, 1);
                }
                $file = $request->file('image');
                $image = (new MediaController())->imageUpload($file, 'images/blog', 1);
            }

            Blog::find($id)->update([
                'panel_id'          => Auth::user()->panel_id,
                'title'             => $request->title,
                'slug'              => $this->createSlug(Str::slug(strtolower($request->title))),
                'category_id'       => $request->category_id,
                'meta_title'        => $request->meta_title,
                'meta_keyword'      => $request->meta_keyword,
                'meta_description'  => $request->meta_description,
                'content'           => $request->blog_content,
                'image'             => isset($image) ? $image['name']:'',
                'type'              => $request->type,
                'status'            => $request->status,
                'created_by'        => Auth::user()->id,
            ]);
            return redirect()->back()->with('success', 'blog Post update successfully !!');
        }else{
            return view('panel.permission');
        }
    }

    public function destroy($id)
    {
        if(Auth::user()->can('blog')) {
            $data = Blog::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route('admin.blog.index');
            }
            if (!empty($data->image)) {
                (new MediaController())->delete('images/blog', $data->image, 1);
            }
            $data->delete();
            return redirect(route('admin.blog.index'))->with('success', 'blog delete successfully !!');
        }else{
            return view('panel.permission');
        }
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
