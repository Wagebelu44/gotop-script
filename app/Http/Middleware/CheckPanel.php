<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Support\Facades\Http;

class CheckPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //Check valid script...
        if (env('PROJECT') == 'live') {
            $domain = request()->getHost();

            if (session('domain') != $domain) {
                try {
                    $response = Http::post(env('PROJECT_LIVE_URL').'/api/check-domain', [
                        'domain' => $domain,
                        'token' => env('PANLE_REQUEST_TOKEN'),
                    ]);

                    if ($response->ok()) {
                        if ($response->successful()) {

                            $data = json_decode($response->body());
                            if ($data->success) {
                                session(['panel' => $data->panel]);
                                session(['domain' => $domain]);
    
                                return $next($request);
                            } else {                        
                                return redirect()->route('panel-not-found')->with('panelErr', 'Domain not found or suspended. Please contact with provider!');
                            }
                        } else {
                            return redirect()->route('panel-not-found')->with('panelErr', 'Network error. Please contact with provider!');
                        }
                    } else {
                        return redirect()->route('panel-not-found')->with('panelErr', 'Network error. Please contact with provider!');
                    }
                } catch(Exception $e) {
                    return redirect()->route('panel-not-found')->with('panelErr', $e->getMessage());
                }
            }
        } elseif (env('PROJECT') == 'sandbox') {
            session(['panel' => env('PROJECT_SANDBOX_PANEL')]);
        }

        return $next($request);
    }
}
