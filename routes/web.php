<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'Web\PageController@index')->name('home');

Auth::routes(['verify' => true]);
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/home', 'User\DashboardController@index')->name('home');
});

Route::group(['prefix' => 'admin'], function () {
    // Authentication Routes...
    Route::get('/', 'Panel\Auth\LoginController@showLoginForm')->name('panel.login');
    Route::post('/', 'Panel\Auth\LoginController@login');
    Route::post('logout', 'Panel\Auth\LoginController@logout')->name('panel.logout');

    Route::get('/register', 'Panel\Auth\RegisterController@showRegistrationForm')->name('panel.register');
    Route::post('/register', 'Panel\Auth\RegisterController@register');

    // Password Reset Routes...
    Route::post('password/email', 'Panel\Auth\ForgotPasswordController@sendResetLinkEmail')->name('panel.password.email');
    Route::get('password/reset', 'Panel\Auth\ForgotPasswordController@showLinkRequestForm')->name('panel.password.request');
    Route::post('password/reset', 'Panel\Auth\ResetPasswordController@reset')->name('panel.password.update');
    Route::get('password/reset/{token}', 'Panel\Auth\ResetPasswordController@showResetForm')->name('panel.password.reset');

    Route::group(['middleware' => 'auth:panelAdmin'], function () {
        Route::get('dashboard', 'Panel\DashboardController@index')->name('panel.dashboard');
    });
});
