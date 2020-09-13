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
        $themes = Theme::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'ASC')->get();
        return view('panel.theme.index', compact('themes'));
    }

    public function edit(Request $request, $id)
    {
        $theme = Theme::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
        if (!empty($theme)) {
            $pages = ThemePage::select('group')->with(['groupPages' => function($q) use($id) {
                $q->where('panel_id', Auth::user()->panel_id);
                $q->where('theme_id', $id);
            }])->where('panel_id', Auth::user()->panel_id)->where('theme_id', $id)->groupBy('group')->orderBy('group', 'DESC')->get();

            $page = null;
            if ($request->page) {
                $page = ThemePage::where('panel_id', Auth::user()->panel_id)->where('name', $request->page)->first();
            }
            return view('panel.theme.view', compact('theme', 'pages', 'page'));
        }
        return redirect()->route('admin.theme.index');
    }

    public function update(Request $request, $id)
    {
        $page = ThemePage::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
        if (empty($page)) {
            return redirect()->back()->with('error', 'Page not found!');
        }

        $request->validate([
            'content' => 'required',
        ]);
        $page->update(['content' => $request->content]);

        return redirect()->back()->with('success', 'Page update successfully!');
    }

    public function active($id)
    {
        $theme = Theme::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
        if (!empty($theme)) {
            Theme::where('panel_id', Auth::user()->panel_id)->update(['status' => 'Deactivated']);
            $theme->update(['status' => 'Active']);
            return redirect()->back()->with('success', 'Theme active successfully!');
        }
        return redirect()->back()->with('error', 'Theme not found!');
    }

    public function reset($id)
    {
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
    }
}
