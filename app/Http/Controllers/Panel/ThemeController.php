<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MediaController;
use Illuminate\Http\Request;
use App\Models\Theme;
use App\Models\ThemePage;
use Illuminate\Support\Facades\Auth;

class ThemeController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('themes')) {
            $themes = Theme::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'ASC')->get();
            return view('panel.theme.index', compact('themes'));
        } else {
            return view('panel.permission');
        }
    }

    public function edit(Request $request, $id)
    {
        if (Auth::user()->can('themes')) {
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
        } else {
            return view('panel.permission');
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('themes')) {
            $page = ThemePage::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($page)) {
                return redirect()->back()->with('error', 'Page not found!');
            }

            $request->validate([
                'content' => 'required',
            ]);
            $page->update(['content' => $request->content]);

            return redirect()->back()->with('success', 'Page update successfully!');
        } else {
            return view('panel.permission');
        }
    }

    public function active($id)
    {
        if (Auth::user()->can('themes')) {
            $theme = Theme::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (!empty($theme)) {
                Theme::where('panel_id', Auth::user()->panel_id)->update(['status' => 'Deactivated']);
                $theme->update(['status' => 'Active']);

                $image = $this->screenShotUrl(Auth::user()->panel_id, $id);
                $theme->update(['snapshot' => $image['name']]);

                return redirect()->back()->with('success', 'Theme active successfully!');
            }
            return redirect()->back()->with('error', 'Theme not found!');
        } else {
            return view('panel.permission');
        }
    }

    public function reset($id)
    {
        if (Auth::user()->can('themes')) {
            // $page = ThemePage::with('page.globalPage')->where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            // if (!empty($page)) {
            //     $content = $page->page->globalPage->content;
            //     if (empty($content)) {
            //         $content = defaultThemePageContent();
            //     }
            //     $page->update(['content' => $content]);
            //     return redirect()->back()->with('success', 'Theme active successfully!');
            // }

            $page = ThemePage::with(['theme', 'page'])->where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (!empty($page)) {
                if (file_exists(public_path($page->theme->location.'/'.$page->page->default_url.'.twig'))) {
                    $content = file_get_contents(public_path($page->theme->location.'/'.$page->page->default_url.'.twig'));
                } else {
                    $content = defaultThemePageContent();
                }
                $page->update(['content' => $content]);
                return redirect()->back()->with('success', 'Theme active successfully!');
            }

            return redirect()->back()->with('error', 'Theme not found!');
        } else {
            return view('panel.permission');
        }
    }

    private function screenShotUrl($panelId, $themeId)
    {
        $url = url('/');
        $resp = file_get_contents("https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=$url&screenshot=true");
        $resp = json_decode($resp, true);
        $data = str_replace('_', '/', $resp['lighthouseResult']['audits']['final-screenshot']['details']['data']);
        $data = str_replace('-', '+', $data);
        echo '<img src="' . $data . '" />';
        $img = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));

        return (new MediaController())->contentUpload($img, 'themes/'.$panelId, $themeId.'.jpg');
    } 
}
