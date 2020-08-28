<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class GeneralController extends Controller
{
    public function index(){
        return view('panel.settings.general');
    }

    public function generalUpdate(Request $request){
        dd($request->all());
        try {
            $data = $request->all();
            $validator = Validator::make($data, [
                'logo.*' => 'image|mimes:jpeg,png,jpg,gif|max:2000'
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }


            // update code......
        }catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

    }
}
