<?php

namespace App\Http\Controllers\Panel;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{

    public function index()
    {
        return view('panel.services.index');
    }

    public function getCateServices(Request $request)
    {
        return ServiceCategory::with('services')->where('panel_id', auth()->user()->panel_id)->where('status', 'active')->orderBy('id', 'ASC')->get();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        if ($request->service_type == 'Custom Comments Package' || $request->service_type == 'Package') {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'price' => 'required|numeric',
                'category_id' => 'required|integer|exists:service_categories,id',
            ]);
        }
        else
        {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'price' => 'required',
                'min_quantity' => 'required',
                'max_quantity' => 'required',
                'category_id' => 'required|integer|exists:service_categories,id',
            ]);
        }


        try {

            if ($request->has('edit_id'))
            {
                $data = $request->except('_token', 'score', 'users','edit_id','edit_mode', 'provider_selected_service_data');
            }
            else
            {
                $data = $request->except('_token', 'score', 'users', 'provider_selected_service_data');
            }
            $data['panel_id'] = auth()->user()->panel_id;
            $data['provider_sync_status'] = $request->provider_sync_status == 'on'? true: false;
            if ($request->service_type == 'Custom Comments Package' || $request->service_type == 'Package')
            {
                $data['min_quantity'] = 1;
                $data['max_quantity'] = 1;
            }

            if (!$request->has('edit_id'))
            {
                $data['status'] = 'active';
            }

            if ($request->has('edit_id') && $request->has('edit_mode'))
            {
                $service = Service::find($request->edit_id);
                $service->update($data);
                if ($data['mode'] == 'Auto') {
                    $json_data = json_decode($request->provider_selected_service_data, true);
                    ProviderService::updateOrCreate(
                        ['service_id'=> $service->id],
                        [
                        'provider_id' => $data['provider_id'],
                        'provider_service_id' => $json_data['service'],
                        'name' => $json_data['name'],
                        'type' => $json_data['type'],
                        'category' =>  $json_data['category'],
                        'rate'=>  $json_data['rate'],
                        'min'=>  $json_data['min'],
                        'max'=>  $json_data['max'],
                    ]);
                }
            }
            else
            {
                $service = Service::create($data);
                if ($data['mode'] == 'Auto') {
                    $json_data = json_decode($request->provider_selected_service_data, true);
                    ProviderService::create([
                        'service_id' => $service->id,
                        'provider_id' => $data['provider_id'],
                        'provider_service_id' => $json_data['service'],
                        'name' => $json_data['name'],
                        'type' => $json_data['type'],
                        'category' =>  $json_data['category'],
                        'rate'=>  $json_data['rate'],
                        'min'=>  $json_data['min'],
                        'max'=>  $json_data['max'],
                    ]);
                }
            }
            return response()->json(['status'=>200,'data'=> $service, 'message'=>'Service created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status'=>401, 'data'=>$e->getMessage()]);
        }
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
