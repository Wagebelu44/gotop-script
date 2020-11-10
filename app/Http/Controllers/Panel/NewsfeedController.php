<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MediaController;
use App\Models\Newsfeed;
use App\Models\NewsfeedCategory;
use App\Models\NewsfeedRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsfeedController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();
        if (Auth::user()->can('newsfeed')) {
            $sql = Newsfeed::where('panel_id', Auth::user()->panel_id);
            if (isset($input['search_text']) && !empty($request['search_text'])) {
                $sql->where(function($q) use($input) {
                    $q->where('title', 'LIKE', '%'.$input['search_text'].'%');
                    $q->orWhere('image', 'LIKE', '%'.$input['search_text'].'%');
                    $q->orWhere('content', 'LIKE', '%'.$input['search_text'].'%');
                    $q->orWhere('status', 'LIKE', '%'.$input['search_text'].'%');
                });
            }
            $data  = $sql->orderBy('id', 'asc')->get();
            $page = 'index';
            return view('panel.newsfeed.index', compact('data', 'page'));
        } else {
            return view('panel.permission');
        }
    }

    public function create()
    {
        if (Auth::user()->can('create newsfeed')) {
            $data = null;
            $page = 'create';
            $categories = NewsfeedCategory::where('panel_id', Auth::user()->panel_id)->get();
            return view('panel.newsfeed.index', compact('data', 'page', 'categories'));
        } else {
            return view('panel.permission');
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('create newsfeed')) {
            $this->validate($request, [
                'title'             => 'required|max:191',
                'newsfeed_content'  => 'required',
                'status'            => 'required',
                'categories'        => 'required|array',
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image = (new MediaController())->imageUpload($file, 'images/newsfeed', 1, null, [400, 400]);
            }
            $newsfeed = Newsfeed::create([
                'panel_id'          => Auth::user()->panel_id,
                'title'             => $request->title,
                'content'           => $request->newsfeed_content,
                'image'             => isset($image) ? $image['name']:'',
                'important_news'    => isset($request->important_news)?'Yes':'No',
                'service_update'    => isset($request->service_update)?'Yes':'No',
                'status'            => $request->status,
                'created_by'        => Auth::user()->id,
            ]);

            if ($newsfeed) {
                if (!empty($request->categories)) {
                    $categoryData = [];
                    foreach ($request->categories as $k => $categoryId) {
                        $categoryData[] = [
                            'newsfeed_id' => $newsfeed->id,
                            'category_id' => $categoryId,
                        ];
                    }
                    if (!empty($categoryData)) {
                        NewsfeedRelation::insert($categoryData);
                    }
                }
            }


            return redirect()->back()->with('success', 'Newsfeed save successfully !!');
        } else {
            return view('panel.permission');
        }
    }

    public function edit($id)
    {
        if (Auth::user()->can('edit newsfeed')) {
            $data = Newsfeed::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            $data->getCategories = $data->getCategories->pluck('category_id')->toArray();
            if (empty($data)) {
                return redirect()->route(' admin.newsfeed.index');
            }
            $categories = NewsfeedCategory::where('panel_id', Auth::user()->panel_id)->get();
            $page = 'edit';
            return view('panel.newsfeed.index', compact('data', 'page', 'categories'));
        } else {
            return view('panel.permission');
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('edit newsfeed')) {
            $this->validate($request, [
                'title'             => 'required|max:191',
                'newsfeed_content'  => 'required',
                'status'            => 'required',
                'categories'        => 'required|array',
            ]);

            if ($request->hasFile('image')) {
                $checkBlogPostImage = Newsfeed::where('panel_id', Auth::user()->panel_id)->first();
                if (!empty($checkBlogPostImage->image)) {
                    (new MediaController())->delete('images/newsfeed', $checkBlogPostImage->image, 1);
                }
                $file = $request->file('image');
                $image = (new MediaController())->imageUpload($file, 'images/newsfeed', 1, null, [400, 400]);
            }

            $newsfeed = Newsfeed::find($id)->update([
                'panel_id'          => Auth::user()->panel_id,
                'title'             => $request->title,
                'content'           => $request->newsfeed_content,
                'image'             => isset($image) ? $image['name']:'',    
                'important_news'    => isset($request->important_news)?'Yes':'No',
                'service_update'    => isset($request->service_update)?'Yes':'No',
                'status'            => $request->status,
                'updated_at'        => Auth::user()->id,
            ]);


            if ($newsfeed){
                if (!empty($request->categories)) {
                    NewsfeedRelation::where('newsfeed_id', $id)->delete();
                    $categoryData = [];
                    foreach ($request->categories as $k => $categoryId) {
                        $categoryData[] = [
                            'newsfeed_id' => $id,
                            'category_id' => $categoryId,
                        ];
                    }
                    if (!empty($categoryData)) {
                        NewsfeedRelation::insert($categoryData);
                    }
                }
            }
            return redirect()->back()->with('success', 'Newsfeed update successfully !!');
        } else {
            return view('panel.permission');
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('delete newsfeed')) {
            $data = Newsfeed::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route('admin.newsfeed.index');
            }
            if (!empty($data->image)) {
                (new MediaController())->delete('images/newsfeed', $data->image, 1);
            }
            $data->delete();
            return redirect(route('admin.newsfeed.index'))->with('success', 'Newsfeed delete successfully !!');
        } else {
            return view('panel.permission');
        }
    }
}
