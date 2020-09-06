<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{

    public function index()
    {
        return view('panel.services.index');
    }

    public function getCateServices(Request $request)
    {
        return ServiceCategory::where('panel_id', auth()->user()->panel_id)->where('status', 'active')->orderBy('id', 'ASC')->get();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }


    /* category starts */

    public function categoryStore(Request $request)
    {
        if ($request->has('edit_id'))
        {
            $request->validate([
                'name' => ['required', 'string', 'max:255']
            ]);
        }
        else
        {
            $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);

        }


        try {
            if($request->has('edit_id'))
            {
                $data = $request->except('_token', 'score','edit_id','edit_mode');
            }
            else
            {
                $data = $request->except('_token', 'score');
            }

            $data['panel_id'] = auth()->user()->panel_id;
            if ($request->hasFile('icon')) {
                $data['icon'] = $request->file('icon')->store('icons', ['disk' => 'public']);
            }
            if ($request->has('edit_id') && $request->has('edit_mode'))
            {
                $payload = ServiceCategory::find($request->edit_id);
                $payload->name = $data['name'] !=''?$data['name']:$payload->name;
                $payload->panel_id = $data['panel_id'] !=''?$data['panel_id']:$payload->panel_id;
                $payload->save();
            }
            else
            {
                $payload = ServiceCategory::create($data);
            }
            return response()->json(['status'=>200,'data'=> $payload, 'message'=>'Category created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status'=>401, 'data'=>$e->getMessage()]);
        }
    }
    
    /* category end */

}
