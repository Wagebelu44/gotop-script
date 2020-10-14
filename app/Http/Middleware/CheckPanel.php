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
                                return abort(10000);
                            }
                        } else {
                            return abort(10001);
                        }
                    } else {
                        return abort(10001);
                    }
                } catch(Exception $e) {
                    return abort(10002);
                }
            }
        } elseif (env('PROJECT') == 'sandbox') {
            return abort(10000);
            session(['panel' => env('PROJECT_SANDBOX_PANEL')]);
        }

        return $next($request);
    }
}
