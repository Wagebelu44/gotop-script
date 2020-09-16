<?php

namespace App\Http\Controllers\Panel;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\ProviderService;
use App\Models\ServiceCategory;
use App\Models\SettingProvider;
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
        return ServiceCategory::with('services', 'services.provider')->where('panel_id', auth()->user()->panel_id)->orderBy('id', 'ASC')->get();
    }

    public function getProviders()
    {
        return SettingProvider::where('panel_id', auth()->user()->panel_id)->get();
    }
    public function getProviderServices(Request $r)
    {
        try {
            if (isset($r->provider_id)) {

                $provider  = SettingProvider::find($r->provider_id);
                if ($provider != null) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $provider->api_url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,
                        http_build_query(array(
                            'key' =>$provider->api_key,
                            'action' => 'services',
                        )));

                    // Receive server response ...
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $server_output = curl_exec($ch);

                    curl_close ($ch);
                    $result  =  json_decode($server_output, true);
                    return response()->json([
                        'status' => true,
                        'data'    => $result,
                    ]);
                }
                else
                {
                    return response()->json([
                        'status' => false,
                        'data'    => "No provider found",
                    ]);
                }

            }
            else
            {
                return response()->json([
                    'status' => false,
                    'data'    => "Invalid parameters",
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data'    => $e->getMessage(),
            ]);
        }
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
                $data['status'] = 'Active';
            }

            if ($request->has('edit_id') && $request->has('edit_mode'))
            {
                
                $service = Service::find($request->edit_id);
                $service->update($data);
                if ($data['mode'] == 'Auto') {
                    $json_data = $request->provider_selected_service_data!=null?json_decode($request->provider_selected_service_data, true):null;
                    if ($json_data!=null) {
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
                            'panel_id' => auth()->user()->panel_id,
                        ]);
                    }
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
                        'panel_id' => auth()->user()->panel_id,
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

    public function enableService($id){
        $servcie = Service::find($id);
        $servcie->status = ($servcie->status =='Active') ? 'Deactivated':'Active';
        if ($servcie->save())
        {
            return response()->json(['status' => 200, 'message' => 'Service updated successfully.', 'data' => $servcie]);
        }
        else
        {
            return response()->json(['status' => 401, 'message' => 'Unable to update data', 'data' => null]);
        }
    }
    public function deleteService($id)
    {
        $service = Service::find($id);
        try {
            if ($service->icon) {
                Storage::delete('public/'.$service->icon);
            }

            $service->delete();

            return response()->json(['status' => 200, 'data'=> $service,  'message' => 'Service deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 401, 'data'=> null, 'message' => 'Unable to delete service']);
        }
    }
    public function duplicateService($service_id)
    {

        try {
            $service_clients = Service::find($service_id)->replicate();
            if ($service_clients->save()) {
                return response()->json(['status' => 200, 'message' => 'Service duplicate successfully.', 'data' => $service_clients]);
                //return redirect()->back()->withSuccess('Service duplicate successfully.');
            }
            else
            {
                return response()->json(['status' => 401, 'message' => 'Unable to duplicate service.']);
                //return redirect()->back()->withErrors(['error' => 'Unable to duplicate service']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 401, 'message' => $e->getMessage()]);
            //return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    public function updateService(Request $request, $id)
    {
        $data = $request->all();
        $servcie = Service::find($id);
        $udpated = $servcie->update($data);
        if ($udpated)
        {
            return response()->json(['status'=>200, 'data'=>$servcie, 'message'=>"Description updated successfully"]);
        }
        else
        {
            return response()->json(['status'=>401,  'data'=> null,  'message'=>"Error occured"]);
        }
    }
    public function bulkEnable(Request $request)
    {
        Service::whereIn('id',explode(',',$request->service_ids))->update([
            'status' => 'Active'
        ]);
        return response()->json(['status'=>200,'message'=>'successfully enabled all']);

    }
    public function bulkDisable(Request $request)
    {
        Service::whereIn('id',explode(',',$request->service_ids))->update([
            'status' => 'Deactivated'
        ]);
        return response()->json(['status'=>200,'message'=>'successfully disabled all']);
    }
    public function bulkCategory(Request $request)
    {
        Service::whereIn('id',explode(',',$request->service_ids))->update([
            'category_id' => $request->bulk_category_id
        ]);
        return response()->json(['status'=>200,'message'=>'successfully disabled all']);
    }
    public function bulkDelete(Request $request)
    {
        Service::whereIn('id',explode(',',$request->service_ids))->delete();
        return response()->json(['status'=>200,'message'=>'successfully disabled all']);
    }
    public function show($id)
    {
        return Service::find($id);
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
    public function showCategory($id)
    {
        return ServiceCategory::find($id);
    }
    public function enablingCategory(Request $request, $id)
    {
        $category = ServiceCategory::find($id);
        $category->status = $category->status == 'Active'?'Deactivated':'Active';
        if($category->save())
            return response()->json(['status'=>200,'data'=> $category, 'message'=>'Category Updated successfully.']);
        else
            return response()->json(['status'=>401,'data'=> null, 'message'=>'error occured.']);
    }
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
