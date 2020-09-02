<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\SettingGeneral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
use Image;
use Auth;

class GeneralController extends Controller
{
    public function index(){
        $general = SettingGeneral::where('panel_id', Auth::user()->panel_id)->first();
        return view('panel.settings.general', compact('general'));
    }

    public function generalUpdate(Request $request){
        $this->validate($request, [
            'logo'    => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2000',
            'favicon' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2000'
        ]);

        $checkLogoFavicon = SettingGeneral::select('logo', 'favicon')->where('panel_id', Auth::user()->panel_id)->first();
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
            [
                'panel_id'   => Auth::user()->panel_id,
                'updated_by' => Auth::user()->id,
            ],
            [
                'logo'               => $logo,
                'favicon'            => $favicon,
                'timezone'           => $request->timezone,
                'currency_format'    => $request->currency_format,
                'rates_rounding'     => $request->rates_rounding,
                'ticket_system'      => $request->ticket_system,
                'tickets_per_user'   => $request->tickets_per_user,
                'signup_page'        => $request->signup_page,
                'email_confirmation' => $request->email_confirmation,
                'skype_field'        => $request->skype_field,
                'name_fields'        => $request->name_fields,
                'terms_checkbox'     => $request->terms_checkbox,
                'reset_password'     => $request->reset_password,
                'average_time'       => $request->average_time,
                'drip_feed_interval' => $request->drip_feed_interval,
                'custom_header_code' => null,
                'custom_footer_code' => null,
            ]
        );

        return redirect()->back()->with('success', 'General Setting update successfully!');
    }
}
