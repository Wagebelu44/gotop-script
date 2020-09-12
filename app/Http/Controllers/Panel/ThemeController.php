<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Theme;
use App\Models\ThemePage;
use Auth;

class ThemeController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('themes')) {
            $themes = Theme::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'ASC')->get();
            return view('panel.theme.index', compact('themes'));
        }else{
            return view('panel.permission');
        }
    }

    public function edit(Request $request, $id)
    {
        if(Auth::user()->can('themes')) {
            $theme = Theme::with('pages.page')->where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (!empty($theme)) {
                $page = null;
                if ($request->page) {
                    $page = ThemePage::where('panel_id', Auth::user()->panel_id)->where('id', $request->page)->first();
                }
                return view('panel.theme.view', compact('theme', 'page'));
            }
            return redirect()->route('admin.theme.index');
        }else{
            return view('panel.permission');
        }
    }

    public function update(Request $request, $id)
    {
        if(Auth::user()->can('themes')) {
            $page = ThemePage::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($page)) {
                return redirect()->back()->with('error', 'Page not found!');
            }

            $request->validate([
                'content' => 'required',
            ]);
            $page->update(['content' => $request->content]);

            return redirect()->back()->with('success', 'Page update successfully!');
        }else{
            return view('panel.permission');
        }
    }

    public function active($id)
    {
        if(Auth::user()->can('themes')) {
            $theme = Theme::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (!empty($theme)) {
                Theme::where('panel_id', Auth::user()->panel_id)->update(['status' => 'Deactivated']);
                $theme->update(['status' => 'Active']);
                return redirect()->back()->with('success', 'Theme active successfully!');
            }
            return redirect()->back()->with('error', 'Theme not found!');
        }else{
            return view('panel.permission');
        }
    }

    public function reset($id)
    {
        if(Auth::user()->can('themes')) {
            $page = ThemePage::with('page.globalPage')->where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (!empty($page)) {
                $content = $page->page->globalPage->content;
                if (empty($content)) {
                    $content = defaultThemePageContent();
                }
                $page->update(['content' => $content]);
                return redirect()->back()->with('success', 'Theme active successfully!');
            }
            return redirect()->back()->with('error', 'Theme not found!');
        }else{
            return view('panel.permission');
        }
    }
}
