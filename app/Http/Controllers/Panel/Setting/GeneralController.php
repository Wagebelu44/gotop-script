<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\SettingGeneral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
use Image;

class GeneralController extends Controller
{
    public function index(){
        $panelId = 1;
        $general = SettingGeneral::where('panel_id', $panelId)->first();
        return view('panel.settings.general', compact('general'));
    }

    public function generalUpdate(Request $request){
        try {
            $data = $request->all();
            $validator = Validator::make($data, [
                'logo'    => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2000',
                'favicon' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2000'
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $panelId = 1;
            $checkLogoFavicon = SettingGeneral::select('logo', 'favicon')->where('panel_id', $panelId)->first();
            if ($request->hasFile('logo')) {
                if (!empty($checkLogoFavicon->logo)){
                    deleteFile('./storage/images/setting/', $checkLogoFavicon->logo);
                }
                $logo = $request->file('logo');
                $mime= $logo->getClientOriginalExtension();
                $logoName = time()."_logo.".$mime;
                $logo = Image::make($logo)->resize(200, 80);
                Storage::disk('public')->put("images/setting/".$logoName, (string) $logo->encode());
            }

            if ($request->hasFile('favicon')) {
                if (!empty($checkLogoFavicon->favicon)){
                    deleteFile('./storage/images/setting/', $checkLogoFavicon->favicon);
                }
                $favicon = $request->file('favicon');
                $mime= $favicon->getClientOriginalExtension();
                $faviconName = time()."_favicon.".$mime;
                $favicon = Image::make($favicon)->resize(16, 16);
                Storage::disk('public')->put("images/setting/".$faviconName, (string) $favicon->encode());
            }

            if (isset($logoName)){
                $logo =  $logoName;
            }else{
                $logo = isset($checkLogoFavicon->logo) ? $checkLogoFavicon->logo:null;
            }

            if (isset($faviconName)){
                $favicon = $faviconName;
            }else{
                $favicon = isset($checkLogoFavicon->favicon) ? $checkLogoFavicon->favicon:null;
            }

             SettingGeneral::updateOrCreate(
                ['panel_id' => $panelId],
                [
                    'updated_by'         => auth()->guard('panelAdmin')->id(),
                    'logo'               => $logo,
                    'favicon'            => $favicon,
                    'timezone'           => $data['timezone'],
                    'currency_format'    => $data['currency_format'],
                    'rates_rounding'     => $data['rates_rounding'],
                    'ticket_system'      => $data['ticket_system'],
                    'tickets_per_user'   => $data['tickets_per_user'],
                    'signup_page'        => $data['signup_page'],
                    'email_confirmation' => $data['email_confirmation'],
                    'skype_field'        => $data['skype_field'],
                    'name_fields'        => $data['name_fields'],
                    'terms_checkbox'     => $data['terms_checkbox'],
                    'reset_password'     => $data['reset_password'],
                    'average_time'       => $data['average_time'],
                    'drip_feed_interval' => $data['drip_feed_interval'],
                    'custom_header_code' => null,
                    'custom_footer_code' => null,
                ]
            );

            return redirect()->back()->with('success', 'General Setting update successfully!');
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }
}
