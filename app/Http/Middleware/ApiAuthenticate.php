<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class ApiAuthenticate
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
        // Validate form data
        if (isset($request->action) && $request->action== 'balance') {
            # code...
            $rules = array(
                'key' => 'required|string|max:255',
                'action' => 'required|string|in:balance',
            );
        }
        else 
        {
            $rules = array(
                'api_token' => 'required|string|max:255',
                'action' => 'required|string|in:services,add,status',
            );
        }
        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors'=> $validator->getMessageBag()->toarray()));
        }

        if (!User::where('api_key', $request->api_token)->first()) {
            return response()->json(["error" => "invalid_credentials", "error_description" => "The api_token were incorrect.", "message" => "The user api_token were incorrect."], 401);
        }
        return $next($request);
    }
}
