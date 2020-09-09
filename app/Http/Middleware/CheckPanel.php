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
        if (env('SANDBOX')) {
            $domain = request()->getHost();
            $domain = 'test.com';

            if (session('domain') != $domain) {
                try {
                    $response = Http::post(env('SANDBOX_URL').'/api/check-domain', [
                        'domain' => $domain,
                        'token' => env('PANLE_REQUEST_TOKEN'),
                    ]);

                    if ($response->successful()) {
                        $data = json_decode($response->body());
                        if ($data->success) {
                            session(['panel' => $data->panel]);
                            session(['domain' => $domain]);

                            return $next($request);
                        } else {
                            $message = null;
                            return view('panel-not-found', compact('message'));
                        }
                    } else {
                        $message = null;
                        return view('panel-not-found', compact('message'));
                    }
                } catch(Exception $e) {
                    $message = $e->getMessage();
                    return view('panel-not-found', compact('message'));
                }
            }
        }

        return $next($request);
    }
}
