<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Support\Arr;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($this->isHttpException($exception)) {
            if ($exception->getStatusCode() == 10000) {
                return response()->view('panel-not-found', ['msg' => 'Domain not found or suspended. Please contact with provider!'], 404);
            }
    
            if ($exception->getStatusCode() == 10001) {
                return response()->view('panel-not-found', ['msg' => 'Network error. Please contact with provider!'], 404);
            }
    
            if ($exception->getStatusCode() == 10002) {
                return response()->view('panel-not-found', ['msg' => 'Server not found. Please contact with provider!'], 404);
            }
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => $exception->getMessage()], 401);
        }
        $guard = Arr::get($exception->guards(), 0);
        switch ($guard) {
            case 'admin':
                $login = 'admin.login';
                break;

            default:
                $login = 'login';
                break;
        }
        return redirect()->guest(route($login));
    }
}
