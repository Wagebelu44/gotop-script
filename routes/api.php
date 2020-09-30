<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/test', 'Panel\ApiController@index');
Route::get('/post-log-name', 'Panel\ApiController@sentLogName');
Route::get('/post-active-log', 'Panel\ApiController@sentActiveLog');
Route::post('/post-permissions', 'Panel\ApiController@postPermissions');
Route::post('/active-panel', 'Panel\ApiController@activePanel');
Route::post('/canceled-panel', 'Panel\ApiController@canceledPanel');
Route::post('/save-user', 'Panel\ApiController@saveUser');
Route::post('/user-password', 'Panel\ApiController@userPasswordUpdate');
Route::post('/save-payment-method', 'Panel\ApiController@saveMethod');
Route::post('/delete-payment-method', 'Panel\ApiController@deleteMethod');
Route::post('/save-currency', 'Panel\ApiController@saveCurrency');
Route::post('/delete-currency', 'Panel\ApiController@deleteCurrency');


//Service API
Route::group(['middleware' => ['api.auth'], 'namespace' => 'Api', 'prefix' => 'v2'], function () {
    Route::post('/', 'ApiController@index');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
