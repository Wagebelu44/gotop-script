<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MediaController;
use App\Models\SettingGeneral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
use Image;
use Illuminate\Support\Facades\Auth;

class GeneralController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('general setting')) {
            $general = SettingGeneral::where('panel_id', Auth::user()->panel_id)->first();
            return view('panel.settings.general', compact('general'));
        } else {
            return view('panel.permission');
        }
    }

    public function generalUpdate(Request $request)
    {
        if (Auth::user()->can('general setting')) {
            $this->validate($request, [
                'logo'    => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2000',
                'favicon' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2000'
            ]);

            $checkLogoFavicon = SettingGeneral::select('logo', 'favicon')->where('panel_id', Auth::user()->panel_id)->first();
            if ($request->hasFile('logo')) {
                if (!empty($checkLogoFavicon->logo)) {
                    (new MediaController())->delete('images/setting', $checkLogoFavicon->logo, 1);
                }
                $file = $request->file('logo');
                $logoImage = (new MediaController())->imageUpload($file, 'images/setting', 1, null, [200, 80]);
            }

            if ($request->hasFile('favicon')) {
                if (!empty($checkLogoFavicon->favicon)) {
                    (new MediaController())->delete('images/setting', $checkLogoFavicon->favicon, 1);
                }
                $favicon = $request->file('favicon');
                $faviconImage = (new MediaController())->imageUpload($favicon, 'images/setting', 1, null, [16, 16]);
            }

            if (isset($logoImage['name'])) {
                $logo =  $logoImage['name'];
            } else {
                $logo = isset($checkLogoFavicon->logo) ? $checkLogoFavicon->logo:null;
            }

            if (isset($faviconImage['name'])) {
                $favicon = $faviconImage['name'];
            } else {
                $favicon = isset($checkLogoFavicon->favicon) ? $checkLogoFavicon->favicon:null;
            }

            SettingGeneral::updateOrCreate([
                'panel_id'   => Auth::user()->panel_id,
            ], [
                'updated_by'         => Auth::user()->id,
                'logo'               => $logo,
                'favicon'            => $favicon,
                'panel_name'         => $request->panel_name,
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
                'newsfeed_align'     => $request->newsfeed_align,
                'newsfeed'           => isset($request->newsfeed) ? 'Yes':'No',
                'horizontal_menu'    => isset($request->horizontal_menu) ? 'Yes':'No',
                'total_order'        => isset($request->total_order) ? 'Yes':'No',
                'total_spent'        => isset($request->total_spent) ? 'Yes':'No',
                'account_status'     => isset($request->account_status) ? 'Yes':'No',
                'point'              => isset($request->point) ? 'Yes':'No',
                'redeem'             => isset($request->redeem) ? 'Yes':'No',
                'custom_header_code' => null,
                'custom_footer_code' => null,
            ]);

            return redirect()->back()->with('success', 'General Setting update successfully!');
        } else {
            return view('panel.permission');
        }
    }
}
